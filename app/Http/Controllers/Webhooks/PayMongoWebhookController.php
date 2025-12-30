<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayMongoWebhookController extends Controller
{
    /**
     * Handle PayMongo webhook events
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature (if PayMongo provides one)
        $signature = $request->header('Paymongo-Signature');
        if ($signature && !$this->verifySignature($request, $signature)) {
            Log::warning('Invalid PayMongo webhook signature', [
                'signature' => $signature,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->all();
        
        Log::info('PayMongo webhook received', [
            'event_type' => $event['type'] ?? 'unknown',
            'data' => $event
        ]);

        try {
            switch ($event['type'] ?? null) {
                case 'payment.paid':
                    return $this->handlePaymentPaid($event['data']);
                
                case 'payment.failed':
                    return $this->handlePaymentFailed($event['data']);
                
                case 'payment.refunded':
                    return $this->handlePaymentRefunded($event['data']);
                
                case 'source.chargeable':
                    return $this->handleSourceChargeable($event['data']);
                
                default:
                    Log::info('Unhandled PayMongo event type', ['type' => $event['type'] ?? 'unknown']);
                    return response()->json(['status' => 'event_not_handled']);
            }
        } catch (\Exception $e) {
            Log::error('Error processing PayMongo webhook', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);
            
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentPaid($data)
    {
        $payment = $data['attributes']['payment'] ?? null;
        if (!$payment) {
            Log::error('Payment data not found in webhook');
            return response()->json(['error' => 'Payment data missing'], 400);
        }

        $paymentId = $payment['id'] ?? null;
        $amount = $payment['amount'] ?? 0;
        $currency = $payment['currency'] ?? 'PHP';
        $sourceId = $payment['source']['id'] ?? null;

        // Find custom order by payment reference or source ID
        $order = $this->findOrderByPaymentReference($paymentId, $sourceId);
        
        if (!$order) {
            Log::warning('No order found for payment', [
                'payment_id' => $paymentId,
                'source_id' => $sourceId
            ]);
            return response()->json(['status' => 'order_not_found']);
        }

        // Update order payment status
        $order->payment_status = 'paid';
        $order->paid_amount = $amount / 100; // Convert from cents
        $order->payment_method = 'paymongo';
        $order->payment_reference = $paymentId;
        $order->paid_at = now();
        $order->save();

        // Update order status if needed
        if ($order->status === 'pending') {
            $order->status = 'approved';
            $order->save();
        }

        // Send payment confirmation notification
        $this->sendPaymentConfirmation($order);

        Log::info('Payment processed successfully', [
            'order_id' => $order->id,
            'payment_id' => $paymentId,
            'amount' => $amount / 100
        ]);

        return response()->json(['status' => 'payment_processed']);
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($data)
    {
        $payment = $data['attributes']['payment'] ?? null;
        if (!$payment) {
            return response()->json(['error' => 'Payment data missing'], 400);
        }

        $paymentId = $payment['id'] ?? null;
        $sourceId = $payment['source']['id'] ?? null;
        $failureReason = $payment['failure_reason'] ?? 'Unknown error';

        $order = $this->findOrderByPaymentReference($paymentId, $sourceId);
        
        if (!$order) {
            return response()->json(['status' => 'order_not_found']);
        }

        // Update order payment status
        $order->payment_status = 'failed';
        $order->payment_failure_reason = $failureReason;
        $order->payment_reference = $paymentId;
        $order->save();

        // Send payment failure notification
        $this->sendPaymentFailureNotification($order, $failureReason);

        Log::info('Payment failure processed', [
            'order_id' => $order->id,
            'payment_id' => $paymentId,
            'reason' => $failureReason
        ]);

        return response()->json(['status' => 'payment_failure_processed']);
    }

    /**
     * Handle refunded payment
     */
    private function handlePaymentRefunded($data)
    {
        $payment = $data['attributes']['payment'] ?? null;
        if (!$payment) {
            return response()->json(['error' => 'Payment data missing'], 400);
        }

        $paymentId = $payment['id'] ?? null;
        $sourceId = $payment['source']['id'] ?? null;
        $refundAmount = $payment['amount'] ?? 0;

        $order = $this->findOrderByPaymentReference($paymentId, $sourceId);
        
        if (!$order) {
            return response()->json(['status' => 'order_not_found']);
        }

        // Update order payment status
        $order->payment_status = 'refunded';
        $order->refunded_amount = $refundAmount / 100;
        $order->refunded_at = now();
        $order->save();

        // Send refund notification
        $this->sendRefundNotification($order);

        Log::info('Payment refund processed', [
            'order_id' => $order->id,
            'payment_id' => $paymentId,
            'refund_amount' => $refundAmount / 100
        ]);

        return response()->json(['status' => 'refund_processed']);
    }

    /**
     * Handle chargeable source (for GCash, etc.)
     */
    private function handleSourceChargeable($data)
    {
        $source = $data['attributes']['source'] ?? null;
        if (!$source) {
            return response()->json(['error' => 'Source data missing'], 400);
        }

        $sourceId = $source['id'] ?? null;
        $sourceType = $source['type'] ?? null;

        // Find order by source ID
        $order = CustomOrder::where('payment_source_id', $sourceId)->first();
        
        if (!$order) {
            return response()->json(['status' => 'order_not_found']);
        }

        // Update order status to show payment is being processed
        $order->payment_status = 'processing';
        $order->save();

        Log::info('Source chargeable processed', [
            'order_id' => $order->id,
            'source_id' => $sourceId,
            'source_type' => $sourceType
        ]);

        return response()->json(['status' => 'source_chargeable_processed']);
    }

    /**
     * Find order by payment reference or source ID
     */
    private function findOrderByPaymentReference($paymentId, $sourceId = null)
    {
        // Try to find by payment reference first
        $order = CustomOrder::where('payment_reference', $paymentId)->first();
        
        if ($order) {
            return $order;
        }

        // Try to find by source ID
        if ($sourceId) {
            $order = CustomOrder::where('payment_source_id', $sourceId)->first();
            
            if ($order) {
                return $order;
            }
        }

        // Try to find by order ID in payment description
        $order = CustomOrder::where('payment_reference', 'like', "%{$paymentId}%")->first();
        
        return $order;
    }

    /**
     * Send payment confirmation notification
     */
    private function sendPaymentConfirmation($order)
    {
        try {
            // Send email notification
            \Mail::to($order->user->email)->send(
                new \App\Mail\CustomOrderPaymentConfirmation($order)
            );

            // Send in-app notification
            $order->user->notifications()->create([
                'type' => 'payment_confirmed',
                'title' => 'Payment Confirmed',
                'message' => "Payment for custom order #{$order->id} has been confirmed. Your order is now being processed.",
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $order->paid_amount
                ]
            ]);

            // Send SMS notification if phone number is available
            if ($order->user->phone) {
                $this->sendSMSNotification($order->user->phone, 
                    "Payment confirmed for custom order #{$order->id}. Amount: ₱{$order->paid_amount}. Your order is now being processed."
                );
            }

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send payment failure notification
     */
    private function sendPaymentFailureNotification($order, $reason)
    {
        try {
            // Send email notification
            \Mail::to($order->user->email)->send(
                new \App\Mail\CustomOrderPaymentFailure($order, $reason)
            );

            // Send in-app notification
            $order->user->notifications()->create([
                'type' => 'payment_failed',
                'title' => 'Payment Failed',
                'message' => "Payment for custom order #{$order->id} failed. Reason: {$reason}. Please try again.",
                'data' => [
                    'order_id' => $order->id,
                    'reason' => $reason
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment failure notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send refund notification
     */
    private function sendRefundNotification($order)
    {
        try {
            // Send email notification
            \Mail::to($order->user->email)->send(
                new \App\Mail\CustomOrderRefundNotification($order)
            );

            // Send in-app notification
            $order->user->notifications()->create([
                'type' => 'payment_refunded',
                'title' => 'Payment Refunded',
                'message' => "Payment for custom order #{$order->id} has been refunded. Amount: ₱{$order->refunded_amount}.",
                'data' => [
                    'order_id' => $order->id,
                    'refund_amount' => $order->refunded_amount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send refund notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send SMS notification (using a third-party service)
     */
    private function sendSMSNotification($phoneNumber, $message)
    {
        // Implementation depends on your SMS service provider
        // This is a placeholder for SMS integration
        
        Log::info('SMS notification sent', [
            'phone' => $phoneNumber,
            'message' => $message
        ]);
        
        // Example implementation with a hypothetical SMS service:
        // try {
        //     $response = Http::post('https://api.sms-service.com/send', [
        //         'api_key' => config('services.sms.api_key'),
        //         'phone' => $phoneNumber,
        //         'message' => $message
        //     ]);
        //     
        //     if (!$response->successful()) {
        //         throw new \Exception('SMS service error');
        //     }
        // } catch (\Exception $e) {
        //     Log::error('SMS notification failed', [
        //         'phone' => $phoneNumber,
        //         'error' => $e->getMessage()
        //     ]);
        // }
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature(Request $request, $signature)
    {
        // Implement signature verification based on PayMongo's documentation
        // This is a placeholder implementation
        
        $webhookSecret = config('services.paymongo.webhook_secret');
        if (!$webhookSecret) {
            return true; // Skip verification if no secret is configured
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }
}
