<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create a payment session/transaction
     */
    public function createPayment(array $data): array;

    /**
     * Verify payment status using transaction ID
     */
    public function verifyPayment(string $transactionId): array;

    /**
     * Handle webhook callbacks
     */
    public function handleWebhook(array $payload): bool;

    /**
     * Get payment gateway name
     */
    public function getName(): string;
}
