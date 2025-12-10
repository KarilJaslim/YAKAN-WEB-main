<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a test user
        $user = User::first() ?? User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Get products
        $product1 = Product::where('name', 'Yakan Shirt')->first();
        $product2 = Product::where('name', 'Yakan Bag')->first();

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $product1->price * 2 + $product2->price,
            'status' => 'pending',
            'payment_method' => 'gcash',
            'payment_status' => 'pending'
        ]);

        // Add order items
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => $product1->price
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price
        ]);
    }
}
