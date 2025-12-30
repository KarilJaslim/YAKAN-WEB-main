<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomOrder;
use App\Services\CustomOrder\CustomOrderService;
use App\Services\CustomOrder\CustomOrderValidationService;
use App\Services\ReplicateService;
use App\Services\Upload\SecureFileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomOrderController
{
    private CustomOrderService $customOrderService;
    private CustomOrderValidationService $validationService;
    private ReplicateService $replicateService;

    public function __construct(
        CustomOrderService $customOrderService,
        CustomOrderValidationService $validationService,
        ReplicateService $replicateService
    ) {
        $this->customOrderService = $customOrderService;
        $this->validationService = $validationService;
        $this->replicateService = $replicateService;
    }

    /**
     * Get all custom orders for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to access custom orders'
                ], 401);
            }

            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            $result = $this->customOrderService->getUserCustomOrders(
                Auth::id(), 
                $page, 
                $perPage
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Custom orders retrieval failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve custom orders'
            ], 500);
        }
    }

    /**
     * Get a specific custom order
     */
    public function show($id)
    {
        try {
            $result = $this->customOrderService->getOrderStatus($id, Auth::id());
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found or you do not have permission to access it'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Custom order retrieval error', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving custom order'
            ], 500);
        }
    }

    /**
     * Create a new custom order
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to create custom orders'
                ], 401);
            }

            $data = $request->all();
            $userId = Auth::id();
            
            $result = $this->customOrderService->createCustomOrder($data, $userId);
            
            if (!$result['success']) {
                return response()->json($result, 422);
            }
            
            return response()->json($result, 201);
        } catch (\Exception $e) {
            Log::error('Custom order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create custom order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a custom order (only if status is pending)
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $result = $this->customOrderService->updateCustomOrder($data, $id, Auth::id());
            
            if (!$result['success']) {
                return response()->json($result, 422);
            }
            
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found or cannot be updated'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Custom order update failed', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update custom order'
            ], 500);
        }
    }

    /**
     * Cancel a custom order (user action)
     */
    public function cancel($id)
    {
        try {
            $result = $this->customOrderService->cancelCustomOrder($id, Auth::id());
            
            if (!$result['success']) {
                return response()->json($result, 400);
            }
            
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Custom order cancellation failed', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel custom order'
            ], 500);
        }
    }

    /**
     * User accepts the quoted price
     */
    public function acceptPrice($id)
    {
        try {
            $result = $this->customOrderService->acceptPrice($id, Auth::id());
            
            if (!$result['success']) {
                return response()->json($result, 400);
            }
            
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Price acceptance failed', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept price'
            ], 500);
        }
    }

    /**
     * User rejects the quoted price (cancels order)
     */
    public function rejectPrice($id)
    {
        try {
            $result = $this->customOrderService->rejectPrice($id, Auth::id());
            
            if (!$result['success']) {
                return response()->json($result, 400);
            }
            
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Price rejection failed', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject price'
            ], 500);
        }
    }

    /**
     * Get order status and available actions
     */
    public function getOrderStatus($id)
    {
        try {
            $result = $this->customOrderService->getOrderStatus($id, Auth::id());
            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom order not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Order status retrieval failed', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order status'
            ], 500);
        }
    }


    /**
     * Get available categories and products for custom orders
     */
    public function getCatalog()
    {
        try {
            $result = $this->customOrderService->getCatalog();
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Catalog retrieval failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve catalog'
            ], 500);
        }
    }

    /**
     * Get custom order pricing estimate
     */
    public function getPricingEstimate(Request $request)
    {
        try {
            $data = $request->all();
            $result = $this->customOrderService->calculatePricingEstimate($data);
            
            if (!$result['success']) {
                return response()->json($result, 422);
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Pricing estimate failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate pricing estimate'
            ], 500);
        }
    }

    /**
     * Upload design files
     */
    public function uploadDesign(Request $request)
    {
        try {
            $file = $request->file('file');
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file provided'
                ], 422);
            }
            
            $result = $this->customOrderService->uploadDesign($file);
            
            if (!$result['success']) {
                return response()->json($result, 422);
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Design upload failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload design'
            ], 500);
        }
    }

    /**
     * Status check endpoint for updates
     */
    public function statusCheck(Request $request)
    {
        try {
            $lastCheck = $request->get('last_check');
            $result = $this->customOrderService->checkStatusUpdates(Auth::id(), $lastCheck);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Status check failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check status updates'
            ], 500);
        }
    }

    /**
     * Get custom order statistics for the user
     */
    public function getStatistics()
    {
        try {
            $result = $this->customOrderService->getUserStatistics(Auth::id());
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Statistics retrieval failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }

    /**
     * Get real-time updates for custom orders
     */
    public function realTimeUpdates(Request $request)
    {
        try {
            $user = Auth::user();
            $lastCheck = $request->input('last_check');
            
            $query = CustomOrder::where('user_id', $user->id);
            
            if ($lastCheck) {
                $query->where('updated_at', '>', $lastCheck);
            }
            
            $orders = $query->with(['user', 'product'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'timestamp' => now()->toISOString(),
                    'has_updates' => $orders->count() > 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Real-time updates error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time updates'
            ], 500);
        }
    }

    /**
     * Generate AI fabric pattern using Replicate
     */
    public function generatePattern(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:500',
                'style' => 'nullable|string|max:100',
                'colors' => 'nullable|array|max:5',
                'colors.*' => 'string|regex:/^#[0-9A-Fa-f]{6}$/',
                'width' => 'nullable|integer|min:256|max:1024',
                'height' => 'nullable|integer|min:256|max:1024',
            ]);

            $params = array_merge([
                'width' => 512,
                'height' => 512,
                'style' => 'fabric pattern',
            ], $validated);

            $result = $this->replicateService->generateFabricPattern($params);

            return response()->json([
                'success' => true,
                'data' => [
                    'prediction_id' => $result['id'],
                    'status' => $result['status'],
                    'created_at' => $result['created_at'],
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('AI pattern generation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'params' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate pattern. Please try again.'
            ], 500);
        }
    }

    /**
     * Get pattern generation status
     */
    public function getPatternStatus($predictionId)
    {
        try {
            $result = $this->replicateService->getPredictionStatus($predictionId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $result['id'],
                    'status' => $result['status'],
                    'output' => $result['output'] ?? null,
                    'error' => $result['error'] ?? null,
                    'created_at' => $result['created_at'],
                    'completed_at' => $result['completed_at'] ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get pattern status', [
                'prediction_id' => $predictionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get pattern status'
            ], 500);
        }
    }
}
