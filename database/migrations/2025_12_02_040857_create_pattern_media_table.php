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
        Schema::create('pattern_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yakan_pattern_id')->constrained('yakan_patterns')->cascadeOnDelete();
            $table->string('type')->default('image'); // image, video, doc
            $table->string('path'); // storage path
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['yakan_pattern_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pattern_media');
    }
};
