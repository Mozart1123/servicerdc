<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_offer_id',
        'user_id',
        'cv_id',
        'status',
        'message',
        'resume_url',
        'cover_letter',
        'applied_at',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'applied_at'  => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_HIRED    = 'hired';
    // Legacy
    const STATUS_ACCEPTED = 'accepted';

    // ==========================================
    // Relationships
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopePending($query)   { return $query->where('status', self::STATUS_PENDING); }
    public function scopeApproved($query)  { return $query->where('status', self::STATUS_APPROVED); }
    public function scopeAccepted($query)  { return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_ACCEPTED]); }
    public function scopeRejected($query)  { return $query->where('status', self::STATUS_REJECTED); }
    public function scopeInterview($query) { return $query->where('status', self::STATUS_INTERVIEW); }
    public function scopeHired($query)     { return $query->where('status', self::STATUS_HIRED); }

    // ==========================================
    // Accessors
    // ==========================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'En attente',
            'approved'  => 'Approuvée',
            'accepted'  => 'Acceptée',
            'rejected'  => 'Refusée',
            'interview' => 'Entretien',
            'hired'     => 'Embauché(e)',
            default     => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'amber',
            'approved', 'accepted' => 'emerald',
            'rejected'  => 'red',
            'interview' => 'blue',
            'hired'     => 'purple',
            default     => 'slate',
        };
    }

    public function canBeWithdrawnBy(int $userId): bool
    {
        return $this->user_id === $userId && $this->status === self::STATUS_PENDING;
    }
}
