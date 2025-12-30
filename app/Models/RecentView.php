<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RecentView extends Model
{
    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function track(Model $item, int $userId): void
    {
        // Remove existing record for this item to keep only the latest
        static::where('user_id', $userId)
            ->where('viewable_type', get_class($item))
            ->where('viewable_id', $item->id)
            ->delete();

        // Create new record
        static::create([
            'user_id' => $userId,
            'viewable_type' => get_class($item),
            'viewable_id' => $item->id,
            'viewed_at' => now(),
        ]);

        // Keep only last 20 items per user
        static::where('user_id', $userId)
            ->orderByDesc('viewed_at')
            ->offset(20)
            ->delete();
    }

    public static function getRecentItems(int $userId, int $limit = 10)
    {
        return static::where('user_id', $userId)
            ->with('viewable')
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->get()
            ->map(fn($view) => $view->viewable)
            ->filter();
    }
}
