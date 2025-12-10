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
        Schema::table('inventory', function (Blueprint $table) {
            $table->integer('total_sold')->default(0)->after('quantity');
            $table->decimal('total_revenue', 12, 2)->default(0)->after('total_sold');
            $table->timestamp('last_sale_at')->nullable()->after('last_restocked_at');
            
            // Indexes
            $table->index('total_sold');
            $table->index('last_sale_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropIndex(['total_sold']);
            $table->dropIndex(['last_sale_at']);
            $table->dropColumn(['total_sold', 'total_revenue', 'last_sale_at']);
        });
    }
};
