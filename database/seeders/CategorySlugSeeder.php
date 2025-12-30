<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing categories with slugs
        DB::table('categories')->get()->each(function ($category) {
            $slug = Str::slug($category->name ?: 'category-' . $category->id);
            DB::table('categories')
                ->where('id', $category->id)
                ->update(['slug' => $slug]);
        });
        
        $this->command->info('Category slugs updated successfully!');
    }
}
