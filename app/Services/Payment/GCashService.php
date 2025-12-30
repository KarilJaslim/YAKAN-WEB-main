<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GCashService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl;
    private string $webhookUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gcash.api_key') ?? '';
        $this->apiSecret = config('services.gcash.api_secret') ?? '';
        $this->baseUrl = config('services.gcash.base_url', 'https://api.gcash.com');
        $this->webhookUrl = route('payment.webhook', ['gateway' => 'gcash']);
    }

    public function getName(): string
    {
        return 'gcash';
    }

    public function createPayment(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/payments', [
                'amount' => $data['amount'],
                'currency' => 'PHP',
                'description' => $data['description'] ?? 'Custom Order Payment',
                'reference_id' => $data['reference_id'],
                'callback_url' => $this->webhookUrl,
                'customer' => [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'phone' => $data['customer_phone'] ?? null,
                ],
                'payment_method_types' => ['gcash_wallet'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GCash payment creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $data,
            ]);

            throw new \Exception('GCash payment creation failed');
        } catch (\Exception $e) {
            Log::error('GCash API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/v1/payments/' . $transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GCash payment verification failed', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('GCash payment verification failed');
        } catch (\Exception $e) {
            Log::error('GCash verification error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            // Verify webhook signature
            $signature = request()->header('X-Gcash-Signature');
            if (!$this->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid GCash webhook signature');
                return false;
            }

            $event = $payload['event'] ?? '';
            $paymentData = $payload['data'] ?? [];

            switch ($event) {
                case 'payment.success':
                    $this->handleSuccessfulPayment($paymentData);
                    break;
                case 'payment.failed':
                    $this->handleFailedPayment($paymentData);
                    break;
                default:
                    Log::info('Unhandled GCash webhook event', ['event' => $event]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('GCash webhook error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function verifyWebhookSignature(array $payload, ?string $signature): bool
    {
        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->apiSecret);
        return hash_equals($expectedSignature, $signature);
    }

    private function handleSuccessfulPayment(array $paymentData): void
    {
        $referenceId = $paymentData['reference_id'] ?? '';
        $transactionId = $paymentData['id'] ?? '';
        $amount = $paymentData['amount'] ?? 0;

        // Find the order by reference_id
        $order = \App\Models\CustomOrder::where('transaction_id', $referenceId)->first();
        if (!$order) {
            Log::warning('Order not found for GCash payment', ['reference_id' => $referenceId]);
            return;
        }

        // Update order status
        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->paid_at = now();
        $order->transaction_id = $transactionId;
        $order->payment_notes = "Paid via GCash - Amount: ₱{$amount}";
        $order->save();

        Log::info('GCash payment processed successfully', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount' => $amount,
        ]);
    }

    private function handleFailedPayment(array $paymentData): void
    {
        $referenceId = $paymentData['reference_id'] ?? '';
        $reason = $paymentData['failure_reason'] ?? 'Unknown error';

        $order = \App\Models\CustomOrder::where('transaction_id', $referenceId)->first();
        if (!$order) {
            Log::warning('Order not found for failed GCash payment', ['reference_id' => $referenceId]);
            return;
        }

        $order->payment_status = 'failed';
        $order->payment_notes = "GCash payment failed: {$reason}";
        $order->save();

        Log::warning('GCash payment failed', [
            'order_id' => $order->id,
            'reference_id' => $referenceId,
            'reason' => $reason,
        ]);
    }
}
