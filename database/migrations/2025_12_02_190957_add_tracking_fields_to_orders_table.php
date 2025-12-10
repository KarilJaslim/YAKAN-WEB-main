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
            $table->string('courier_name')->nullable()->after('tracking_history');
            $table->string('courier_tracking_url')->nullable()->after('courier_name');
            $table->string('courier_contact')->nullable()->after('courier_tracking_url');
            $table->date('estimated_delivery_date')->nullable()->after('courier_contact');
            $table->string('delivery_proof')->nullable()->after('estimated_delivery_date');
            $table->timestamp('delivered_at')->nullable()->after('delivery_proof');
            $table->text('tracking_notes')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_name',
                'courier_tracking_url',
                'courier_contact',
                'estimated_delivery_date',
                'delivery_proof',
                'delivered_at',
                'tracking_notes'
            ]);
        });
    }
};
