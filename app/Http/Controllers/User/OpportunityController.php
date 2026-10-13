<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeedDispatch;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * "Opportunités" de l'artisan : uniquement les demandes publiées que le matching
 * lui a envoyées (cahier des charges §2.3), jamais une liste publique.
 */
class OpportunityController extends Controller
{
    public function index(): View
    {
        $artisan = Auth::user();
        abort_unless($artisan->isArtisan(), 403);

        $dispatches = NeedDispatch::where('artisan_id', $artisan->id)
            ->whereIn('status', [NeedDispatch::STATUS_SENT, NeedDispatch::STATUS_EXPIRED, NeedDispatch::STATUS_PROPOSED, NeedDispatch::STATUS_DECLINED])
            ->whereHas('need', fn ($q) => $q->where('mode', ServiceRequest::MODE_OPEN)->where('status', ServiceRequest::STATUS_PENDING))
            ->with(['need.commune:id,name', 'need.category:id,name'])
            // En attente de réponse d'abord, les plus pressées en tête.
            ->orderByRaw("CASE status WHEN 'sent' THEN 0 WHEN 'expired' THEN 1 ELSE 2 END")
            ->orderBy('deadline_at')
            ->paginate(10);

        $awaitingCount = NeedDispatch::where('artisan_id', $artisan->id)->awaitingResponse()
            ->whereHas('need', fn ($q) => $q->where('status', ServiceRequest::STATUS_PENDING))->count();

        return view('user.artisan.opportunities', compact('dispatches', 'awaitingCount'));
    }

    public function show(ServiceRequest $serviceRequest): View|RedirectResponse
    {
        $artisan = Auth::user();
        abort_unless($artisan->isArtisan(), 403);

        // Seul un artisan à qui la demande a été envoyée peut la consulter.
        $dispatch = NeedDispatch::where('service_request_id', $serviceRequest->id)
            ->where('artisan_id', $artisan->id)
            ->firstOrFail();

        if ($serviceRequest->status !== ServiceRequest::STATUS_PENDING || $dispatch->status === NeedDispatch::STATUS_CLOSED) {
            return redirect()->route('user.artisan.opportunities.index')
                ->with('info', 'Cette demande n\'est plus disponible : le client l\'a clôturée ou a déjà choisi un artisan.');
        }

        $serviceRequest->load(['category:id,name', 'serviceType:id,title', 'commune:id,name', 'user:id,name']);

        return view('user.artisan.opportunity-show', ['need' => $serviceRequest, 'dispatch' => $dispatch]);
    }
}
