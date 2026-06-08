<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users (paginated, filterable).
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('super-admin.users.create');
    }

    /**
     * Show a single user.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('super-admin.users.show', compact('user'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('super-admin.users.edit', compact('user'));
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
     * Roles & Permissions overview page.
     */
    public function roles()
    {
        $roleSummary = [
            'super_admin' => [
                'label' => 'Super Admin',
                'description' => 'Full system access. Can manage all users, settings, billing, and platform configuration.',
                'color' => 'blue',
                'permissions' => [
                    'Manage all users & roles',
                    'Access system settings & API keys',
                    'View audit trail & logs',
                    'Manage billing & payouts',
                    'Platform configuration',
                    'Delete any content',
                ],
                'count' => User::where('role', 'super_admin')->count(),
                'users' => User::where('role', 'super_admin')->latest()->take(5)->get(),
            ],
            'admin' => [
                'label' => 'Admin',
                'description' => 'Access to user management, job offers, services, and reports. Cannot access billing or system settings.',
                'color' => 'amber',
                'permissions' => [
                    'Manage users (non-super admin)',
                    'Manage job offers & services',
                    'View & manage categories',
                    'Generate reports',
                    'Moderate content',
                ],
                'count' => User::where('role', 'admin')->count(),
                'users' => User::where('role', 'admin')->latest()->take(5)->get(),
            ],
            'user' => [
                'label' => 'User',
                'description' => 'Standard platform user. Can browse services, apply for jobs, and manage their own profile.',
                'color' => 'gray',
                'permissions' => [
                    'Browse & search services',
                    'Apply for job offers',
                    'Request artisan services',
                    'Track missions',
                    'Manage own profile',
                ],
                'count' => User::where('role', 'user')->count(),
                'users' => User::where('role', 'user')->latest()->take(5)->get(),
            ],
        ];

        $allUsers = User::latest()->paginate(15);

        return view('super-admin.roles', compact('roleSummary', 'allUsers'));
    }

    /**
     * Update a user's role from the Roles page.
     */
    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $request->validate(['role' => ['required', 'in:user,admin,super_admin']]);

        $user = User::findOrFail($id);

        if ($user->id === auth()->id() && $request->role !== 'super_admin') {
            return redirect()->back()->with('error', 'You cannot demote your own account.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "{$user->name}'s role has been updated to " . ucfirst(str_replace('_', ' ', $request->role)) . '.');
    }

    /**
     * Sessions overview page.
     */
    public function sessions()
    {
        $activeSessions = 0;
        $sessionData = collect();
        try {
            $sessionData = \DB::table('sessions')
                ->orderByDesc('last_activity')
                ->take(50)
                ->get();
            $activeSessions = $sessionData->where('last_activity', '>', now()->subMinutes(30)->timestamp)->count();
        } catch (\Exception $e) {
            // sessions table may not exist
        }

        return view('super-admin.sessions', compact('sessionData', 'activeSessions'));
    }

    /**
     * Delete a user.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super admin accounts cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User {$name} has been permanently deleted.");
    }
}
