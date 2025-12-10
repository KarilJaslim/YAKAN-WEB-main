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
            // Add new timestamp fields for the workflow
            $table->timestamp('price_quoted_at')->nullable()->after('rejection_reason');
            $table->timestamp('user_notified_at')->nullable()->after('price_quoted_at');
            
            // Update the status enum to include 'price_quoted'
            $table->enum('status', ['pending', 'price_quoted', 'approved', 'rejected', 'processing', 'completed', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_orders', function (Blueprint $table) {
            $table->dropColumn(['price_quoted_at', 'user_notified_at']);
            
            // Revert status enum (note: this might not work in all MySQL versions)
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }
};
