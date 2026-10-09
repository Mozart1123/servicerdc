<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_USER       = 'user';
    public const ROLE_ADMIN      = 'admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const TYPE_CLIENT    = 'client';
    public const TYPE_ARTISAN   = 'artisan';
    public const TYPE_JOB_SEEKER = 'job_seeker';
    public const TYPE_RECRUITER = 'recruiter';

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_PENDING   = 'pending';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_DELETED   = 'deleted';

    public const ROLES = [
        self::ROLE_USER       => 'Utilisateur',
        self::ROLE_ADMIN      => 'Administrateur',
        self::ROLE_SUPER_ADMIN => 'Super Administrateur',
    ];

    private const ROLE_HIERARCHY = [
        self::ROLE_USER       => 1,
        self::ROLE_ADMIN      => 2,
        self::ROLE_SUPER_ADMIN => 3,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'province',
        'city',
        'password',
        'user_type',
        'terms_accepted_at',
        'google_id',
        'facebook_id',
        'apple_id',
        'profile_photo',
        'cover_photo',
        'bio',
        'company_description',
        'skills',
        'interests',
        'years_experience',
        'languages',
        'intervention_zone',
        'home_service',
        'address',
        'availability_days',
        'availability_note',
        'is_available',
        'available_until',
        'working_hours_start',
        'working_hours_end',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role'   => self::ROLE_USER,
        'status' => self::STATUS_ACTIVE,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'terms_accepted_at'  => 'datetime',
            'password'           => 'hashed',
            'skills'             => 'array',
            'interests'          => 'array',
            'languages'          => 'array',
            'home_service'       => 'boolean',
            'availability_days'  => 'array',
            'is_available'       => 'boolean',
            'available_until'    => 'date',
        ];
    }

    // ==========================================
    // Role Check Methods
    // ==========================================

    public function hasRole(string $role): bool        { return $this->role === $role; }
    public function hasAnyRole(array $roles): bool     { return in_array($this->role, $roles, true); }
    public function isUser(): bool                     { return $this->hasRole(self::ROLE_USER); }
    public function isAdmin(): bool                    { return $this->hasRole(self::ROLE_ADMIN); }
    public function isSuperAdmin(): bool               { return $this->hasRole(self::ROLE_SUPER_ADMIN); }
    public function hasAdminAccess(): bool             { return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]); }

    public function hasMinimumRole(string $minimumRole): bool
    {
        $userLevel     = self::ROLE_HIERARCHY[$this->role] ?? 0;
        $requiredLevel = self::ROLE_HIERARCHY[$minimumRole] ?? PHP_INT_MAX;
        return $userLevel >= $requiredLevel;
    }

    // ==========================================
    // User Type Check Methods
    // ==========================================

    public function isClient(): bool    { return $this->user_type === self::TYPE_CLIENT; }
    public function isArtisan(): bool   { return $this->user_type === self::TYPE_ARTISAN; }
    public function isJobSeeker(): bool { return $this->user_type === self::TYPE_JOB_SEEKER; }
    public function isRecruiter(): bool { return in_array($this->user_type, [self::TYPE_RECRUITER, self::TYPE_JOB_SEEKER]); }

    public function isPremiumRecruiter(): bool
    {
        return $this->isRecruiter() && $this->activeSubscription && $this->activeSubscription->plan && $this->activeSubscription->plan->slug === 'recruiter-premium';
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where(function ($q) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
        })->latest();
    }

    // ==========================================
    // Accessor Methods
    // ==========================================

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Inconnu';
    }

    public function getUserTypeLabelAttribute(): string
    {
        return match ($this->user_type) {
            self::TYPE_CLIENT     => 'Client',
            self::TYPE_ARTISAN    => 'Artisan',
            self::TYPE_JOB_SEEKER => 'Chercheur d\'emploi',
            self::TYPE_RECRUITER  => 'Recruteur',
            default               => 'Utilisateur',
        };
    }

    public function getDashboardRouteAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'super-admin.dashboard',
            self::ROLE_ADMIN       => 'admin.dashboard',
            default                => 'user.dashboard',
        };
    }

    /**
     * Get the profile photo URL (storage or avatar fallback).
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=29B6D1&color=fff&size=128';
    }

    // ==========================================
    // Relationships
    // ==========================================

    public function artisanLevel()
    {
        return $this->hasOne(ArtisanLevel::class);
    }

    public function identityVerification()
    {
        return $this->hasOne(IdentityVerification::class);
    }

    /**
     * Check if user has an approved identity verification.
     */
    public function isVerified(): bool
    {
        if ($this->isArtisan()) {
            if (!Schema::hasTable('artisan_levels')) {
                return false;
            }

            return $this->artisanLevel && $this->artisanLevel->isIdentityApproved();
        }

        if (!Schema::hasTable('identity_verifications')) {
            return false;
        }

        return $this->identityVerification && $this->identityVerification->isApproved();
    }

    /**
     * Get user role-specific verified badge label.
     */
    public function getVerifiedBadgeLabelAttribute(): string
    {
        return match ($this->user_type) {
            self::TYPE_ARTISAN   => 'Artisan vérifié',
            self::TYPE_RECRUITER => 'Recruteur vérifié',
            default              => 'Compte vérifié',
        };
    }


    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'artisan_id');
    }

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'employer_id');
    }

    public function missionsAsClient()
    {
        return $this->hasMany(Mission::class, 'client_id');
    }

    public function missionsAsArtisan()
    {
        return $this->hasMany(Mission::class, 'artisan_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function cv()
    {
        return $this->hasOne(Cv::class);
    }

    /**
     * Reviews left by this user as a client.
     */
    public function sentReviews()
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    /**
     * Reviews received by this user as an artisan.
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'artisan_id');
    }

    /**
     * Rating summary accessor (Point 5).
     * Centralized computation of average rating, reviews count and "Nouveau" badge.
     */
    public function getRatingSummaryAttribute(): array
    {
        $count = 0;
        $avg = 0.0;

        if ($this->relationLoaded('receivedReviews')) {
            $approved = $this->receivedReviews->where('status', 'approved');
            $count = $approved->count();
            if ($count > 0) {
                $avg = (float) $approved->avg('rating');
            }
        } else {
            $approved = $this->receivedReviews()->where('status', 'approved');
            $count = $approved->count();
            if ($count > 0) {
                $avg = (float) $approved->avg('rating');
            } elseif ($this->artisanLevel && (float) $this->artisanLevel->average_rating > 0) {
                $avg = (float) $this->artisanLevel->average_rating;
                $count = (int) ($this->artisanLevel->total_missions ?? 1);
            }
        }

        $isNew = $count === 0;
        $avgFormatted = number_format($avg, 1, ',', '');
        $badge = $isNew ? 'Nouveau' : "{$avgFormatted} ({$count} " . ($count > 1 ? 'avis' : 'avis') . ')';

        return [
            'average'           => round($avg, 1),
            'average_formatted' => $avgFormatted,
            'count'             => $count,
            'is_new'            => $isNew,
            'badge'             => $badge,
        ];
    }

    public function getRatingStatsAttribute(): array
    {
        return $this->getRatingSummaryAttribute();
    }

    /**
     * Primary profession of the artisan from their services or skills (Point 5).
     */
    public function getMainProfessionAttribute(): string
    {
        $srv = $this->relationLoaded('services') ? $this->services->first() : $this->services()->first();

        if ($srv && !empty($srv->profession)) {
            return $srv->profession;
        }

        if ($srv && $srv->category) {
            return $srv->category->name;
        }

        if (!empty($this->skills) && is_array($this->skills) && count($this->skills) > 0) {
            return $this->skills[0];
        }

        return 'Artisan';
    }

    /**
     * Artisan responsiveness level over the last 30 days (Point 9).
     * Returns e.g. "Répond généralement en moins d'1 h" or null if not enough data.
     */
    public function getResponseTimeBadgeAttribute(): ?string
    {
        if (!$this->isArtisan()) {
            return null;
        }

        $delaysInMinutes = [];

        // 1. Service requests responsiveness
        $requests = ServiceRequest::where('artisan_id', $this->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('responded_at')
            ->get(['created_at', 'responded_at']);

        foreach ($requests as $req) {
            if ($req->responded_at && $req->created_at) {
                $diff = $req->created_at->diffInMinutes($req->responded_at);
                if ($diff >= 0 && $diff <= 10080) {
                    $delaysInMinutes[] = $diff;
                }
            }
        }

        // 2. Messaging responsiveness
        try {
            $recentConvs = Conversation::where(function ($q) {
                    $oneCol = \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
                    $twoCol = \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';
                    $q->where($oneCol, $this->id)->orWhere($twoCol, $this->id);
                })
                ->where('updated_at', '>=', now()->subDays(30))
                ->take(15)
                ->get();

            foreach ($recentConvs as $conv) {
                $messages = $conv->messages()
                    ->where('created_at', '>=', now()->subDays(30))
                    ->orderBy('created_at')
                    ->take(10)
                    ->get(['sender_id', 'created_at']);

                $pendingClientMsgTime = null;
                foreach ($messages as $msg) {
                    if ($msg->sender_id !== $this->id) {
                        if (!$pendingClientMsgTime) {
                            $pendingClientMsgTime = $msg->created_at;
                        }
                    } else {
                        if ($pendingClientMsgTime) {
                            $diff = $pendingClientMsgTime->diffInMinutes($msg->created_at);
                            if ($diff >= 0 && $diff <= 10080) {
                                $delaysInMinutes[] = $diff;
                            }
                            $pendingClientMsgTime = null;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback if tables or columns vary
        }

        if (count($delaysInMinutes) === 0) {
            return null; // Not enough data -> hide
        }

        $avgMinutes = array_sum($delaysInMinutes) / count($delaysInMinutes);

        if ($avgMinutes <= 60) {
            return "Répond généralement en moins d'1 h";
        }
        if ($avgMinutes <= 180) {
            return "Répond généralement en moins de 3 h";
        }
        if ($avgMinutes <= 1440) {
            return "Répond généralement en moins de 24 h";
        }

        return null;
    }

    /**
     * All conversations this user participates in.
     */
    public function conversations()
    {
        $oneCol = \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
        $twoCol = \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';
        return Conversation::where($oneCol, $this->id)->orWhere($twoCol, $this->id);
    }

    /**
     * Unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Unread messages count for this user across all their conversations (Point 6).
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        $unread = 0;
        try {
            foreach ($this->conversations()->get() as $conv) {
                /** @var \App\Models\Conversation $conv */
                $unread += $conv->unreadCountFor($this->id);
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }
        return $unread;
    }

    /**
     * User's wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get user's wallet or create one if missing.
     */
    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet()->firstOrCreate([], [
            'balance'         => 0.00,
            'pending_balance' => 0.00,
            'currency'        => 'USD',
        ]);
    }
}
