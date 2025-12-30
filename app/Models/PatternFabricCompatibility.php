<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatternFabricCompatibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'yakan_pattern_id',
        'fabric_type_id',
        'difficulty_level',
        'price_multiplier',
        'estimated_production_days',
        'notes',
        'is_recommended',
        'is_active',
    ];

    protected $casts = [
        'price_multiplier' => 'decimal:2',
        'estimated_production_days' => 'integer',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the pattern
     */
    public function pattern(): BelongsTo
    {
        return $this->belongsTo(YakanPattern::class, 'yakan_pattern_id');
    }

    /**
     * Get the fabric type
     */
    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(FabricType::class);
    }

    /**
     * Scope for active compatibilities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for recommended combinations
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    /**
     * Get difficulty color for display
     */
    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'simple' => 'green',
            'medium' => 'yellow',
            'complex' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get formatted price multiplier
     */
    public function getFormattedMultiplierAttribute(): string
    {
        return '×' . number_format($this->price_multiplier, 2);
    }
}
