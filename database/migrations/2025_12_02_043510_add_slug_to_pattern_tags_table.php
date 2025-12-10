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
        if (!Schema::hasColumn('pattern_tags', 'slug')) {
            Schema::table('pattern_tags', function (Blueprint $table) {
                $table->string('slug')->unique()->after('name');
            });
        }
        // Populate slugs for existing records
        \DB::table('pattern_tags')->whereNull('slug')->update([
            'slug' => \DB::raw('LOWER(REPLACE(name, " ", "-"))')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pattern_tags', function (Blueprint $table) {
            //
        });
    }
};
