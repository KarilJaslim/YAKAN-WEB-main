<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WishlistItem extends Model
{
    protected $fillable = [
        'wishlist_id',
        'item_type',
        'item_id',
    ];

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
