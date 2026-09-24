<?php

declare(strict_types=1);

namespace App\Services\Reports\Builders;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\User;
use Carbon\Carbon;

class UserReportBuilder implements ReportBuilderInterface
{
    public function getType(): string
    {
        return 'users';
    }

    public function getName(): string
    {
        return 'Rapport des Utilisateurs';
    }

    public function build(array $filters = [], ?User $user = null): ReportData
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;
        $userType = $filters['user_type'] ?? 'all';
        $role = $filters['role'] ?? 'all';
        $status = $filters['status'] ?? 'all';
        $search = trim($filters['search'] ?? '');

        // Sélection STRICTEMENT sécurisée : AUCUN mot de passe, token ou identifiant OAuth
        $query = User::query()
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'user_type',
                'role',
                'status',
                'province',
                'city',
                'email_verified_at',
                'created_at'
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        if ($userType !== 'all') {
            $query->where('user_type', $userType);
        }
        if ($role !== 'all') {
            $query->where('role', $role);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('id')->get();

        // Calcul des KPIs
        $totalCount = $users->count();
        $artisansCount = $users->filter(fn ($u) => in_array($u->user_type, ['artisan']) || $u->role === 'artisan')->count();
        $clientsCount = $users->filter(fn ($u) => in_array($u->user_type, ['client']) || $u->role === 'user')->count();
        $activeCount = $users->where('status', 'active')->count();
        $suspendedCount = $users->where('status', 'suspended')->count();

        $kpis = [
            [
                'label' => 'Total Utilisateurs',
                'value' => number_format($totalCount, 0, ',', ' '),
                'badge' => 'Communauté',
                'color' => 'blue',
                'icon'  => 'fas fa-users'
            ],
            [
                'label' => 'Artisans',
                'value' => number_format($artisansCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($artisansCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'amber',
                'icon'  => 'fas fa-hammer'
            ],
            [
                'label' => 'Clients',
                'value' => number_format($clientsCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($clientsCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'cyan',
                'icon'  => 'fas fa-user'
            ],
            [
                'label' => 'Comptes Actifs',
                'value' => number_format($activeCount, 0, ',', ' '),
                'badge' => $totalCount > 0 ? round(($activeCount / $totalCount) * 100) . '%' : '0%',
                'color' => 'emerald',
                'icon'  => 'fas fa-user-check'
            ],
            [
                'label' => 'Suspendus / Bloqués',
                'value' => number_format($suspendedCount, 0, ',', ' '),
                'badge' => 'Modération',
                'color' => 'rose',
                'icon'  => 'fas fa-user-slash'
            ]
        ];

        $headers = [
            'ID',
            'Nom & Prénom',
            'Email',
            'Téléphone',
            'Type Profil',
            'Rôle Système',
            'Localisation',
            'Statut',
            'Date Inscription'
        ];

        $rows = [];
        $rawRows = [];

        foreach ($users as $u) {
            $userTypeLabels = [
                'artisan' => 'Artisan',
                'client' => 'Client',
                'recruiter' => 'Recruteur',
                'job_seeker' => 'Demandeur d\'emploi',
            ];
            $typeLabel = $userTypeLabels[$u->user_type] ?? ucfirst($u->user_type ?? 'Standard');

            $roleLabels = [
                'super_admin' => 'Super Admin',
                'admin' => 'Admin',
                'user' => 'Utilisateur',
                'artisan' => 'Artisan'
            ];
            $roleLabel = $roleLabels[$u->role] ?? ucfirst($u->role ?? 'User');

            $loc = trim(($u->city ?? '') . ' ' . ($u->province ? '(' . $u->province . ')' : ''));
            if ($loc === '') {
                $loc = 'Non renseigné';
            }

            $statusLabels = [
                'active' => 'Actif',
                'suspended' => 'Suspendu',
                'pending' => 'En attente',
            ];
            $statusLabel = $statusLabels[$u->status] ?? ucfirst($u->status ?? 'Inconnu');

            $dateFormatted = $u->created_at ? $u->created_at->format('d/m/Y') : '-';

            $rows[] = [
                '#' . $u->id,
                $u->name,
                $u->email,
                $u->phone ?: 'Non renseigné',
                $typeLabel,
                $roleLabel,
                $loc,
                $statusLabel,
                $dateFormatted
            ];

            $rawRows[] = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'user_type' => $typeLabel,
                'role' => $roleLabel,
                'location' => $loc,
                'status' => $u->status,
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
            type: 'users',
            title: 'Rapport des Utilisateurs & Communauté',
            subtitle: 'Registre consolidé des comptes utilisateurs, profils et statuts de vérification',
            periodLabel: $periodLabel,
            generatedAt: now()->format('d/m/Y H:i'),
            generatedBy: $user?->name ?? 'Administration ProConnect',
            filters: [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'user_type' => $userType,
                'role' => $role,
                'status' => $status,
                'search' => $search
            ],
            kpis: $kpis,
            headers: $headers,
            rows: $rows,
            rawRows: $rawRows,
            summary: [
                'total_users' => $totalCount,
                'artisans_count' => $artisansCount,
                'clients_count' => $clientsCount,
                'active_count' => $activeCount,
                'suspended_count' => $suspendedCount
            ]
        );
    }
}
