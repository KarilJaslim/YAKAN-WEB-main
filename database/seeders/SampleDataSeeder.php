<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // -------------------------------
        // Create sample users
        // -------------------------------
        $users = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
            ['name' => 'Charlie', 'email' => 'charlie@example.com'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('password'),
            ]);
        }

        // -------------------------------
        // Create sample products
        // -------------------------------
        $products = [
            ['name' => 'Burger', 'price' => 120, 'stock' => 50],
            ['name' => 'Pizza', 'price' => 250, 'stock' => 30],
            ['name' => 'Soda', 'price' => 50, 'stock' => 100],
            ['name' => 'Fries', 'price' => 80, 'stock' => 60],
            ['name' => 'Ice Cream', 'price' => 60, 'stock' => 40],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }

        // -------------------------------
        // Create sample orders
        // -------------------------------
        $allUsers = User::all();
        $allProducts = Product::all();

        foreach ($allUsers as $user) {
            for ($i = 0; $i < 3; $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total' => 0,
                    'status' => 'pending',
                    'payment_status' => 'paid',
                ]);

                // Random products for order
                $orderTotal = 0;
                $items = $allProducts->random(rand(1, 3));

                foreach ($items as $item) {
                    $quantity = rand(1, 3);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->id,
                        'quantity' => $quantity,
                        'price' => $item->price,
                    ]);
                    $orderTotal += $item->price * $quantity;
                }

                // Update order total
                $order->total = $orderTotal;
                $order->save();
            }
        }
    }
}
