<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CulturalHeritage extends Model
{
    use HasFactory;

    protected $table = 'cultural_heritage';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'image',
        'category',
        'order',
        'is_published',
        'author',
        'published_date',
        'gallery',
        'metadata',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_date' => 'date',
        'gallery' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($heritage) {
            if (empty($heritage->slug)) {
                $heritage->slug = Str::slug($heritage->title);
            }
        });

        static::updating(function ($heritage) {
            if ($heritage->isDirty('title') && empty($heritage->slug)) {
                $heritage->slug = Str::slug($heritage->title);
            }
        });
    }

    /**
     * Scope to get only published items
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to order by custom order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/default-heritage.jpg');
        }

        if (strpos($this->image, 'http') === 0) {
            return $this->image;
        }

        // Check if file exists in new uploads directory
        if (file_exists(public_path('uploads/' . $this->image))) {
            return asset('uploads/' . $this->image);
        }
        
        // Check if file exists in storage directory
        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        // Default to uploads for new paths
        return asset('uploads/' . $this->image);
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Get reading time in minutes
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // Average reading speed: 200 words/min
    }
}
