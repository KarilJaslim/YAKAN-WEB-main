<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the custom order creation
try {
    $requestData = [
        'product_type' => 'Test Custom Order',
        'specifications' => 'Test specifications from wizard',
        'quantity' => 2,
        'budget_range' => '5000-10000',
        'primary_color' => '#DC143C',
        'secondary_color' => '#4169E1',
        'accent_color' => '#FFD700',
        'dimensions' => '60x80 cm',
        'phone' => '+639123456789',
        'email' => 'test@example.com',
        'delivery_address' => 'Test Address, City',
        'additional_notes' => 'Test custom pattern description',
        'urgency' => 'normal',
        'estimated_price' => 1500
    ];

    $request = \Illuminate\Http\Request::create('/api/v1/custom-orders', 'POST', [], [], [], [], 
        json_encode($requestData)
    );
    $request->headers->set('Content-Type', 'application/json');

    // Simulate the API call
    $controller = new \App\Http\Controllers\Api\CustomOrderController(
        app(\App\Services\CustomOrder\CustomOrderService::class),
        app(\App\Services\CustomOrder\CustomOrderValidationService::class),
        app(\App\Services\ReplicateService::class)
    );
    
    $response = $controller->store($request);
    
    echo "Create Custom Order API Response Status: " . $response->status() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
