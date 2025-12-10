<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PatternTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    protected $casts = [
        'color' => 'string',
    ];

    public function patterns(): BelongsToMany
    {
        return $this->belongsToMany(YakanPattern::class, 'pattern_tag_pivot', 'pattern_tag_id', 'yakan_pattern_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $tag->name));
            }
        });
    }
}
