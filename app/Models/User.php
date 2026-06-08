<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    public const STATUS_SUSPENDED = 'suspended';

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
        'password',
        'role',
        'user_type',
        'status',
        'terms_accepted_at',
        'google_id',
        'facebook_id',
        'apple_id',
        'profile_photo',
        'bio',
        'skills',
        'interests',
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
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password'          => 'hashed',
            'skills'            => 'array',
            'interests'         => 'array',
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
}
