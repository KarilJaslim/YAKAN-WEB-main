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
        if (Schema::hasTable('pattern_fabric_compatibility')) {
            return;
        }
        Schema::create('pattern_fabric_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yakan_pattern_id')->constrained('yakan_patterns')->onDelete('cascade');
            $table->foreignId('fabric_type_id')->constrained('fabric_types')->onDelete('cascade');
            $table->enum('difficulty_level', ['simple', 'medium', 'complex'])->default('medium');
            $table->decimal('price_multiplier', 3, 2)->default(1.00); // Multiplier for pattern complexity
            $table->integer('estimated_production_days')->default(14); // Days needed for pattern application
            $table->text('notes')->nullable(); // Special considerations for this combination
            $table->boolean('is_recommended')->default(false); // Whether this is a recommended combination
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique combinations (short index name for MariaDB)
            $table->unique(['yakan_pattern_id', 'fabric_type_id'], 'pattern_fabric_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pattern_fabric_compatibility');
    }
};
