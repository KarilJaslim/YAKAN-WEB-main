<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatternMedia extends Model
{
    protected $fillable = [
        'yakan_pattern_id',
        'type',
        'path',
        'alt_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function pattern(): BelongsTo
    {
        return $this->belongsTo(YakanPattern::class, 'yakan_pattern_id');
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return '';
        }
        
        // If it's already a full URL or data URI, return as is
        if (str_starts_with($this->path, 'http') || str_starts_with($this->path, 'data:')) {
            return $this->path;
        }
        
        // Check if file exists in new uploads directory (for new uploads)
        if (file_exists(public_path('uploads/' . $this->path))) {
            return asset('uploads/' . $this->path);
        }
        
        // Check if file exists in storage directory (for old uploads)
        if (file_exists(public_path('storage/' . $this->path))) {
            return asset('storage/' . $this->path);
        }
        
        // Default to uploads for new paths
        return asset('uploads/' . $this->path);
    }
}
