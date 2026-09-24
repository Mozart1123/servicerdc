<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Contracts\ReportBuilderInterface;
use App\DTO\ReportData;
use App\Models\User;
use App\Services\Reports\Builders\ServiceReportBuilder;
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
        UserReportBuilder $userBuilder
    ) {
        $this->registerBuilder($serviceBuilder);
        $this->registerBuilder($userBuilder);
    }

    public function registerBuilder(ReportBuilderInterface $builder): void
    {
        $this->builders[$builder->getType()] = $builder;
    }

    /**
     * Liste des types de rapports disponibles avec leurs libellés.
     *
     * @return array<string, string>
     */
    public function getAvailableTypes(): array
    {
        $list = [];
        foreach ($this->builders as $type => $builder) {
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
