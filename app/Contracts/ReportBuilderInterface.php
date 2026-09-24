<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\ReportData;
use App\Models\User;

interface ReportBuilderInterface
{
    /**
     * Type de rapport géré (ex: 'services', 'users', 'missions').
     */
    public function getType(): string;

    /**
     * Libellé humain du type de rapport.
     */
    public function getName(): string;

    /**
     * Construit et retourne l'objet ReportData à partir des filtres donnés.
     *
     * @param array $filters Filtres (date_from, date_to, status, etc.)
     * @param User|null $user Utilisateur demandeur
     * @return ReportData
     */
    public function build(array $filters = [], ?User $user = null): ReportData;
}
