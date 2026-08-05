<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityVerification extends Model
{
    protected $fillable = [
        'user_id',
        'identity_document_type',
        'identity_document_path',
        'selfie_path',
        'verification_status',
        'verification_rejection_reason',
        'verification_rejection_comment',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    const STATUS_NOT_SUBMITTED = 'not_submitted';
    const STATUS_PENDING       = 'pending';
    const STATUS_APPROVED      = 'approved';
    const STATUS_REJECTED       = 'rejected';

    const DOC_VOTER_CARD = 'voter_card';
    const DOC_PASSPORT   = 'passport';
    const DOC_NATIONAL_ID = 'national_id';

    public static function rejectionReasons(): array
    {
        return [
            'Document illisible ou flou'           => 'Document illisible ou flou',
            'Le visage ne correspond pas au document' => 'Le visage ne correspond pas au document',
            'Document expiré'                      => 'Document expiré',
            'Document incomplet'                   => 'Document incomplet',
            'Photo de mauvaise qualité'             => 'Photo de mauvaise qualité',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isApproved(): bool
    {
        return $this->verification_status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->verification_status === self::STATUS_REJECTED;
    }
}
