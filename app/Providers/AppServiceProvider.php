<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Review;
use App\Observers\ReviewObserver;
use App\View\Composers\ClientLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share layout-selection variables with all user.* views (Client vs Dashboard)
        View::composer('user.*', ClientLayoutComposer::class);

        // Observers
        Review::observe(ReviewObserver::class);

        \Illuminate\Support\Facades\Gate::define('super-admin', function (\App\Models\User $user) {
            return $user->isSuperAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('access-admin-panel', function (\App\Models\User $user) {
            return $user->hasAdminAccess(); // Helpers from User model
        });

        // Granular permissions for sidebar navigation
        \Illuminate\Support\Facades\Gate::define('manage-users', fn(\App\Models\User $user) => $user->isSuperAdmin());
        \Illuminate\Support\Facades\Gate::define('manage-services', fn(\App\Models\User $user) => $user->hasAdminAccess());
        \Illuminate\Support\Facades\Gate::define('manage-jobs', fn(\App\Models\User $user) => $user->hasAdminAccess());
        \Illuminate\Support\Facades\Gate::define('view-reports', fn(\App\Models\User $user) => $user->hasAdminAccess());
        \Illuminate\Support\Facades\Gate::define('manage-settings', fn(\App\Models\User $user) => $user->hasAdminAccess());
        \Illuminate\Support\Facades\Gate::define('system-config', fn(\App\Models\User $user) => $user->isSuperAdmin());
    }
}
