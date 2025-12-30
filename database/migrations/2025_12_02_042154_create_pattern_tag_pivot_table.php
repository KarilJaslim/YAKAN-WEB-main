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
        Schema::create('pattern_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yakan_pattern_id')->constrained('yakan_patterns')->cascadeOnDelete();
            $table->foreignId('pattern_tag_id')->constrained('pattern_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['yakan_pattern_id', 'pattern_tag_id'], 'pattern_tag_unique');
            $table->index(['pattern_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pattern_tag_pivot');
    }
};
