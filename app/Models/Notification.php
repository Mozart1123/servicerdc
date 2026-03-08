<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the user who receives this notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Filter read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Filter by notification type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // ==========================================
    // Methods
    // ==========================================

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return $this;
    }
}
