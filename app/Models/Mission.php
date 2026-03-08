<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'client_id',
        'artisan_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'amount',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'rating' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the service related to this mission.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the client for this mission.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the artisan for this mission.
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter missions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter pending missions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter in-progress missions.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Filter completed missions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Filter cancelled missions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
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
            'in_progress' => 'En cours',
            'completed' => 'Complétée',
            'cancelled' => 'Annulée',
            default => 'Inconnu',
        };
    }
}
