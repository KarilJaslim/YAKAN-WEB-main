<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = App\Models\Product::all();

echo "Total products: " . $products->count() . "\n\n";

foreach ($products as $product) {
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name}\n";
    echo "Image field: {$product->image}\n";
    
    $path1 = public_path('uploads/products/' . $product->image);
    $exists1 = file_exists($path1);
    echo "File exists at uploads/products/: " . ($exists1 ? 'YES' : 'NO') . "\n";
    
    if (!$exists1 && $product->image) {
        // Check if it's a full path stored
        $path2 = public_path($product->image);
        $exists2 = file_exists($path2);
        echo "File exists at direct path: " . ($exists2 ? 'YES' : 'NO') . " - {$path2}\n";
    }
    
    echo "---\n\n";
}
