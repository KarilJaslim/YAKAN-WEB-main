<?php

namespace App\Services\Payment;

use App\Models\CustomOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SandboxPaymentService
{
    private bool $isSandboxMode;
    private array $mockResponses;
    private array $testCards;

    public function __construct()
    {
        $this->isSandboxMode = config('services.payment.sandbox_mode', true);
        $this->initializeMockResponses();
        $this->initializeTestCards();
    }

    /**
     * Create sandbox payment with simulated responses
     */
    public function createSandboxPayment(CustomOrder $order, string $method): array
    {
        if (!$this->isSandboxMode) {
            throw new \Exception('Sandbox mode is not enabled');
        }

        $transactionId = $this->generateTransactionId($method);
        
        // Store sandbox transaction details
        $order->update([
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'payment_status' => 'pending',
            'payment_notes' => "[SANDBOX] Test transaction created"
        ]);

        $response = $this->getMockPaymentResponse($method, $order, $transactionId);
        
        Log::info('Sandbox payment created', [
            'order_id' => $order->id,
            'method' => $method,
            'transaction_id' => $transactionId,
            'amount' => $order->final_price
        ]);

        return $response;
    }

    /**
     * Simulate payment verification with various scenarios
     */
    public function verifySandboxPayment(string $method, string $transactionId, string $scenario = 'success'): array
    {
        if (!$this->isSandboxMode) {
            throw new \Exception('Sandbox mode is not enabled');
        }

        $order = CustomOrder::where('transaction_id', $transactionId)->first();
        if (!$order) {
            throw new \Exception('Order not found');
        }

        $response = $this->getMockVerificationResponse($method, $transactionId, $scenario);
        
        // Update order based on scenario
        $this->updateOrderStatus($order, $scenario, $response);
        
        return $response;
    }

    /**
     * Generate test payment scenarios
     */
    public function generateTestScenarios(): array
    {
        return [
            'success' => [
                'name' => 'Successful Payment',
                'description' => 'Payment completes successfully',
                'status' => 'paid',
                'delay' => 2 // seconds
            ],
            'failed' => [
                'name' => 'Failed Payment',
                'description' => 'Payment fails due to insufficient funds',
                'status' => 'failed',
                'delay' => 1
            ],
            'pending' => [
                'name' => 'Pending Payment',
                'description' => 'Payment is processing',
                'status' => 'pending',
                'delay' => 5
            ],
            'timeout' => [
                'name' => 'Payment Timeout',
                'description' => 'Payment gateway timeout',
                'status' => 'timeout',
                'delay' => 30
            ],
            'fraud' => [
                'name' => 'Fraud Detection',
                'description' => 'Payment flagged for review',
                'status' => 'fraud_review',
                'delay' => 3
            ]
        ];
    }

    /**
     * Get sandbox payment URLs for testing
     */
    public function getSandboxUrls(): array
    {
        return [
            'gcash' => [
                'payment_url' => route('payment.sandbox.simulate', ['method' => 'gcash']),
                'webhook_url' => route('payment.sandbox.webhook', ['gateway' => 'gcash']),
                'redirect_url' => route('payment.sandbox.redirect', ['method' => 'gcash'])
            ],
            'online_banking' => [
                'payment_url' => route('payment.sandbox.simulate', ['method' => 'online_banking']),
                'webhook_url' => route('payment.sandbox.webhook', ['gateway' => 'online_banking']),
                'redirect_url' => route('payment.sandbox.redirect', ['method' => 'online_banking'])
            ],
            'bank_transfer' => [
                'instructions_url' => route('payment.sandbox.instructions'),
                'verification_url' => route('payment.sandbox.verify')
            ]
        ];
    }

    /**
     * Generate test bank transfer details
     */
    public function generateBankTransferDetails(CustomOrder $order): array
    {
        return [
            'bank_name' => 'SANDBOX BANK',
            'account_name' => 'TEST ACCOUNT',
            'account_number' => '1234567890',
            'amount' => $order->final_price,
            'reference_code' => 'SANDBOX-' . strtoupper(uniqid()),
            'expiry_time' => now()->addHours(24)->format('Y-m-d H:i:s'),
            'test_deposit_accounts' => [
                'success_account' => '9999999999',
                'failed_account' => '1111111111',
                'pending_account' => '8888888888'
            ]
        ];
    }

    private function initializeMockResponses(): void
    {
        $this->mockResponses = [
            'gcash' => [
                'success' => [
                    'status' => 'success',
                    'transaction_id' => 'GCASH_SANDBOX_' . uniqid(),
                    'payment_url' => 'https://sandbox.gcash.com/pay/TEST_' . uniqid(),
                    'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
                    'expires_at' => now()->addMinutes(15)->toISOString()
                ],
                'failed' => [
                    'status' => 'failed',
                    'error' => 'Insufficient funds',
                    'error_code' => 'INSUFFICIENT_FUNDS'
                ]
            ],
            'online_banking' => [
                'success' => [
                    'status' => 'pending',
                    'transaction_id' => 'BANK_SANDBOX_' . uniqid(),
                    'redirect_url' => 'https://sandbox.onlinebanking.ph/redirect/TEST_' . uniqid(),
                    'reference_code' => 'BANK' . rand(100000, 999999)
                ]
            ]
        ];
    }

    private function initializeTestCards(): void
    {
        $this->testCards = [
            'success' => [
                'card_number' => '4111111111111111',
                'expiry' => '12/25',
                'cvv' => '123',
                'name' => 'TEST SUCCESS CARD'
            ],
            'failed' => [
                'card_number' => '4000000000000002',
                'expiry' => '12/25',
                'cvv' => '123',
                'name' => 'TEST FAIL CARD'
            ],
            'fraud' => [
                'card_number' => '4100000000000001',
                'expiry' => '12/25',
                'cvv' => '123',
                'name' => 'TEST FRAUD CARD'
            ]
        ];
    }

    private function generateTransactionId(string $method): string
    {
        return strtoupper($method) . '_SANDBOX_' . uniqid() . '_' . time();
    }

    private function getMockPaymentResponse(string $method, CustomOrder $order, string $transactionId): array
    {
        $baseResponse = [
            'success' => true,
            'transaction_id' => $transactionId,
            'amount' => $order->final_price,
            'currency' => 'PHP',
            'created_at' => now()->toISOString(),
            'sandbox' => true
        ];

        switch ($method) {
            case 'gcash':
                return array_merge($baseResponse, [
                    'payment_url' => 'https://sandbox.gcash.com/pay/' . $transactionId,
                    'qr_code' => $this->generateMockQRCode($transactionId),
                    'expires_at' => now()->addMinutes(15)->toISOString()
                ]);
                
            case 'online_banking':
                return array_merge($baseResponse, [
                    'redirect_url' => 'https://sandbox.onlinebanking.ph/redirect/' . $transactionId,
                    'bank_options' => ['BPI', 'BDO', 'Metrobank', 'UnionBank']
                ]);
                
            case 'bank_transfer':
                return array_merge($baseResponse, $this->generateBankTransferDetails($order));
                
            default:
                throw new \Exception("Unsupported payment method: {$method}");
        }
    }

    private function getMockVerificationResponse(string $method, string $transactionId, string $scenario): array
    {
        $responses = [
            'success' => [
                'status' => 'paid',
                'paid_at' => now()->toISOString(),
                'amount' => CustomOrder::where('transaction_id', $transactionId)->first()->final_price
            ],
            'failed' => [
                'status' => 'failed',
                'failure_reason' => 'Insufficient funds',
                'failed_at' => now()->toISOString()
            ],
            'pending' => [
                'status' => 'processing',
                'estimated_completion' => now()->addMinutes(5)->toISOString()
            ],
            'timeout' => [
                'status' => 'timeout',
                'timeout_reason' => 'Payment gateway timeout'
            ],
            'fraud' => [
                'status' => 'fraud_review',
                'fraud_score' => 0.95,
                'review_required' => true
            ]
        ];

        return $responses[$scenario] ?? $responses['success'];
    }

    private function updateOrderStatus(CustomOrder $order, string $scenario, array $response): void
    {
        $statusMap = [
            'success' => 'paid',
            'failed' => 'failed',
            'pending' => 'processing',
            'timeout' => 'timeout',
            'fraud' => 'fraud_review'
        ];

        $order->update([
            'payment_status' => $statusMap[$scenario] ?? 'pending',
            'status' => $scenario === 'success' ? 'completed' : 'pending_payment',
            'payment_notes' => "[SANDBOX] {$scenario}: " . json_encode($response),
            'paid_at' => $scenario === 'success' ? now() : null
        ]);
    }

    private function generateMockQRCode(string $transactionId): string
    {
        // Generate a simple mock QR code (base64 encoded 1x1 pixel)
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    }
}
