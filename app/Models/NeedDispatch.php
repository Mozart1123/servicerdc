<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Une demande publiée envoyée à un artisan lors d'une vague du matching. */
class NeedDispatch extends Model
{
    public const STATUS_SENT     = 'sent';
    public const STATUS_PROPOSED = 'proposed';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_CLOSED   = 'closed'; // demande annulée par le client avant réponse

    protected $fillable = [
        'service_request_id', 'artisan_id', 'wave', 'score', 'proximity',
        'status', 'sent_at', 'deadline_at', 'responded_at', 'decline_reason',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'deadline_at'  => 'datetime',
        'responded_at' => 'datetime',
        'score'        => 'float',
        'wave'         => 'integer',
    ];

    public function need(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    public function artisan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function scopeAwaitingResponse(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function isAwaitingResponse(): bool
    {
        return $this->status === self::STATUS_SENT;
    }
}
