<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate API request
try {
    $query = \App\Models\Product::with('category')->active();
    $products = $query->paginate(12);
    
    echo "API Response Simulation:\n";
    echo "Success: true\n";
    echo "Products count: " . $products->count() . "\n";
    
    foreach ($products as $product) {
        echo "- Product: " . $product->name . " (Price: " . $product->price . ")\n";
        echo "  Status: " . $product->status . "\n";
        echo "  Category: " . ($product->category ? $product->category->name : 'None') . "\n";
    }
    
    // Test JSON response format
    $response = [
        'success' => true,
        'data' => $products
    ];
    
    echo "\nJSON Response (first 500 chars):\n";
    echo substr(json_encode($response), 0, 500) . "...\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
