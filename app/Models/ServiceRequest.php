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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the user who made this request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service for this request.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the admin who responded to this request.
     */
    public function respondedByUser()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter addressed requests.
     */
    public function scopeAddressed($query)
    {
        return $query->where('status', 'addressed');
    }

    /**
     * Filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter by urgency.
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
    }

    /**
     * Filter by city.
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Filter by category needed.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category_needed', 'like', "%{$category}%");
    }

    /**
     * Search in title and description.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('requested_service_name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Get unresponded requests.
     */
    public function scopeUnresponded($query)
    {
        return $query->where('responded_at', null);
    }

    // ==========================================
    // Accessors & Mutators
    // ==========================================

    /**
     * Get status label in French.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'addressed' => 'Traitée',
            default => $this->status,
        };
    }

    /**
     * Get urgency label in French.
     */
    public function getUrgencyLabelAttribute(): string
    {
        return match($this->urgency ?? 'medium') {
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Élevée',
            'urgent' => 'Urgente',
            default => 'Moyenne',
        };
    }

    /**
     * Get budget range formatted.
     */
    public function getBudgetRangeAttribute(): string
    {
        if (!$this->budget_min && !$this->budget_max) {
            return 'Non spécifié';
        }
        
        $min = $this->budget_min ? number_format($this->budget_min, 0, ',', ' ') . ' FC' : 'Néant';
        $max = $this->budget_max ? number_format($this->budget_max, 0, ',', ' ') . ' FC' : 'Illimité';
        
        return "{$min} - {$max}";
    }
}
