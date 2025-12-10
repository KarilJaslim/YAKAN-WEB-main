<?php

namespace App\Services\Payment;

use App\Models\CustomOrder;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private array $gateways;
    private SandboxPaymentService $sandboxService;
    private bool $isSandboxMode;

    public function __construct(SandboxPaymentService $sandboxService = null)
    {
        $this->sandboxService = $sandboxService ?? new SandboxPaymentService();
        $this->isSandboxMode = config('services.payment.sandbox_mode', false);
        
        $this->gateways = [
            'gcash' => new GCashService(),
            'online_banking' => new OnlineBankingService(),
            'bank_transfer' => new BankTransferService(),
        ];
    }

    /**
     * Get payment gateway instance
     */
    public function getGateway(string $method): ?PaymentGatewayInterface
    {
        return $this->gateways[$method] ?? null;
    }

    /**
     * Create payment using selected gateway
     */
    public function createPayment(CustomOrder $order, string $method): array
    {
        // Use sandbox if enabled
        if ($this->isSandboxMode) {
            return $this->sandboxService->createSandboxPayment($order, $method);
        }

        $gateway = $this->getGateway($method);
        if (!$gateway) {
            throw new \Exception("Payment gateway '{$method}' not found");
        }

        $data = [
            'amount' => $order->final_price,
            'reference_id' => 'ORD-' . $order->id,
            'description' => "Payment for Custom Order #{$order->id}",
            'customer_name' => $order->user->name,
            'customer_email' => $order->user->email,
            'customer_phone' => $order->user->phone ?? null,
        ];

        try {
            $result = $gateway->createPayment($data);
            
            // Store payment method on order
            $order->payment_method = $method;
            $order->payment_status = 'pending';
            $order->save();

            Log::info('Payment created successfully', [
                'order_id' => $order->id,
                'method' => $method,
                'result' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'order_id' => $order->id,
                'method' => $method,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(string $method, string $transactionId): array
    {
        // Use sandbox if enabled
        if ($this->isSandboxMode) {
            return $this->sandboxService->verifySandboxPayment($method, $transactionId, 'success');
        }

        $gateway = $this->getGateway($method);
        if (!$gateway) {
            throw new \Exception("Payment gateway '{$method}' not found");
        }

        return $gateway->verifyPayment($transactionId);
    }

    /**
     * Handle webhook from payment gateway
     */
    public function handleWebhook(string $gateway, array $payload): bool
    {
        // Use sandbox if enabled
        if ($this->isSandboxMode) {
            return $this->sandboxService->handleWebhook($payload);
        }

        $gatewayInstance = $this->getGateway($gateway);
        if (!$gatewayInstance) {
            Log::warning("Payment gateway '{$gateway}' not found for webhook");
            return false;
        }

        return $gatewayInstance->handleWebhook($payload);
    }

    /**
     * Get payment instructions for bank transfer
     */
    public function getBankTransferInstructions(CustomOrder $order): array
    {
        // Use sandbox if enabled
        if ($this->isSandboxMode) {
            return $this->sandboxService->generateBankTransferDetails($order);
        }

        $gateway = $this->getGateway('bank_transfer');
        if (!$gateway) {
            throw new \Exception('Bank transfer gateway not found');
        }

        return $gateway->generatePaymentInstructions([
            'amount' => $order->final_price,
            'reference_id' => 'ORD-' . $order->id,
        ]);
    }

    /**
     * Validate bank transfer proof
     */
    public function validateBankTransferProof(array $data): array
    {
        // Use sandbox if enabled
        if ($this->isSandboxMode) {
            return $this->sandboxService->verifySandboxPayment('bank_transfer', $data['transaction_id'], 'success');
        }

        $gateway = $this->getGateway('bank_transfer');
        if (!$gateway) {
            throw new \Exception('Bank transfer gateway not found');
        }

        return $gateway->validateTransferProof($data);
    }

    /**
     * Get available payment methods
     */
    public function getAvailableMethods(): array
    {
        return array_map(function ($gateway) {
            return [
                'name' => $gateway->getName(),
                'display_name' => ucwords(str_replace('_', ' ', $gateway->getName())),
                'description' => $this->getMethodDescription($gateway->getName()),
                'sandbox' => $this->isSandboxMode
            ];
        }, $this->gateways);
    }

    /**
     * Get sandbox status and URLs
     */
    public function getSandboxInfo(): array
    {
        return [
            'enabled' => $this->isSandboxMode,
            'urls' => $this->sandboxService->getSandboxUrls(),
            'scenarios' => $this->sandboxService->generateTestScenarios()
        ];
    }

    private function getMethodDescription(string $method): string
    {
        $descriptions = [
            'gcash' => 'Pay instantly using your GCash wallet',
            'online_banking' => 'Pay through your online banking account',
            'bank_transfer' => 'Direct bank transfer to our account',
        ];

        return $descriptions[$method] ?? 'Payment method';
    }
}
