<?php

namespace App\Services\Matching;

use App\Models\Commune;
use App\Models\NeedDispatch;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Sélectionne et classe les artisans pour une demande publiée (cahier des charges §2).
 *
 * 1. Filtres éliminatoires (SQL) : catégorie, commune ou commune voisine, disponibilité,
 *    moins de N demandes/missions en cours, compte actif.
 * 2. Score de pertinence sur 100, pondérations réglables dans l'admin.
 *
 * Quand une donnée manque (aucun avis, aucune demande reçue, aucun prix connu),
 * le critère reçoit la moitié de ses points : ni pénalité ni avantage. Seul le bonus
 * "nouvel artisan" favorise volontairement les débutants.
 */
class ArtisanMatcher
{
    public const SAME_COMMUNE = 'same';
    public const NEIGHBOR_COMMUNE = 'neighbor';
    public const WIDER_ZONE = 'wide';

    public const WEEKDAY_SLUGS = [1 => 'lun', 2 => 'mar', 3 => 'mer', 4 => 'jeu', 5 => 'ven', 6 => 'sam', 7 => 'dim'];

    public function __construct(private MatchingSettings $settings)
    {
    }

    /**
     * Les $size meilleurs artisans. Si la commune et ses voisines n'en fournissent
     * pas assez, on élargit à toute la ville ("zone élargie", à signaler au client).
     *
     * @param int[] $excludeIds artisans déjà contactés
     * @return array{candidates: Collection<int, MatchCandidate>, widened: bool}
     */
    public function shortlist(ServiceRequest $need, int $size, array $excludeIds = []): array
    {
        $candidates = $this->rank($need, $excludeIds);
        $widened = false;

        if ($candidates->count() < $size) {
            $candidates = $this->rank($need, $excludeIds, widen: true);
            $widened = $candidates->contains(fn (MatchCandidate $c) => $c->proximity === self::WIDER_ZONE);
        }

        return ['candidates' => $candidates->take($size)->values(), 'widened' => $widened];
    }

    /**
     * Tous les artisans éligibles, classés par score décroissant.
     *
     * @param int[] $excludeIds
     * @return Collection<int, MatchCandidate>
     */
    public function rank(ServiceRequest $need, array $excludeIds = [], bool $widen = false): Collection
    {
        if (!$need->category_id) {
            return collect();
        }

        $commune = $need->commune_id ? Commune::with('neighbors:id')->find($need->commune_id) : null;
        $nearbyIds = $commune ? array_merge([$commune->id], $commune->neighbors->pluck('id')->all()) : [];

        // Sans commune connue, seule la zone élargie a un sens.
        if (!$nearbyIds && !$widen) {
            return collect();
        }

        $artisans = $this->eligibleQuery($need, $excludeIds, $widen ? null : $nearbyIds)->get();

        if ($need->timing === 'urgent') {
            $artisans = $artisans->filter(fn (User $a) => $this->isWorkingNow($a))->values();
        }

        if ($artisans->isEmpty()) {
            return collect();
        }

        return $this->score($need, $artisans, $commune);
    }

