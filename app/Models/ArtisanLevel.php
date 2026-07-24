<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtisanLevel extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'total_missions',
        'missions_via_platform',
        'average_rating',
        'warning_count',
        'visibility_penalty_until',
        'level_updated_at',
        'identity_document_path',
        'identity_document_type',
        'verification_status',
        'verification_rejection_reason',
        'verified_at',
        'verified_by',
        'grace_period_ends_at',
    ];

    protected $casts = [
        'average_rating'           => 'decimal:1',
        'visibility_penalty_until' => 'datetime',
        'level_updated_at'         => 'datetime',
        'verified_at'              => 'datetime',
        'grace_period_ends_at'     => 'datetime',
    ];

    const LEVEL_NOUVEAU  = 'nouveau';
    const LEVEL_ACTIF    = 'actif';
    const LEVEL_VERIFIE  = 'verifie';
    const LEVEL_ELITE    = 'elite';

    const STATUS_NOT_SUBMITTED = 'not_submitted';
    const STATUS_PENDING       = 'pending';
    const STATUS_APPROVED      = 'approved';
    const STATUS_REJECTED       = 'rejected';

    const DOC_NATIONAL_ID     = 'national_id';
    const DOC_PASSPORT        = 'passport';
    const DOC_DRIVING_LICENSE = 'driving_license';

    const LEVELS = [
        self::LEVEL_NOUVEAU  => ['label' => 'Nouveau',  'color' => 'slate',   'icon' => 'fa-seedling'],
        self::LEVEL_ACTIF    => ['label' => 'Actif',    'color' => 'blue',    'icon' => 'fa-bolt'],
        self::LEVEL_VERIFIE  => ['label' => 'Vérifié',  'color' => 'teal',    'icon' => 'fa-circle-check'],
        self::LEVEL_ELITE    => ['label' => 'Élite',    'color' => 'amber',   'icon' => 'fa-crown'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->level]['label'] ?? 'Nouveau';
    }

    public function getLevelColorAttribute(): string
    {
        return self::LEVELS[$this->level]['color'] ?? 'slate';
    }

    public function getLevelIconAttribute(): string
    {
        return self::LEVELS[$this->level]['icon'] ?? 'fa-seedling';
    }

    public function isUnderPenalty(): bool
    {
        return $this->visibility_penalty_until && $this->visibility_penalty_until->isFuture();
    }

    public function isIdentityApproved(): bool
    {
        return $this->verification_status === self::STATUS_APPROVED;
    }

    public function isInGracePeriod(): bool
    {
        return $this->grace_period_ends_at && $this->grace_period_ends_at->isFuture();
    }
}
