<?php

namespace App\Services\CustomOrder;

use App\Models\CustomOrder;
use App\Models\Product;
use App\Models\Category;
use App\Services\Upload\SecureFileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomOrderService
{
    private SecureFileUploadService $secureUploadService;

    public function __construct(SecureFileUploadService $secureUploadService)
    {
        $this->secureUploadService = $secureUploadService;
    }
    /**
     * Get catalog data with caching
     */
    public function getCatalog(): array
    {
        $cacheKey = 'custom_order_catalog';
        
        return Cache::remember($cacheKey, 3600, function () {
            $categories = Category::with(['products' => function($query) {
                $query->where('is_active', true);
            }])->get();
            
            return [
                'success' => true,
                'data' => $categories,
                'message' => 'Catalog retrieved successfully'
            ];
        });
    }

    /**
     * Calculate pricing estimate
     */
    public function calculatePricingEstimate(array $data): array
    {
        $validator = Validator::make($data, [
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'dimensions' => 'nullable|string|max:255',
            'complexity' => 'nullable|in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        $estimatedPrice = $this->performPriceCalculation($data);
        $productionTime = $this->estimateProductionTime($data);

        return [
            'success' => true,
            'data' => [
                'estimated_price' => $estimatedPrice,
                'price_breakdown' => [
                    'base_price' => $estimatedPrice * 0.6,
                    'labor_cost' => $estimatedPrice * 0.25,
                    'materials_cost' => $estimatedPrice * 0.15
                ],
                'production_time' => $productionTime,
                'notes' => 'This is an estimate. Final price will be provided after review.'
            ],
            'message' => 'Pricing estimate calculated successfully'
        ];
    }

    /**
     * Upload design file securely
     */
    public function uploadDesign(UploadedFile $file): array
    {
        try {
            return $this->secureUploadService->uploadFile($file, 'custom_orders/designs');
        } catch (\Exception $e) {
            Log::error('Secure design upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to upload design file securely'
            ];
        }
    }

    /**
     * Create custom order
     */
    public function createCustomOrder(array $data, int $userId): array
    {
        $validator = Validator::make($data, [
            'product_type' => 'required|string|max:255',
            'specifications' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:1',
            'budget_range' => 'nullable|string|max:100',
            'expected_date' => 'nullable|date|after:today',
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'accent_color' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'delivery_address' => 'required|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
            'design_upload' => 'nullable|string',
            'patterns' => 'nullable|array',
            'patterns.*.name' => 'required|string|max:255',
            'patterns.*.description' => 'nullable|string|max:500',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            $orderData = $data;
            $orderData['user_id'] = $userId;
            $orderData['status'] = 'pending';
            $orderData['payment_status'] = 'pending';
            $orderData['estimated_price'] = $this->performPriceCalculation($data);

            // Handle patterns
            if (isset($data['patterns']) && is_array($data['patterns'])) {
                $orderData['patterns'] = json_encode($data['patterns']);
            }

            // Handle product references
            if (isset($data['product_ids']) && is_array($data['product_ids'])) {
                $orderData['product_id'] = $data['product_ids'][0] ?? null;
            }

            $customOrder = CustomOrder::create($orderData);
            $customOrder->load(['product', 'user']);

            // Clear user's cached statistics
            $this->clearUserCache($userId);

            return [
                'success' => true,
                'data' => $customOrder,
                'message' => 'Custom order created successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Custom order creation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create custom order'
            ];
        }
    }

    /**
     * Update custom order
     */
    public function updateCustomOrder(array $data, int $orderId, int $userId): array
    {
        $order = CustomOrder::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($orderId);

        $validator = Validator::make($data, [
            'specifications' => 'nullable|string|max:2000',
            'quantity' => 'sometimes|integer|min:1',
            'budget_range' => 'nullable|string|max:100',
            'expected_date' => 'nullable|date|after:today',
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'accent_color' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'delivery_address' => 'sometimes|string|max:500',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            $order->update($data);
            $order->load(['product', 'user']);

            return [
                'success' => true,
                'data' => $order,
                'message' => 'Custom order updated successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Custom order update failed', [
                'order_id' => $orderId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update custom order'
            ];
        }
    }

    /**
     * Get user's custom orders with caching
     */
    public function getUserCustomOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        $cacheKey = "user_custom_orders_{$userId}_page_{$page}";
        
        return Cache::remember($cacheKey, 300, function () use ($userId, $page, $perPage) {
            $orders = CustomOrder::with(['product', 'user'])
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                'data' => $orders,
                'message' => 'Custom orders retrieved successfully'
            ];
        });
    }

    /**
     * Get custom order statistics with caching
     */
    public function getUserStatistics(int $userId): array
    {
        $cacheKey = "user_custom_order_stats_{$userId}";
        
        return Cache::remember($cacheKey, 600, function () use ($userId) {
            $stats = [
                'total_orders' => CustomOrder::where('user_id', $userId)->count(),
                'pending_orders' => CustomOrder::where('user_id', $userId)->where('status', 'pending')->count(),
                'approved_orders' => CustomOrder::where('user_id', $userId)->where('status', 'approved')->count(),
                'completed_orders' => CustomOrder::where('user_id', $userId)->where('status', 'completed')->count(),
                'cancelled_orders' => CustomOrder::where('user_id', $userId)->where('status', 'cancelled')->count(),
                'total_spent' => CustomOrder::where('user_id', $userId)
                    ->where('payment_status', 'paid')
                    ->sum('final_price'),
                'recent_orders' => CustomOrder::with(['product'])
                    ->where('user_id', $userId)
                    ->orderByDesc('created_at')
                    ->take(5)
                    ->get()
            ];

            return [
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ];
        });
    }

    /**
     * Handle price acceptance
     */
    public function acceptPrice(int $orderId, int $userId): array
    {
        $order = CustomOrder::where('user_id', $userId)->findOrFail($orderId);

        if (!$order->acceptPrice()) {
            return [
                'success' => false,
                'message' => 'Cannot accept price at this stage'
            ];
        }

        $this->clearUserCache($userId);

        return [
            'success' => true,
            'data' => $order,
            'message' => 'Price accepted. Order is now ready for payment.'
        ];
    }

    /**
     * Handle price rejection
     */
    public function rejectPrice(int $orderId, int $userId): array
    {
        $order = CustomOrder::where('user_id', $userId)->findOrFail($orderId);

        if (!$order->rejectPrice()) {
            return [
                'success' => false,
                'message' => 'Cannot reject price at this stage'
            ];
        }

        $this->clearUserCache($userId);

        return [
            'success' => true,
            'data' => $order,
            'message' => 'Price rejected. Order has been cancelled.'
        ];
    }

    /**
     * Cancel custom order
     */
    public function cancelCustomOrder(int $orderId, int $userId): array
    {
        $order = CustomOrder::where('user_id', $userId)->findOrFail($orderId);

        if (!$order->cancel()) {
            return [
                'success' => false,
                'message' => 'Cannot cancel this order at this stage'
            ];
        }

        $this->clearUserCache($userId);

        return [
            'success' => true,
            'data' => $order,
            'message' => 'Custom order cancelled successfully'
        ];
    }

    /**
     * Get order status and available actions
     */
    public function getOrderStatus(int $orderId, int $userId): array
    {
        $cacheKey = "order_status_{$orderId}_{$userId}";
        
        return Cache::remember($cacheKey, 60, function () use ($orderId, $userId) {
            $order = CustomOrder::with(['product', 'user'])
                ->where('user_id', $userId)
                ->findOrFail($orderId);

            return [
                'success' => true,
                'data' => [
                    'order' => $order,
                    'status_description' => $order->getStatusDescription(),
                    'available_actions' => $order->getAvailableActions(),
                    'workflow_stage' => $this->getWorkflowStage($order)
                ],
                'message' => 'Order status retrieved successfully'
            ];
        });
    }

    /**
     * Check for real-time updates
     */
    public function checkStatusUpdates(int $userId, ?string $lastCheck = null): array
    {
        $awaitingDecision = CustomOrder::where('user_id', $userId)
            ->where('status', 'price_quoted')
            ->whereNotNull('user_notified_at')
            ->count();
        
        $recentUpdates = CustomOrder::where('user_id', $userId)
            ->when($lastCheck, function($query, $lastCheck) {
                return $query->where('updated_at', '>', $lastCheck);
            })
            ->count();
        
        $newNotifications = CustomOrder::where('user_id', $userId)
            ->where('status', 'price_quoted')
            ->whereNotNull('user_notified_at')
            ->when($lastCheck, function($query, $lastCheck) {
                return $query->where('user_notified_at', '>', $lastCheck);
            })
            ->count();
        
        $hasUpdates = $awaitingDecision > 0 || $recentUpdates > 0 || $newNotifications > 0;
        
        return [
            'success' => true,
            'data' => [
                'has_updates' => $hasUpdates,
                'awaiting_decision' => $awaitingDecision,
                'recent_updates' => $recentUpdates,
                'new_notifications' => $newNotifications,
                'last_check' => now()->toISOString()
            ],
            'message' => $hasUpdates ? 'New updates available' : 'No new updates'
        ];
    }

    /**
     * Perform actual price calculation
     */
    private function performPriceCalculation(array $data): float
    {
        $basePrice = 1000; // Base price in PHP
        
        // Adjust based on quantity
        $quantity = $data['quantity'] ?? 1;
        $quantityMultiplier = $quantity > 5 ? 0.8 : ($quantity > 2 ? 0.9 : 1.0);
        
        // Adjust based on complexity (if provided)
        $complexity = $data['complexity'] ?? 'medium';
        $complexityMultiplier = [
            'low' => 0.7,
            'medium' => 1.0,
            'high' => 1.5
        ][$complexity] ?? 1.0;
        
        // Adjust based on dimensions (simple heuristic)
        $dimensions = $data['dimensions'] ?? '';
        $sizeMultiplier = 1.0;
        if (stripos($dimensions, 'large') !== false) {
            $sizeMultiplier = 1.3;
        } elseif (stripos($dimensions, 'small') !== false) {
            $sizeMultiplier = 0.8;
        }
        
        return round($basePrice * $quantity * $quantityMultiplier * $complexityMultiplier * $sizeMultiplier);
    }

    /**
     * Estimate production time
     */
    private function estimateProductionTime(array $data): int
    {
        $baseDays = 7; // Base production time in days
        
        $quantity = $data['quantity'] ?? 1;
        $complexity = $data['complexity'] ?? 'medium';
        
        $additionalDays = 0;
        if ($quantity > 5) $additionalDays += 3;
        if ($quantity > 10) $additionalDays += 5;
        if ($complexity === 'high') $additionalDays += 4;
        
        return $baseDays + $additionalDays;
    }

    /**
     * Determine workflow stage
     */
    private function getWorkflowStage(CustomOrder $order): string
    {
        if ($order->isPendingReview()) {
            return 'admin_review';
        }
        
        if ($order->isAwaitingUserDecision()) {
            return 'user_decision';
        }
        
        if ($order->status === 'approved' && $order->payment_status === 'pending') {
            return 'payment';
        }
        
        if ($order->status === 'processing') {
            return 'production';
        }
        
        if ($order->isCompleted()) {
            return 'completed';
        }
        
        return 'other';
    }

    /**
     * Clear user-specific cache
     */
    private function clearUserCache(int $userId): void
    {
        $patterns = [
            "user_custom_orders_{$userId}_page_*",
            "user_custom_order_stats_{$userId}",
            "order_status_*_{$userId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
