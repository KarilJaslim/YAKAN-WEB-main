<?php
// Quick API test without going through web server
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request
$request = Illuminate\Http\Request::create(
    '/api/v1/orders',
    'POST',
    [],
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode([
        'total_amount' => 250,
        'payment_method' => 'gcash',
        'delivery_type' => 'delivery',
        'delivery_address' => '123 Main St, City',
        'customer_notes' => 'Test order',
        'items' => [
            ['product_id' => 10, 'quantity' => 2]
        ]
    ])
);

// Send through kernel
$response = $kernel->handle($request);
echo $response->getContent();
