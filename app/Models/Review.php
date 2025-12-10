<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'title',
        'comment',
        'is_verified',
        'is_approved',
        'admin_response',
        'admin_response_at',
        'admin_id',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'helpful_count' => 'integer',
        'admin_response_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                $stars .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        return $stars;
    }

    public function getRatingPercentageAttribute(): int
    {
        return ($this->rating / 5) * 100;
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function markAsVerified(): void
    {
        $this->is_verified = true;
        $this->save();
    }

    public function approve(): void
    {
        $this->is_approved = true;
        $this->save();
    }

    public function reject(): void
    {
        $this->is_approved = false;
        $this->save();
    }

    public function addAdminResponse(string $response, $adminId): void
    {
        $this->admin_response = $response;
        $this->admin_response_at = now();
        $this->admin_id = $adminId;
        $this->save();
    }

    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_approved) {
            return 'Pending Approval';
        } elseif ($this->is_verified) {
            return 'Verified Purchase';
        } else {
            return 'Approved';
        }
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->is_approved) {
            return 'text-yellow-600 bg-yellow-50';
        } elseif ($this->is_verified) {
            return 'text-green-600 bg-green-50';
        } else {
            return 'text-blue-600 bg-blue-50';
        }
    }
}
