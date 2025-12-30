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
        Schema::table('custom_orders', function (Blueprint $table) {
            // Fabric-specific fields (replacing product customization)
            $table->string('fabric_type')->nullable()->after('product_id'); // cotton, silk, polyester
            $table->integer('fabric_weight_gsm')->nullable()->after('fabric_type'); // grams per square meter
            $table->decimal('fabric_quantity_meters', 8, 2)->nullable()->after('fabric_weight_gsm'); // length needed
            $table->string('intended_use')->nullable()->after('fabric_quantity_meters'); // clothing, decor, crafts
            $table->text('fabric_specifications')->nullable()->after('intended_use'); // texture, treatments, etc.
            $table->text('special_requirements')->nullable()->after('fabric_specifications'); // waterproof, fire-retardant
            
            // Keep existing fields but make some nullable since they're less relevant
            $table->string('dimensions')->nullable()->change();
            $table->string('product_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_orders', function (Blueprint $table) {
            // Remove fabric-specific fields
            $table->dropColumn([
                'fabric_type',
                'fabric_weight_gsm', 
                'fabric_quantity_meters',
                'intended_use',
                'fabric_specifications',
                'special_requirements'
            ]);
            
            // Revert existing fields to original state if needed
            $table->string('dimensions')->nullable(false)->change();
            $table->string('product_type')->nullable(false)->change();
        });
    }
};
