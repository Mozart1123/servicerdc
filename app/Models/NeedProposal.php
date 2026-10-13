<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Proposition d'un artisan en réponse à une demande publiée. */
class NeedProposal extends Model
{
    public const TYPE_PRICE = 'price';
    public const TYPE_VISIT = 'visit';

    public const STATUS_PENDING        = 'pending';
    public const STATUS_VISIT_ACCEPTED = 'visit_accepted';
    public const STATUS_ACCEPTED       = 'accepted';
    public const STATUS_REJECTED       = 'rejected';

    /** Propositions encore en lice, que le client peut retenir. */
    public const ACTIVE_STATUSES = [self::STATUS_PENDING, self::STATUS_VISIT_ACCEPTED];

    protected $fillable = [
        'service_request_id', 'artisan_id', 'need_dispatch_id', 'type', 'status',
        'price', 'intervention_date', 'message', 'visit_at', 'study_fee',
        'quote_labor', 'quote_materials', 'quote_duration', 'quote_payment_steps', 'quote_message', 'quote_sent_at',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'study_fee'           => 'decimal:2',
        'quote_labor'         => 'decimal:2',
        'quote_materials'     => 'decimal:2',
        'intervention_date'   => 'date',
        'visit_at'            => 'datetime',
        'quote_sent_at'       => 'datetime',
        'quote_payment_steps' => 'array',
    ];

    public function need(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    public function artisan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(NeedDispatch::class, 'need_dispatch_id');
    }

    public function isVisit(): bool
    {
        return $this->type === self::TYPE_VISIT;
    }

    /** Le devis peut être envoyé une fois la visite acceptée par le client, et une seule fois. */
    public function canSendQuote(): bool
    {
        return $this->isVisit() && $this->status === self::STATUS_VISIT_ACCEPTED && $this->quote_sent_at === null;
    }

    /** Montant comparable : prix proposé, ou total du devis (main d'œuvre + matériaux). */
    public function getTotalAttribute(): ?float
    {
        if ($this->quote_sent_at) {
            return (float) $this->quote_labor + (float) $this->quote_materials;
        }

        return $this->price !== null ? (float) $this->price : null;
    }
}
