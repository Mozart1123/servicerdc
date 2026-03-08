<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_offer_id',
        'user_id',
        'status',
        'message',
        'resume_url',
        'cover_letter',
        'applied_at',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the user who applied.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job offer applied for.
     */
    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter accepted applications.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Filter rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Get the status label in French.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'rejected' => 'Rejeté',
            default => 'Inconnu',
        };
    }
}

