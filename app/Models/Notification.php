<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'url',
        'is_read',
        'read_at',
        'icon',
        'color',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    public function markAsUnread(): void
    {
        $this->is_read = false;
        $this->read_at = null;
        $this->save();
    }

    public function getIconAttribute(): string
    {
        $icons = [
            'order' => 'fas fa-shopping-bag',
            'payment' => 'fas fa-credit-card',
            'shipping' => 'fas fa-truck',
            'review' => 'fas fa-star',
            'inventory' => 'fas fa-warehouse',
            'system' => 'fas fa-cog',
            'promotion' => 'fas fa-tag',
            'custom_order' => 'fas fa-palette',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    public function getColorAttribute(): string
    {
        $colors = [
            'order' => 'blue',
            'payment' => 'green',
            'shipping' => 'purple',
            'review' => 'yellow',
            'inventory' => 'orange',
            'system' => 'gray',
            'promotion' => 'red',
            'custom_order' => 'indigo',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    public static function createNotification($userId, $type, $title, $message, $url = null, $data = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'data' => $data,
        ]);
    }
}
