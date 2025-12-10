<?php

namespace Tests\Unit;

use App\Models\CustomOrder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomOrderTest extends TestCase
{
    use RefreshDatabase;
    public function test_custom_order_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 2,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_custom_order_belongs_to_product(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 2,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $this->assertInstanceOf(Product::class, $order->product);
        $this->assertEquals($product->id, $order->product->id);
    }

    public function test_custom_order_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $orderData = [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Custom specifications',
            'quantity' => 3,
            'status' => 'pending',
            'payment_status' => 'pending',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'delivery_address' => '123 Test Street',
            'product_type' => 'custom',
            'dimensions' => '10x20x30',
            'preferred_colors' => 'red, blue',
            'primary_color' => 'red',
            'secondary_color' => 'blue',
            'budget_range' => '100-500',
            'expected_date' => '2023-12-31',
            'additional_notes' => 'Test notes',
            'estimated_price' => 250.00,
            'final_price' => 300.00
        ];

        $order = CustomOrder::create($orderData);

        foreach ($orderData as $key => $value) {
            if ($key === 'expected_date') {
                // Date fields are cast to Carbon instances
                $this->assertInstanceOf(\Carbon\Carbon::class, $order->$key);
                $this->assertEquals($value, $order->$key->format('Y-m-d'));
            } else {
                $this->assertEquals($value, $order->$key);
            }
        }
    }

    public function test_patterns_are_cast_to_array(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $patterns = ['pattern1', 'pattern2', 'pattern3'];

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'pending',
            'payment_status' => 'pending',
            'patterns' => $patterns
        ]);

        $this->assertIsArray($order->patterns);
        $this->assertEquals($patterns, $order->patterns);
    }

    public function test_estimated_price_is_cast_to_decimal(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'pending',
            'payment_status' => 'pending',
            'estimated_price' => 250.75
        ]);

        $this->assertEquals('250.75', $order->estimated_price);
        $this->assertIsString($order->estimated_price);
    }

    public function test_final_price_is_cast_to_decimal(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'pending',
            'payment_status' => 'pending',
            'final_price' => 300.50
        ]);

        $this->assertEquals('300.50', $order->final_price);
        $this->assertIsString($order->final_price);
    }

    public function test_expected_date_is_cast_to_date(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'pending',
            'payment_status' => 'pending',
            'expected_date' => '2023-12-31'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $order->expected_date);
        $this->assertEquals('2023-12-31', $order->expected_date->format('Y-m-d'));
    }

    public function test_approved_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'approved',
            'payment_status' => 'pending',
            'approved_at' => now()
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $order->approved_at);
    }

    public function test_rejected_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'rejected',
            'payment_status' => 'pending',
            'rejected_at' => now()
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $order->rejected_at);
    }

    public function test_transfer_date_is_cast_to_date(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id,
            'stock' => 10,
            'is_active' => true,
            'status' => 'active'
        ]);

        $order = CustomOrder::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'specifications' => 'Test specifications',
            'quantity' => 1,
            'status' => 'pending',
            'payment_status' => 'pending',
            'transfer_date' => '2023-12-15'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $order->transfer_date);
        $this->assertEquals('2023-12-15', $order->transfer_date->format('Y-m-d'));
    }
}
