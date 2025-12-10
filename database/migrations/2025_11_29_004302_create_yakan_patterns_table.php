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
        Schema::create('yakan_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Traditional pattern names (e.g., "Sinaluan", "Bunga Sama")
            $table->text('description')->nullable(); // Cultural significance
            $table->string('category'); // geometric, floral, abstract, traditional
            $table->string('difficulty_level'); // simple, medium, complex
            $table->json('pattern_data'); // SVG or pattern coordinates
            $table->string('base_color'); // Primary color
            $table->json('color_variations')->nullable(); // Alternative color schemes
            $table->decimal('base_price_multiplier', 3, 2)->default(1.00); // Price impact
            $table->boolean('is_active')->default(true);
            $table->integer('popularity_score')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index('difficulty_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yakan_patterns');
    }
};
