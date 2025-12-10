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
        Schema::create('cultural_heritage', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('category')->default('history'); // history, tradition, culture, art
            $table->integer('order')->default(0); // For ordering sections
            $table->boolean('is_published')->default(true);
            $table->string('author')->nullable();
            $table->date('published_date')->nullable();
            $table->json('gallery')->nullable(); // For multiple images
            $table->json('metadata')->nullable(); // For additional data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultural_heritage');
    }
};
