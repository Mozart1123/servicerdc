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
use App\Http\Middleware\PreventClientDashboardAccess;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\MissionController as AdminMissionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\User\ServiceController as UserServiceController;
use App\Http\Controllers\User\JobController as UserJobController;
use App\Http\Controllers\User\ServiceRequestController as UserServiceRequestController;
use App\Http\Controllers\User\SubscriptionController as UserSubscriptionController;
use App\Http\Controllers\User\CvController as UserCvController;
use App\Http\Controllers\User\MessageController as UserMessageController;
use App\Http\Controllers\User\PhotoController as UserPhotoController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\User\RecruiterProfileController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SystemController as SuperAdminSystemController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\FinancialController as AdminFinancialController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\SuperAdmin\ApiKeyController;
use App\Http\Controllers\SuperAdmin\SystemHealthController;
use App\Http\Controllers\SuperAdmin\BillingController;
use App\Http\Controllers\SuperAdmin\ActivityLogController;
use App\Http\Controllers\SuperAdmin\OrganizationController as SuperAdminOrganizationController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\ServiceController as SuperAdminServiceController;
use App\Http\Controllers\SuperAdmin\SettingController as SuperAdminSettingController;

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

// Public Catalog Routes
Route::get('/public/services', [PublicController::class, 'services'])->name('public.services.index');
Route::get('/public/services/{id}', [PublicController::class, 'serviceShow'])->name('public.services.show');
Route::get('/public/jobs', [PublicController::class, 'jobs'])->name('public.jobs.index');
Route::get('/public/jobs/{id}', [PublicController::class, 'jobShow'])->name('public.jobs.show');
Route::get('/public/jobs/{id}/apply', [PublicController::class, 'jobApplyRedirect'])->name('public.jobs.apply');
Route::get('/public/artisans', [PublicController::class, 'artisans'])->name('public.artisans.index');
Route::get('/public/artisans/{id}', [PublicController::class, 'artisanShow'])->name('public.artisans.show');

