<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'content',
        'message',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $touches = ['conversation'];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    /**
     * Retrieve message body from either 'content' or 'message' column.
     */
    public function getBodyAttribute(): string
    {
        return $this->content ?? $this->message ?? '';
    }

    /**
     * Get attachment URL for displaying.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) return null;
        return \Storage::url($this->attachment);
    }
}
