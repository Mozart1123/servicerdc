<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationController extends Controller
{
    /**
     * Show general identity verification page for Recruiter / Client.
     */
    public function show()
    {
        $user = Auth::user();

        if ($user->isArtisan()) {
            return redirect()->route('user.artisan.level');
        }

        $verification = IdentityVerification::firstOrCreate(
            ['user_id' => $user->id],
            ['verification_status' => IdentityVerification::STATUS_NOT_SUBMITTED]
        );

        return view('user.identity-verification', compact('user', 'verification'));
    }

    /**
     * Store identity verification document and selfie.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isArtisan()) {
            return redirect()->route('user.artisan.level');
        }

        $request->validate([
            'document'               => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'selfie'                 => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'identity_document_type' => ['required', 'string', 'in:voter_card,passport,national_id'],
        ], [
            'document.required'               => 'Veuillez joindre la photo ou le scan de votre pièce d\'identité.',
            'document.mimes'                  => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'document.max'                    => 'La taille du document ne doit pas dépasser 5 Mo.',
            'selfie.required'                 => 'Veuillez joindre la photo selfie de vous tenant votre pièce d\'identité.',
            'selfie.mimes'                    => 'Le selfie doit être une image au format JPG, JPEG ou PNG.',
            'selfie.max'                      => 'La taille du selfie ne doit pas dépasser 5 Mo.',
            'identity_document_type.required' => 'Veuillez sélectionner le type de pièce d\'identité.',
            'identity_document_type.in'       => 'Type de pièce d\'identité invalide.',
        ]);

        $verification = IdentityVerification::firstOrCreate(['user_id' => $user->id]);

        // Clean up previous files if exists
        if ($verification->identity_document_path && Storage::disk('local')->exists($verification->identity_document_path)) {
            Storage::disk('local')->delete($verification->identity_document_path);
        }
        if ($verification->selfie_path && Storage::disk('local')->exists($verification->selfie_path)) {
            Storage::disk('local')->delete($verification->selfie_path);
        }

        $docPath    = $request->file('document')->store('identity_documents', 'local');
        $selfiePath = $request->file('selfie')->store('identity_selfies', 'local');

        $verification->update([
            'identity_document_path'        => $docPath,
            'selfie_path'                   => $selfiePath,
            'identity_document_type'        => $request->identity_document_type,
            'verification_status'           => IdentityVerification::STATUS_PENDING,
            'verification_rejection_reason' => null,
            'verification_rejection_comment' => null,
        ]);

        // Notify admins about submitted general identity verification
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'identity_verification_submitted',
                'related_type' => 'user',
                'related_id'   => $user->id,
                'title'        => 'Vérification d\'identité reçue',
                'message'      => "L'utilisateur {$user->name} ({$user->user_type_label}) a soumis ses documents d'identité pour vérification.",
                'action_url'   => route('admin.verifications-general.index'),
                'is_read'      => false,
            ]);
        }

        return back()->with('success', 'Votre demande de vérification d\'identité a été soumise avec succès. Elle est en cours d\'examen.');
    }

    /**
     * Download own identity files.
     */
    public function download(Request $request)
    {
        $user = Auth::user();

        $verification = IdentityVerification::where('user_id', $user->id)->firstOrFail();

        $fileType = $request->query('file', 'document');
        $filePath = ($fileType === 'selfie') ? $verification->selfie_path : $verification->identity_document_path;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('local')->response($filePath);
    }
}