// Newsletter subscription from the landing page
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// Static public legal pages
Route::view('/privacy', 'public.static.privacy')->name('privacy');
Route::view('/terms', 'public.static.terms')->name('terms');
Route::view('/legal', 'public.static.legal')->name('legal');
Route::view('/sitemap', 'public.static.sitemap')->name('sitemap');

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
        Route::middleware([PreventClientDashboardAccess::class])->group(function (): void {
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

            // Services Management (Artisan)
            Route::get('/services/create', [UserServiceController::class, 'create'])->name('services.create');
            Route::post('/services', [UserServiceController::class, 'store'])->name('services.store');
            Route::get('/services/{id}/edit', [UserServiceController::class, 'edit'])->name('services.edit');
            Route::put('/services/{id}', [UserServiceController::class, 'update'])->name('services.update');
            Route::delete('/services/{id}', [UserServiceController::class, 'destroy'])->name('services.destroy');
            Route::get('/my-services', [UserServiceController::class, 'myServices'])->name('services.my');
            Route::post('/services/{id}/remove-image', [UserServiceController::class, 'removeImage'])->name('services.remove-image');

            // Missions
            Route::get('/missions', [UserDashboardController::class, 'missions'])->name('missions.index');
            Route::get('/missions/{id}', [UserDashboardController::class, 'missionDetail'])->name('missions.show');
            Route::put('/missions/{id}/status', [UserDashboardController::class, 'updateMissionStatus'])->name('missions.update-status');
            
            // Artisan – incoming requests & reviews
            Route::post('/service-requests/{serviceRequest}/accept', [UserServiceRequestController::class, 'accept'])->name('service-requests.accept');
            Route::post('/service-requests/{serviceRequest}/reject', [UserServiceRequestController::class, 'reject'])->name('service-requests.reject');
            Route::post('/service-requests/{serviceRequest}/complete', [UserServiceRequestController::class, 'complete'])->name('service-requests.complete');
            Route::post('/service-requests/{serviceRequest}/start', [UserServiceRequestController::class, 'startMission'])->name('service-requests.start');
            Route::post('/service-requests/{serviceRequest}/pay-cash', [UserServiceRequestController::class, 'payCash'])->name('service-requests.pay-cash');
            Route::get('/artisan/service-requests', [UserServiceRequestController::class, 'artisanRequests'])->name('artisan.service-requests.index');
            Route::get('/artisan/reviews', [UserServiceRequestController::class, 'artisanReviews'])->name('artisan.reviews.index');
            Route::get('/artisan/level', [UserDashboardController::class, 'level'])->name('artisan.level');
        });

        // Account Main Menu (Mobile)
        Route::get('/account', function () {
            return view('user.account');
        })->name('account');

        // Profile
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserDashboardController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');

        // Service Catalog (Logged-in view, though we will add public routes too)
        Route::get('/services', [UserServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{id}', [UserServiceController::class, 'show'])->name('services.show');

        // Jobs Catalog
        Route::get('/jobs', [UserJobController::class, 'index'])->name('jobs.index');
        Route::get('/job-offers/{id}/apply', [UserJobController::class, 'showApplyForm'])->name('jobs.apply.form');
        Route::get('/jobs/{id}', [UserJobController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{job}/apply', [UserJobController::class, 'apply'])->name('jobs.apply');
        
        // Client: My Applications & Service Requests
        Route::get('/my-applications', [UserJobController::class, 'myApplications'])->name('applications.index');
        Route::delete('/applications/{applicationId}', [UserJobController::class, 'withdrawApplication'])->name('applications.withdraw');
        Route::get('/service-requests', [UserServiceRequestController::class, 'index'])->name('service-requests.index');
        Route::get('/service-requests/{serviceRequest}', [UserServiceRequestController::class, 'show'])->name('service-requests.show');
        Route::post('/service-requests', [UserServiceRequestController::class, 'store'])->name('service-requests.store');
        Route::post('/service-requests/{serviceRequest}/cancel', [UserServiceRequestController::class, 'cancel'])->name('service-requests.cancel');
        Route::post('/service-requests/{serviceRequest}/rate', [UserServiceRequestController::class, 'rate'])->name('service-requests.rate');
        Route::get('/reviews', [UserDashboardController::class, 'myReviews'])->name('reviews.index');
        // Service Requests
        // (Handled above, client-facing vs artisan-facing separated)
        
        // CV Management (Job seeker/client)
        Route::get('/cv', [\App\Http\Controllers\User\CvController::class, 'index'])->name('cv.index');
        Route::get('/cv/create', [\App\Http\Controllers\User\CvController::class, 'create'])->name('cv.create');
        Route::post('/cv', [\App\Http\Controllers\User\CvController::class, 'store'])->name('cv.store');
        Route::put('/cv', [\App\Http\Controllers\User\CvController::class, 'update'])->name('cv.update');
        Route::delete('/cv', [\App\Http\Controllers\User\CvController::class, 'destroy'])->name('cv.destroy');
        Route::post('/cv/file-upload', [\App\Http\Controllers\User\CvController::class, 'fileUpload'])->name('cv.file.upload');

        Route::middleware([PreventClientDashboardAccess::class])->group(function (): void {
            // Jobs — recruiter publishes offers
            Route::get('/my-job-offers', [UserJobController::class, 'myJobOffers'])->name('jobs.my-offers');
            Route::get('/job-offers/create', [UserJobController::class, 'create'])->name('jobs.create');
            Route::post('/job-offers', [UserJobController::class, 'storeOffer'])->name('jobs.store');
            Route::get('/job-offers/{id}/edit', [UserJobController::class, 'editOffer'])->name('jobs.edit');
            Route::put('/job-offers/{id}', [UserJobController::class, 'updateOffer'])->name('jobs.update');
            Route::delete('/job-offers/{id}', [UserJobController::class, 'destroyOffer'])->name('jobs.destroy');
            // Recruiter sees & manages received applications
            Route::get('/received-applications', [UserJobController::class, 'receivedApplications'])->name('applications.received');
            Route::get('/received-applications/{id}/details', [UserJobController::class, 'applicationDetails'])->name('applications.details');
            Route::post('/applications/{application}/approve', [UserJobController::class, 'approveApplication'])->name('applications.approve');
            Route::post('/applications/{application}/reject', [UserJobController::class, 'rejectApplication'])->name('applications.reject');
            Route::post('/applications/{application}/interview', [UserJobController::class, 'interviewApplication'])->name('applications.interview');
            Route::post('/applications/{application}/hire', [UserJobController::class, 'hireApplication'])->name('applications.hire');
        });

        // Artisan public profile (from within app)
        Route::get('/artisans/{artisan}', [UserServiceController::class, 'artisanProfile'])->name('artisans.show');

        // Recruiter public profile
        Route::get('/recruiters/{id}', [RecruiterProfileController::class, 'show'])->name('recruiters.show');

        // Notifications
        Route::get('/notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [UserNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [UserNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notifications/{notification}', [UserNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/notifications/unread-count', [UserNotificationController::class, 'unreadCount'])->name('notifications.unread-count');

        // Messages
        Route::get('/messages', [UserMessageController::class, 'index'])->name('messages.index');
        Route::post('/messages', [UserMessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/start', [UserMessageController::class, 'start'])->name('messages.start');
        Route::get('/messages/start/{user}', [UserMessageController::class, 'startConversation'])->name('messages.start.user');
        Route::post('/messages/{conversation}/send', [UserMessageController::class, 'send'])->name('messages.send');
        Route::post('/messages/{conversation}/read', [UserMessageController::class, 'markRead'])->name('messages.read');
        Route::post('/messages/{conversation}/attachment', [UserMessageController::class, 'send'])->name('messages.attachment');

        // Photo uploads
        Route::post('/profile/photo', [UserPhotoController::class, 'updateProfilePhoto'])->name('profile.photo'); // updated to spec
        Route::post('/services/{service}/image', [UserPhotoController::class, 'updateServiceImage'])->name('services.image');
        Route::post('/services/{service}/gallery', [UserPhotoController::class, 'updateServiceGallery'])->name('services.gallery');
        Route::delete('/services/{service}/gallery/{index}', [UserPhotoController::class, 'deleteGalleryImage'])->name('services.gallery.delete');
        Route::post('/jobs/{job}/logo', [UserPhotoController::class, 'updateJobLogo'])->name('jobs.logo');
        Route::post('/jobs/{job}/cover', [UserPhotoController::class, 'updateJobCover'])->name('jobs.cover');
        Route::post('/cv/photo', [UserPhotoController::class, 'updateCvPhoto'])->name('cv.photo');
        Route::post('/cv/file', [UserPhotoController::class, 'uploadCvFile'])->name('cv.file');

        // Subscriptions
        Route::get('/subscription', [UserSubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/checkout', [UserSubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::post('/subscription/subscribe', [UserSubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
        Route::post('/subscription/cancel', [UserSubscriptionController::class, 'cancel'])->name('subscription.cancel');

        // Artisan — Gains & Retraits
        Route::get('/gains', [\App\Http\Controllers\User\PayoutController::class, 'index'])->name('gains.index');
        Route::post('/gains/request', [\App\Http\Controllers\User\PayoutController::class, 'store'])->name('gains.request');

        // Placeholder Routes for Premium UX
        Route::get('/favorites', [UserDashboardController::class, 'favorites'])->name('favorites');
        Route::get('/new', [UserDashboardController::class, 'newOpportunities'])->name('new');
        Route::get('/security', [UserDashboardController::class, 'security'])->name('security');
        Route::get('/help', [UserDashboardController::class, 'help'])->name('help');
        Route::get('/report', [UserDashboardController::class, 'report'])->name('report');
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

        // Messages Supervision
        Route::prefix('messages')->name('messages.')->group(function (): void {
            Route::get('/', [AdminMessageController::class, 'index'])->name('index');
            Route::get('/{conversation}', [AdminMessageController::class, 'show'])->name('show');
            Route::post('/{conversation}/flag', [AdminMessageController::class, 'flag'])->name('flag');
            Route::post('/{conversation}/note', [AdminMessageController::class, 'addNote'])->name('note');
        });

        // Job Applications Management
        Route::prefix('job-applications')->name('job-applications.')->group(function (): void {
            Route::get('/', [AdminJobApplicationController::class, 'index'])->name('index');
            Route::patch('/{id}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('status');
        });

        Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
        // Service Requests Management
        Route::prefix('service-requests')->name('service-requests.')->group(function (): void {
            Route::get('/', [UserServiceRequestController::class, 'adminIndex'])->name('index');
            Route::get('/{serviceRequest}', [UserServiceRequestController::class, 'show'])->name('show');
            Route::patch('/{serviceRequest}', [UserServiceRequestController::class, 'adminRespond'])->name('respond');
        });

        Route::get('/account', function () {
            return view('admin.placeholder');
        })->name('account');

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

        Route::prefix('moderation')->name('moderation.')->group(function () {
            Route::get('/services', [AdminServiceController::class, 'index'])->name('services');
            Route::get('/reviews', [ModerationController::class, 'reviews'])->name('reviews');
            Route::post('/reviews/{id}/approve', [ModerationController::class, 'approveReview'])->name('reviews.approve');
            Route::post('/reviews/{id}/reject', [ModerationController::class, 'rejectReview'])->name('reviews.reject');
        });

        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/dashboard', [AdminFinancialController::class, 'dashboard'])->name('dashboard');
            
            Route::get('/withdrawals', [AdminFinancialController::class, 'withdrawals'])->name('withdrawals');
            Route::get('/withdraw', [AdminFinancialController::class, 'withdraw'])->name('withdraw');
            Route::post('/withdraw', [AdminFinancialController::class, 'processWithdrawal'])->name('withdraw.process');

            Route::get('/transactions', [AdminFinancialController::class, 'transactions'])->name('transactions');
            Route::get('/transactions/export', [AdminFinancialController::class, 'exportTransactions'])->name('transactions.export');
            
            Route::get('/commissions', [AdminFinancialController::class, 'commissions'])->name('commissions');
            Route::post('/commissions', [AdminFinancialController::class, 'updateCommission'])->name('commissions.update');
            
            Route::get('/debug-kpay', function () {
                $service = app(\App\Services\KpayService::class);
                try {
                    $res = \Illuminate\Support\Facades\Http::withHeaders([
                        'X-API-Key' => env('KPAY_API_KEY'),
                        'X-Secret-Key' => env('KPAY_SECRET_KEY'),
                        'Accept' => 'application/json'
                    ])->get('https://admin.kpay.site/api/v1/payments/balance');
                    
                    return [
                        'status' => $res->status(),
                        'body' => $res->body(),
                        'json' => $res->json(),
                        'api_key' => substr(env('KPAY_API_KEY'), 0, 15) . '...',
                    ];
                } catch (\Exception $e) {
                    return ['error' => $e->getMessage()];
                }
            });

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

        Route::prefix('settings-hq')->name('settings-hq.')->group(function () {
            Route::get('/geo', [AdminSettingController::class, 'geo'])->name('geo');
            Route::get('/api', [AdminSettingController::class, 'api'])->name('api');
        });

        Route::prefix('reports-hq')->name('reports-hq.')->group(function () {
            Route::get('/analytics', [AdminReportController::class, 'analytics'])->name('analytics');
            Route::get('/financial', [AdminReportController::class, 'financial'])->name('financial');
            Route::get('/export', [AdminReportController::class, 'export'])->name('export');
            Route::get('/export/users', [AdminReportController::class, 'exportUsers'])->name('export.users');
            Route::get('/export/services', [AdminReportController::class, 'exportServices'])->name('export.services');
        });

        Route::prefix('tools')->name('tools.')->group(function () {
            Route::get('/maintenance', [AdminSettingController::class, 'maintenance'])->name('maintenance');
            Route::post('/maintenance/toggle', [AdminSettingController::class, 'toggleMaintenance'])->name('maintenance.toggle');
            Route::get('/cache', [AdminSettingController::class, 'cache'])->name('cache');
            Route::post('/cache/clear', [AdminSettingController::class, 'clearCache'])->name('cache.clear');
            Route::get('/logs', [AdminSettingController::class, 'logs'])->name('logs');
            Route::post('/logs/clear', [AdminSettingController::class, 'clearLogs'])->name('logs.clear');
        });

        Route::prefix('support-hq')->name('support-hq.')->group(function () {
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

        // Admin Missions & Reviews
        Route::prefix('missions')->name('missions.')->group(function (): void {
            Route::get('/', [AdminMissionController::class, 'index'])->name('index');
            Route::get('/{mission}', [AdminMissionController::class, 'show'])->name('show');
        });

        // Payout Requests (demandes de retrait artisans)
        Route::prefix('finances/payout-requests')->name('finances.payout-requests.')->group(function (): void {
            Route::get('/', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'reject'])->name('reject');
            Route::get('/{artisanId}/missions', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'artisanMissions'])->name('missions');
        });
    });

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function (): void {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/roles', [SuperAdminUserController::class, 'roles'])->name('roles');
        Route::post('/roles/{id}/update', [SuperAdminUserController::class, 'updateRole'])->name('roles.update');
        Route::get('/sessions', [SuperAdminUserController::class, 'sessions'])->name('sessions');

        // Admin Hierarchy
        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [SuperAdminUserController::class, 'index'])->name('index');
            Route::get('/create', [SuperAdminUserController::class, 'create'])->name('create');
            Route::get('/{id}', [SuperAdminUserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SuperAdminUserController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [SuperAdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/promote', [SuperAdminDashboardController::class, 'promoteUser'])->name('promote');
            Route::post('/{id}/toggle-status', [SuperAdminUserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Organizations
        Route::prefix('organizations')->name('organizations.')->group(function (): void {
            Route::get('/', [SuperAdminOrganizationController::class, 'index'])->name('index');
            Route::get('/create', [SuperAdminOrganizationController::class, 'create'])->name('create');
            Route::post('/', [SuperAdminOrganizationController::class, 'store'])->name('store');
            Route::get('/{organization}', [SuperAdminOrganizationController::class, 'show'])->name('show');
            // Using ID instead of model binding for simplicity in my CRUD logic if needed, but Controller uses Organization typehint
            Route::get('/{organization}/edit', [SuperAdminOrganizationController::class, 'edit'])->name('edit');
            Route::put('/{organization}', [SuperAdminOrganizationController::class, 'update'])->name('update');
            Route::delete('/{organization}', [SuperAdminOrganizationController::class, 'destroy'])->name('destroy');
            Route::post('/{organization}/toggle-status', [SuperAdminOrganizationController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Service Moderation
        Route::prefix('services')->name('services.')->group(function (): void {
            Route::get('/', [SuperAdminServiceController::class, 'index'])->name('index');
            Route::post('/{service}/toggle-verification', [SuperAdminServiceController::class, 'toggleVerification'])->name('toggle-verification');
            Route::post('/{service}/toggle-status', [SuperAdminServiceController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{service}', [SuperAdminServiceController::class, 'destroy'])->name('destroy');
        });

        // Plans & Features
        Route::prefix('plans')->name('plans.')->group(function (): void {
            Route::get('/', [SuperAdminPlanController::class, 'index'])->name('index');
            Route::get('/create', [SuperAdminPlanController::class, 'create'])->name('create');
            Route::post('/', [SuperAdminPlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [SuperAdminPlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}', [SuperAdminPlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [SuperAdminPlanController::class, 'destroy'])->name('destroy');
            Route::post('/{plan}/toggle-status', [SuperAdminPlanController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Billing & Finance
        Route::prefix('billing')->name('billing.')->group(function (): void {
            Route::get('/', [BillingController::class, 'index'])->name('index');
            Route::get('/transactions', [BillingController::class, 'transactions'])->name('transactions');
            Route::get('/payouts', [BillingController::class, 'payouts'])->name('payouts');
        });

        // SYSTEM CORE
    
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/settings', [SuperAdminSettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SuperAdminSettingController::class, 'update'])->name('settings.update');

            Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
            Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
            Route::post('/api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke'])->name('api-keys.revoke');

            Route::get('/health', [SystemHealthController::class, 'index'])->name('health');

            Route::get('/console', function () {
                return view('super-admin.system.console');
            })->name('console');
            Route::get('/debug-env', function () {
    return [
        'api_key' => env('KPAY_API_KEY'),
        'secret_key' => env('KPAY_SECRET_KEY'),
    ];
});

Route::get('/', function () {
                return view('super-admin.system.env');
            })->name('env');
            Route::get('/files', function () {
                return view('super-admin.placeholder');
            })->name('files');
            Route::get('/debug', function () {
                return view('super-admin.placeholder');
            })->name('debug');
        });

        // BDD MASTER
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/phpmyadmin', function () {
                return view('super-admin.placeholder');
            })->name('phpmyadmin');
            Route::get('/tables', function () {
                return view('super-admin.database.tables');
            })->name('tables');
            Route::get('/migrations', function () {
                return view('super-admin.placeholder');
            })->name('migrations');
            Route::get('/backup', function () {
                return view('super-admin.placeholder');
            })->name('backup');
        });

        // SECURE
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/firewall', function () {
                return view('super-admin.security.firewall');
            })->name('firewall');
            Route::get('/logs', function () {
                return view('super-admin.placeholder');
            })->name('logs');
            Route::get('/hashing', function () {
                return view('super-admin.placeholder');
            })->name('hashing');
            Route::get('/lockdown', function () {
                return view('super-admin.placeholder');
            })->name('lockdown');
        });

        // MULTI-INSTANCE
        Route::prefix('instances')->name('instances.')->group(function () {
            Route::get('/all', function () {
                return view('super-admin.instances.all');
            })->name('all');
            Route::get('/sync', function () {
                return view('super-admin.placeholder');
            })->name('sync');
            Route::get('/stats', function () {
                return view('super-admin.placeholder');
            })->name('stats');
        });

        // VISION DIVINE
        Route::prefix('divine')->name('divine.')->group(function () {
            Route::get('/messages', function () {
                return view('super-admin.placeholder');
            })->name('messages');
            Route::get('/files', function () {
                return view('super-admin.placeholder');
            })->name('files');
            Route::get('/tracking', function () {
                return view('super-admin.divine.tracking');
            })->name('tracking');
            Route::get('/impersonate', function () {
                return view('super-admin.divine.impersonate');
            })->name('impersonate');
            Route::get('/powers', function () {
                return view('super-admin.divine.powers');
            })->name('powers');
        });

        // Placeholder for legacy calls (if any)
        Route::get('/jobs', [SuperAdminSystemController::class, 'jobs'])->name('jobs');
        Route::get('/reports', [SuperAdminSystemController::class, 'reports'])->name('reports');
        Route::get('/settings', [SuperAdminSystemController::class, 'settings'])->name('settings');
        Route::get('/audit-trail', [ActivityLogController::class, 'index'])->name('logs');
        Route::post('/audit-trail/clear', [ActivityLogController::class, 'clear'])->name('logs.clear');
    });
