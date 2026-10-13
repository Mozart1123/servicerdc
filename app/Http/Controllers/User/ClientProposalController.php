<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Mission;
use App\Models\NeedDispatch;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Services\Matching\NeedDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Choix du client parmi les propositions reçues (cahier des charges §2.5) :
 * accepter une proposition (prix verrouillé, bascule dans le suivi de mission),
 * accepter une visite (l'artisan enverra ensuite son devis), ou tout refuser.
 */
class ClientProposalController extends Controller
{
    public function accept(NeedProposal $proposal): RedirectResponse
    {
        $need = DB::transaction(function () use ($proposal) {
            $need = $this->lockOwnPendingNeed($proposal->service_request_id);
            $proposal = NeedProposal::whereKey($proposal->id)->lockForUpdate()->firstOrFail();

            $acceptable = ($proposal->type === NeedProposal::TYPE_PRICE && $proposal->status === NeedProposal::STATUS_PENDING)
                || ($proposal->isVisit() && $proposal->status === NeedProposal::STATUS_VISIT_ACCEPTED && $proposal->quote_sent_at);
            if (!$acceptable) {
                throw ValidationException::withMessages(['proposal' => $proposal->isVisit()
                    ? 'Acceptez d\'abord la visite, puis attendez le devis de l\'artisan.'
                    : 'Cette proposition ne peut plus être acceptée.']);
            }

            $price = $proposal->total;

            $proposal->update(['status' => NeedProposal::STATUS_ACCEPTED]);

            $others = $need->proposals()->whereKeyNot($proposal->id)->whereIn('status', NeedProposal::ACTIVE_STATUSES)->get();
            $need->proposals()->whereKeyNot($proposal->id)->whereIn('status', NeedProposal::ACTIVE_STATUSES)
                ->update(['status' => NeedProposal::STATUS_REJECTED, 'updated_at' => now()]);
            // Les artisans contactés qui n'avaient pas encore répondu ne sont pas pénalisés.
            $need->dispatches()->awaitingResponse()->update(['status' => NeedDispatch::STATUS_CLOSED, 'updated_at' => now()]);

            $need->update([
                'artisan_id'           => $proposal->artisan_id,
                'accepted_proposal_id' => $proposal->id,
                'status'               => ServiceRequest::STATUS_ACCEPTED,
                'wave_deadline_at'     => null,
            ]);

            // Prix verrouillé : c'est le montant de la mission qui sera payé.
            Mission::firstOrCreate(
                ['service_request_id' => $need->id],
                [
                    'service_id'  => null,
                    'client_id'   => $need->user_id,
                    'artisan_id'  => $proposal->artisan_id,
                    'title'       => $need->requested_service_name ?? 'Mission',
                    'description' => $need->description,
                    'status'      => 'pending',
                    'amount'      => $price,
                ]
            );

            Conversation::findOrCreateBetween($need->user_id, $proposal->artisan_id, 'service', $need->id);

            $this->notify($proposal->artisan_id, 'need_proposal_accepted', 'Proposition retenue 🎉',
                Auth::user()->name . ' a retenu votre proposition pour « ' . $need->requested_service_name . ' ».',
                route('user.artisan.service-requests.index'), $need->id);
            foreach ($others as $other) {
                $this->notify($other->artisan_id, 'need_proposal_rejected', 'Proposition non retenue',
                    'Le client a retenu une autre proposition pour « ' . $need->requested_service_name . ' ». Merci d\'avoir répondu.',
                    route('user.artisan.opportunities.index'), $need->id);
            }

            return $need;
        });

        return redirect()->route('user.service-requests.show', $need)
            ->with('success', 'Proposition acceptée : le prix est verrouillé. Choisissez votre mode de paiement pour démarrer la mission.');
    }

    public function acceptVisit(NeedProposal $proposal): RedirectResponse
    {
        $need = DB::transaction(function () use ($proposal) {
            $need = $this->lockOwnPendingNeed($proposal->service_request_id);
            $proposal = NeedProposal::whereKey($proposal->id)->lockForUpdate()->firstOrFail();

            if (!$proposal->isVisit() || $proposal->status !== NeedProposal::STATUS_PENDING) {
                throw ValidationException::withMessages(['proposal' => 'Cette visite ne peut plus être acceptée.']);
            }

            $proposal->update(['status' => NeedProposal::STATUS_VISIT_ACCEPTED]);
            Conversation::findOrCreateBetween($need->user_id, $proposal->artisan_id, 'service', $need->id);

            $this->notify($proposal->artisan_id, 'need_visit_accepted', 'Visite acceptée',
                'Le client a accepté votre visite du ' . $proposal->visit_at->copy()->setTimezone(config('matching.timezone'))->format('d/m à H:i')
                . '. Après la visite, envoyez votre devis depuis la demande.',
                route('user.artisan.opportunities.show', $need), $need->id);

            return $need;
        });

        return redirect()->route('user.service-requests.show', $need)
            ->with('success', 'Visite acceptée. L\'artisan vous enverra son devis après son passage.');
    }

    /** Aucune proposition ne convient : elles sont refusées et la recherche continue avec d'autres artisans. */
    public function rejectAll(ServiceRequest $serviceRequest, NeedDispatcher $dispatcher): RedirectResponse
    {
        DB::transaction(function () use ($serviceRequest) {
            $need = $this->lockOwnPendingNeed($serviceRequest->id);
            $active = $need->proposals()->whereIn('status', NeedProposal::ACTIVE_STATUSES)->get();
            if ($active->isEmpty()) {
                throw ValidationException::withMessages(['proposal' => 'Aucune proposition à refuser.']);
            }

            $need->proposals()->whereIn('status', NeedProposal::ACTIVE_STATUSES)
                ->update(['status' => NeedProposal::STATUS_REJECTED, 'updated_at' => now()]);
            foreach ($active as $proposal) {
                $this->notify($proposal->artisan_id, 'need_proposal_rejected', 'Proposition non retenue',
                    'Le client n\'a pas retenu votre proposition pour « ' . $need->requested_service_name . ' ».',
                    route('user.artisan.opportunities.index'), $need->id);
            }

            // Relance immédiate : la vague suivante part vers de nouveaux artisans.
            $need->update(['wave_deadline_at' => now()->subSecond()]);
        });

        $dispatcher->advance($serviceRequest->id);

        return redirect()->route('user.service-requests.show', $serviceRequest)
            ->with('info', 'Propositions refusées. Nous contactons d\'autres artisans pour vous.');
    }

    /** La demande publiée du client connecté, verrouillée et encore ouverte. */
    private function lockOwnPendingNeed(int $needId): ServiceRequest
    {
        $need = ServiceRequest::whereKey($needId)->lockForUpdate()->firstOrFail();

        abort_unless($need->user_id === Auth::id() && $need->isOpenNeed(), 403);

        if ($need->status !== ServiceRequest::STATUS_PENDING) {
            throw ValidationException::withMessages(['proposal' => 'Cette demande n\'est plus ouverte : une proposition a déjà été retenue ou elle a été clôturée.']);
        }

        return $need;
    }

    private function notify(int $userId, string $type, string $title, string $message, string $url, int $needId): void
    {
        Notification::create([
            'user_id'      => $userId,
            'type'         => $type,
            'related_type' => 'request',
            'related_id'   => $needId,
            'title'        => $title,
            'message'      => $message,
            'action_url'   => $url,
            'is_read'      => false,
        ]);
    }
}
