<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'active'
        ]);
    }

    public function test_can_get_all_products(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'description',
                                'price',
                                'stock',
                                'category_id',
                                'is_active',
                                'status',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::first();

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price
                    ]
                ]);
    }

    public function test_can_get_products_by_category(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/v1/products/category/{$category->name}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'description',
                                'price',
                                'stock',
                                'category_id',
                                'is_active',
                                'status',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }

    public function test_products_endpoint_has_rate_limiting(): void
    {
        $responses = collect(range(1, 65))->map(function () {
            return $this->getJson('/api/v1/products');
        });

        $rateLimitedResponses = $responses->filter(function ($response) {
            return $response->status() === 429;
        });

        $this->assertGreaterThan(0, $rateLimitedResponses->count());
    }

    public function test_nonexistent_product_returns_404(): void
    {
        $response = $this->getJson('/api/v1/products/999');

        $response->assertStatus(404);
    }
}