    /** Filtres éliminatoires §2.1 (hors "disponible tout de suite", vérifié en PHP). */
    public function eligibleQuery(ServiceRequest $need, array $excludeIds, ?array $communeIds): Builder
    {
        $activeStatuses = config('matching.active_statuses');

        return User::query()
            ->where('user_type', User::TYPE_ARTISAN)
            ->where('status', User::STATUS_ACTIVE)
            ->whereKeyNot($need->user_id)
            ->when($excludeIds, fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->where('is_available', true)
            // "Absent jusqu'au X" : indisponible jusqu'à X inclus.
            ->where(fn ($q) => $q->whereNull('available_until')->orWhereDate('available_until', '<', today()))
            ->whereHas('services', fn ($q) => $q->where('category_id', $need->category_id)->where('status', 'active'))
            ->when($communeIds !== null, fn ($q) => $q->whereHas('interventionCommunes', fn ($c) => $c->whereIn('communes.id', $communeIds)))
            ->whereHas('assignedServiceRequests', fn ($q) => $q->whereIn('status', $activeStatuses), '<', (int) config('matching.max_active_jobs'));
    }

    /** Disponible tout de suite : jour et heure de travail habituels, à l'heure de Kinshasa. */
    public function isWorkingNow(User $artisan): bool
    {
        $now = now(config('matching.timezone'));
        $days = $artisan->availability_days ?: ['lun', 'mar', 'mer', 'jeu', 'ven'];
        if (!in_array(self::WEEKDAY_SLUGS[$now->dayOfWeekIso], $days, true)) {
            return false;
        }

        $start = $this->parseTime($artisan->working_hours_start, '08:00');
        $end = $this->parseTime($artisan->working_hours_end, '18:00');
        $current = $now->format('H:i');

        return $start <= $end
            ? $current >= $start && $current < $end
            : $current >= $start || $current < $end; // horaires de nuit (ex. 20:00–06:00)
    }

    /**
     * @param Collection<int, User> $artisans
     * @return Collection<int, MatchCandidate>
     */
    private function score(ServiceRequest $need, Collection $artisans, ?Commune $commune): Collection
    {
        $weights = $this->settings->weights();
        $ids = $artisans->pluck('id')->all();
        $stats = $this->loadStats($need, $ids);
        $neighborIds = $commune ? $commune->neighbors->pluck('id')->all() : [];
        $priorWeight = (float) config('matching.bayes_prior_weight');
        $platformAvg = $stats['platform_avg'];

        return $artisans->map(function (User $artisan) use ($need, $weights, $stats, $commune, $neighborIds, $priorWeight, $platformAvg) {
            $id = $artisan->id;
            $reviewCount = (int) ($stats['reviews'][$id]->review_count ?? 0);
            $reviewSum = (float) ($stats['reviews'][$id]->rating_sum ?? 0);
            $completed = (int) ($stats['completed'][$id] ?? 0);
            $cancelledByArtisan = (int) ($stats['cancelled_by_artisan'][$id] ?? 0);
            $usualPrice = isset($stats['prices'][$id]) ? (float) $stats['prices'][$id] : null;
            $artisanCommunes = $stats['communes'][$id] ?? [];

            // Note ajustée (moyenne bayésienne) : (C × moyenne plateforme + somme des notes) ÷ (C + nb d'avis).
            $adjusted = ($priorWeight * $platformAvg + $reviewSum) / ($priorWeight + $reviewCount);
            $ratingShare = max(0.0, min(1.0, ($adjusted - 1) / 4));

            $proximity = match (true) {
                $commune && in_array($commune->id, $artisanCommunes, true) => self::SAME_COMMUNE,
                (bool) array_intersect($neighborIds, $artisanCommunes)     => self::NEIGHBOR_COMMUNE,
                default                                                   => self::WIDER_ZONE,
            };
            $proximityShare = ['same' => 20, 'neighbor' => 12, 'wide' => 5][$proximity] / 20;

            $budgetShare = $this->budgetShare($need, $usualPrice);

            // Réactivité : taux de réponse × 10 + délai (< 1 h = 5, < 3 h = 3, au-delà = 0), sur 15.
            $received = (int) ($stats['received'][$id] ?? 0);
            $responded = (int) ($stats['responded'][$id] ?? 0);
            $ratePoints = $received > 0 ? min(1, $responded / $received) * 10 : 5;
            $avgMinutes = $artisan->averageResponseMinutes();
            $delayPoints = match (true) {
                $avgMinutes === null => 2.5,
                $avgMinutes < 60     => 5,
                $avgMinutes < 180    => 3,
                default              => 0,
            };
            $responsivenessShare = ($ratePoints + $delayPoints) / 15;

            // Fiabilité : terminées et confirmées ÷ (terminées + annulées par l'artisan).
            $settled = $completed + $cancelledByArtisan;
            $reliabilityShare = $settled > 0 ? $completed / $settled : 0.5;

            $newcomerShare = match (true) {
                $completed < 5   => 1.0,
                $completed <= 10 => 0.5,
                default          => 0.0,
            };

            $breakdown = [
                'rating'         => $ratingShare * $weights['rating'],
                'proximity'      => $proximityShare * $weights['proximity'],
                'budget'         => $budgetShare * $weights['budget'],
                'responsiveness' => $responsivenessShare * $weights['responsiveness'],
                'reliability'    => $reliabilityShare * $weights['reliability'],
                'newcomer'       => $newcomerShare * $weights['newcomer'],
            ];

            return new MatchCandidate(
                artisan: $artisan,
                score: round(array_sum($breakdown), 1),
                breakdown: array_map(fn ($p) => round($p, 1), $breakdown),
                proximity: $proximity,
                adjustedRating: round($adjusted, 2),
                reviewCount: $reviewCount,
                usualPrice: $usualPrice,
                completedMissions: $completed,
            );
        })
            ->sort(fn (MatchCandidate $a, MatchCandidate $b) => [$b->score, $b->reviewCount, $a->artisan->id] <=> [$a->score, $a->reviewCount, $b->artisan->id])
            ->values();
    }

    /** Part (0–1) du critère budget. Barème §2.2 : dans le budget 15, ≤ +20 % 8, au-delà 0, sans info 10. */
    private function budgetShare(ServiceRequest $need, ?float $usualPrice): float
    {
        $ceiling = (float) $need->budget_max;
        if ($ceiling <= 0 || $usualPrice === null) {
            return 10 / 15;
        }

        return match (true) {
            $usualPrice <= $ceiling       => 1.0,
            $usualPrice <= $ceiling * 1.2 => 8 / 15,
            default                       => 0.0,
        };
    }

    /**
     * Statistiques groupées pour tous les candidats (quelques requêtes, pas une par artisan).
     *
     * @param int[] $ids
     */
    private function loadStats(ServiceRequest $need, array $ids): array
    {
        $since = now()->subDays((int) config('matching.responsiveness_window_days'));

        $platformAvg = Review::approved()->avg('rating');

        $reviews = Review::approved()->whereIn('artisan_id', $ids)
            ->groupBy('artisan_id')
            ->selectRaw('artisan_id, COUNT(*) as review_count, SUM(rating) as rating_sum')
            ->get()->keyBy('artisan_id');

        $completed = ServiceRequest::whereIn('artisan_id', $ids)->where('status', ServiceRequest::STATUS_COMPLETED)
            ->groupBy('artisan_id')->selectRaw('artisan_id, COUNT(*) as n')->pluck('n', 'artisan_id');

        $cancelledByArtisan = ServiceRequest::whereIn('artisan_id', $ids)->where('status', ServiceRequest::STATUS_CANCELLED)
            ->whereColumn('cancelled_by', 'artisan_id')
            ->groupBy('artisan_id')->selectRaw('artisan_id, COUNT(*) as n')->pluck('n', 'artisan_id');

        // Demandes reçues sur 30 jours. Une demande annulée par le client avant toute
        // réponse ne compte pas contre l'artisan.
        $receivedQuery = fn () => ServiceRequest::whereIn('artisan_id', $ids)
            ->where('created_at', '>=', $since)
            ->where(fn ($q) => $q->where('status', '!=', ServiceRequest::STATUS_CANCELLED)
                ->orWhereNotNull('responded_at')
                ->orWhereColumn('cancelled_by', 'artisan_id'));

        $received = $receivedQuery()->groupBy('artisan_id')->selectRaw('artisan_id, COUNT(*) as n')->pluck('n', 'artisan_id');

        // Une réponse = accepter ou refuser (responded_at), ou statut qui l'implique
        // (données antérieures à l'enregistrement de responded_at).
        $responded = $receivedQuery()
            ->where(fn ($q) => $q->whereNotNull('responded_at')
                ->orWhereIn('status', ['accepted', 'in_progress', 'awaiting_validation', 'completed', 'rejected']))
            ->groupBy('artisan_id')->selectRaw('artisan_id, COUNT(*) as n')->pluck('n', 'artisan_id');

        // Demandes publiées envoyées par le matching : comptent une fois répondues ou
        // le délai écoulé (en cours ou annulées par le client : ignorées).
        $dispatchStats = NeedDispatch::whereIn('artisan_id', $ids)
            ->where('sent_at', '>=', $since)
            ->whereIn('status', [NeedDispatch::STATUS_PROPOSED, NeedDispatch::STATUS_DECLINED, NeedDispatch::STATUS_EXPIRED])
            ->groupBy('artisan_id')
            ->selectRaw("artisan_id, COUNT(*) as received, SUM(CASE WHEN status IN ('proposed', 'declined') THEN 1 ELSE 0 END) as responded")
            ->get()->keyBy('artisan_id');
        foreach ($dispatchStats as $artisanId => $row) {
            $received[$artisanId] = (int) ($received[$artisanId] ?? 0) + (int) $row->received;
            $responded[$artisanId] = (int) ($responded[$artisanId] ?? 0) + (int) $row->responded;
        }

        // Prix habituel : moyenne des prix affichés de ses services actifs dans la catégorie.
        $prices = Service::whereIn('artisan_id', $ids)
            ->where('category_id', $need->category_id)
            ->where('status', 'active')
            ->where('pricing_type', '!=', 'quote')
            ->where('price', '>', 0)
            ->groupBy('artisan_id')->selectRaw('artisan_id, AVG(price) as avg_price')->pluck('avg_price', 'artisan_id');

        $communes = DB::table('artisan_communes')->whereIn('user_id', $ids)->get(['user_id', 'commune_id'])
            ->groupBy('user_id')->map(fn ($rows) => $rows->pluck('commune_id')->map(fn ($v) => (int) $v)->all());

        return [
            'platform_avg'         => $platformAvg !== null ? (float) $platformAvg : (float) config('matching.default_platform_avg'),
            'reviews'              => $reviews,
            'completed'            => $completed,
            'cancelled_by_artisan' => $cancelledByArtisan,
            'received'             => $received,
            'responded'            => $responded,
            'prices'               => $prices,
            'communes'             => $communes,
        ];
    }

    private function parseTime(?string $value, string $default): string
    {
        return is_string($value) && preg_match('/^([01]\d|2[0-3]):[0-5]\d/', $value) ? substr($value, 0, 5) : $default;
    }
}
