<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnlineBankingService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $merchantId;
    private string $baseUrl;
    private string $webhookUrl;

    public function __construct()
    {
        $this->apiKey = config('services.online_banking.api_key') ?? '';
        $this->merchantId = config('services.online_banking.merchant_id') ?? '';
        $this->baseUrl = config('services.online_banking.base_url', 'https://api.onlinebanking.ph');
        $this->webhookUrl = route('payment.webhook', ['gateway' => 'online_banking']);
    }

    public function getName(): string
    {
        return 'online_banking';
    }

    public function createPayment(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/payment-requests', [
                'merchant_id' => $this->merchantId,
                'amount' => $data['amount'],
                'currency' => 'PHP',
                'description' => $data['description'] ?? 'Custom Order Payment',
                'reference_number' => $data['reference_id'],
                'redirect_url' => route('payment.return', ['gateway' => 'online_banking']),
                'callback_url' => $this->webhookUrl,
                'customer' => [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                ],
                'payment_methods' => [
                    'bdo_online',
                    'bpi_online',
                    'metrobank_online',
                    'landbank_online',
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Online Banking payment creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $data,
            ]);

            throw new \Exception('Online Banking payment creation failed');
        } catch (\Exception $e) {
            Log::error('Online Banking API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/v1/payment-requests/' . $transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Online Banking payment verification failed', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Online Banking payment verification failed');
        } catch (\Exception $e) {
            Log::error('Online Banking verification error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            // Verify webhook signature
            $signature = request()->header('X-OnlineBanking-Signature');
            if (!$this->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid Online Banking webhook signature');
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
                case 'payment.expired':
                    $this->handleExpiredPayment($paymentData);
                    break;
                default:
                    Log::info('Unhandled Online Banking webhook event', ['event' => $event]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Online Banking webhook error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function verifyWebhookSignature(array $payload, ?string $signature): bool
    {
        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->apiKey);
        return hash_equals($expectedSignature, $signature);
    }

    private function handleSuccessfulPayment(array $paymentData): void
    {
        $referenceNumber = $paymentData['reference_number'] ?? '';
        $transactionId = $paymentData['payment_request_id'] ?? '';
        $amount = $paymentData['amount'] ?? 0;
        $bankCode = $paymentData['bank_code'] ?? '';

        $order = \App\Models\CustomOrder::where('transaction_id', $referenceNumber)->first();
        if (!$order) {
            Log::warning('Order not found for Online Banking payment', ['reference_number' => $referenceNumber]);
            return;
        }

        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->paid_at = now();
        $order->transaction_id = $transactionId;
        $order->payment_notes = "Paid via Online Banking ({$bankCode}) - Amount: ₱{$amount}";
        $order->save();

        Log::info('Online Banking payment processed successfully', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'bank_code' => $bankCode,
            'amount' => $amount,
        ]);
    }

    private function handleFailedPayment(array $paymentData): void
    {
        $referenceNumber = $paymentData['reference_number'] ?? '';
        $reason = $paymentData['failure_reason'] ?? 'Unknown error';

        $order = \App\Models\CustomOrder::where('transaction_id', $referenceNumber)->first();
        if (!$order) {
            Log::warning('Order not found for failed Online Banking payment', ['reference_number' => $referenceNumber]);
            return;
        }

        $order->payment_status = 'failed';
        $order->payment_notes = "Online Banking payment failed: {$reason}";
        $order->save();

        Log::warning('Online Banking payment failed', [
            'order_id' => $order->id,
            'reference_number' => $referenceNumber,
            'reason' => $reason,
        ]);
    }

    private function handleExpiredPayment(array $paymentData): void
    {
        $referenceNumber = $paymentData['reference_number'] ?? '';

        $order = \App\Models\CustomOrder::where('transaction_id', $referenceNumber)->first();
        if (!$order) {
            Log::warning('Order not found for expired Online Banking payment', ['reference_number' => $referenceNumber]);
            return;
        }

        $order->payment_status = 'expired';
        $order->payment_notes = "Online Banking payment expired";
        $order->save();

        Log::info('Online Banking payment expired', [
            'order_id' => $order->id,
            'reference_number' => $referenceNumber,
        ]);
    }
}
