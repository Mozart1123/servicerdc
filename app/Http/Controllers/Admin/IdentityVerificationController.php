<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationController extends Controller
{
    /**
     * Display general verifications (Recruiters & Clients).
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'pending');

        $query = IdentityVerification::with('user');

        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected', 'not_submitted'])) {
            $query->where('verification_status', $statusFilter);
        }

        $verifications = $query->latest('updated_at')->paginate(20);

        $stats = [
            'pending'  => IdentityVerification::where('verification_status', 'pending')->count(),
            'approved' => IdentityVerification::where('verification_status', 'approved')->count(),
            'rejected' => IdentityVerification::where('verification_status', 'rejected')->count(),
        ];

        $rejectionReasons = IdentityVerification::rejectionReasons();

        return view('admin.users.identity-verifications-general', compact('verifications', 'stats', 'statusFilter', 'rejectionReasons'));
    }

    /**
     * Download files securely.
     */
    public function download(Request $request, int $id)
    {
        $verification = IdentityVerification::findOrFail($id);

        $fileType = $request->query('file', 'document');
        $filePath = ($fileType === 'selfie') ? $verification->selfie_path : $verification->identity_document_path;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'Fichier d\'identité introuvable.');
        }

        return Storage::disk('local')->response($filePath);
    }

    /**
     * Approve verification.
     */
    public function approve(int $id)
    {
        $verification = IdentityVerification::findOrFail($id);

        $verification->update([
            'verification_status'           => IdentityVerification::STATUS_APPROVED,
            'verified_at'                   => now(),
            'verified_by'                   => Auth::id(),
            'verification_rejection_reason' => null,
            'verification_rejection_comment' => null,
        ]);

        Notification::create([
            'user_id'    => $verification->user_id,
            'type'       => 'identity_verified',
            'title'      => 'Identité vérifiée',
            'message'    => 'Votre identité a été vérifiée ! Vous disposez maintenant du badge "Vérifié" sur votre profil.',
            'action_url' => route('user.identity-verification.show'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'La vérification d\'identité a été approuvée avec succès.');
    }

    /**
     * Reject verification.
     */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'reason'  => ['required', 'string'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Le motif du rejet est obligatoire.',
        ]);

        $verification = IdentityVerification::findOrFail($id);

        $verification->update([
            'verification_status'           => IdentityVerification::STATUS_REJECTED,
            'verification_rejection_reason' => $request->reason,
            'verification_rejection_comment' => $request->comment,
            'verified_at'                   => null,
            'verified_by'                   => Auth::id(),
        ]);

        $fullReason = $request->reason . ($request->comment ? ' (' . $request->comment . ')' : '');

        Notification::create([
            'user_id'    => $verification->user_id,
            'type'       => 'identity_rejected',
            'title'      => 'Identité non validée',
            'message'    => "Votre demande de vérification d'identité n'a pas pu être validée. Motif : {$fullReason}. Vous pouvez soumettre une nouvelle demande.",
            'action_url' => route('user.identity-verification.show'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'La demande de vérification d\'identité a été rejetée.');
    }
}
