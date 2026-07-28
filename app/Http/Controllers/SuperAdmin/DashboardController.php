<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\JobOffer;
use App\Models\Mission;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

/**
 * Super Admin Dashboard Controller
 */
class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $totalAdmins = User::whereIn('role', ['admin', 'super_admin'])->count();

        // Active sessions: approximate from sessions table
        $activeSessions = 0;
        try {
            $activeSessions = \DB::table('sessions')
                ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
                ->count();
        } catch (\Exception $e) {
            $activeSessions = 0;
        }

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'suspended_users' => $suspendedUsers,
            'total_admins' => $totalAdmins,
            'active_sessions' => $activeSessions,
            'monthly_revenue' => 0,        // placeholder — connect payment gateway later
            'system_uptime' => $this->getUptime(),
            'total_services' => Service::count(),
            'total_jobs' => JobOffer::count(),
            'active_missions' => Mission::where('status', 'active')->count(),
            'total_organizations' => Organization::count(),
        ];

        // Recent users for activity feed
        $recentActivity = User::latest()->take(8)->get();

        // All users for table (paginated)
        $users = User::latest()->paginate(10);

        return view('super-admin.dashboard', compact('stats', 'recentActivity', 'users'));
    }

    /**
     * Get a human-readable server uptime string.
     */
    private function getUptime(): string
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $uptime = (int) file_get_contents('/proc/uptime');
            $days = floor($uptime / 86400);
            $hours = floor(($uptime % 86400) / 3600);
            return "{$days}d {$hours}h";
        }
        return 'N/A';
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super admin accounts cannot be deleted.');
        }

        $user->delete();

        return redirect()->back()->with('success', "User {$user->name} has been deleted.");
    }

    /**
     * Promote / change a user's role.
     */
    public function promoteUser(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,admin,super_admin'],
        ]);

        $user = User::findOrFail($id);

        if ($user->id === auth()->id() && $request->role !== User::ROLE_SUPER_ADMIN) {
            return redirect()->back()->with('error', 'You cannot demote your own account.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "{$user->name}'s role has been updated to {$request->role}.");
    }

    /**
     * Toggle user active/suspended status.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot suspend your own account.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'activated' : 'suspended';
        return redirect()->back()->with('success', "{$user->name} has been {$label}.");
    }

    /**
     * Show the profile edit page for Super Admin.
     */
    public function profile(): View
    {
        $user = auth()->user();
        return view('super-admin.profile', compact('user'));
    }

    /**
     * Update the profile of the logged-in Super Admin.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
