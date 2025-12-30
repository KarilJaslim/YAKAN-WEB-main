<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });
        
        // Update existing categories with slugs
        \DB::table('categories')->get()->each(function ($category) {
            $slug = \Str::slug($category->name ?: 'category-' . $category->id);
            \DB::table('categories')
                ->where('id', $category->id)
                ->update(['slug' => $slug]);
        });
        
        // Now make the slug column unique and not nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
