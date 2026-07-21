<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_offer_id',
        'user_id',
        'cv_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'status',
        'message',
        'resume_url',
        'cover_letter',
        'cv_attachment',
        'applied_at',
        'reviewed_at',
        'admin_notes',
        'rejection_reason',
    ];

    protected $appends = [
        'is_premium_locked',
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

    public function getIsPremiumLockedAttribute(): bool
    {
        $user = auth()->user();
        
        if (!$user) return false;

        // The applicant always sees their own application
        if ($user->id === $this->user_id) {
            return false;
        }
        
        // Admin/Super Admin see everything
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return false;
        }

        $employer = $this->jobOffer->employer ?? $this->jobOffer->user;
        if (!$employer) return false;

        // If recruiter is premium, it's not locked
        if ($employer->isPremiumRecruiter()) {
            return false;
        }

        // Check if this application is within the first 10 for this job
        $top10Ids = static::where('job_offer_id', $this->job_offer_id)
                          ->orderBy('created_at', 'asc')
                          ->limit(10)
                          ->pluck('id');
                          
        return !$top10Ids->contains($this->id);
    }
}
