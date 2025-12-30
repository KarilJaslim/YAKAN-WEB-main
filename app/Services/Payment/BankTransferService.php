<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankTransferService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $webhookUrl;

    public function __construct()
    {
        $this->apiKey = config('services.bank_transfer.api_key') ?? '';
        $this->baseUrl = config('services.bank_transfer.base_url', 'https://api.banktransfer.ph');
        $this->webhookUrl = route('payment.webhook', ['gateway' => 'bank_transfer']);
    }

    public function getName(): string
    {
        return 'bank_transfer';
    }

    public function createPayment(array $data): array
    {
        try {
            // For bank transfer, we generate a reference and return payment instructions
            $referenceId = $data['reference_id'];
            
            return [
                'status' => 'pending',
                'reference_id' => $referenceId,
                'payment_instructions' => [
                    'bank_name' => 'Sample Bank',
                    'account_name' => 'Yakan E-commerce',
                    'account_number' => '1234567890',
                    'branch' => 'Main Branch',
                    'amount' => $data['amount'],
                    'currency' => 'PHP',
                    'reference_code' => 'BT-' . $referenceId,
                ],
                'expires_at' => now()->addHours(24)->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Bank Transfer service error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        // For bank transfer, verification is manual through admin verification
        // This method can be used to check if there's a pending verification
        $order = \App\Models\CustomOrder::where('transaction_id', $transactionId)->first();
        
        if (!$order) {
            return ['status' => 'not_found'];
        }

        return [
            'status' => $order->payment_status,
            'transaction_id' => $order->transaction_id,
            'amount' => $order->final_price,
            'verified_at' => $order->paid_at,
        ];
    }

    public function handleWebhook(array $payload): bool
    {
        // Bank transfer typically doesn't have webhooks as it's manual
        // This can be used for bank API integrations if available
        Log::info('Bank Transfer webhook received', ['payload' => $payload]);
        return true;
    }

    /**
     * Generate payment instructions for bank transfer
     */
    public function generatePaymentInstructions(array $data): array
    {
        return [
            'bank_name' => 'Sample Bank',
            'account_name' => 'Yakan E-commerce',
            'account_number' => '1234567890',
            'branch' => 'Main Branch',
            'swift_code' => 'SAMPHMMXXX',
            'amount' => $data['amount'],
            'currency' => 'PHP',
            'reference_code' => 'BT-' . $data['reference_id'],
            'notes' => 'Please include the reference code in your transaction description',
        ];
    }

    /**
     * Validate bank transfer proof (receipt upload)
     */
    public function validateTransferProof(array $data): array
    {
        try {
            // In a real implementation, this might use OCR or AI to validate receipts
            // For now, we'll do basic validation
            $requiredFields = ['transaction_id', 'amount', 'date', 'account_number'];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'valid' => false,
                        'error' => "Missing required field: {$field}",
                    ];
                }
            }

            // Check if amount matches
            if (abs($data['amount'] - $data['expected_amount']) > 0.01) {
                return [
                    'valid' => false,
                    'error' => 'Amount does not match expected amount',
                ];
            }

            return ['valid' => true];
        } catch (\Exception $e) {
            Log::error('Bank transfer proof validation error', ['error' => $e->getMessage()]);
            return [
                'valid' => false,
                'error' => 'Validation failed',
            ];
        }
    }
}
