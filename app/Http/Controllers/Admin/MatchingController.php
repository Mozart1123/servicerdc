<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\Matching\ArtisanMatcher;
use App\Services\Matching\MatchingSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Réglage des pondérations du score de matching + aperçu du classement
 * des artisans pour une demande publiée.
 */
class MatchingController extends Controller
{
    public function index(Request $request, MatchingSettings $settings, ArtisanMatcher $matcher): View
    {
        $openNeeds = ServiceRequest::where('mode', ServiceRequest::MODE_OPEN)
            ->with(['category:id,name', 'commune:id,name'])
            ->latest()
            ->take(30)
            ->get(['id', 'requested_service_name', 'category_id', 'commune_id', 'status', 'timing', 'created_at']);

        $selected = $request->filled('need')
            ? ServiceRequest::where('mode', ServiceRequest::MODE_OPEN)->with(['category', 'commune', 'user:id,name'])->find($request->integer('need'))
            : null;

        $ranking = null;
        if ($selected) {
            // Aperçu en zone élargie : on voit aussi les artisans hors commune (5 pts de proximité).
            $ranking = $matcher->rank($selected, widen: true)->take(20);
        }

        return view('admin.matching.index', [
            'weights'   => $settings->weights(),
            'defaults'  => $settings->defaults(),
            'labels'    => MatchingSettings::LABELS,
            'openNeeds' => $openNeeds,
            'selected'  => $selected,
            'ranking'   => $ranking,
        ]);
    }

    public function update(Request $request, MatchingSettings $settings): RedirectResponse
    {
        $rules = [];
        foreach (array_keys(MatchingSettings::LABELS) as $criterion) {
            $rules["weights.$criterion"] = ['required', 'integer', 'min:0', 'max:100'];
        }
        $validated = $request->validate($rules, [
            'weights.*.required' => 'Chaque critère doit avoir une pondération.',
            'weights.*.integer'  => 'Les pondérations doivent être des nombres entiers.',
            'weights.*.min'      => 'Une pondération ne peut pas être négative.',
            'weights.*.max'      => 'Une pondération ne peut pas dépasser 100.',
        ]);

        $weights = array_map('intval', $validated['weights']);
        if (array_sum($weights) !== 100) {
            return back()->withInput()->withErrors(['weights' => 'Le total des pondérations doit faire exactement 100 (actuellement ' . array_sum($weights) . ').']);
        }

        $settings->save($weights);

        return back()->with('success', 'Pondérations du matching enregistrées.');
    }
}
