<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\JobOffer;
use App\Models\Service;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $users = User::latest()->paginate(20, ['*'], 'users_page');
        
        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Get dynamic counts for sidebar badges.
     */
    public function getCountsApi()
    {
        return response()->json([
            'pending' => User::where('status', 'pending')->count(),
            'flagged' => User::where('status', 'suspended')->count(),
            'jobs' => JobOffer::where('status', 'active')->count(),
            'services' => 8, // Mocked until reporting is implemented
            'reviews' => 12, // Mocked until reviews are implemented
        ]);
    }

    /**
     * API for user listing with search and filters.
     */
    public function apiIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20);

        // Add stats to response
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => User::where('status', User::STATUS_SUSPENDED)->count(),
            'verified' => User::whereHas('documents', function($q) {
                $q->where('status', 'verified');
            })->count(),
        ];

        return response()->json([
            'users' => $users,
            'stats' => $stats
        ]);
    }

    /**
     * Toggle status via AJAX.
     */
    public function toggleStatusApi(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return response()->json(['error' => 'Action impossible sur Super-Admin'], 403);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'Action impossible sur soi-même'], 403);
        }

        $newStatus = $user->status === User::STATUS_ACTIVE ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === User::STATUS_SUSPENDED ? 'Compte suspendu' : 'Compte réactivé'
        ]);
    }

    /**
     * Promote a regular user to admin role.
     */
    public function promoteToAdmin(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Impossible de modifier un Super Administrateur.');
        }

        $user->update(['role' => User::ROLE_ADMIN]);

        return back()->with('success', "{$user->name} est désormais Administrateur.");
    }

    /**
     * Toggle user status between active and suspended.
     */
    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);

        // Security check: Admin cannot suspend Super-Admin
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Vous ne pouvez pas suspendre un Super Administrateur.');
        }

        // Prevent self-suspension
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas suspendre votre propre compte.');
        }

        $newStatus = $user->status === User::STATUS_ACTIVE ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        $message = $newStatus === User::STATUS_SUSPENDED
            ? "Le compte de {$user->name} a été suspendu."
            : "Le compte de {$user->name} a été réactivé.";

        return back()->with('success', $message);
    }

    /**
     * Permanently delete a user account.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        // Security check: Admin cannot delete Super-Admin
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer un Super Administrateur.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return back()->with('success', "Le compte de {$user->name} a été supprimé definitivement.");
    }

    /**
     * Display users pending validation.
     */
    public function pending(Request $request)
    {
        $users = User::where('status', 'pending')->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.pending', compact('users'));
    }

    /**
     * Display flagged or suspended users.
     */
    public function flagged(Request $request)
    {
        $users = User::where('status', 'suspended')->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.flagged', compact('users'));
    }

    /**
     * Display documents pending verification.
     */
    public function documents(Request $request)
    {
        $query = Document::with('user');

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $documents = $query->latest()->paginate(20);

        $stats = [
            'pending' => Document::where('status', 'pending')->count(),
            'verified_30d' => Document::where('status', 'verified')->where('updated_at', '>=', now()->subDays(30))->count(),
            'rejected_rate' => Document::count() > 0 
                ? round((Document::where('status', 'rejected')->count() / Document::count()) * 100) 
                : 0,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'documents' => $documents,
                'stats' => $stats
            ]);
        }

        return view('admin.users.documents', compact('documents', 'stats'));
    }

    /**
     * Verify a document.
     */
    public function verifyDocument(Request $request, int $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'verified']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Document vérifié avec succès.');
    }

    public function rejectDocument(Request $request, int $id)
    {
        $request->validate(['reason' => 'required|string']);

        $doc = Document::findOrFail($id);
        $doc->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Document rejeté.');
    }

    /**
     * Approve a pending user via AJAX.
     */
    public function approveApi(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => User::STATUS_ACTIVE]);

        return response()->json([
            'success' => true,
            'message' => "Compte de {$user->name} approuvé."
        ]);
    }

    /**
     * Reject/Delete a pending user via AJAX.
     */
    public function rejectApi(int $id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Inscription de {$name} refusée."
        ]);
    }
}
