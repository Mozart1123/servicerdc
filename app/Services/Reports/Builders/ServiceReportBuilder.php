<?php

declare(strict_types=1);

namespace App\Services\Reports\Builders;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class ServiceReportBuilder implements ReportBuilderInterface
{
    public function getType(): string
    {
        return 'services';
    }

    public function getName(): string
    {
        return 'Rapport des Services';
    }

    public function build(array $filters = [], ?User $user = null): ReportData
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;
        $status = $filters['status'] ?? 'all';
        $categoryId = !empty($filters['category_id']) ? (int) $filters['category_id'] : null;
        $search = trim($filters['search'] ?? '');

        // Query des services avec sélection explicite
        $query = Service::query()
            ->select([
                'id',
                'artisan_id',
                'category_id',
                'title',
                'provider_name',
                'price',
                'pricing_type',
                'location',
                'city',
                'is_verified',
                'rating',
                'status',
                'created_at'
            ])
            ->with([
                'artisan:id,name,email,phone',
                'category:id,name'
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        if ($status !== 'all' && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('artisan', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $services = $query->latest('id')->get();

        // Calcul des KPIs
        $totalCount = $services->count();
        $activeCount = $services->where('status', 'active')->count();
        $inactiveCount = $services->where('status', 'inactive')->count();
        $verifiedCount = $services->where('is_verified', true)->count();

        // Prix moyen sur les services à prix numérique fixe
        $fixedPriceServices = $services->filter(fn ($s) => $s->pricing_type !== 'quote' && (float) $s->price > 0);
        $avgPrice = $fixedPriceServices->count() > 0 ? (float) $fixedPriceServices->avg('price') : 0.0;

        $kpis = [
            [
                'label' => 'Total Services',
                'value' => number_format($totalCount, 0, ',', ' '),
                'badge' => 'Catalogue',
                'color' => 'blue',
                'icon'  => 'fas fa-screwdriver-wrench'
            ],
            [
                'label' => 'Services Actifs',
                'value' => number_format($activeCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($activeCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'emerald',
                'icon'  => 'fas fa-circle-check'
            ],
            [
                'label' => 'Services Inactifs',
                'value' => number_format($inactiveCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($inactiveCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'amber',
                'icon'  => 'fas fa-circle-pause'
            ],
            [
                'label' => 'Vérifiés ProConnect',
                'value' => number_format($verifiedCount, 0, ',', ' '),
                'badge' => 'Certifiés',
                'color' => 'indigo',
                'icon'  => 'fas fa-shield-halved'
            ],
            [
                'label' => 'Prix Moyen (Fixe)',
                'value' => number_format($avgPrice, 0, ',', ' ') . ' CDF',
                'badge' => 'CDF',
                'color' => 'cyan',
                'icon'  => 'fas fa-coins'
            ]
        ];

        $headers = [
            'ID',
            'Service',
            'Artisan / Prestataire',
            'Catégorie',
            'Prix (CDF)',
            'Localisation',
            'Certification',
            'Statut',
            'Date Création'
        ];

        $rows = [];
        $rawRows = [];

        foreach ($services as $service) {
            $artisanName = $service->artisan?->name ?? $service->provider_name ?? 'Non assigné';
            $categoryName = $service->category?->name ?? 'Général';
            $loc = $service->city ?: ($service->location ?: 'Non précisé');

            // Formatage du prix en CDF
            if ($service->pricing_type === 'quote') {
                $priceFormatted = 'Sur devis';
            } elseif ($service->pricing_type === 'starting_from') {
                $priceFormatted = 'À partir de ' . number_format((float) ($service->price ?? 0), 0, ',', ' ') . ' CDF';
            } else {
                $priceFormatted = number_format((float) ($service->price ?? 0), 0, ',', ' ') . ' CDF';
            }

            $cert = $service->is_verified ? 'Vérifié' : 'Standard';
            $statusLabel = $service->status === 'active' ? 'Actif' : 'Inactif';
            $dateFormatted = $service->created_at ? $service->created_at->format('d/m/Y') : '-';

            $rows[] = [
                '#' . $service->id,
                $service->title,
                $artisanName,
                $categoryName,
                $priceFormatted,
                $loc,
                $cert,
                $statusLabel,
                $dateFormatted
            ];

            $rawRows[] = [
                'id' => $service->id,
                'title' => $service->title,
                'artisan' => $artisanName,
                'category' => $categoryName,
                'price' => $priceFormatted,
                'location' => $loc,
                'is_verified' => $service->is_verified,
                'status' => $service->status,
                'created_at' => $dateFormatted
            ];
        }

        // Période
        $periodLabel = 'Toutes dates confondues';
        if ($dateFrom && $dateTo) {
            $periodLabel = 'Du ' . $dateFrom->format('d/m/Y') . ' au ' . $dateTo->format('d/m/Y');
        } elseif ($dateFrom) {
            $periodLabel = 'À partir du ' . $dateFrom->format('d/m/Y');
        } elseif ($dateTo) {
            $periodLabel = "Jusqu'au " . $dateTo->format('d/m/Y');
        }

        return new ReportData(
            type: 'services',
            title: 'Rapport d\'Activité des Services',
            subtitle: 'Vue d\'ensemble du catalogue des prestations et compétences enregistrées',
            periodLabel: $periodLabel,
            generatedAt: now()->format('d/m/Y H:i'),
            generatedBy: $user?->name ?? 'Administration ProConnect',
            filters: [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'status' => $status,
                'category_id' => $categoryId,
                'search' => $search
            ],
            kpis: $kpis,
            headers: $headers,
            rows: $rows,
            rawRows: $rawRows,
            summary: [
                'total_services' => $totalCount,
                'active_services' => $activeCount,
                'inactive_services' => $inactiveCount,
                'verified_services' => $verifiedCount,
                'average_price_cdf' => $avgPrice
            ]
        );
    }
}
