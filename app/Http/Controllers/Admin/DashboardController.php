<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Service;
use App\Models\JobOffer;
use App\Models\Transaction;
use App\Models\SystemLog;
use App\Models\SystemAlert;
use App\Services\KpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Admin Dashboard Controller
 * 
 * Handles admin-specific functionality.
 */
class DashboardController extends Controller
{
    protected KpayService $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Real revenue from Transaction table (payments confirmed by K-PAY)
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $monthlyRevenue = Transaction::where('status', 'succeeded')
            ->where('created_at', '>=', $thisMonth)
            ->sum('amount');

        $lastMonthRevenue = Transaction::where('status', 'succeeded')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->sum('amount');

        // Calculate real growth vs last month
        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        }

        // Real user growth (users created this month vs last month)
        $usersThisMonth = User::where('created_at', '>=', $thisMonth)->count();
        $usersLastMonth = User::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $userGrowth = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        // K-PAY wallet balance (CDF)
        $kpayBalance = 0;
        try {
            $wallets = $this->kpayService->getBalance();
            if (is_array($wallets)) {
                foreach ($wallets as $wallet) {
                    if (($wallet['currency'] ?? '') === 'CDF') {
                        $kpayBalance = $wallet['availableBalance'] ?? 0;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // If K-PAY is unreachable, show 0
        }

        $stats = [
            'total_users'        => User::count(),
            'active_services'    => Service::active()->count(),
            'total_jobs'         => JobOffer::count(),
            'total_applications' => \App\Models\JobApplication::count(),
            'monthly_revenue'    => $kpayBalance,   // Real K-PAY CDF balance
            'revenue_growth'     => $revenueGrowth,
            'user_growth'        => $userGrowth,
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

    /**
     * Display the admin profile.
     */
    public function profile(): View
    {
        $user = auth()->user();
        
        // Mock recent activity for the UI since there is no standard activity table.
        $recentLogs = \App\Models\SystemLog::where('user_id', $user->id)
                                           ->latest()
                                           ->take(5)
                                           ->get();
                                           
        return view('admin.profile', compact('user', 'recentLogs'));
    }

    /**
     * Update the admin profile.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('admin.profile')->with('success', 'Profil mis à jour avec succès.');
    }
}
