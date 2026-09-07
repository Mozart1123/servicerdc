<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtisanRealisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RealisationController extends Controller
{
    /**
     * Nombre maximum de réalisations qu'un artisan peut publier — évite un
     * usage abusif du stockage tant qu'il n'y a pas de palier premium dédié.
     */
    private const MAX_REALISATIONS = 20;

    /**
     * Page de gestion des réalisations de l'artisan connecté
     * (ajout / suppression de photos avec légende).
     */
    public function index(): View
    {
        $realisations = ArtisanRealisation::where('artisan_id', Auth::id())
            ->latest()
            ->get();

        return view('user.realisations.index', [
            'realisations' => $realisations,
            'maxRealisations' => self::MAX_REALISATIONS,
        ]);
    }

    /**
     * Ajoute une photo de réalisation.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isArtisan()) {
            abort(403, 'Seuls les artisans peuvent ajouter des réalisations.');
        }

        $request->validate([
            'image'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $currentCount = ArtisanRealisation::where('artisan_id', Auth::id())->count();
        if ($currentCount >= self::MAX_REALISATIONS) {
            return back()->with('error', 'Vous avez atteint la limite de ' . self::MAX_REALISATIONS . ' réalisations. Supprimez-en une pour en ajouter une nouvelle.');
        }

        $path = $request->file('image')->store('artisan-realisations', 'public');

        ArtisanRealisation::create([
            'artisan_id' => Auth::id(),
            'image_path' => $path,
            'caption'    => $request->input('caption'),
        ]);

        return back()->with('success', 'Réalisation ajoutée avec succès.');
    }

    /**
     * Supprime une réalisation (photo + ligne en base).
     */
    public function destroy(ArtisanRealisation $realisation): RedirectResponse
    {
        if ($realisation->artisan_id !== Auth::id()) {
            abort(403);
        }

        if ($realisation->image_path) {
            Storage::disk('public')->delete($realisation->image_path);
        }

        $realisation->delete();

        return back()->with('success', 'Réalisation supprimée.');
    }
}
