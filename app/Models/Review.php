<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'service_request_id',
        'client_id',
        'artisan_id',
        'rating',
        'feedback',
        'status',
        'rejection_reason',
        'migrated_from_artisan_rating_id',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the mission for this review.
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Get the client who left the review.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the artisan being reviewed.
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Filter rejected reviews.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Filter by artisan.
     */
    public function scopeForArtisan($query, $artisanId)
    {
        return $query->where('artisan_id', $artisanId);
    }

    /**
     * Filter by client.
     */
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
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
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default => 'Inconnu',
        };
    }

    /**
     * Get star display.
     */
    public function getStarDisplayAttribute(): string
    {
        return str_repeat('⭐', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
