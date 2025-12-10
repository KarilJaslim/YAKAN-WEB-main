<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'discount_amount',
        'coupon_id',
        'coupon_code',
        'status',
        'payment_status',
        'payment_method',
        'tracking_number',
        'tracking_status',
        'tracking_history',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tracking_history' => 'array',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function appendTrackingEvent(string $status, string $date = null): void
    {
        $history = $this->tracking_history ?? [];
        
        // Handle case where tracking_history is a string (not properly cast)
        if (is_string($history)) {
            $history = json_decode($history, true) ?? [];
        }
        
        $history[] = [
            'status' => $status,
            'date' => $date ?? now()->format('Y-m-d h:i A'),
        ];
        $this->tracking_history = $history;
    }
}
