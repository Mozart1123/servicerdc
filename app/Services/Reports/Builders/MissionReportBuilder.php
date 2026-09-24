<?php

declare(strict_types=1);

namespace App\Services\Reports\Builders;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\Mission;
use App\Models\User;
use Carbon\Carbon;

class MissionReportBuilder implements ReportBuilderInterface
{
    public function getType(): string
    {
        return 'missions';
    }

    public function getName(): string
    {
        return 'Rapport des Missions';
    }

    public function build(array $filters = [], ?User $user = null): ReportData
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;
        $status = $filters['status'] ?? 'all';
        $search = trim($filters['search'] ?? '');

        // Sélection explicite sécurisée — aucun token ou champ technique interne
        $query = Mission::query()
            ->select([
                'id',
                'title',
                'client_id',
                'artisan_id',
                'service_id',
                'status',
                'amount',
                'commission_amount',
                'commission_status',
                'payout_status',
                'start_date',
                'end_date',
                'created_at'
            ])
            ->with([
                'client:id,name,email,phone',
                'artisan:id,name,email,phone',
                'service:id,title'
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        if ($status !== 'all' && in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('artisan', fn($aq) => $aq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('service', fn($sq) => $sq->where('title', 'like', "%{$search}%"));
            });
        }

        $missions = $query->latest('id')->get();

        // Calcul des KPIs pertinents
        $totalCount = $missions->count();
        $inProgressCount = $missions->where('status', 'in_progress')->count();
        $completedCount = $missions->where('status', 'completed')->count();
        $cancelledCount = $missions->where('status', 'cancelled')->count();
        $pendingCount = $missions->where('status', 'pending')->count();

        $totalAmount = (float) $missions->sum('amount');
        $totalCommission = (float) $missions->sum('commission_amount');

        $kpis = [
            [
                'label' => 'Total Missions',
                'value' => number_format($totalCount, 0, ',', ' '),
                'badge' => 'Volume Global',
                'color' => 'blue',
                'icon'  => 'fas fa-briefcase'
            ],
            [
                'label' => 'Missions en Cours',
                'value' => number_format($inProgressCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($inProgressCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'amber',
                'icon'  => 'fas fa-spinner'
            ],
            [
                'label' => 'Missions Clôturées',
                'value' => number_format($completedCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'emerald',
                'icon'  => 'fas fa-check-double'
            ],
            [
                'label' => 'Missions Annulées',
                'value' => number_format($cancelledCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($cancelledCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'rose',
                'icon'  => 'fas fa-ban'
            ],
            [
                'label' => 'Commissions HQ',
                'value' => number_format($totalCommission, 0, ',', ' ') . ' CDF',
                'badge' => 'Revenus Plateforme',
                'color' => 'indigo',
                'icon'  => 'fas fa-hand-holding-dollar'
            ]
        ];

        $headers = [
            'ID',
            'Mission',
            'Client',
            'Artisan',
            'Service Lié',
            'Montant (CDF)',
            'Commission HQ',
            'Statut',
            'Date Début',
            'Date Création'
        ];

        $rows = [];
        $rawRows = [];

        foreach ($missions as $mission) {
            $clientName = $mission->client?->name ?? 'N/A';
            $artisanName = $mission->artisan?->name ?? 'Non assigné';
            $serviceTitle = $mission->service?->title ?? 'Prestation directe';
            $amountFormatted = number_format((float) ($mission->amount ?? 0), 0, ',', ' ') . ' CDF';
            $commissionFormatted = number_format((float) ($mission->commission_amount ?? 0), 0, ',', ' ') . ' CDF';

            $statusLabel = match ($mission->status) {
                'pending'     => 'En attente',
                'in_progress' => 'En cours',
                'completed'   => 'Complétée',
                'cancelled'   => 'Annulée',
                default       => ucfirst((string) $mission->status),
            };

            $startDateFormatted = $mission->start_date ? $mission->start_date->format('d/m/Y') : '-';
            $createdAtFormatted = $mission->created_at ? $mission->created_at->format('d/m/Y') : '-';

            $rows[] = [
                '#MIS-' . str_pad((string) $mission->id, 4, '0', STR_PAD_LEFT),
                $mission->title,
                $clientName,
                $artisanName,
                $serviceTitle,
                $amountFormatted,
                $commissionFormatted,
                $statusLabel,
                $startDateFormatted,
                $createdAtFormatted
            ];

            $rawRows[] = [
                'id' => $mission->id,
                'title' => $mission->title,
                'client' => $clientName,
                'artisan' => $artisanName,
                'service' => $serviceTitle,
                'amount' => $amountFormatted,
                'commission' => $commissionFormatted,
                'status' => $statusLabel,
                'start_date' => $startDateFormatted,
                'created_at' => $createdAtFormatted
            ];
        }

        // Période couverte
        $periodLabel = 'Toutes dates confondues';
        if ($dateFrom && $dateTo) {
            $periodLabel = 'Du ' . $dateFrom->format('d/m/Y') . ' au ' . $dateTo->format('d/m/Y');
        } elseif ($dateFrom) {
            $periodLabel = 'À partir du ' . $dateFrom->format('d/m/Y');
        } elseif ($dateTo) {
            $periodLabel = "Jusqu'au " . $dateTo->format('d/m/Y');
        }

        return new ReportData(
            type: 'missions',
            title: 'Rapport d\'Activité des Missions',
            subtitle: 'Suivi opérationnel des prestations commandées, interventions et commissions de la plateforme',
            periodLabel: $periodLabel,
            generatedAt: now()->format('d/m/Y H:i'),
            generatedBy: $user?->name ?? 'Administration ProConnect',
            filters: [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'status' => $status,
                'search' => $search
            ],
            kpis: $kpis,
            headers: $headers,
            rows: $rows,
            rawRows: $rawRows,
            summary: [
                'total_missions' => $totalCount,
                'in_progress' => $inProgressCount,
                'completed' => $completedCount,
                'cancelled' => $cancelledCount,
                'pending' => $pendingCount,
                'total_amount' => $totalAmount,
                'total_commission' => $totalCommission
            ]
        );
    }
}
