<?php

declare(strict_types=1);

namespace App\Services\Reports\Builders;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;

class TransactionReportBuilder implements ReportBuilderInterface
{
    public function getType(): string
    {
        return 'transactions';
    }

    public function getName(): string
    {
        return 'Rapport Financier & Transactions';
    }

    public function build(array $filters = [], ?User $user = null): ReportData
    {
        // Restriction stricte au super_admin
        if (!$user || !$user->isSuperAdmin()) {
            throw new AuthorizationException('Accès interdit. Le rapport financier est strictement réservé au Super Administrateur.');
        }

        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;
        $status = $filters['status'] ?? 'all';
        $operationType = $filters['operation_type'] ?? 'all';
        $search = trim($filters['search'] ?? '');

        // Sélection STRICTEMENT sécurisée : exclusion des tokens bancaires et payloads bruts
        $query = Transaction::query()
            ->select([
                'id',
                'reference_id',
                'kpay_reference',
                'user_id',
                'mission_id',
                'type',
                'amount',
                'currency',
                'status',
                'description',
                'created_at'
            ])
            ->with([
                'user:id,name,email,phone',
                'mission:id,title'
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        if ($status !== 'all' && in_array($status, ['succeeded', 'pending', 'failed', 'refunded'], true)) {
            $query->where('status', $status);
        }
        if ($operationType !== 'all' && in_array($operationType, ['deposit', 'payout', 'refund', 'subscription'], true)) {
            $query->where('type', $operationType);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('kpay_reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $transactions = $query->latest('id')->get();

        // Calcul des KPIs
        $totalCount = $transactions->count();
        $succeededCount = $transactions->where('status', 'succeeded')->count();
        $pendingCount = $transactions->where('status', 'pending')->count();
        $failedCount = $transactions->where('status', 'failed')->count();
        $payoutCount = $transactions->where('type', 'payout')->count();
        $subCount = $transactions->where('type', 'subscription')->count();

        $successRate = $totalCount > 0 ? round(($succeededCount / $totalCount) * 100, 1) : 0.0;
        $totalVolume = (float) $transactions->where('status', 'succeeded')->sum('amount');

        $kpis = [
            [
                'label' => 'Total Transactions',
                'value' => number_format($totalCount, 0, ',', ' '),
                'badge' => 'Volume Global',
                'color' => 'blue',
                'icon'  => 'fas fa-money-bill-transfer'
            ],
            [
                'label' => 'Volume Validé',
                'value' => number_format($totalVolume, 2, ',', ' ') . ' $',
                'badge' => 'Encaissé / Validé',
                'color' => 'emerald',
                'icon'  => 'fas fa-vault'
            ],
            [
                'label' => 'Taux de Succès',
                'value' => $successRate . ' %',
                'badge' => $succeededCount . ' réussies',
                'color' => 'indigo',
                'icon'  => 'fas fa-circle-check'
            ],
            [
                'label' => 'En Attente',
                'value' => number_format($pendingCount, 0, ',', ' '),
                'badge' => 'À surveiller',
                'color' => 'amber',
                'icon'  => 'fas fa-clock'
            ],
            [
                'label' => 'Versements Artisans',
                'value' => number_format($payoutCount, 0, ',', ' '),
                'badge' => 'Payouts',
                'color' => 'cyan',
                'icon'  => 'fas fa-hand-holding-dollar'
            ]
        ];

        $headers = [
            'ID / Réf',
            'Date & Heure',
            'Client / Artisan',
            'Type Opération',
            'Montant',
            'Devise',
            'Réf. Externe / K-PAY',
            'Statut'
        ];

        $rows = [];
        $rawRows = [];

        foreach ($transactions as $tx) {
            $ref = $tx->reference_id ?? ('#TRX-' . str_pad((string) $tx->id, 5, '0', STR_PAD_LEFT));
            $userName = $tx->user?->name ?? 'Système / Anonyme';
            $typeLabel = match ($tx->type) {
                'deposit'      => 'Dépôt',
                'payout'       => 'Versement (Payout)',
                'refund'       => 'Remboursement',
                'subscription' => 'Abonnement Pro',
                default        => ucfirst((string) $tx->type),
            };

            $currency = strtoupper($tx->currency ?: 'USD');
            $amountFormatted = number_format((float) $tx->amount, 2, ',', ' ') . ' ' . $currency;
            $kpayRef = $tx->kpay_reference ?: '-';

            $statusLabel = match ($tx->status) {
                'succeeded' => 'Validé',
                'pending'   => 'En attente',
                'failed'    => 'Échoué',
                'refunded'  => 'Remboursé',
                default     => ucfirst((string) $tx->status),
            };

            $dateFormatted = $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-';

            $rows[] = [
                $ref,
                $dateFormatted,
                $userName,
                $typeLabel,
                $amountFormatted,
                $currency,
                $kpayRef,
                $statusLabel
            ];

            $rawRows[] = [
                'id' => $tx->id,
                'reference' => $ref,
                'created_at' => $dateFormatted,
                'user' => $userName,
                'type' => $typeLabel,
                'amount' => $amountFormatted,
                'currency' => $currency,
                'kpay_reference' => $kpayRef,
                'status' => $statusLabel
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
            type: 'transactions',
            title: 'Rapport Financier & Audit des Flux',
            subtitle: 'Journal consolidé des transactions, encaissements, commissions et reversements de la plateforme',
            periodLabel: $periodLabel,
            generatedAt: now()->format('d/m/Y H:i'),
            generatedBy: $user->name,
            filters: [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'status' => $status,
                'operation_type' => $operationType,
                'search' => $search
            ],
            kpis: $kpis,
            headers: $headers,
            rows: $rows,
            rawRows: $rawRows,
            summary: [
                'total_count' => $totalCount,
                'succeeded_count' => $succeededCount,
                'pending_count' => $pendingCount,
                'failed_count' => $failedCount,
                'payout_count' => $payoutCount,
                'subscription_count' => $subCount,
                'total_volume' => $totalVolume,
                'success_rate' => $successRate
            ]
        );
    }
}
