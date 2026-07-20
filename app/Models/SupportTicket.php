<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'mission_id',
        'subject',
        'message',
        'ticket_type',
        'priority',
        'status',
        'resolution',
        'refund_amount',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at'    => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
