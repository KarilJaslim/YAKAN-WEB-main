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
        // Nothing to do here because 'total' column was already renamed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, you could add the 'total' column back if needed
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->decimal('total', 10, 2)->nullable();
        // });
    }
};
