<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            if (!$product->inventory) {
                Inventory::create([
                    'product_id' => $product->id,
                    'quantity' => 50,
                    'min_stock_level' => 10,
                    'max_stock_level' => 100,
                    'cost_price' => 500.00,
                    'selling_price' => 750.00,
                    'supplier' => 'Sample Supplier',
                    'location' => 'Warehouse A',
                    'low_stock_alert' => false
                ]);
                echo "Inventory created for product: {$product->name}\n";
            }
        }
    }
}
