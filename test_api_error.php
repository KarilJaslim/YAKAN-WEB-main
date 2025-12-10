<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a mock request to test the API
try {
    $request = \Illuminate\Http\Request::create('/api/v1/products', 'GET');
    
    // Simulate the API call
    $controller = new \App\Http\Controllers\Api\ProductController();
    $response = $controller->index($request);
    
    echo "API Response Status: " . $response->status() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
