<?php

namespace App\Services\Matching;

use App\Models\NeedDispatch;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Envoi d'une demande publiée par vagues de 5 artisans (cahier des charges §2.3) :
 * - vague 1 à la publication, délai 2 h (30 min si urgent) ;
 * - à la fin du délai : moins de 3 propositions → vague suivante, sinon on s'arrête ;
 * - 5 propositions → plus aucun envoi ;
 * - plus d'artisan disponible → on prévient le client et on réessaie toutes les heures.
 *
 * Chaque avancement verrouille la demande : publication et tâche planifiée
 * peuvent tourner en même temps sans envoyer deux fois la même vague.
 */
class NeedDispatcher
{
    /** Délai avant de réessayer une demande pour laquelle aucun artisan n'était disponible. */
    public const RETRY_EXHAUSTED_MINUTES = 60;

    public function __construct(private ArtisanMatcher $matcher)
    {
    }

    /** Première vague, juste après la publication. */
    public function start(ServiceRequest $need): void
    {
        $this->advance($need->id);
    }

    /** Fait avancer toutes les demandes dont c'est le moment (tâche planifiée). */
    public function advanceDue(): int
    {
        if (!config('matching.dispatch_enabled')) {
            return 0;
        }

        $ids =ServiceRequest::where('mode', ServiceRequest::MODE_OPEN)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($q) => $q
                ->where('current_wave', 0)                                   // jamais envoyée (ex. erreur à la publication)
                ->orWhere('wave_deadline_at', '<=', now())                    // délai de la vague écoulé
                ->orWhere('matching_exhausted_at', '<=', now()->subMinutes(self::RETRY_EXHAUSTED_MINUTES)))
            ->pluck('id');

        foreach ($ids as $id) {
            try {
                $this->advance($id);
            } catch (\Throwable $e) {
                Log::error("Matching : échec de l'avancement de la demande #{$id} : " . $e->getMessage());
            }
        }

