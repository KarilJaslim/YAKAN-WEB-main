<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a mock request to test the custom orders API
try {
    $request = \Illuminate\Http\Request::create('/api/v1/custom-orders', 'GET');
    
    // Simulate the API call
    $controller = new \App\Http\Controllers\Api\CustomOrderController(
        app(\App\Services\CustomOrder\CustomOrderService::class),
        app(\App\Services\CustomOrder\CustomOrderValidationService::class),
        app(\App\Services\ReplicateService::class)
    );
    
    $response = $controller->index($request);
    
    echo "Custom Orders API Response Status: " . $response->status() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
