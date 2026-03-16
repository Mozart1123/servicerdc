<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\User\ServiceController as UserServiceController;
use App\Http\Controllers\User\JobController as UserJobController;
use App\Http\Controllers\User\ServiceRequestController as UserServiceRequestController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SystemController as SuperAdminSystemController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\FinancialController as AdminFinancialController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/how-it-works', [HomeController::class, 'howItWorks'])->name('how-it-works');

// Guest Routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');

    // Social Authentication
    Route::get('/auth/{provider}', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
});

// Auth Routes
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});

// User Routes
Route::middleware(['auth', 'role:user,admin,super_admin'])
    ->prefix('user')
    ->name('user.')
    ->group(function (): void {
        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        
        // Profile
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserDashboardController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        
        // Services
        Route::get('/services', [UserServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [UserServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [UserServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{id}', [UserServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{id}/edit', [UserServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{id}', [UserServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{id}', [UserServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/my-services', [UserServiceController::class, 'myServices'])->name('services.my');
        Route::post('/services/{id}/remove-image', [UserServiceController::class, 'removeImage'])->name('services.remove-image');
        
        // Jobs
        Route::get('/jobs', [UserJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{id}', [UserJobController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{job}/apply', [UserJobController::class, 'apply'])->name('jobs.apply');
        Route::get('/my-applications', [UserJobController::class, 'myApplications'])->name('applications.index');
        Route::delete('/applications/{applicationId}', [UserJobController::class, 'withdrawApplication'])->name('applications.withdraw');
        
        // Missions
        Route::get('/missions', [UserDashboardController::class, 'missions'])->name('missions.index');
        Route::get('/missions/{id}', [UserDashboardController::class, 'missionDetail'])->name('missions.show');
        Route::put('/missions/{id}/status', [UserDashboardController::class, 'updateMissionStatus'])->name('missions.update-status');
        
        // Notifications
        Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications.index');
        Route::post('/notifications/{notificationId}/read', [UserDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');

        // Messages (Premium Demo)
        Route::get('/messages', function() {
            return view('user.messages.index');
        })->name('messages.index');

        // Placeholder Routes for Premium UX
        Route::get('/favorites', [UserDashboardController::class, 'favorites'])->name('favorites');
        Route::get('/new', [UserDashboardController::class, 'newOpportunities'])->name('new');
        Route::get('/security', [UserDashboardController::class, 'security'])->name('security');
        Route::get('/help', [UserDashboardController::class, 'help'])->name('help');
        Route::get('/report', [UserDashboardController::class, 'report'])->name('report');

        // Service Requests (Custom service requests from users)
        Route::get('/service-requests', [UserServiceRequestController::class, 'index'])->name('service-requests.index');
        Route::get('/service-requests/{serviceRequest}', [UserServiceRequestController::class, 'show'])->name('service-requests.show');
        Route::post('/service-requests', [UserServiceRequestController::class, 'store'])->name('service-requests.store');
    });

// Admin Routes
Route::middleware(['auth', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/api', [AdminUserController::class, 'apiIndex'])->name('api.index');
            Route::post('/{id}/promote', [AdminUserController::class, 'promoteToAdmin'])->name('promote');
            Route::post('/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{id}/toggle-status-api', [AdminUserController::class, 'toggleStatusApi'])->name('toggle-status-api');
            Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
        });

        // Administrative Resources
        Route::resource('services', AdminServiceController::class);
        Route::resource('jobs', AdminJobController::class);
        Route::resource('categories', AdminCategoryController::class);

        // Job Applications Management
        Route::prefix('job-applications')->name('job-applications.')->group(function (): void {
            Route::get('/', [AdminJobApplicationController::class, 'index'])->name('index');
            Route::patch('/{id}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('status');
        });

        Route::get('/profile', function() { return view('admin.placeholder'); })->name('profile');
        
        // Service Requests Management
        Route::prefix('service-requests')->name('service-requests.')->group(function (): void {
            Route::get('/', [UserServiceRequestController::class, 'adminIndex'])->name('index');
            Route::get('/{serviceRequest}', [UserServiceRequestController::class, 'show'])->name('show');
            Route::patch('/{serviceRequest}', [UserServiceRequestController::class, 'adminRespond'])->name('respond');
        });

        Route::get('/account', function() { return view('admin.placeholder'); })->name('account');

        // Administrative Placeholders for Premium UI
        Route::get('/stats-realtime', [AdminDashboardController::class, 'stats'])->name('stats');
        Route::get('/api/logs', [AdminDashboardController::class, 'getLogs'])->name('api.logs');
        Route::post('/api/logs/export', [AdminReportController::class, 'exportLogs'])->name('api.logs.export');
        Route::get('/api/alerts/unread-count', [AdminDashboardController::class, 'getUnreadAlertCount'])->name('api.alerts.unread-count');
        Route::get('/alerts', [AdminDashboardController::class, 'alerts'])->name('alerts');
        Route::post('/alerts/{alert}/resolve', [AdminDashboardController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::post('/alerts/mark-all-read', [AdminDashboardController::class, 'markAllAlertsRead'])->name('alerts.mark-all-read');
        
        Route::prefix('users-mgmt')->name('users-mgmt.')->group(function() {
            Route::get('/counts', [AdminUserController::class, 'getCountsApi'])->name('counts');
            Route::get('/pending', [AdminUserController::class, 'pending'])->name('pending');
            Route::post('/pending/{id}/approve-api', [AdminUserController::class, 'approveApi'])->name('pending.approve-api');
            Route::post('/pending/{id}/reject-api', [AdminUserController::class, 'rejectApi'])->name('pending.reject-api');
            Route::get('/flagged', [AdminUserController::class, 'flagged'])->name('flagged');
            Route::get('/documents', [AdminUserController::class, 'documents'])->name('docs');
            Route::post('/documents/{id}/verify', [AdminUserController::class, 'verifyDocument'])->name('docs.verify');
            Route::post('/documents/{id}/reject', [AdminUserController::class, 'rejectDocument'])->name('docs.reject');
        });

        Route::prefix('moderation')->name('moderation.')->group(function() {
            Route::get('/services', [AdminServiceController::class, 'index'])->name('services');
            Route::get('/reviews', [ModerationController::class, 'reviews'])->name('reviews');
        });

        Route::prefix('finances')->name('finances.')->group(function() {
            Route::get('/transactions', [AdminFinancialController::class, 'transactions'])->name('transactions');
            Route::get('/transactions/export', [AdminFinancialController::class, 'exportTransactions'])->name('transactions.export');
            
            Route::get('/commissions', [AdminFinancialController::class, 'commissions'])->name('commissions');
            Route::post('/commissions', [AdminFinancialController::class, 'updateCommission'])->name('commissions.update');
            
            Route::get('/invoicing', [AdminFinancialController::class, 'invoicing'])->name('invoicing');
            Route::get('/invoicing/export', [AdminFinancialController::class, 'exportInvoices'])->name('invoicing.export');
        });

        // Communication Logic
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/news', [AdminContentController::class, 'news'])->name('news');
        Route::post('/news', [AdminContentController::class, 'newsStore'])->name('news.store');
        Route::delete('/news/{article}', [AdminContentController::class, 'newsDelete'])->name('news.delete');

        Route::get('/newsletter', [AdminContentController::class, 'newsletter'])->name('newsletter');
        Route::post('/newsletter', [AdminContentController::class, 'newsletterStore'])->name('newsletter.store');
        Route::post('/newsletter/{campaign}/duplicate', [AdminContentController::class, 'newsletterDuplicate'])->name('newsletter.duplicate');
        Route::delete('/newsletter/{campaign}', [AdminContentController::class, 'newsletterDelete'])->name('newsletter.delete');

        Route::get('/push', [AdminContentController::class, 'push'])->name('push');
        Route::post('/push/broadcast', [AdminContentController::class, 'pushBroadcast'])->name('push.broadcast');
    });

        Route::prefix('settings-hq')->name('settings-hq.')->group(function() {
            Route::get('/geo', [AdminSettingController::class, 'geo'])->name('geo');
            Route::get('/api', [AdminSettingController::class, 'api'])->name('api');
        });

        Route::prefix('reports-hq')->name('reports-hq.')->group(function() {
            Route::get('/analytics', [AdminReportController::class, 'analytics'])->name('analytics');
            Route::get('/financial', [AdminReportController::class, 'financial'])->name('financial');
            Route::get('/export', [AdminReportController::class, 'export'])->name('export');
            Route::get('/export/users', [AdminReportController::class, 'exportUsers'])->name('export.users');
            Route::get('/export/services', [AdminReportController::class, 'exportServices'])->name('export.services');
        });

        Route::prefix('tools')->name('tools.')->group(function() {
            Route::get('/maintenance', [AdminSettingController::class, 'maintenance'])->name('maintenance');
            Route::post('/maintenance/toggle', [AdminSettingController::class, 'toggleMaintenance'])->name('maintenance.toggle');
            Route::get('/cache', [AdminSettingController::class, 'cache'])->name('cache');
            Route::post('/cache/clear', [AdminSettingController::class, 'clearCache'])->name('cache.clear');
            Route::get('/logs', [AdminSettingController::class, 'logs'])->name('logs');
            Route::post('/logs/clear', [AdminSettingController::class, 'clearLogs'])->name('logs.clear');
        });

        Route::prefix('support-hq')->name('support-hq.')->group(function() {
            Route::get('/tickets', [AdminSupportController::class, 'tickets'])->name('tickets');
            Route::post('/tickets/{id}/reply', [AdminSupportController::class, 'replyTicket'])->name('tickets.reply');
            Route::post('/tickets/{id}/close', [AdminSupportController::class, 'closeTicket'])->name('tickets.close');
            Route::get('/docs', [AdminSupportController::class, 'docs'])->name('docs');
            Route::get('/suggestions', [AdminSupportController::class, 'suggestions'])->name('suggestions');
            Route::post('/suggestions/{id}/toggle', [AdminSupportController::class, 'toggleSuggestionStatus'])->name('suggestions.toggle');
        });

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [AdminReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/{report}/download', [AdminReportController::class, 'download'])->name('reports.download');
        Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function (): void {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Admin Hierarchy
        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [SuperAdminUserController::class, 'index'])->name('index');
            Route::get('/create', [SuperAdminUserController::class, 'create'])->name('create');
            Route::get('/{id}', [SuperAdminUserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SuperAdminUserController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [SuperAdminDashboardController::class, 'deleteUser'])->name('destroy');
            Route::post('/{id}/promote', [SuperAdminDashboardController::class, 'promoteUser'])->name('promote');
        });

        // SYSTEM CORE
        Route::prefix('system')->name('system.')->group(function() {
            Route::get('/console', function() { return view('super-admin.system.console'); })->name('console');
            Route::get('/env', function() { return view('super-admin.system.env'); })->name('env');
            Route::get('/files', function() { return view('super-admin.placeholder'); })->name('files');
            Route::get('/debug', function() { return view('super-admin.placeholder'); })->name('debug');
        });

        // BDD MASTER
        Route::prefix('database')->name('database.')->group(function() {
            Route::get('/phpmyadmin', function() { return view('super-admin.placeholder'); })->name('phpmyadmin');
            Route::get('/tables', function() { return view('super-admin.database.tables'); })->name('tables');
            Route::get('/migrations', function() { return view('super-admin.placeholder'); })->name('migrations');
            Route::get('/backup', function() { return view('super-admin.placeholder'); })->name('backup');
        });

        // SECURE
        Route::prefix('security')->name('security.')->group(function() {
            Route::get('/firewall', function() { return view('super-admin.security.firewall'); })->name('firewall');
            Route::get('/logs', function() { return view('super-admin.placeholder'); })->name('logs');
            Route::get('/hashing', function() { return view('super-admin.placeholder'); })->name('hashing');
            Route::get('/lockdown', function() { return view('super-admin.placeholder'); })->name('lockdown');
        });

        // MULTI-INSTANCE
        Route::prefix('instances')->name('instances.')->group(function() {
            Route::get('/all', function() { return view('super-admin.instances.all'); })->name('all');
            Route::get('/sync', function() { return view('super-admin.placeholder'); })->name('sync');
            Route::get('/stats', function() { return view('super-admin.placeholder'); })->name('stats');
        });

        // VISION DIVINE
        Route::prefix('divine')->name('divine.')->group(function() {
            Route::get('/messages', function() { return view('super-admin.placeholder'); })->name('messages');
            Route::get('/files', function() { return view('super-admin.placeholder'); })->name('files');
            Route::get('/tracking', function() { return view('super-admin.divine.tracking'); })->name('tracking');
            Route::get('/impersonate', function() { return view('super-admin.divine.impersonate'); })->name('impersonate');
            Route::get('/powers', function() { return view('super-admin.divine.powers'); })->name('powers');
        });

        // Placeholder for legacy calls (if any)
        Route::get('/services', [SuperAdminSystemController::class, 'services'])->name('services');
        Route::get('/jobs', [SuperAdminSystemController::class, 'jobs'])->name('jobs');
        Route::get('/reports', [SuperAdminSystemController::class, 'reports'])->name('reports');
        Route::get('/settings', [SuperAdminSystemController::class, 'settings'])->name('settings');
        Route::get('/logs', [SuperAdminSystemController::class, 'logs'])->name('logs');
    });
