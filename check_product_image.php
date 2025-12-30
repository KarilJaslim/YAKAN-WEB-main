<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$product = App\Models\Product::find(11);

if ($product) {
    echo "Product ID: {$product->id}\n";
    echo "Product Name: {$product->name}\n";
    echo "Image in DB: " . ($product->image ?? 'NULL') . "\n";
    
    if ($product->image) {
        $filePath = public_path('uploads/products/' . $product->image);
        echo "Full Path: {$filePath}\n";
        echo "File Exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
        
        if (file_exists($filePath)) {
            echo "File Size: " . filesize($filePath) . " bytes\n";
        }
    }
} else {
    echo "Product not found\n";
}

echo "\n--- All products with images ---\n";
$products = App\Models\Product::whereNotNull('image')->get(['id', 'name', 'image']);
foreach ($products as $p) {
    $exists = file_exists(public_path('uploads/products/' . $p->image)) ? '✓' : '✗';
    echo "{$exists} ID:{$p->id} | {$p->name} | {$p->image}\n";
}
