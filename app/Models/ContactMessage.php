<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContactMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include unread messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead()
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark the message as unread.
     *
     * @return bool
     */
    public function markAsUnread()
    {
        return $this->update(['is_read' => false]);
    }

    /**
     * Get the formatted created date.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Get a short excerpt of the message.
     *
     * @param  int  $length
     * @return string
     */
    public function getExcerpt($length = 100)
    {
        return \Illuminate\Support\Str::limit($this->message, $length);
    }

    /**
     * Check if message was created today.
     *
     * @return bool
     */
    public function isToday()
    {
        return $this->created_at->isToday();
    }

    /**
     * Get human readable time difference.
     *
     * @return string
     */
    public function getTimeDifferenceAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}