        return $ids->count();
    }

    /** Idempotent : ne fait rien si ce n'est pas le moment. */
    public function advance(int $needId): void
    {
        if (!config('matching.dispatch_enabled')) {
            return;
        }

        DB::transaction(function () use ($needId) {
            $need = ServiceRequest::whereKey($needId)->lockForUpdate()->first();
            if (!$need || !$need->isOpenNeed() || $need->status !== ServiceRequest::STATUS_PENDING) {
                return;
            }
            if ($need->expires_at && $need->expires_at->isPast()) {
                return;
            }

            if ($need->current_wave === 0) {
                $this->sendWave($need);
                return;
            }

            if ($need->matching_exhausted_at) {
                if ($need->matching_exhausted_at->lte(now()->subMinutes(self::RETRY_EXHAUSTED_MINUTES))) {
                    $this->sendWave($need);
                }
                return;
            }

            if (!$need->wave_deadline_at || $need->wave_deadline_at->isFuture()) {
                return;
            }

            // Fin du délai : les artisans qui n'ont pas répondu passent en "expiré" (réactivité en baisse).
            $need->dispatches()->awaitingResponse()->where('deadline_at', '<=', now())
                ->update(['status' => NeedDispatch::STATUS_EXPIRED, 'updated_at' => now()]);

            if ($this->proposalsCount($need) >= (int) config('matching.waves.min_proposals')) {
                $need->update(['wave_deadline_at' => null]); // assez de propositions : on arrête
                return;
            }

            $this->sendWave($need);
        });
    }

    public function proposalsCount(ServiceRequest $need): int
    {
        return $need->dispatches()->where('status', NeedDispatch::STATUS_PROPOSED)->count();
    }

    /** Appelé à chaque proposition : à 5 propositions, on arrête les envois (§2.3). */
    public function stopIfEnoughProposals(ServiceRequest $need): void
    {
        if ($this->proposalsCount($need) >= (int) config('matching.waves.max_proposals')) {
            ServiceRequest::whereKey($need->id)->update(['wave_deadline_at' => null]);
        }
    }

    /** Annulation par le client : les artisans en attente ne sont pas pénalisés. */
    public function closeDispatches(ServiceRequest $need): void
    {
        $need->dispatches()->awaitingResponse()->update(['status' => NeedDispatch::STATUS_CLOSED, 'updated_at' => now()]);
        $need->update(['wave_deadline_at' => null]);
    }

    private function sendWave(ServiceRequest $need): void
    {
        if ($this->proposalsCount($need) >= (int) config('matching.waves.max_proposals')) {
            $need->update(['wave_deadline_at' => null]);
            return;
        }

        $alreadyContacted = $need->dispatches()->pluck('artisan_id')->all();
        $result = $this->matcher->shortlist($need, (int) config('matching.waves.size'), $alreadyContacted);
        $candidates = $result['candidates'];

        if ($candidates->isEmpty()) {
            $firstTime = $need->matching_exhausted_at === null;
            $need->update(['wave_deadline_at' => null, 'matching_exhausted_at' => now()]);
            if ($firstTime) {
                $this->notifyClient($need, 'need_no_artisan', 'Recherche d\'artisans en cours',
                    $alreadyContacted
                        ? 'Tous les artisans disponibles pour votre demande ont été contactés. Nous continuons à chercher et vous prévenons dès qu\'un nouvel artisan est disponible.'
                        : 'Aucun artisan n\'est disponible pour le moment pour votre demande. Nous continuons à chercher et vous prévenons dès qu\'un artisan est disponible.');
            }
            return;
        }

        $wave = $need->current_wave + 1;
        $minutes = (int) config($need->timing === 'urgent' ? 'matching.waves.urgent_minutes' : 'matching.waves.deadline_minutes');
        $now = now();
        $deadline = $now->copy()->addMinutes($minutes);

        foreach ($candidates as $candidate) {
            NeedDispatch::create([
                'service_request_id' => $need->id,
                'artisan_id'         => $candidate->artisan->id,
                'wave'               => $wave,
                'score'              => $candidate->score,
                'proximity'          => $candidate->proximity,
                'status'             => NeedDispatch::STATUS_SENT,
                'sent_at'            => $now,
                'deadline_at'        => $deadline,
            ]);

            Notification::create([
                'user_id'      => $candidate->artisan->id,
                'type'         => 'need_dispatched',
                'related_type' => 'request',
                'related_id'   => $need->id,
                'title'        => ($need->timing === 'urgent' ? '⚡ Demande urgente : ' : 'Nouvelle demande : ') . $need->requested_service_name,
                'message'      => 'Un client' . ($need->commune ? ' à ' . $need->commune->name : '') . ' recherche un artisan. Répondez avant ' . $deadline->copy()->setTimezone(config('matching.timezone'))->format('H:i') . '.',
                'action_url'   => route('user.artisan.opportunities.show', $need),
                'is_read'      => false,
            ]);
        }

        $updates = ['current_wave' => $wave, 'wave_deadline_at' => $deadline, 'matching_exhausted_at' => null];
        if ($result['widened'] && !$need->matching_widened_at) {
            $updates['matching_widened_at'] = $now;
            $this->notifyClient($need, 'need_widened', 'Recherche élargie',
                'Peu d\'artisans sont disponibles dans votre commune : nous avons élargi la recherche aux communes plus éloignées.');
        }
        $need->update($updates);
    }

    private function notifyClient(ServiceRequest $need, string $type, string $title, string $message): void
    {
        Notification::create([
            'user_id'      => $need->user_id,
            'type'         => $type,
            'related_type' => 'request',
            'related_id'   => $need->id,
            'title'        => $title,
            'message'      => $message,
            'action_url'   => route('user.service-requests.show', $need),
            'is_read'      => false,
        ]);
    }
}
