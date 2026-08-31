<?php

namespace App\Services;

use App\Models\JobOffer;
use App\Models\Mission;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Calcule, pour chacune des 26 provinces de la RDC, la présence
 * d'utilisateurs (par rôle) et le volume d'usage réel du système
 * (missions, demandes de service, offres d'emploi) — utilisé par la
 * page carte (/admin/settings/geo) et les statistiques (/admin/stats).
 *
 * Attribution géographique : aucune mission, demande de service ou
 * offre d'emploi n'a de champ de localisation géocodé propre. On
 * utilise donc la province déclarée par l'utilisateur qui en est à
 * l'origine comme proxy :
 *   - mission / demande de service → province du CLIENT (le demandeur)
 *   - offre d'emploi → province du RECRUTEUR (employer_id) qui l'a publiée
 * Les offres créées avant l'ajout de la colonne employer_id (nullable,
 * migration du 2026-01-12) ne sont pas comptées dans le volume d'usage —
 * seule la présence de l'utilisateur reste inchangée pour elles.
 */
class ProvinceStatsService
{
    /**
     * @return array<int, array{
     *   name: string, is_active: bool, user_count: int,
     *   users_by_type: array{client:int, artisan:int, job_seeker:int, recruiter:int},
     *   missions_count: int, service_requests_count: int, job_offers_count: int,
     *   usage_count: int,
     * }>
     */
    public function compute(): array
    {
        $provinces = config('drc.provinces');

        $usersByProvinceAndType = User::query()
            ->whereNotNull('province')
            ->select('province', 'user_type', DB::raw('count(*) as total'))
            ->groupBy('province', 'user_type')
            ->get()
            ->groupBy(fn ($row) => $this->normalize($row->province));

        $missionsByClientProvince = Mission::query()
            ->join('users', 'users.id', '=', 'missions.client_id')
            ->whereNotNull('users.province')
            ->select('users.province', DB::raw('count(*) as total'))
            ->groupBy('users.province')
            ->pluck('total', 'users.province');

        $serviceRequestsByProvince = ServiceRequest::query()
            ->join('users', 'users.id', '=', 'service_requests.user_id')
            ->whereNotNull('users.province')
            ->select('users.province', DB::raw('count(*) as total'))
            ->groupBy('users.province')
            ->pluck('total', 'users.province');

        $jobOffersByProvince = JobOffer::query()
            ->join('users', 'users.id', '=', 'job_offers.employer_id')
            ->whereNotNull('users.province')
            ->select('users.province', DB::raw('count(*) as total'))
            ->groupBy('users.province')
            ->pluck('total', 'users.province');

        $result = [];

        foreach ($provinces as $name) {
            $key = $this->normalize($name);

            $typeCounts = [
                'client'     => 0,
                'artisan'    => 0,
                'job_seeker' => 0,
                'recruiter'  => 0,
            ];

            foreach ($usersByProvinceAndType->get($key, collect()) as $row) {
                if (array_key_exists($row->user_type, $typeCounts)) {
                    $typeCounts[$row->user_type] = (int) $row->total;
                }
            }

            $userCount            = array_sum($typeCounts);
            $missionsCount        = $this->lookup($missionsByClientProvince, $name);
            $serviceRequestsCount = $this->lookup($serviceRequestsByProvince, $name);
            $jobOffersCount       = $this->lookup($jobOffersByProvince, $name);

            $result[] = [
                'name'                   => $name,
                'is_active'              => $userCount > 0,
                'user_count'             => $userCount,
                'users_by_type'          => $typeCounts,
                'missions_count'         => $missionsCount,
                'service_requests_count' => $serviceRequestsCount,
                'job_offers_count'       => $jobOffersCount,
                'usage_count'            => $missionsCount + $serviceRequestsCount + $jobOffersCount,
            ];
        }

        return $result;
    }

    /**
     * Recherche insensible à la casse/espaces d'une province dans une
     * collection [province_brute => total] — tolère des variations de
     * saisie historiques (ex. "kinshasa " vs "Kinshasa").
     */
    private function lookup(iterable $collection, string $provinceName): int
    {
        foreach ($collection as $dbProvince => $total) {
            if ($this->normalize((string) $dbProvince) === $this->normalize($provinceName)) {
                return (int) $total;
            }
        }

        return 0;
    }

    private function normalize(?string $value): string
    {
        return strtolower(trim((string) $value));
    }
}
