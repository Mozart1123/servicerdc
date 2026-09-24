<?php

declare(strict_types=1);

namespace App\DTO;

class ReportData
{
    /**
     * @param string $type Code du type de rapport (services, users, etc.)
     * @param string $title Titre principal du rapport
     * @param string $subtitle Sous-titre explicatif
     * @param string $periodLabel Libellé de la période (ex: "Du 01/09/2026 au 23/09/2026")
     * @param string $generatedAt Date et heure de génération
     * @param string $generatedBy Nom de l'utilisateur ayant généré le rapport
     * @param array $filters Filtres appliqués (date_from, date_to, status, etc.)
     * @param array $kpis Liste des cartes KPI ['label', 'value', 'badge', 'color', 'icon']
     * @param array $headers Liste des libellés d'en-tête de colonnes
     * @param array $rows Tableau des lignes de données (chaque ligne est une liste de valeurs formatées)
     * @param array $rawRows Lignes brutes pour affichage spécifique si nécessaire
     * @param array $summary Informations de résumé ou totaux
     */
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly string $periodLabel,
        public readonly string $generatedAt,
        public readonly string $generatedBy,
        public readonly array $filters,
        public readonly array $kpis,
        public readonly array $headers,
        public readonly array $rows,
        public readonly array $rawRows = [],
        public readonly array $summary = []
    ) {}

    public function rowCount(): int
    {
        return count($this->rows);
    }
}
