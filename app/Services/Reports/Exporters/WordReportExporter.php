<?php

declare(strict_types=1);

namespace App\Services\Reports\Exporters;

use App\DTO\ReportData;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class WordReportExporter
{
    /**
     * Escape a string for safe use inside OOXML (PhpWord does not auto-escape & < > etc.).
     */
    private function xmlText(string $text): string
    {
        return htmlspecialchars($text, ENT_COMPAT | ENT_XML1, 'UTF-8');
    }

    public function export(ReportData $data): BinaryFileResponse
    {
        $phpWord = new PhpWord();

        // Paramétrage par défaut
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(10);

        $orientation = count($data->headers) > 6 ? 'landscape' : 'portrait';
        $section = $phpWord->addSection([
            'orientation'   => $orientation,
            'marginTop'     => 600,
            'marginBottom'  => 600,
            'marginLeft'    => 600,
            'marginRight'   => 600,
        ]);

        // 1. En-tête ProConnect — Logo + Texte côte à côte
        $headerTable = $section->addTable(['alignment' => JcTable::CENTER]);
        $headerTable->addRow(600);

        // Sous-table gauche : logo | texte
        $leftContainer = $headerTable->addCell(6000);

        // Logo ProConnect (si fichier disponible)
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $leftContainer->addImage($logoPath, [
                'width'            => 40,
                'height'           => 40,
                'alignment'        => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
                'wrappingStyle'    => 'inline',
            ]);
        }
        $leftContainer->addText('PROCONNECT RDC', ['bold' => true, 'size' => 16, 'color' => '0D2137']);
        $leftContainer->addText($this->xmlText('Plateforme Nationale des Services & Artisans'), ['size' => 8, 'color' => '64748B', 'italic' => true]);

        $rightCell = $headerTable->addCell(5000);
        $rightCell->addText($this->xmlText(mb_strtoupper($data->title, 'UTF-8')), ['bold' => true, 'size' => 11, 'color' => '0D2137'], ['alignment' => 'right']);
        $rightCell->addText($this->xmlText('Période : ' . $data->periodLabel), ['size' => 9, 'color' => '475569'], ['alignment' => 'right']);
        $rightCell->addText($this->xmlText('Généré le ' . $data->generatedAt . ' par ' . $data->generatedBy), ['size' => 8, 'color' => '64748B'], ['alignment' => 'right']);

        $section->addTextBreak(1);

        // 2. Bloc KPI
        if (!empty($data->kpis)) {
            $section->addText('INDICATEURS CLÉS', ['bold' => true, 'size' => 10, 'color' => '1E3A5F']);
            $kpiTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => 'E2E8F0',
                'alignment' => JcTable::CENTER,
                'cellMargin' => 80
            ]);
            $kpiTable->addRow();
            foreach ($data->kpis as $kpi) {
                $cell = $kpiTable->addCell(2200, ['bgColor' => 'F8FAFC']);
                $cell->addText($this->xmlText(mb_strtoupper($kpi['label'], 'UTF-8')), ['size' => 7.5, 'bold' => true, 'color' => '64748B'], ['alignment' => 'center']);
                $cell->addText($this->xmlText((string) $kpi['value']), ['size' => 12, 'bold' => true, 'color' => '0D2137'], ['alignment' => 'center']);
                if (!empty($kpi['badge'])) {
                    $cell->addText($this->xmlText((string) $kpi['badge']), ['size' => 7, 'bold' => true, 'color' => '29B6D1'], ['alignment' => 'center']);
                }
            }
            $section->addTextBreak(1);
        }

        // 3. Tableau principal
        $dataTable = $section->addTable([
            'borderSize'  => 6,
            'borderColor' => 'CBD5E1',
            'alignment'   => JcTable::CENTER,
            'cellMargin'  => 60
        ]);

        // Ligne d'en-tête
        $dataTable->addRow(400, ['tblHeader' => true, 'cantSplit' => true]);
        foreach ($data->headers as $header) {
            $dataTable->addCell(null, ['bgColor' => '0D2137'])
                ->addText($this->xmlText(mb_strtoupper($header, 'UTF-8')), ['bold' => true, 'color' => 'FFFFFF', 'size' => 8.5], ['alignment' => 'center']);
        }

        // Lignes de données
        foreach ($data->rows as $idx => $row) {
            $bgColor = ($idx % 2 === 1) ? 'F8FAFC' : 'FFFFFF';
            $dataTable->addRow(300, ['cantSplit' => true]);

            foreach ($row as $colIdx => $val) {
                $valStr = (string) $val;
                $align = 'left';
                if ($colIdx === 0 || str_starts_with($valStr, '#') || in_array($valStr, ['Actif', 'Inactif', 'Vérifié', 'Standard', 'Suspendu', 'En attente', 'En cours', 'Complétée', 'Annulée', 'Validé', 'Approuvé', 'Rejeté', 'Échoué', 'Remboursé'])) {
                    $align = 'center';
                } elseif (str_contains($valStr, 'CDF') || str_contains($valStr, '$') || str_contains($valStr, 'USD')) {
                    $align = 'right';
                }

                $dataTable->addCell(null, ['bgColor' => $bgColor])
                    ->addText($this->xmlText($valStr), ['size' => 8, 'color' => '0F172A'], ['alignment' => $align]);
            }
        }

        // Pied de page
        $footer = $section->addFooter();
        $footer->addPreserveText('ProConnect RDC  ·  Document Confidentiel  ·  Page {PAGE} sur {NUMPAGES}', ['size' => 8, 'color' => '94A3B8', 'italic' => true], ['alignment' => 'center']);

        $filename = 'ProConnect_Rapport_' . ucfirst($data->type) . '_' . date('Y-m-d') . '.docx';

        // ---------------------------------------------------------------
        // Écrire dans un fichier temp PROPRE, puis streamer via BinaryFileResponse
        // Cela évite tout octet parasite (whitespace PHP, headers de debug)
        // qui corromprait le ZIP interne du .docx si on utilisait php://output
        // ---------------------------------------------------------------
        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('proconnect_', true) . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpPath);

        $response = new BinaryFileResponse($tmpPath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Supprimer le fichier temporaire après envoi
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
