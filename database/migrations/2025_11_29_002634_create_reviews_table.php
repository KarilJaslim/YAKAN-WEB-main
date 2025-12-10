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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->check('rating >= 1 AND rating <= 5');
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_response_at')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'rating']);
            $table->index(['user_id', 'product_id']);
            $table->index('is_approved');
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
