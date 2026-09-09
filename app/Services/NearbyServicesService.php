<?php

namespace App\Services;

use App\Models\User;

/**
 * Calcule, pour une ville donnée, le nombre réel d'artisans actifs
 * proposant l'un des 3 métiers mis en avant sur la page d'accueil
 * (widget "Services à proximité" du hero). Alimente à la fois le rendu
 * initial de la page (ville par défaut, calculée côté serveur) et
 * l'endpoint AJAX appelé après une géolocalisation réelle du visiteur.
 *
 * Les 3 métiers sont retrouvés via une recherche flexible sur
 * categories.name (ex. "lectric" matche "Électricité" ET "Électricien",
 * accent ou pas) plutôt qu'un category_id figé, pour éviter de casser
 * si les catégories sont renommées depuis l'admin. Si aucune catégorie
 * ne matche un terme, le compteur correspondant est simplement 0 — pas
 * de valeur inventée.
 */
class NearbyServicesService
{
    private const HIGHLIGHTED = [
        'electriciens' => ['label' => 'Électriciens', 'term' => 'lectric'],
        'plombiers'    => ['label' => 'Plombiers',    'term' => 'plomb'],
        'couturiers'   => ['label' => 'Couturiers',   'term' => 'coutur'],
    ];

    /**
     * @return array<string, array{label: string, count: int}>
     */
    public function countByCity(?string $city): array
    {
        $result = [];

        foreach (self::HIGHLIGHTED as $key => $meta) {
            $query = User::where('user_type', 'artisan')
                ->where('status', 'active')
                ->whereHas('services', function ($sq) use ($meta) {
                    $sq->whereHas('category', function ($cq) use ($meta) {
                        $cq->where('name', 'like', '%' . $meta['term'] . '%');
                    });
                });

            if ($city !== null && trim($city) !== '') {
                $query->where('city', 'like', '%' . trim($city) . '%');
            }

            $result[$key] = [
                'label' => $meta['label'],
                'count' => $query->count(),
            ];
        }

        return $result;
    }
}
