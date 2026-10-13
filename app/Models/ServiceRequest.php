<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'artisan_id',
        'mode',
        'category_id',
        'service_type_id',
        'commune_id',
        'quartier',
        'timing',
        'preferred_date',
        'budget_type',
        'photos',
        'expires_at',
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
        'payment_status',
        'paid_at',
        'accepted_at',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'notes',
        'response',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at'  => 'datetime',
        'accepted_at'   => 'datetime',
        'completed_at'  => 'datetime',
        'cancelled_at'  => 'datetime',
        'expires_at'    => 'datetime',
        'preferred_date' => 'date',
        'photos'        => 'array',
        'paid_at'       => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status !== 'paid';
    }

    // Status constants
    const STATUS_PENDING             = 'pending';
    const STATUS_ACCEPTED            = 'accepted';
    const STATUS_IN_PROGRESS         = 'in_progress';
    const STATUS_AWAITING_VALIDATION = 'awaiting_validation'; // artisan says done, waiting on client confirmation
    const STATUS_REJECTED            = 'rejected';
    const STATUS_COMPLETED           = 'completed';
    const STATUS_CANCELLED           = 'cancelled';

    // Origine de la demande
    const MODE_DIRECT = 'direct'; // adressée à un artisan depuis le catalogue
    const MODE_OPEN   = 'open';   // besoin publié, envoyé par le matching

    const MAX_PHOTOS = 5;
    const OPEN_DAYS  = 7; // une demande publiée sans suite se ferme après 7 jours

    public const TIMINGS = [
        'urgent'    => 'Dès que possible (urgent)',
        'this_week' => 'Cette semaine',
        'date'      => 'À une date précise',
    ];

    public function isOpenNeed(): bool
    {
        return $this->mode === self::MODE_OPEN;
    }

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

    public function mission()
    {
        return $this->hasOne(Mission::class, 'service_request_id');
    }

    public function ratings()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function respondedByUser()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function rating()
    {
        return $this->hasOne(ArtisanRating::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'service_request_id');
    }

    /**
     * Get review left for this service request (via direct link or legacy rating).
     */
    public function getClientReviewAttribute(): ?Review
    {
        if ($this->relationLoaded('review')) {
            return $this->review;
        }
        return $this->review()->first();
    }

    /**
     * Nicely formatted mission duration, or null if duration is 0 / < 1min.
     * Prevents displaying '0min' on completed requests.
     */
    public function getDurationFormattedAttribute(): ?string
    {
        if (!$this->accepted_at || !$this->completed_at) {
            return null;
        }

        $totalSeconds = $this->completed_at->diffInSeconds($this->accepted_at);
        if ($totalSeconds < 60) {
            return null;
        }

        $diffDays = (int) floor($totalSeconds / 86400);
        $diffHours = (int) floor(($totalSeconds % 86400) / 3600);
        $diffMinutes = (int) floor(($totalSeconds % 3600) / 60);

        if ($diffDays > 0) {
            return $diffDays . 'j' . ($diffHours > 0 ? " {$diffHours}h" : '');
        }
        if ($diffHours > 0) {
            return "{$diffHours}h" . ($diffMinutes > 0 ? " " . sprintf('%02d', $diffMinutes) . "min" : '');
        }
        return "{$diffMinutes} min";
    }

    public function workSessions()
    {
        return $this->hasMany(ServiceRequestWorkSession::class)->orderBy('started_at');
    }

    /**
     * The currently-open work session, if any. Null means work is paused
     * (or simply hasn't started/isn't in_progress).
     */
    public function activeWorkSession()
    {
        return $this->hasOne(ServiceRequestWorkSession::class)->whereNull('ended_at')->latest('started_at');
    }

    /**
     * True when the service is in progress but there's no active work
     * session — i.e. the artisan has paused.
     */
    public function isWorkPaused(): bool
    {
        return $this->status === 'in_progress' && !$this->activeWorkSession;
    }

    /**
     * Total worked time across all sessions, in seconds. If a session is
     * still active this keeps growing on every call (each session's own
     * durationInSeconds() counts up to now() while unclosed), which is what
     * makes the live-ticking display work without any extra bookkeeping.
     */
    public function totalWorkedSeconds(): int
    {
        return $this->workSessions->sum(fn (ServiceRequestWorkSession $session) => $session->durationInSeconds());
    }

    /**
     * Worked time broken down by calendar day (grouped by the date portion
     * of each session's started_at), sorted chronologically. Each entry is
     * ['date' => 'Y-m-d', 'seconds' => int]. A session that happens to still
     * be active is simply attributed to the day it started on.
     */
    public function workSessionsByDay()
    {
        return $this->workSessions
            ->groupBy(fn (ServiceRequestWorkSession $session) => $session->started_at->toDateString())
            ->map(function ($sessions, $date) {
                return [
                    'date'    => $date,
                    'seconds' => $sessions->sum(fn (ServiceRequestWorkSession $s) => $s->durationInSeconds()),
                ];
            })
            ->sortKeys()
            ->values();
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
        if ($this->isOpenNeed() && $this->status === self::STATUS_PENDING) {
            return 'Publiée';
        }

        return match($this->status) {
            'pending'             => 'En attente',
            'accepted'            => 'Acceptée',
            'in_progress'         => 'En cours',
            'awaiting_validation' => 'À valider',
            'rejected'            => 'Refusée',
            'completed'           => 'Terminée',
            'cancelled'           => 'Annulée',
            'addressed'           => 'Traitée',
            default               => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'             => 'amber',
            'accepted'            => 'orange',
            'in_progress'         => 'emerald',
            'awaiting_validation' => 'amber',
            'rejected'            => 'red',
            'completed'           => 'blue',
            'cancelled'           => 'slate',
            default               => 'slate',
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
        // Montants en dollars, comme les prix des services (comparés par le matching).
        if (!$this->budget_min && !$this->budget_max) {
            return $this->budget_type === 'none' ? 'Pas d\'idée du prix' : 'Non spécifié';
        }
        $fmt = fn ($v) => '$' . number_format((float) $v, 0, ',', ' ');
        if ((float) $this->budget_min === (float) $this->budget_max) {
            return $fmt($this->budget_max);
        }
        $min = $this->budget_min ? $fmt($this->budget_min) : 'Néant';
        $max = $this->budget_max ? $fmt($this->budget_max) : 'Illimité';
        return "{$min} - {$max}";
    }

    public function getTimingLabelAttribute(): ?string
    {
        if ($this->timing === 'date' && $this->preferred_date) {
            return 'Le ' . $this->preferred_date->translatedFormat('d F Y');
        }
        return self::TIMINGS[$this->timing] ?? null;
    }

    public function getLocationLabelAttribute(): ?string
    {
        $parts = array_filter([$this->quartier, $this->commune?->name]);
        return $parts ? implode(', ', $parts) : $this->city;
    }

    /** @return string[] URLs publiques des photos jointes. */
    public function getPhotoUrlsAttribute(): array
    {
        return array_map(fn ($path) => Storage::disk('public')->url($path), $this->photos ?? []);
    }

    public function canBeCancelledBy(int $userId): bool
    {
        return $this->user_id === $userId && in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED]);
    }
}
