<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::latest()->paginate(20, ['*'], 'users_page');
        
        return view('admin.users.index', compact('users'));
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
    public function pending()
    {
        $users = User::where('status', 'pending')->latest()->paginate(20);
        return view('admin.users.pending', compact('users'));
    }

    /**
     * Display flagged or suspended users.
     */
    public function flagged()
    {
        $users = User::where('status', 'suspended')->latest()->paginate(20);
        return view('admin.users.flagged', compact('users'));
    }

    /**
     * Display documents pending verification.
     */
    public function documents(Request $request)
    {
        $query = Document::with('user')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $documents = $query->paginate(15);
        
        // Stats
        $stats = [
            'pending' => Document::where('status', 'pending')->count(),
            'verified_30d' => Document::where('status', 'verified')
                ->where('verified_at', '>=', now()->subDays(30))
                ->count(),
            'rejected_rate' => Document::count() > 0 
                ? round((Document::where('status', 'rejected')->count() / Document::count()) * 100, 1) 
                : 0,
        ];

        return view('admin.users.documents', compact('documents', 'stats'));
    }

    /**
     * Verify a document.
     */
    public function verifyDocument(int $id)
    {
        $document = Document::findOrFail($id);
        
        $document->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Document validé avec succès.');
    }

    /**
     * Reject a document.
     */
    public function rejectDocument(Request $request, int $id)
    {
        $document = Document::findOrFail($id);
        
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $document->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Document rejeté.');
    }
}
