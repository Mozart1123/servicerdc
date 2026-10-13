<?php

namespace App\Services\Matching;

use App\Models\ArtisanRealisation;
use App\Models\NeedProposal;
use App\Models\ServiceRequest;
use Illuminate\Support\Collection;

/**
 * Prépare les propositions d'une demande publiée sous forme de cartes comparables
 * et désigne la meilleure ("Recommandé pour toi") avec sa raison (§2.5).
 *
 * Valeur d'une proposition = score de matching de l'artisan (sur 100, note, proximité,
 * fiabilité…) + jusqu'à 30 points pour le prix (le moins cher en a 30, les autres au
 * prorata ; une visite sans devis, donc sans prix, en a 15).
 */
class ProposalComparator
{
    private const PRICE_POINTS = 30;

    /**
     * @return array{cards: Collection<int, array>, recommendedId: ?int, reason: ?string}
     */
    public function compare(ServiceRequest $need): array
    {
        $proposals = $need->proposals()
            ->whereIn('status', NeedProposal::ACTIVE_STATUSES)
            ->with(['artisan.artisanLevel', 'artisan.services.category', 'dispatch'])
            ->oldest()
            ->get();

        if ($proposals->isEmpty()) {
            return ['cards' => collect(), 'recommendedId' => null, 'reason' => null];
        }

        $realisations = ArtisanRealisation::whereIn('artisan_id', $proposals->pluck('artisan_id'))
            ->latest()->get()->groupBy('artisan_id');

        $cards = $proposals->map(function (NeedProposal $p) use ($realisations) {
            $artisan = $p->artisan;
            $rating = $artisan->rating_summary;

            return [
                'proposal'     => $p,
                'artisan'      => $artisan,
                'profession'   => $artisan->main_profession,
                'rating'       => $rating,
                'verified'     => $artisan->isVerified(),
                'responseBadge'=> $artisan->response_time_badge,
                'proximity'    => $p->dispatch?->proximity,
                'score'        => (float) ($p->dispatch?->score ?? 0),
                'total'        => $p->total,
                'realisations' => ($realisations[$artisan->id] ?? collect())->take(3),
            ];
        })->values();

        [$recommendedId, $reason] = $this->recommend($cards);

        return ['cards' => $cards, 'recommendedId' => $recommendedId, 'reason' => $reason];
    }

    /** @return array{0: ?int, 1: ?string} */
    private function recommend(Collection $cards): array
    {
        // Une seule proposition : rien à comparer.
        if ($cards->count() < 2) {
            return [null, null];
        }

        $prices = $cards->pluck('total')->filter(fn ($t) => $t !== null && $t > 0);
        $minPrice = $prices->min();

        $best = $cards->sortByDesc(function (array $c) use ($minPrice) {
            $pricePoints = ($c['total'] && $minPrice) ? self::PRICE_POINTS * $minPrice / $c['total'] : self::PRICE_POINTS / 2;
            return $c['score'] + $pricePoints;
        })->first();

        $reasons = [];
        $rated = $cards->filter(fn ($c) => !$c['rating']['is_new']);
        if (!$best['rating']['is_new'] && $rated->count() > 0
            && $best['rating']['average'] >= $rated->max(fn ($c) => $c['rating']['average'])) {
            $reasons[] = 'le mieux noté (' . $best['rating']['average_formatted'] . '/5, ' . $best['rating']['count'] . ' avis)';
        }
        if ($prices->count() >= 2 && $best['total'] !== null && (float) $best['total'] === (float) $minPrice) {
            $reasons[] = 'le prix le plus bas';
        }
        if ($best['proximity'] === ArtisanMatcher::SAME_COMMUNE && $cards->contains(fn ($c) => $c['proximity'] !== ArtisanMatcher::SAME_COMMUNE)) {
            $reasons[] = 'le plus proche de chez vous';
        }

        $reason = $reasons
            ? ucfirst(implode(' et ', $reasons)) . '.'
            : 'Le meilleur équilibre entre la note, le prix et la proximité.';

        return [$best['proposal']->id, $reason];
    }
}
