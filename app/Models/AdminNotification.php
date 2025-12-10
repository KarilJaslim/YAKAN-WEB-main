<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'admin_id',
        'type',
        'title',
        'message',
        'data',
        'url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
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
            'custom_order' => 'fas fa-palette',
            'payment' => 'fas fa-credit-card',
            'shipping' => 'fas fa-truck',
            'inventory' => 'fas fa-warehouse',
            'user' => 'fas fa-user',
            'system' => 'fas fa-cog',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    public function getColorAttribute(): string
    {
        $colors = [
            'order' => 'blue',
            'custom_order' => 'indigo',
            'payment' => 'green',
            'shipping' => 'purple',
            'inventory' => 'orange',
            'user' => 'teal',
            'system' => 'gray',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    public static function createNotification($adminId, $type, $title, $message, $url = null, $data = null): self
    {
        return self::create([
            'admin_id' => $adminId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'data' => $data,
        ]);
    }

    public static function notifyAllAdmins($type, $title, $message, $url = null, $data = null): void
    {
        $admins = Admin::all();
        foreach ($admins as $admin) {
            self::createNotification($admin->id, $type, $title, $message, $url, $data);
        }
    }
}
