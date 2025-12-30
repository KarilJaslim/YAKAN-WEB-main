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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            // type: percent or fixed
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 10, 2); // percent: 0-100, fixed: currency amount
            $table->decimal('max_discount', 10, 2)->nullable(); // cap for percent discounts
            $table->decimal('min_spend', 10, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable(); // total uses allowed
            $table->unsignedInteger('usage_limit_per_user')->nullable();
            $table->unsignedInteger('times_redeemed')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
