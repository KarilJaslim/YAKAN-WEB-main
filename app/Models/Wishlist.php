<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'is_default',
        'is_public',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'item', 'wishlist_items');
    }

    public function patterns()
    {
        return $this->morphedByMany(YakanPattern::class, 'item', 'wishlist_items');
    }

    public function addItem(Model $item): WishlistItem
    {
        return $this->items()->firstOrCreate([
            'item_type' => get_class($item),
            'item_id' => $item->id,
        ]);
    }

    public function removeItem(Model $item): bool
    {
        return $this->items()
            ->where('item_type', get_class($item))
            ->where('item_id', $item->id)
            ->delete();
    }

    public function hasItem(Model $item): bool
    {
        return $this->items()
            ->where('item_type', get_class($item))
            ->where('item_id', $item->id)
            ->exists();
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
