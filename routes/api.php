<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ProConnect Platform
|--------------------------------------------------------------------------
| All routes are protected with Sanctum auth.
| Role/type checking uses the api.role middleware (checks user_type field).
|
| Roles:   artisan | client | recruiter
*/

// ─── Artisan Routes ────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'api.role:artisan'])
    ->prefix('artisan')
    ->name('api.artisan.')
    ->group(function () {

        // Services
        Route::get('/services',  [\App\Http\Controllers\Artisan\ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [\App\Http\Controllers\Artisan\ServiceController::class, 'store'])->name('services.store');

        // Service Requests
        Route::get('/service-requests',               [\App\Http\Controllers\Artisan\ServiceController::class, 'requests'])->name('requests.index');
        Route::post('/service-requests/{id}/accept',  [\App\Http\Controllers\Artisan\ServiceController::class, 'acceptRequest'])->name('requests.accept');
        Route::post('/service-requests/{id}/reject',  [\App\Http\Controllers\Artisan\ServiceController::class, 'rejectRequest'])->name('requests.reject');
    });

// ─── Client Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'api.role:client'])
    ->prefix('client')
    ->name('api.client.')
    ->group(function () {

        // Browse & request services
        Route::get('/services',              [\App\Http\Controllers\Client\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{id}',         [\App\Http\Controllers\Client\ServiceController::class, 'show'])->name('services.show');
        Route::post('/services/{id}/request',[\App\Http\Controllers\Client\ServiceController::class, 'storeRequest'])->name('services.request');

        // Browse & apply for jobs
        Route::get('/jobs',              [\App\Http\Controllers\Client\JobOfferController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{id}',         [\App\Http\Controllers\Client\JobOfferController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{id}/apply',  [\App\Http\Controllers\Client\JobOfferController::class, 'apply'])->name('jobs.apply');
        Route::get('/cv-check',          [\App\Http\Controllers\Client\JobOfferController::class, 'checkCv'])->name('cv.check');

        // CV management
        Route::get('/cv',  [\App\Http\Controllers\Client\CvController::class, 'show'])->name('cv.show');
        Route::post('/cv', [\App\Http\Controllers\Client\CvController::class, 'store'])->name('cv.store');
    });

// ─── Recruiter Routes ──────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'api.role:recruiter'])
    ->prefix('recruiter')
    ->name('api.recruiter.')
    ->group(function () {

        // Job Offers
        Route::get('/jobs',  [\App\Http\Controllers\Recruiter\JobOfferController::class, 'index'])->name('jobs.index');
        Route::post('/jobs', [\App\Http\Controllers\Recruiter\JobOfferController::class, 'store'])->name('jobs.store');

        // Applications
        Route::get('/applications',              [\App\Http\Controllers\Recruiter\JobOfferController::class, 'applications'])->name('applications.index');
        Route::get('/applications/{id}',         [\App\Http\Controllers\Recruiter\JobOfferController::class, 'showApplication'])->name('applications.show');
        Route::post('/applications/{id}/approve',[\App\Http\Controllers\Recruiter\JobOfferController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [\App\Http\Controllers\Recruiter\JobOfferController::class, 'rejectApplication'])->name('applications.reject');
    });

// ─── Notifications (all authenticated users) ───────────────────────────────
Route::middleware(['auth:sanctum'])
    ->prefix('notifications')
    ->name('api.notifications.')
    ->group(function () {
        Route::get('/',             [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read',   [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all',    [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

// ─── Authenticated user info ────────────────────────────────────────────────
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'data' => array_merge($request->user()->toArray(), [
            'has_cv'      => (bool) $request->user()->cv,
            'unread_notifications' => \App\Models\Notification::where('user_id', $request->user()->id)->unread()->count(),
        ]),
    ]);
});
