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
        Schema::create('fabric_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Cotton", "Silk", "Polyester Blend"
            $table->text('description')->nullable();
            $table->decimal('base_price_per_meter', 10, 2); // Base price per meter
            $table->string('material_composition')->nullable(); // e.g., "100% Cotton", "80% Cotton 20% Polyester"
            $table->integer('weight_gsm')->nullable(); // Grams per square meter
            $table->string('texture')->nullable(); // e.g., "Smooth", "Textured", "Woven"
            $table->json('typical_uses')->nullable(); // Array of typical uses
            $table->text('care_instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_types');
    }
};
