<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'required|string',
                'shipping_address' => 'required|string',
                'payment_method' => 'required|string',
                'payment_status' => 'required|string',
                'items' => 'required|array',
                'subtotal' => 'required|numeric',
                'shipping_fee' => 'required|numeric',
                'total' => 'required|numeric',
                'notes' => 'nullable|string',
            ]);

            $order = Order::create([
                'user_id' => null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? 'mobile@user.com',
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'status' => 'pending',
                'subtotal' => $validated['subtotal'],
                'shipping_fee' => $validated['shipping_fee'],
                'total' => $validated['total'],
                'notes' => $validated['notes'] ?? '',
                'source' => 'mobile',
                'device_id' => 'mobile-app',
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load('orderItems'),
                'message' => 'Order created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
