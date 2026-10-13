<?php

namespace App\Services\Matching;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Pondérations du score de matching, réglables dans l'admin.
 * Stockées dans la table settings (clés "matching.weight.<critère>").
 */
class MatchingSettings
{
    private const CACHE_KEY = 'matching.weights';

    public const LABELS = [
        'rating'         => 'Note et avis',
        'proximity'      => 'Proximité',
        'budget'         => 'Compatibilité avec le budget',
        'responsiveness' => 'Réactivité',
        'reliability'    => 'Fiabilité',
        'newcomer'       => 'Bonus nouvel artisan',
    ];

    /** @return array<string,int> Pondérations valides (entiers 0–100, total = 100). */
    public function weights(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            $defaults = $this->defaults();
            $stored = Setting::where('key', 'like', 'matching.weight.%')->pluck('value', 'key');

            $weights = [];
            foreach ($defaults as $criterion => $default) {
                $value = $stored['matching.weight.' . $criterion] ?? null;
                // La table settings est aussi écrite par un formulaire générique :
                // toute valeur non entière ou hors bornes retombe sur le défaut.
                $weights[$criterion] = is_numeric($value) && (int) $value == $value && $value >= 0 && $value <= 100
                    ? (int) $value
                    : $default;
            }

            return array_sum($weights) === 100 ? $weights : $defaults;
        });
    }

    /** @param array<string,int> $weights */
    public function save(array $weights): void
    {
        foreach (array_keys($this->defaults()) as $criterion) {
            Setting::updateOrCreate(['key' => 'matching.weight.' . $criterion], ['value' => (string) (int) $weights[$criterion]]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string,int> */
    public function defaults(): array
    {
        return array_map('intval', config('matching.weights'));
    }
}
