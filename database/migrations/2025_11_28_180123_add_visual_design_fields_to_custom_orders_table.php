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
            // Design method tracking
            $table->string('design_method')->default('text')->after('specifications'); // text, visual, hybrid
            $table->json('design_metadata')->nullable()->after('design_upload'); // Pattern positions, colors, transformations
            $table->string('design_version')->default('1.0')->after('design_metadata'); // Version tracking for design compatibility
            
            // Visual design specific fields
            $table->decimal('canvas_width', 8, 2)->nullable()->after('design_version');
            $table->decimal('canvas_height', 8, 2)->nullable()->after('canvas_width');
            $table->json('pattern_positions')->nullable()->after('canvas_height'); // Detailed pattern placement data
            $table->string('color_palette')->nullable()->after('pattern_positions'); // JSON array of used colors
            
            // Design collaboration fields
            $table->text('artisan_notes')->nullable()->after('color_palette'); // Artisan feedback on design
            $table->timestamp('design_approved_at')->nullable()->after('artisan_notes'); // When design was approved for production
            $table->foreignId('design_approved_by')->nullable()->after('design_approved_at')->constrained('admins')->onDelete('set null');
            
            // Design modification tracking
            $table->json('design_modifications')->nullable()->after('design_approved_by'); // Track changes to design
            $table->timestamp('last_design_update')->nullable()->after('design_modifications');
            
            // Performance analytics
            $table->integer('design_completion_time')->nullable()->after('last_design_update'); // Time spent in designer (seconds)
            $table->integer('pattern_count')->default(0)->after('design_completion_time'); // Number of patterns used
            $table->string('complexity_score')->nullable()->after('pattern_count'); // Calculated complexity score
            
            // Indexes for performance
            $table->index(['design_method', 'status']);
            $table->index(['design_approved_at']);
            $table->index(['pattern_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_orders', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['design_method', 'status']);
            $table->dropIndex(['design_approved_at']);
            $table->dropIndex(['pattern_count']);
            
            // Drop columns
            $table->dropColumn([
                'design_method',
                'design_metadata', 
                'design_version',
                'canvas_width',
                'canvas_height',
                'pattern_positions',
                'color_palette',
                'artisan_notes',
                'design_approved_at',
                'design_approved_by',
                'design_modifications',
                'last_design_update',
                'design_completion_time',
                'pattern_count',
                'complexity_score'
            ]);
        });
    }
};
