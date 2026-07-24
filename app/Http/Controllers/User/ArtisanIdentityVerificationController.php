<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtisanLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtisanIdentityVerificationController extends Controller
{
    /**
     * Store artisan identity verification document.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isArtisan()) {
            abort(403, 'Seuls les artisans peuvent soumettre une vérification d\'identité.');
        }

        $request->validate([
            'document'               => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'identity_document_type' => ['required', 'string', 'in:national_id,passport,driving_license'],
        ], [
            'document.required'               => 'Veuillez joindre la photo ou le scan de votre pièce d\'identité.',
            'document.mimes'                  => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'document.max'                    => 'La taille du document ne doit pas dépasser 5 Mo.',
            'identity_document_type.required' => 'Veuillez sélectionner le type de pièce d\'identité.',
            'identity_document_type.in'       => 'Type de pièce d\'identité invalide.',
        ]);

        $artisanLevel = ArtisanLevel::firstOrCreate(
            ['user_id' => $user->id],
            ['level' => ArtisanLevel::LEVEL_NOUVEAU]
        );

        // Delete old document if existing
        if ($artisanLevel->identity_document_path && Storage::disk('local')->exists($artisanLevel->identity_document_path)) {
            Storage::disk('local')->delete($artisanLevel->identity_document_path);
        }

        // Store file on private disk ('local')
        $path = $request->file('document')->store('identity_documents', 'local');

        $artisanLevel->update([
            'identity_document_path'        => $path,
            'identity_document_type'        => $request->identity_document_type,
            'verification_status'           => ArtisanLevel::STATUS_PENDING,
            'verification_rejection_reason' => null,
        ]);

        return back()->with('success', 'Votre pièce d\'identité a été soumise avec succès. Elle est en cours de vérification par notre équipe.');
    }

    /**
     * Download the artisan's own identity document securely.
     */
    public function download()
    {
        $user = Auth::user();

        $artisanLevel = ArtisanLevel::where('user_id', $user->id)->firstOrFail();

        if (!$artisanLevel->identity_document_path || !Storage::disk('local')->exists($artisanLevel->identity_document_path)) {
            abort(404, 'Aucun document d\'identité trouvé.');
        }

        return Storage::disk('local')->response($artisanLevel->identity_document_path);
    }
}
