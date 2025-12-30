<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use App\Services\Payment\SandboxPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SandboxPaymentController extends Controller
{
    private SandboxPaymentService $sandboxService;

    public function __construct(SandboxPaymentService $sandboxService)
    {
        $this->sandboxService = $sandboxService;
    }

    /**
     * Show sandbox payment dashboard
     */
    public function dashboard()
    {
        if (!config('services.payment.sandbox_mode', true)) {
            abort(403, 'Sandbox mode is disabled');
        }

        try {
            $scenarios = $this->sandboxService->generateTestScenarios();
            $urls = $this->sandboxService->getSandboxUrls();
            
            // Safely get recent orders with error handling
            $recentOrders = collect();
            try {
                $recentOrders = CustomOrder::where('payment_notes', 'like', '%[SANDBOX]%')
                    ->latest()
                    ->take(10)
                    ->get();
            } catch (\Exception $e) {
                // If database query fails, continue with empty collection
                \Log::warning('Could not fetch recent sandbox orders: ' . $e->getMessage());
            }

            return view('payment.sandbox.dashboard_standalone');
        } catch (\Exception $e) {
            \Log::error('Sandbox dashboard error: ' . $e->getMessage());
            
            // Return a simple error page if dashboard fails
            return response()->view('errors.500', [
                'message' => 'Sandbox dashboard temporarily unavailable. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create sandbox payment
     */
    public function createPayment(Request $request, CustomOrder $order): JsonResponse
    {
        $request->validate([
            'method' => 'required|in:gcash,online_banking,bank_transfer'
        ]);

        try {
            $result = $this->sandboxService->createSandboxPayment($order, $request->method);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Sandbox payment created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate payment flow
     */
    public function simulatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'method' => 'required|in:gcash,online_banking',
            'scenario' => 'required|in:success,failed,pending,timeout,fraud',
            'transaction_id' => 'required|string'
        ]);

        try {
            $result = $this->sandboxService->verifySandboxPayment(
                $request->method,
                $request->transaction_id,
                $request->scenario
            );
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => "Payment simulation completed: {$request->scenario}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle sandbox webhooks
     */
    public function handleWebhook(Request $request, string $gateway): JsonResponse
    {
        try {
            $payload = $request->all();
            
            // Simulate webhook processing delay
            sleep(1);
            
            Log::info('Sandbox webhook received', [
                'gateway' => $gateway,
                'payload' => $payload
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sandbox webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate payment redirect
     */
    public function handleRedirect(Request $request, string $method)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status', 'success');
        
        $order = CustomOrder::where('transaction_id', $transactionId)->first();
        if (!$order) {
            return redirect()->route('dashboard')
                ->with('error', 'Order not found');
        }

        // Update order based on redirect status
        $this->sandboxService->verifySandboxPayment($method, $transactionId, $status);

        return redirect()->route('custom_orders.show', $order)
            ->with('success', "Payment simulation completed: {$status}");
    }

    /**
     * Generate bank transfer instructions
     */
    public function generateBankInstructions(CustomOrder $order): JsonResponse
    {
        try {
            $instructions = $this->sandboxService->generateBankTransferDetails($order);
            
            return response()->json([
                'success' => true,
                'data' => $instructions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify bank transfer (sandbox)
     */
    public function verifyBankTransfer(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'deposit_account' => 'required|string',
            'reference_code' => 'required|string',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            $transactionId = $request->transaction_id;
            $depositAccount = $request->deposit_account;
            
            // Determine scenario based on deposit account
            $scenario = match($depositAccount) {
                '9999999999' => 'success',
                '1111111111' => 'failed',
                '8888888888' => 'pending',
                default => 'success'
            };
            
            $result = $this->sandboxService->verifySandboxPayment('bank_transfer', $transactionId, $scenario);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'scenario' => $scenario,
                'message' => "Bank transfer verification completed: {$scenario}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate test data
     */
    public function generateTestData(): JsonResponse
    {
        try {
            $testOrders = [];
            
            // Create 5 test orders with different scenarios
            for ($i = 1; $i <= 5; $i++) {
                $order = CustomOrder::create([
                    'user_id' => auth()->id(),
                    'product_name' => "Test Product {$i}",
                    'final_price' => rand(100, 1000),
                    'status' => 'pending_payment',
                    'payment_method' => ['gcash', 'online_banking', 'bank_transfer'][array_rand(['gcash', 'online_banking', 'bank_transfer'])],
                    'transaction_id' => 'TEST_' . uniqid(),
                    'payment_notes' => "[SANDBOX] Auto-generated test order #{$i}"
                ]);
                
                $testOrders[] = $order;
            }
            
            return response()->json([
                'success' => true,
                'data' => $testOrders,
                'message' => 'Test data generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear sandbox data
     */
    public function clearSandboxData(): JsonResponse
    {
        try {
            $deleted = CustomOrder::where('payment_notes', 'like', '%[SANDBOX]%')->delete();
            
            return response()->json([
                'success' => true,
                'deleted_count' => $deleted,
                'message' => 'Sandbox data cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate GCash payment for testing
     */
    public function simulateGCashPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            $order = \App\Models\Order::findOrFail($request->order_id);
            
            // Verify the amount matches
            if ((float)$request->amount != (float)$order->total_amount) {
                return redirect()->back()->with('error', 'Payment amount mismatch!');
            }

            // Simulate successful GCash payment
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_method' => 'gcash_sandbox',
                'payment_notes' => '[SANDBOX] Simulated GCash payment at ' . now()->format('Y-m-d H:i:s') . ' | Reference: GCASH-' . strtoupper(uniqid())
            ]);

            \Log::info('Sandbox GCash payment simulated', [
                'order_id' => $order->id,
                'amount' => $request->amount,
                'timestamp' => now()
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('success', '✅ Payment successful! Your order is now being processed. (Sandbox Mode)');

        } catch (\Exception $e) {
            \Log::error('Sandbox GCash payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment simulation failed: ' . $e->getMessage());
        }
    }

    /**
     * Simulate Credit Card payment for testing
     */
    public function simulateCardPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'card_number' => 'required|string',
            'expiry' => 'required|string',
            'cvv' => 'required|string|size:3',
            'card_name' => 'required|string'
        ]);

        try {
            $order = \App\Models\Order::findOrFail($request->order_id);
            
            // Verify the amount matches
            if ((float)$request->amount != (float)$order->total_amount) {
                return redirect()->back()->with('error', 'Payment amount mismatch!');
            }

            // Remove spaces from card number
            $cardNumber = str_replace(' ', '', $request->card_number);
            
            // Test card scenarios
            $testCards = [
                '4242424242424242' => ['status' => 'success', 'message' => 'Payment successful!'],
                '4000000000000002' => ['status' => 'declined', 'message' => 'Card declined. Please try another card.'],
                '4000000000009995' => ['status' => 'insufficient', 'message' => 'Insufficient funds. Please try another card.'],
                '4000000000000069' => ['status' => 'expired', 'message' => 'Card expired. Please use a valid card.'],
                '4000000000000127' => ['status' => 'incorrect_cvc', 'message' => 'Incorrect CVV. Please check and try again.'],
            ];

            $scenario = $testCards[$cardNumber] ?? ['status' => 'success', 'message' => 'Payment successful!'];

            // Simulate payment based on card number
            if ($scenario['status'] === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'payment_method' => 'credit_card_sandbox',
                    'payment_notes' => '[SANDBOX] Simulated Card payment at ' . now()->format('Y-m-d H:i:s') . ' | Card: ****' . substr($cardNumber, -4) . ' | Reference: CARD-' . strtoupper(uniqid())
                ]);

                \Log::info('Sandbox Card payment simulated - SUCCESS', [
                    'order_id' => $order->id,
                    'amount' => $request->amount,
                    'card_last4' => substr($cardNumber, -4),
                    'timestamp' => now()
                ]);

                return redirect()->route('orders.show', $order->id)
                    ->with('success', '✅ ' . $scenario['message'] . ' Your order is now being processed. (Test Mode)');
            } else {
                // Simulate failed payment
                \Log::info('Sandbox Card payment simulated - FAILED', [
                    'order_id' => $order->id,
                    'amount' => $request->amount,
                    'card_last4' => substr($cardNumber, -4),
                    'scenario' => $scenario['status'],
                    'timestamp' => now()
                ]);

                return redirect()->back()
                    ->with('error', '❌ ' . $scenario['message'] . ' (Test Mode)');
            }

        } catch (\Exception $e) {
            \Log::error('Sandbox Card payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment simulation failed: ' . $e->getMessage());
        }
    }
}
