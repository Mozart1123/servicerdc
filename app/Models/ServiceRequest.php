<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'artisan_id',
        'phone',
        'email',
        'requested_service_name',
        'category_needed',
        'description',
        'city',
        'location',
        'budget_min',
        'budget_max',
        'urgency',
        'status',
        'notes',
        'response',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // ==========================================
    // Relationships
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function respondedByUser()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopePending($query)    { return $query->where('status', self::STATUS_PENDING); }
    public function scopeAccepted($query)   { return $query->where('status', self::STATUS_ACCEPTED); }
    public function scopeRejected($query)   { return $query->where('status', self::STATUS_REJECTED); }
    public function scopeCompleted($query)  { return $query->where('status', self::STATUS_COMPLETED); }
    public function scopeCancelled($query)  { return $query->where('status', self::STATUS_CANCELLED); }
    public function scopeAddressed($query)  { return $query->where('status', 'addressed'); }

    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeByUrgency($query, $urgency) { return $query->where('urgency', $urgency); }
    public function scopeByCity($query, $city) { return $query->where('city', 'like', "%{$city}%"); }

    public function scopeSearch($query, $term)
    {
        return $query->where('requested_service_name', 'like', "%{$term}%")
                     ->orWhere('description', 'like', "%{$term}%");
    }

    // ==========================================
    // Accessors
    // ==========================================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'accepted'  => 'Acceptée',
            'rejected'  => 'Refusée',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            'addressed' => 'Traitée',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'amber',
            'accepted'  => 'emerald',
            'rejected'  => 'red',
            'completed' => 'blue',
            'cancelled' => 'slate',
            default     => 'slate',
        };
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match($this->urgency ?? 'medium') {
            'low'    => 'Basse',
            'medium' => 'Moyenne',
            'high'   => 'Élevée',
            'urgent' => 'Urgente',
            default  => 'Moyenne',
        };
    }

    public function getBudgetRangeAttribute(): string
    {
        if (!$this->budget_min && !$this->budget_max) return 'Non spécifié';
        $min = $this->budget_min ? number_format($this->budget_min, 0, ',', ' ') . ' FC' : 'Néant';
        $max = $this->budget_max ? number_format($this->budget_max, 0, ',', ' ') . ' FC' : 'Illimité';
        return "{$min} - {$max}";
    }

    public function canBeCancelledBy(int $userId): bool
    {
        return $this->user_id === $userId && in_array($this->status, [self::STATUS_PENDING]);
    }
}
