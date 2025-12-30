<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sortBy = $request->get('sort_by', 'relevance');
        $page = $request->get('page', 1);

        $cacheKey = 'search:' . md5(json_encode($request->all()));
        
        $results = Cache::remember($cacheKey, env('CACHE_TTL', 3600), function () use ($query, $category, $minPrice, $maxPrice, $sortBy, $page) {
            
            $productsQuery = Product::with('category')->active();
            
            // Search query
            if (!empty($query)) {
                $productsQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('sku', 'like', '%' . $query . '%');
                });
            }
            
            // Category filter
            if ($category) {
                $productsQuery->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }
            
            // Price range filter
            if ($minPrice) {
                $productsQuery->where('price', '>=', $minPrice);
            }
            
            if ($maxPrice) {
                $productsQuery->where('price', '<=', $maxPrice);
            }
            
            // Sorting
            switch ($sortBy) {
                case 'price_low':
                    $productsQuery->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $productsQuery->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
                case 'name':
                    $productsQuery->orderBy('name', 'asc');
                    break;
                case 'relevance':
                default:
                    if (!empty($query)) {
                        $productsQuery->orderByRaw("CASE 
                            WHEN name LIKE ? THEN 1 
                            WHEN description LIKE ? THEN 2 
                            ELSE 3 END", 
                            ["%{$query}%", "%{$query}%"]);
                    }
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
            }
            
            return $productsQuery->paginate(12);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $results,
                'filters' => [
                    'categories' => $this->getCategoriesWithCounts(),
                    'price_range' => $this->getPriceRange(),
                ],
                'search_metadata' => [
                    'query' => $query,
                    'total_results' => $results->total(),
                    'current_page' => $results->currentPage(),
                    'per_page' => $results->perPage(),
                ]
            ]
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $cacheKey = 'suggestions:' . md5($query);
        
        $suggestions = Cache::remember($cacheKey, env('CACHE_TTL', 3600), function () use ($query) {
            $productNames = Product::active()
                ->where('name', 'like', '%' . $query . '%')
                ->limit(5)
                ->pluck('name');
                
            $categoryNames = Category::where('name', 'like', '%' . $query . '%')
                ->limit(3)
                ->pluck('name');
                
            return [
                'products' => $productNames,
                'categories' => $categoryNames,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    private function getCategoriesWithCounts()
    {
        return Cache::remember('categories:with_counts', env('CATEGORY_CACHE_TTL', 86400), function () {
            return Category::withCount(['products' => function ($query) {
                    $query->active();
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    private function getPriceRange()
    {
        return Cache::remember('price_range', env('CACHE_TTL', 3600), function () {
            $prices = Product::active()->pluck('price');
            
            return [
                'min' => $prices->min(),
                'max' => $prices->max(),
                'avg' => round($prices->avg(), 2),
            ];
        });
    }
}
