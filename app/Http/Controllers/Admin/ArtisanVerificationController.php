<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtisanLevel;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtisanVerificationController extends Controller
{
    /**
     * Display a list of artisan identity verifications.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'pending');

        $query = ArtisanLevel::with('user')
            ->whereNotNull('identity_document_path');

        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected', 'not_submitted'])) {
            $query->where('verification_status', $statusFilter);
        }

        $verifications = $query->latest('updated_at')->paginate(20);

        $stats = [
            'pending'   => ArtisanLevel::where('verification_status', 'pending')->count(),
            'approved'  => ArtisanLevel::where('verification_status', 'approved')->count(),
            'rejected'  => ArtisanLevel::where('verification_status', 'rejected')->count(),
        ];

        return view('admin.users.identity-verifications', compact('verifications', 'stats', 'statusFilter'));
    }

    /**
     * Download the identity document securely for admin review.
     */
    public function download(int $id)
    {
        $artisanLevel = ArtisanLevel::findOrFail($id);

        if (!$artisanLevel->identity_document_path || !Storage::disk('local')->exists($artisanLevel->identity_document_path)) {
            abort(404, 'Fichier d\'identité introuvable.');
        }

        return Storage::disk('local')->response($artisanLevel->identity_document_path);
    }

    /**
     * Approve the artisan's identity verification.
     */
    public function approve(int $id)
    {
        $artisanLevel = ArtisanLevel::findOrFail($id);

        $artisanLevel->update([
            'verification_status'           => ArtisanLevel::STATUS_APPROVED,
            'verified_at'                   => now(),
            'verified_by'                   => Auth::id(),
            'verification_rejection_reason' => null,
            'grace_period_ends_at'          => null,
        ]);

        // Send notification to the artisan
        Notification::create([
            'user_id'    => $artisanLevel->user_id,
            'type'       => 'identity_verified',
            'title'      => 'Identité Vérifiée ✓',
            'message'    => 'Félicitations ! Votre pièce d\'identité a été validée avec succès par l\'équipe ProConnect. Votre statut vérifié est désormais actif.',
            'action_url' => route('user.artisan.level'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'La vérification d\'identité a été approuvée avec succès.');
    }

    /**
     * Reject the artisan's identity verification with a reason.
     */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Le motif du rejet est obligatoire.',
        ]);

        $artisanLevel = ArtisanLevel::findOrFail($id);

        $artisanLevel->update([
            'verification_status'           => ArtisanLevel::STATUS_REJECTED,
            'verification_rejection_reason' => $request->reason,
            'verified_at'                   => null,
            'verified_by'                   => Auth::id(),
        ]);

        // Send notification to the artisan
        Notification::create([
            'user_id'    => $artisanLevel->user_id,
            'type'       => 'identity_rejected',
            'title'      => 'Identité non validée ✗',
            'message'    => 'Votre demande de vérification d\'identité a été refusée. Motif : ' . $request->reason,
            'action_url' => route('user.artisan.level'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'La demande de vérification d\'identité a été rejetée.');
    }
}
