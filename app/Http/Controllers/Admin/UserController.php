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
        $query = User::latest();
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('role', '!=', User::ROLE_SUPER_ADMIN);
        }
        $users = $query->paginate(20, ['*'], 'users_page');
        
        if ($request->wantsJson()) {
            return response()->json($users);
        }

        $provinces = config('drc.provinces');

        return view('admin.users.index', compact('users', 'provinces'));
    }

    /**
     * Get dynamic counts for sidebar badges.
     */
    public function getCountsApi()
    {
        return response()->json([
            'pending'  => User::where('status', User::STATUS_PENDING)->count(),
            'flagged'  => User::whereIn('status', ['suspended', 'inactive', 'banned', 'disabled'])->count(),
            'jobs'     => JobOffer::where('status', 'active')->count(),
            'services' => Service::where('status', 'active')->count(),
            'reviews'  => \App\Models\Review::count(),
        ]);
    }

    /**
     * API for user listing with search and filters.
     */
    public function apiIndex(Request $request)
    {
        $query = User::query();

        // Hide Super-Admin from standard Admin view
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('role', '!=', User::ROLE_SUPER_ADMIN);
        }

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

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        $users = $query->latest()->paginate(20);

        // Stats calculation respecting visibility rules
        $statsQuery = User::query();
        if (!Auth::user()->isSuperAdmin()) {
            $statsQuery->where('role', '!=', User::ROLE_SUPER_ADMIN);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => (clone $statsQuery)->where('status', User::STATUS_SUSPENDED)->count(),
            'verified' => (clone $statsQuery)->whereHas('documents', function($q) {
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
        $user->forceFill(['status' => $newStatus])->save();

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === User::STATUS_SUSPENDED ? 'Compte suspendu' : 'Compte réactivé'
        ]);
    }

    /**
     * Promote a regular user to admin role via AJAX.
     */
    public function promoteToAdminApi(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return response()->json(['error' => 'Impossible de modifier un Super Administrateur.'], 403);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'Vous ne pouvez pas modifier votre propre rôle.'], 403);
        }

        // Promote to admin keeping all existing data intact
        $user->forceFill(['role' => User::ROLE_ADMIN])->save();

        return response()->json([
            'success' => true,
            'role' => User::ROLE_ADMIN,
            'message' => "{$user->name} est désormais Administrateur."
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

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->forceFill(['role' => User::ROLE_ADMIN])->save();

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
        $user->forceFill(['status' => $newStatus])->save();

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
        $users = User::where('status', User::STATUS_PENDING)
            ->latest()
            ->paginate(20);

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
        $users = User::whereIn('status', ['suspended', 'inactive', 'banned', 'disabled'])
            ->latest()
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.flagged', compact('users'));
    }

    /**
     * Display documents & identity verifications pending verification.
     */
    public function documents(Request $request)
    {
        // 1. Standard Document table records
        $standardDocs = Document::with('user')->get()->map(function($d) {
            return [
                'id'         => $d->id,
                'user'       => $d->user,
                'type'       => $d->type ?? 'Document',
                'file_path'  => $d->file_path,
                'status'     => $d->status,
                'created_at' => $d->created_at,
                'source'     => 'document'
            ];
        });

        // 2. Artisan Identity Verifications
        $artisanDocs = \App\Models\ArtisanLevel::with('user')
            ->whereNotNull('identity_document_path')
            ->get()
            ->map(function($a) {
                return [
                    'id'         => $a->id,
                    'user'       => $a->user,
                    'type'       => 'Identité Artisan (' . ($a->identity_document_type ?? 'Doc') . ')',
                    'file_path'  => $a->identity_document_path,
                    'status'     => $a->verification_status ?? 'pending',
                    'created_at' => $a->updated_at,
                    'source'     => 'artisan_level'
                ];
            });

        // 3. General Identity Verifications (Recruiters & Clients)
        $generalDocs = \App\Models\IdentityVerification::with('user')
            ->whereNotNull('identity_document_path')
            ->get()
            ->map(function($g) {
                return [
                    'id'         => $g->id,
                    'user'       => $g->user,
                    'type'       => 'Identité Recruteur/Client (' . ($g->identity_document_type ?? 'Doc') . ')',
                    'file_path'  => $g->identity_document_path,
                    'status'     => $g->verification_status ?? 'pending',
                    'created_at' => $g->updated_at,
                    'source'     => 'identity_verification'
                ];
            });

        $allDocs = $standardDocs->concat($artisanDocs)->concat($generalDocs)->sortByDesc('created_at')->values();

        if ($request->has('type') && $request->type) {
            $typeFilter = strtolower($request->type);
            $allDocs = $allDocs->filter(function($item) use ($typeFilter) {
                return str_contains(strtolower($item['type']), $typeFilter);
            })->values();
        }

        $pendingCount   = $allDocs->where('status', 'pending')->count();
        $verified30d    = $allDocs->where('status', 'approved')->where('created_at', '>=', now()->subDays(30))->count()
                        + $allDocs->where('status', 'verified')->where('created_at', '>=', now()->subDays(30))->count();
        $rejectedCount  = $allDocs->where('status', 'rejected')->count();
        $totalCount     = $allDocs->count();

        $stats = [
            'pending'       => $pendingCount,
            'verified_30d'  => $verified30d,
            'rejected_rate' => $totalCount > 0 ? round(($rejectedCount / $totalCount) * 100) : 0,
        ];

        // Paginate manually
        $page = (int) $request->get('page', 1);
        $perPage = 20;
        $paginatedItems = $allDocs->slice(($page - 1) * $perPage, $perPage)->values();
        $lastPage = max(1, (int) ceil($totalCount / $perPage));

        $documents = [
            'data'         => $paginatedItems,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $totalCount,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'documents' => $documents,
                'stats'     => $stats
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
        $user->forceFill(['status' => User::STATUS_ACTIVE])->save();

        return response()->json([
            'success' => true,
            'message' => "Compte de {$user->name} approuvé."
        ]);
    }

    /**
     * Display the specified user details.
     */
    public function show(int $id)
    {
        $user = User::with(['artisanLevel', 'identityVerification', 'services', 'jobOffers', 'jobApplications'])->findOrFail($id);

        if (!Auth::user()->isSuperAdmin() && $user->isSuperAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);

        if (!Auth::user()->isSuperAdmin() && $user->isSuperAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if (!Auth::user()->isSuperAdmin() && $user->isSuperAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone'     => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', 'string', 'in:client,artisan,recruiter,job_seeker'],
            'status'    => ['required', 'string', 'in:active,pending,suspended'],
        ]);

        try {
            $user->update($validated);
            return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
        } catch (\Throwable $e) {
            \Log::error('Erreur mise à jour utilisateur admin: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.')->withInput();
        }
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
