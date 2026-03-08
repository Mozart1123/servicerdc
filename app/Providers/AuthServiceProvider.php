<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Authentication and Authorization Service Provider
 * 
 * Defines gates and policies for role-based access control.
 * Following Open/Closed Principle - extend via new gates without modifying existing.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Define policies here
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->defineGates();
    }

    /**
     * Define all authorization gates.
     */
    private function defineGates(): void
    {
        // Super Admin - Full access
        Gate::define('super-admin', function (User $user): bool {
            return $user->isSuperAdmin();
        });

        // Admin - Admin level access
        Gate::define('admin', function (User $user): bool {
            return $user->isAdmin() || $user->isSuperAdmin();
        });

        // User - Basic access
        Gate::define('user', function (User $user): bool {
            return true; // All authenticated users
        });

        // Access admin panel (admin or super_admin)
        Gate::define('access-admin-panel', function (User $user): bool {
            return $user->hasAdminAccess();
        });

        // Manage users (super_admin only)
        Gate::define('manage-users', function (User $user): bool {
            return $user->isSuperAdmin();
        });

        // Manage settings (admin or super_admin)
        Gate::define('manage-settings', function (User $user): bool {
            return $user->hasAdminAccess();
        });

        // Manage services
        Gate::define('manage-services', function (User $user): bool {
            return $user->hasAdminAccess();
        });

        // Manage jobs
        Gate::define('manage-jobs', function (User $user): bool {
            return $user->hasAdminAccess();
        });

        // View reports
        Gate::define('view-reports', function (User $user): bool {
            return $user->hasAdminAccess();
        });

        // System configuration (super_admin only)
        Gate::define('system-config', function (User $user): bool {
            return $user->isSuperAdmin();
        });

        // Before callback - Super Admin bypasses all checks (optional)
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->isSuperAdmin() && $ability !== 'super-admin') {
                return true;
            }
            return null;
        });
    }
}
