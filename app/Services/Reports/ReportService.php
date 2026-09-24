<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\User;
use App\Services\Reports\Builders\MissionReportBuilder;
use App\Services\Reports\Builders\ReviewReportBuilder;
use App\Services\Reports\Builders\ServiceReportBuilder;
use App\Services\Reports\Builders\TransactionReportBuilder;
use App\Services\Reports\Builders\UserReportBuilder;
use App\Services\Reports\Exporters\ExcelReportExporter;
use App\Services\Reports\Exporters\PdfReportExporter;
use App\Services\Reports\Exporters\WordReportExporter;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ReportService
{
    /** @var array<string, ReportBuilderInterface> */
    protected array $builders = [];

    public function __construct(
        ServiceReportBuilder $serviceBuilder,
        UserReportBuilder $userBuilder,
        MissionReportBuilder $missionBuilder,
        TransactionReportBuilder $transactionBuilder,
        ReviewReportBuilder $reviewBuilder
    ) {
        $this->registerBuilder($serviceBuilder);
        $this->registerBuilder($userBuilder);
        $this->registerBuilder($missionBuilder);
        $this->registerBuilder($transactionBuilder);
        $this->registerBuilder($reviewBuilder);
    }

    public function registerBuilder(ReportBuilderInterface $builder): void
    {
        $this->builders[$builder->getType()] = $builder;
    }

    /**
     * Liste des types de rapports disponibles avec leurs libellés.
     *
     * @param User|null $user
     * @return array<string, string>
     */
    public function getAvailableTypes(?User $user = null): array
    {
        $list = [];
        foreach ($this->builders as $type => $builder) {
            // Le rapport des transactions est réservé au super_admin
            if ($type === 'transactions' && (!$user || !$user->isSuperAdmin())) {
                continue;
            }
            $list[$type] = $builder->getName();
        }
        return $list;
    }

    /**
     * Génère l'objet ReportData (utilisé pour la prévisualisation et l'export).
     */
    public function generate(string $type, array $filters = [], ?User $user = null): ReportData
    {
        if (!isset($this->builders[$type])) {
            throw new InvalidArgumentException("Type de rapport non supporté : {$type}");
        }

        return $this->builders[$type]->build($filters, $user);
    }

    /**
     * Exporte directement le rapport dans le format demandé (excel, pdf, word).
     */
    public function export(string $type, string $format, array $filters = [], ?User $user = null): Response
    {
        $reportData = $this->generate($type, $filters, $user);

        return match (strtolower($format)) {
            'excel', 'xlsx' => (new ExcelReportExporter())->export($reportData),
            'pdf'           => (new PdfReportExporter())->export($reportData),
            'word', 'docx'  => (new WordReportExporter())->export($reportData),
            default         => throw new InvalidArgumentException("Format d'export non supporté : {$format}. Formats valides : excel, pdf, word"),
        };
    }
}
