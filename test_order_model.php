<?php
// Test the Order model directly
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

try {
    $order = new \App\Models\Order();
    
    // Test relationship exists
    echo "Testing relationships...\n";
    echo "✓ items() method exists: " . (method_exists($order, 'items') ? 'YES' : 'NO') . "\n";
    echo "✓ orderItems() method exists: " . (method_exists($order, 'orderItems') ? 'YES' : 'NO') . "\n";
    
    // Try to create an order
    echo "\nTesting order creation...\n";
    $order = \App\Models\Order::create([
        'order_ref' => 'TEST-' . uniqid(),
        'total_amount' => 250,
        'payment_method' => 'gcash',
        'delivery_address' => '123 Main St',
        'status' => 'pending',
    ]);
    
    echo "✓ Order created: ID " . $order->id . "\n";
    echo "✓ Order can load items: " . $order->load('items')->items->count() . " items\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
