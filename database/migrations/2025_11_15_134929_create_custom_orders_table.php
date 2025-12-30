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
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            
            // Core details
            $table->integer('quantity')->default(1);
            $table->text('specifications');
            $table->string('design_upload')->nullable();
            
            // Contact Information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('delivery_address')->nullable();
            
            // Product Details
            $table->string('product_type')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('preferred_colors')->nullable();
            
            // Color Customization
            $table->string('primary_color')->default('#ef4444');
            $table->string('secondary_color')->default('#3b82f6');
            $table->string('accent_color')->default('#10b981');
            
            // Budget & Timeline
            $table->string('budget_range')->nullable();
            $table->date('expected_date')->nullable();
            
            // Additional Details
            $table->text('additional_notes')->nullable();
            
            // Pricing & Status
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            
            // Admin fields
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};