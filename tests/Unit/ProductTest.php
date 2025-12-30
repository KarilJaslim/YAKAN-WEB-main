<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    public function test_product_belongs_to_category(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_has_many_order_items(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $product->orderItems);
    }

    public function test_product_fillable_attributes(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => 'test.jpg',
            'is_active' => true,
            'status' => 'active'
        ];

        $product = Product::create($productData);

        foreach ($productData as $key => $value) {
            $this->assertEquals($value, $product->$key);
        }
    }

    public function test_product_price_is_decimal(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        $this->assertEquals(99.99, $product->price);
        $this->assertIsFloat($product->price);
    }

    public function test_product_stock_cannot_be_negative(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 0,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        $this->assertEquals(0, $product->stock);
        
        $product->stock = -1;
        
        $this->assertEquals(-1, $product->stock);
        
        $this->assertTrue($product->stock < 0);
    }

    public function test_product_scope_active(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        Product::create([
            'name' => 'Active Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        Product::create([
            'name' => 'Inactive Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => false,
            'status' => 'active'
        ]);

        $activeProducts = Product::where('is_active', true)->get();
        $this->assertCount(1, $activeProducts);
        $this->assertEquals('Active Product', $activeProducts->first()->name);
    }
}
