<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Super Admin Dashboard Controller
 * 
 * Handles super-admin specific functionality including user management.
 */
class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_super_admins' => User::where('role', 'super_admin')->count(),
            'system_load' => 12.5,
            'ram_usage' => '2.4 GB',
            'uptime' => '1,245h 32m',
            'monthly_revenue' => 12450.75,
            'pending_security_alerts' => 5,
            'active_instances' => 3,
            'database_size' => '1.2 GB',
        ];

        $recent_users = User::latest()->take(10)->get();
        
        return view('super-admin.dashboard', compact('stats', 'recent_users'));
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Prevent deletion of other super admins
        if ($user->isSuperAdmin()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer un autre Super Administrateur.');
        }

        $user->delete();

        return redirect()->route('super-admin.users')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Promote a user to admin or super admin.
     */
    public function promoteUser(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,admin,super_admin'],
        ]);

        $user = User::findOrFail($id);

        // Prevent self-demotion
        if ($user->id === auth()->id() && $request->role !== User::ROLE_SUPER_ADMIN) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas vous rétrograder vous-même.');
        }

        $user->update(['role' => $request->role]);

        $roleLabel = User::ROLES[$request->role] ?? $request->role;

        return redirect()->back()
            ->with('success', "Le rôle de {$user->name} a été changé en {$roleLabel}.");
    }
}
