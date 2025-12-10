<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['name' => 'Apparel', 'slug' => 'apparel'],
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
            ['name' => 'Home & Living', 'slug' => 'home-living'],
        ]);
    }
}
