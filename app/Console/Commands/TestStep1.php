<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CustomOrderController;
use Illuminate\Http\Request;
use App\Models\Product;

class TestStep1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:step1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test custom order step1 functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Testing CustomOrderController...');
            
            // Test the controller instantiation
            $controller = new CustomOrderController();
            $this->info('Controller created successfully');
            
            // Test if categories are available
            $categories = \App\Models\Category::with('products')->get();
            $this->info("Found {$categories->count()} categories");
            
            foreach ($categories as $category) {
                $this->line("- {$category->name}: {$category->products->count()} products");
            }
            
            // Test if products exist
            $products = \App\Models\Product::all();
            $this->info("Found {$products->count()} products");
            
            if ($products->count() > 0) {
                $this->info('Step1 should work correctly!');
            } else {
                $this->warn('No products found - this might cause issues');
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
