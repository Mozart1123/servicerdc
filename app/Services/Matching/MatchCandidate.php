<?php

namespace App\Services\Matching;

use App\Models\User;

/** Un artisan retenu par le matching, avec son score et le détail par critère. */
final class MatchCandidate
{
    /** @param array<string,float> $breakdown points par critère (clés de MatchingSettings::LABELS) */
    public function __construct(
        public readonly User $artisan,
        public readonly float $score,
        public readonly array $breakdown,
        public readonly string $proximity,
        public readonly float $adjustedRating,
        public readonly int $reviewCount,
        public readonly ?float $usualPrice,
        public readonly int $completedMissions,
    ) {
    }
}
