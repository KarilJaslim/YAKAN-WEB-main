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
        Schema::table('orders', function (Blueprint $table) {
            // Add source field to track where order came from
            $table->enum('source', ['web', 'mobile', 'admin'])->default('web')->after('payment_method');
            // Add device info for mobile orders
            $table->string('device_id')->nullable()->after('source');
            // Index for faster queries
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'device_id']);
        });
    }
};
