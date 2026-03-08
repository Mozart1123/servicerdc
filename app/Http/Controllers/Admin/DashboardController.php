<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Service;
use App\Models\JobOffer;

/**
 * Admin Dashboard Controller
 * 
 * Handles admin-specific functionality.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Premium Dashboard Data
        $stats = [
            'total_users' => User::count(),
            'active_services' => Service::active()->count(),
            'total_jobs' => JobOffer::count(),
            'total_applications' => \App\Models\JobApplication::count(),
            'monthly_revenue' => 12450.75, 
            'revenue_growth' => 12.5,
            'user_growth' => 8.2,
        ];

        $users = User::latest()->take(10)->get();
        $recentJobs = JobOffer::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'users', 'recentJobs'));
    }

    /**
     * Display real-time statistics.
     */
    public function stats(): View
    {
        return view('admin.stats');
    }

    /**
     * Display system alerts.
     */
    public function alerts(): View
    {
        return view('admin.alerts');
    }
}
