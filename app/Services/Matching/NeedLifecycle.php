<?php

namespace App\Services\Matching;

use App\Models\NeedDispatch;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Cas limites d'une demande publiée (cahier des charges §2.6) :
 * - aucune proposition après 24 h : le client est invité à modifier son budget ou sa date, ou à relancer ;
 * - expiration : une demande sans suite se ferme seule après 7 jours ;
 * - modification possible tant qu'aucune proposition n'est en lice ;
 * - relance : nouvelle vague vers d'autres artisans et 7 jours de plus.
 */
class NeedLifecycle
{
    public const STALE_AFTER_HOURS = 24;

    public function __construct(private NeedDispatcher $dispatcher)
    {
    }

    /** Ferme les demandes publiées arrivées à expiration (tâche planifiée). */
    public function expireDue(): int
    {
        $ids = ServiceRequest::where('mode', ServiceRequest::MODE_OPEN)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->pluck('id');

        foreach ($ids as $id) {
            try {
                $this->expire($id);
            } catch (\Throwable $e) {
                Log::error("Demande #{$id} : échec de l'expiration : " . $e->getMessage());
            }
        }

        return $ids->count();
    }

    /** Prévient une fois le client dont la demande n'a reçu aucune proposition après 24 h. */
    public function notifyStale(): int
    {
        $needs = ServiceRequest::where('mode', ServiceRequest::MODE_OPEN)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->whereNull('stale_notified_at')
            ->where(fn ($q) => $q->whereNull('relaunched_at')->where('created_at', '<=', now()->subHours(self::STALE_AFTER_HOURS))
                ->orWhere('relaunched_at', '<=', now()->subHours(self::STALE_AFTER_HOURS)))
            ->whereDoesntHave('proposals', fn ($q) => $q->whereIn('status', NeedProposal::ACTIVE_STATUSES))
            ->get();

        foreach ($needs as $need) {
            // Mise à jour conditionnelle : un seul envoi même si deux tâches tournent en parallèle.
            $claimed = ServiceRequest::whereKey($need->id)->whereNull('stale_notified_at')->update(['stale_notified_at' => now()]);
            if ($claimed) {
                $this->notifyClient($need, 'need_stale', 'Pas encore de proposition',
                    'Aucun artisan n\'a encore fait de proposition pour « ' . $need->requested_service_name . ' ». '
                    . 'Vous pouvez ajuster votre budget ou votre date, ou relancer la recherche.');
            }
        }

        return $needs->count();
    }

    /** Aucune proposition depuis 24 h (ou plus aucun artisan disponible) : on propose d'agir. */
    public function isStale(ServiceRequest $need): bool
    {
        if (!$need->isOpenNeed() || $need->status !== ServiceRequest::STATUS_PENDING || $this->activeProposals($need) > 0) {
            return false;
        }
        $since = $need->relaunched_at ?? $need->created_at;

        return $need->matching_exhausted_at !== null || $since->lte(now()->subHours(self::STALE_AFTER_HOURS));
    }

    /** Modification possible tant qu'aucune proposition n'est en lice. */
    public function canEdit(ServiceRequest $need): bool
    {
        return $need->isOpenNeed() && $need->status === ServiceRequest::STATUS_PENDING && $this->activeProposals($need) === 0;
    }

    /** Relance possible si la demande est sans suite (après 24 h ou sans artisan) ou expirée. */
    public function canRelaunch(ServiceRequest $need): bool
    {
        if (!$need->isOpenNeed() || $need->artisan_id) {
            return false;
        }

        return $need->status === ServiceRequest::STATUS_EXPIRED || $this->isStale($need);
    }

    /** Relance : la demande repart pour 7 jours et une vague part aussitôt vers de nouveaux artisans. */
    public function relaunch(ServiceRequest $need): void
    {
        DB::transaction(function () use ($need) {
            $locked = ServiceRequest::whereKey($need->id)->lockForUpdate()->firstOrFail();
            abort_unless($this->canRelaunch($locked), 422, 'Cette demande ne peut pas être relancée pour le moment.');

            $locked->update([
                'status'                => ServiceRequest::STATUS_PENDING,
                'expires_at'            => now()->addDays(ServiceRequest::OPEN_DAYS),
                'relaunched_at'         => now(),
                'stale_notified_at'     => null,
                'matching_exhausted_at' => null,
                // Déclenche une vague immédiate (les artisans déjà contactés sont exclus).
                'wave_deadline_at'      => now()->subSecond(),
                'current_wave'          => max(1, $locked->current_wave),
            ]);
        });

        $this->dispatcher->advance($need->id);
    }

    private function expire(int $needId): void
    {
        DB::transaction(function () use ($needId) {
            $need = ServiceRequest::whereKey($needId)->lockForUpdate()->first();
            if (!$need || $need->status !== ServiceRequest::STATUS_PENDING || !$need->expires_at || $need->expires_at->isFuture()) {
                return;
            }

            $pending = $need->proposals()->whereIn('status', NeedProposal::ACTIVE_STATUSES)->get();
            $need->proposals()->whereIn('status', NeedProposal::ACTIVE_STATUSES)
                ->update(['status' => NeedProposal::STATUS_REJECTED, 'updated_at' => now()]);
            $need->dispatches()->awaitingResponse()->update(['status' => NeedDispatch::STATUS_CLOSED, 'updated_at' => now()]);
            $need->update(['status' => ServiceRequest::STATUS_EXPIRED, 'wave_deadline_at' => null]);

            $this->notifyClient($need, 'need_expired', 'Demande expirée',
                'Votre demande « ' . $need->requested_service_name . ' » s\'est fermée après ' . ServiceRequest::OPEN_DAYS . ' jours sans suite. '
                . 'Vous pouvez la relancer en un clic.');

            foreach ($pending as $proposal) {
                Notification::create([
                    'user_id'      => $proposal->artisan_id,
                    'type'         => 'need_expired',
                    'related_type' => 'request',
                    'related_id'   => $need->id,
                    'title'        => 'Demande fermée',
                    'message'      => 'La demande « ' . $need->requested_service_name . ' » a expiré sans que le client fasse de choix.',
                    'action_url'   => route('user.artisan.opportunities.index'),
                    'is_read'      => false,
                ]);
            }
        });
    }

    private function activeProposals(ServiceRequest $need): int
    {
        return $need->proposals()->whereIn('status', NeedProposal::ACTIVE_STATUSES)->count();
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
