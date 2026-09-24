<?php

declare(strict_types=1);

namespace App\Services\Reports\Builders;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;

class ReviewReportBuilder implements ReportBuilderInterface
{
    public function getType(): string
    {
        return 'reviews';
    }

    public function getName(): string
    {
        return 'Rapport de Modération (Avis)';
    }

    public function build(array $filters = [], ?User $user = null): ReportData
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;
        $status = $filters['status'] ?? 'all';
        $rating = !empty($filters['rating']) && $filters['rating'] !== 'all' ? (int) $filters['rating'] : null;
        $search = trim($filters['search'] ?? '');

        // Sélection explicite sécurisée
        $query = Review::query()
            ->select([
                'id',
                'mission_id',
                'client_id',
                'artisan_id',
                'rating',
                'feedback',
                'status',
                'rejection_reason',
                'created_at'
            ])
            ->with([
                'client:id,name,email',
                'artisan:id,name,email',
                'mission:id,title'
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }
        if ($rating !== null) {
            $query->where('rating', $rating);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('feedback', 'like', "%{$search}%")
                  ->orWhere('rejection_reason', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('artisan', fn($aq) => $aq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('mission', fn($mq) => $mq->where('title', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->latest('id')->get();

        // Calcul des KPIs
        $totalCount = $reviews->count();
        $pendingCount = $reviews->where('status', 'pending')->count();
        $approvedCount = $reviews->where('status', 'approved')->count();
        $rejectedCount = $reviews->where('status', 'rejected')->count();
        $avgRating = $totalCount > 0 ? round((float) $reviews->avg('rating'), 1) : 0.0;

        $kpis = [
            [
                'label' => 'Total Avis Déposés',
                'value' => number_format($totalCount, 0, ',', ' '),
                'badge' => 'Tous retours',
                'color' => 'blue',
                'icon'  => 'fas fa-comments'
            ],
            [
                'label' => 'En Attente Modération',
                'value' => number_format($pendingCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($pendingCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'amber',
                'icon'  => 'fas fa-hourglass-half'
            ],
            [
                'label' => 'Avis Approuvés',
                'value' => number_format($approvedCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($approvedCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'emerald',
                'icon'  => 'fas fa-circle-check'
            ],
            [
                'label' => 'Avis Rejetés',
                'value' => number_format($rejectedCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($rejectedCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'rose',
                'icon'  => 'fas fa-shield-xmark'
            ],
            [
                'label' => 'Note Moyenne Globale',
                'value' => $avgRating . ' / 5',
                'badge' => 'Score Satisfaction',
                'color' => 'cyan',
                'icon'  => 'fas fa-star'
            ]
        ];

        $headers = [
            'ID',
            'Date Déposition',
            'Client (Auteur)',
            'Artisan Évalué',
            'Mission',
            'Note',
            'Commentaire Client',
            'Statut Modération',
            'Motif Rejet'
        ];

        $rows = [];
        $rawRows = [];

        foreach ($reviews as $review) {
            $clientName = $review->client?->name ?? 'Client anonyme';
            $artisanName = $review->artisan?->name ?? 'Artisan non assigné';
            $missionTitle = $review->mission?->title ?? 'Prestation générale';
            $starDisplay = $review->rating ? "{$review->rating}/5" : 'Sans note';

            $statusLabel = match ($review->status) {
                'pending'  => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default    => ucfirst((string) $review->status),
            };

            $feedbackSnippet = $review->feedback ? (mb_strlen($review->feedback) > 70 ? mb_substr($review->feedback, 0, 67) . '...' : $review->feedback) : '-';
            $rejectionReason = $review->rejection_reason ?: '-';
            $createdAt = $review->created_at ? $review->created_at->format('d/m/Y H:i') : '-';

            $rows[] = [
                '#REV-' . str_pad((string) $review->id, 4, '0', STR_PAD_LEFT),
                $createdAt,
                $clientName,
                $artisanName,
                $missionTitle,
                $starDisplay,
                $feedbackSnippet,
                $statusLabel,
                $rejectionReason
            ];

            $rawRows[] = [
                'id' => $review->id,
                'created_at' => $createdAt,
                'client' => $clientName,
                'artisan' => $artisanName,
                'mission' => $missionTitle,
                'rating' => $review->rating,
                'feedback' => $review->feedback,
                'status' => $statusLabel,
                'rejection_reason' => $review->rejection_reason
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
            type: 'reviews',
            title: 'Rapport de Modération & Qualité des Avis',
            subtitle: 'Surveillance des avis clients, scores de satisfaction des artisans et décisions de conformité',
            periodLabel: $periodLabel,
            generatedAt: now()->format('d/m/Y H:i'),
            generatedBy: $user?->name ?? 'Administration ProConnect',
            filters: [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'status' => $status,
                'rating' => $rating,
                'search' => $search
            ],
            kpis: $kpis,
            headers: $headers,
            rows: $rows,
            rawRows: $rawRows,
            summary: [
                'total_reviews' => $totalCount,
                'pending_reviews' => $pendingCount,
                'approved_reviews' => $approvedCount,
                'rejected_reviews' => $rejectedCount,
                'average_rating' => $avgRating
            ]
        );
    }
}
