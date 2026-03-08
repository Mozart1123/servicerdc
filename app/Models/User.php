<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model with RBAC Support
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $role
 * @property string $user_type
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon|null $terms_accepted_at
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Role Constants - Following Single Responsibility Principle
     */
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * User Type Constants
     */
    public const TYPE_CLIENT = 'client';
    public const TYPE_ARTISAN = 'artisan';
    public const TYPE_JOB_SEEKER = 'job_seeker';

    /**
     * Status Constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Available roles with their display names
     */
    public const ROLES = [
        self::ROLE_USER => 'Utilisateur',
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_SUPER_ADMIN => 'Super Administrateur',
    ];

    /**
     * Role hierarchy (higher index = more permissions)
     */
    private const ROLE_HIERARCHY = [
        self::ROLE_USER => 1,
        self::ROLE_ADMIN => 2,
        self::ROLE_SUPER_ADMIN => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Default attribute values
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_USER,
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // Role Check Methods - Single Responsibility
    // ==========================================

    /**
     * Check if user has the specified role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles.
     *
     * @param string[] $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Check if user is a regular user (client).
     */
    public function isUser(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * Check if user has admin-level access or higher.
     */
    public function hasAdminAccess(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    /**
     * Check if user has higher or equal role than specified.
     */
    public function hasMinimumRole(string $minimumRole): bool
    {
        $userLevel = self::ROLE_HIERARCHY[$this->role] ?? 0;
        $requiredLevel = self::ROLE_HIERARCHY[$minimumRole] ?? PHP_INT_MAX;

        return $userLevel >= $requiredLevel;
    }

    // ==========================================
    // User Type Check Methods
    // ==========================================

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->user_type === self::TYPE_CLIENT;
    }

    /**
     * Check if user is an artisan.
     */
    public function isArtisan(): bool
    {
        return $this->user_type === self::TYPE_ARTISAN;
    }

    /**
     * Check if user is a job seeker.
     */
    public function isJobSeeker(): bool
    {
        return $this->user_type === self::TYPE_JOB_SEEKER;
    }

    // ==========================================
    // Accessor Methods
    // ==========================================

    /**
     * Get user role label in French.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Inconnu';
    }

    /**
     * Get user type label in French.
     */
    public function getUserTypeLabelAttribute(): string
    {
        return match ($this->user_type) {
            self::TYPE_CLIENT => 'Client',
            self::TYPE_ARTISAN => 'Artisan',
            self::TYPE_JOB_SEEKER => 'Chercheur d\'emploi',
            default => 'Utilisateur',
        };
    }

    /**
     * Get the dashboard route name based on user role.
     */
    public function getDashboardRouteAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'super-admin.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            default => 'user.dashboard',
        };
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get all job applications submitted by this user.
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get all documents uploaded by this user.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all services created by this artisan.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'artisan_id');
    }

    /**
     * Get all job offers created by this admin/employer.
     */
    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'employer_id');
    }

    /**
     * Get all missions as client.
     */
    public function missionsAsClient()
    {
        return $this->hasMany(Mission::class, 'client_id');
    }

    /**
     * Get all missions as artisan.
     */
    public function missionsAsArtisan()
    {
        return $this->hasMany(Mission::class, 'artisan_id');
    }

    /**
     * Get all notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all service requests made by this user.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
