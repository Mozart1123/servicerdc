<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtisanLevel;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtisanIdentityVerificationController extends Controller
{
    /**
     * Store artisan identity verification document and selfie.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isArtisan()) {
            abort(403, 'Seuls les artisans peuvent soumettre une vérification d\'identité sur cette page.');
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

        $artisanLevel = ArtisanLevel::firstOrCreate(
            ['user_id' => $user->id],
            ['level' => ArtisanLevel::LEVEL_NOUVEAU]
        );

        // Delete old document & selfie if existing
        if ($artisanLevel->identity_document_path && Storage::disk('local')->exists($artisanLevel->identity_document_path)) {
            Storage::disk('local')->delete($artisanLevel->identity_document_path);
        }
        if ($artisanLevel->selfie_path && Storage::disk('local')->exists($artisanLevel->selfie_path)) {
            Storage::disk('local')->delete($artisanLevel->selfie_path);
        }

        // Store files on private disk ('local')
        $docPath    = $request->file('document')->store('identity_documents', 'local');
        $selfiePath = $request->file('selfie')->store('identity_selfies', 'local');

        $artisanLevel->update([
            'identity_document_path'        => $docPath,
            'selfie_path'                   => $selfiePath,
            'identity_document_type'        => $request->identity_document_type,
            'verification_status'           => ArtisanLevel::STATUS_PENDING,
            'verification_rejection_reason' => null,
            'verification_rejection_comment' => null,
        ]);

        // Notify admins about submitted artisan identity verification
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'artisan_verification_submitted',
                'related_type' => 'user',
                'related_id'   => $user->id,
                'title'        => 'Vérification d\'identité artisan reçue',
                'message'      => "L'artisan {$user->name} a soumis ses documents d'identité pour vérification.",
                'action_url'   => route('admin.verifications.index'),
                'is_read'      => false,
            ]);
        }

        return back()->with('success', 'Votre dossier de vérification d\'identité a été soumis avec succès. Il est en cours d\'examen par notre équipe.');
    }

    /**
     * Download the artisan's own document or selfie.
     */
    public function download(Request $request)
    {
        $user = Auth::user();

        $artisanLevel = ArtisanLevel::where('user_id', $user->id)->firstOrFail();

        $fileType = $request->query('file', 'document');
        $filePath = ($fileType === 'selfie') ? $artisanLevel->selfie_path : $artisanLevel->identity_document_path;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('local')->response($filePath);
    }
}
