<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\CustomOrder::find(2);
if ($order) {
    echo "Order #2 Found:\n";
    echo "design_upload length: " . (is_null($order->design_upload) ? 'NULL' : strlen($order->design_upload)) . "\n";
    echo "design_upload preview: " . (is_null($order->design_upload) ? 'NULL' : substr($order->design_upload, 0, 50)) . "...\n";
    echo "design_method: " . ($order->design_method ?? 'NULL') . "\n";
    echo "design_metadata: " . json_encode($order->design_metadata) . "\n";
    echo "fabric_type: " . ($order->fabric_type ?? 'NULL') . "\n";
} else {
    echo "Order #2 not found\n";
}
