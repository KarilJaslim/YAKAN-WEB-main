<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\CustomOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];
        
        if (!empty($query)) {
            $results = $this->performSearch($query, $request);
        }
        
        return view('search', [
            'query' => $query,
            'products' => $results['products'] ?? collect(),
            'orders' => $results['orders'] ?? collect(),
            'customOrders' => $results['customOrders'] ?? collect(),
            'totalResults' => $results['total'] ?? 0,
        ]);
    }
    
    /**
     * Perform search across products, orders, and custom orders
     */
    private function performSearch($query, Request $request)
    {
        $user = Auth::user();
        $results = [
            'products' => collect(),
            'orders' => collect(),
            'customOrders' => collect(),
            'total' => 0
        ];
        
        // Search Products
        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with('category')
            ->limit(10)
            ->get();
            
        $results['products'] = $products;
        
        // Search Orders (only for authenticated users)
        if ($user) {
            $orders = Order::where('user_id', $user->id)
                ->where(function($q) use ($query) {
                    $q->where('tracking_number', 'LIKE', "%{$query}%")
                      ->orWhere('status', 'LIKE', "%{$query}%")
                      ->orWhere('payment_method', 'LIKE', "%{$query}%");
                })
                ->limit(10)
                ->get();
                
            $results['orders'] = $orders;
            
            // Search Custom Orders
            $customOrders = CustomOrder::where('user_id', $user->id)
                ->where(function($q) use ($query) {
                    $q->where('product_type', 'LIKE', "%{$query}%")
                      ->orWhere('status', 'LIKE', "%{$query}%")
                      ->orWhere('specifications', 'LIKE', "%{$query}%")
                      ->orWhere('patterns', 'LIKE', "%{$query}%")
                      ->orWhere('preferred_colors', 'LIKE', "%{$query}%");
                })
                ->limit(10)
                ->get();
                
            $results['customOrders'] = $customOrders;
        }
        
        $results['total'] = $results['products']->count() + 
                           $results['orders']->count() + 
                           $results['customOrders']->count();
        
        return $results;
    }
    
    /**
     * API endpoint for AJAX live search - OPTIMIZED
     */
    public function liveSearch(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'orders' => [],
                'customOrders' => [],
                'total' => 0
            ]);
        }
        
        // Use cache for search results to improve performance
        $cacheKey = 'search_live_' . md5($query . '_' . (auth()->user()->id ?? 'guest'));
        $cached = \Cache::get($cacheKey);
        
        if ($cached) {
            return response()->json($cached);
        }
        
        $results = $this->performSearch($query, $request);
        
        $response = [
            'products' => $results['products']->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => Str::limit($product->description, 100),
                    'price' => number_format($product->price, 2),
                    'image' => $product->image,
                    'category' => $product->category?->name,
                    'url' => route('products.show', $product),
                    'type' => 'product'
                ];
            }),
            'orders' => $results['orders']->map(function($order) {
                return [
                    'id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'status' => $order->status,
                    'total_amount' => number_format($order->total_amount, 2),
                    'created_at' => $order->created_at->format('M d, Y'),
                    'url' => route('orders.show', $order),
                    'type' => 'order'
                ];
            }),
            'customOrders' => $results['customOrders']->map(function($order) {
                return [
                    'id' => $order->id,
                    'product_type' => $order->product_type,
                    'status' => $order->status,
                    'specifications' => Str::limit($order->specifications, 100),
                    'created_at' => $order->created_at->format('M d, Y'),
                    'url' => route('custom_orders.show', $order),
                    'type' => 'custom_order'
                ];
            }),
            'total' => $results['total']
        ];
        
        // Cache results for 5 minutes
        \Cache::put($cacheKey, $response, 300);
        
        return response()->json($response);
    }
}
