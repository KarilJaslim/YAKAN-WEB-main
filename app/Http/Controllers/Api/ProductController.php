<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Temporarily disable caching to debug 500 error
        // $cacheKey = 'products:' . md5(json_encode($request->all()));
        
        // $products = Cache::remember($cacheKey, env('PRODUCT_CACHE_TTL', 7200), function () use ($request) {
            $query = Product::with('category')->active();
            
            if ($request->has('category')) {
                $query->where('category_id', $request->category);
            }
            
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->has('sort_by')) {
                $sortBy = $request->sort_by;
                $sortOrder = $request->get('sort_order', 'asc');
                
                if (in_array($sortBy, ['name', 'price', 'created_at'])) {
                    $query->orderBy($sortBy, $sortOrder);
                }
            }
            
            $products = $query->paginate($request->get('per_page', 12));
        // });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $cacheKey = "product:{$product->id}";
        
        $product = Cache::remember($cacheKey, env('PRODUCT_CACHE_TTL', 7200), function () use ($product) {
            return $product->load(['category', 'orderItems']);
        });

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function byCategory($category): JsonResponse
    {
        $cacheKey = "products:category:{$category}";
        
        $products = Cache::remember($cacheKey, env('CATEGORY_CACHE_TTL', 86400), function () use ($category) {
            return Product::with('category')
                ->whereHas('category', function($query) use ($category) {
                    $query->where('slug', $category);
                })
                ->active()
                ->paginate(12);
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
