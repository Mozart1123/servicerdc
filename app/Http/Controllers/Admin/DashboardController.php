<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Service;
use App\Models\JobOffer;
use App\Models\SystemLog;
use App\Models\SystemAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        \App\Services\SystemActivityService::log('SERV', 'Consultation des statistiques live', 'info');
        return view('admin.stats');
    }

    /**
     * Display system alerts.
     */
    public function alerts(): View
    {
        $alerts = SystemAlert::latest()->get();
        $stats = [
            'critical' => SystemAlert::where('level', 'critical')->where('is_resolved', false)->count(),
            'warning' => SystemAlert::where('level', 'warning')->where('is_resolved', false)->count(),
            'resolved' => SystemAlert::where('is_resolved', true)->count(),
        ];
        
        return view('admin.alerts', compact('alerts', 'stats'));
    }

    /**
     * Resolve a system alert.
     */
    public function resolveAlert(SystemAlert $alert): JsonResponse
    {
        $alert->update([
            'is_resolved' => true,
            'resolved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all alerts as read/resolved.
     */
    public function markAllAlertsRead(): JsonResponse
    {
        SystemAlert::where('is_resolved', false)->update([
            'is_resolved' => true,
            'resolved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get system logs for the real-time stats page.
     */
    public function getLogs(Request $request): JsonResponse
    {
        $query = SystemLog::latest();

        if ($request->has('type') && $request->type !== 'ALL') {
            if ($request->type === 'ERRORS') {
                $query->whereIn('level', ['error', 'critical']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $logs = $query->take(50)->get()->map(function($log) {
            return [
                'id' => $log->id,
                'time' => $log->created_at->format('H:i:s'),
                'label' => $log->type,
                'type' => $log->level === 'info' ? 'info' : 'error',
                'message' => $log->message
            ];
        });

        return response()->json($logs);
    }

    public function getUnreadAlertCount(): JsonResponse
    {
        $count = SystemAlert::where('is_resolved', false)->count();
        return response()->json(['count' => $count]);
    }
}
