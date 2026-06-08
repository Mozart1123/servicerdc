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
        'related_type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'related_id',
        'action_url',
    ];

    protected $casts = [
        'data'    => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function markAsRead(): static
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
        return $this;
    }

    public function markAsUnread(): static
    {
        $this->update(['is_read' => false, 'read_at' => null]);
        return $this;
    }
}
