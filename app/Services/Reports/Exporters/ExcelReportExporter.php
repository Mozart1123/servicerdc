<?php

declare(strict_types=1);

namespace App\Services\Reports\Exporters;

use App\DTO\ReportData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelReportExporter
{
    // Palette ProConnect
    const C_HEADER_BG   = '0D2137'; // Bleu nuit profond ProConnect
    const C_ACCENT_BG   = '1E3A5F'; // Bleu pétrole
    const C_BRAND_BLUE  = '29B6D1'; // Bleu ciel RDC ProConnect
    const C_WHITE       = 'FFFFFF';
    const C_ZEBRA_ROW   = 'F8FAFC'; // Arrière-plan alterné
    const C_BORDER      = 'E2E8F0'; // Bordure discrète
    const C_TEXT_DARK   = '0F172A'; // Texte sombre
    const C_TEXT_MUTED  = '64748B'; // Texte secondaire

    public function export(ReportData $data): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($data);
        $filename = 'ProConnect_Rapport_' . ucfirst($data->type) . '_' . date('Y-m-d') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    protected function buildSpreadsheet(ReportData $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle(substr($data->title, 0, 31));
        $ws->setShowGridlines(true);

        $colCount = max(count($data->headers), 4);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $r = 1;

        // 1. BANDEAU SUPÉRIEUR PROCONNECT
        $ws->mergeCells("A{$r}:{$lastColLetter}{$r}");
        $ws->setCellValue("A{$r}", 'PROCONNECT — ' . mb_strtoupper($data->title, 'UTF-8'));
        $ws->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => self::C_WHITE], 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension($r)->setRowHeight(40);

        // Logo ProConnect dans le coin gauche de la bannière
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('ProConnect Logo');
            $drawing->setDescription('ProConnect Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(36);
            $drawing->setCoordinates('A' . $r);
            $drawing->setOffsetX(6);
            $drawing->setOffsetY(2);
            $drawing->setWorksheet($ws);
        }
        $r++;

        // 2. SOUS-TITRE / MÉTADONNÉES RAPPORT
        $ws->mergeCells("A{$r}:{$lastColLetter}{$r}");
        $ws->setCellValue("A{$r}", $data->subtitle . '  ·  ' . $data->periodLabel . '  ·  Généré le ' . $data->generatedAt . ' par ' . $data->generatedBy);
        $ws->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => self::C_WHITE], 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_ACCENT_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension($r)->setRowHeight(20);
        $r += 2;

        // 3. BLOC KPIS (STATISTIQUES CLÉS)
        if (!empty($data->kpis)) {
            $ws->setCellValue("A{$r}", 'INDICATEURS CLÉS DE PERFORMANCE');
            $ws->getStyle("A{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::C_ACCENT_BG], 'name' => 'Calibri'],
            ]);
            $r++;

            $kpiStartRow = $r;
            $kpiCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            foreach ($data->kpis as $idx => $kpi) {
                if ($idx >= count($kpiCols)) break;
                $col = $kpiCols[$idx];

                // Label KPI
                $ws->setCellValue("{$col}{$r}", mb_strtoupper($kpi['label'], 'UTF-8'));
                $ws->getStyle("{$col}{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::C_TEXT_MUTED]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
                ]);

                // Value KPI
                $valRow = $r + 1;
                $ws->setCellValue("{$col}{$valRow}", $kpi['value']);
                $ws->getStyle("{$col}{$valRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::C_TEXT_DARK]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_WHITE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
                ]);
            }
            $ws->getRowDimension($r)->setRowHeight(18);
            $ws->getRowDimension($r + 1)->setRowHeight(24);
            $r += 3;
        }

        // 4. EN-TÊTE DU TABLEAU DE DONNÉES
        $tableHeaderRow = $r;
        foreach ($data->headers as $idx => $headerText) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $ws->setCellValue("{$colLetter}{$r}", mb_strtoupper($headerText, 'UTF-8'));
            $ws->getStyle("{$colLetter}{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::C_WHITE], 'name' => 'Calibri'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_HEADER_BG]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
            ]);
        }
        $ws->getRowDimension($r)->setRowHeight(22);
        $r++;

        // 5. LIGNES DU TABLEAU DE DONNÉES
        if (empty($data->rows)) {
            $ws->mergeCells("A{$r}:{$lastColLetter}{$r}");
            $ws->setCellValue("A{$r}", 'Aucune donnée enregistrée pour les filtres sélectionnés.');
            $ws->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => self::C_TEXT_MUTED]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $ws->getRowDimension($r)->setRowHeight(26);
            $r++;
        } else {
            foreach ($data->rows as $rowIdx => $rowValues) {
                $bg = ($rowIdx % 2 === 1) ? self::C_ZEBRA_ROW : self::C_WHITE;
                $rowNum = $r;

                foreach ($rowValues as $colIdx => $val) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                    $ws->setCellValue("{$colLetter}{$rowNum}", (string) $val);

                    // Alignement selon nature
                    $align = Alignment::HORIZONTAL_LEFT;
                    if ($colIdx === 0 || str_starts_with((string) $val, '#')) {
                        $align = Alignment::HORIZONTAL_CENTER;
                    } elseif (str_contains((string) $val, 'CDF') || is_numeric($val)) {
                        $align = Alignment::HORIZONTAL_RIGHT;
                    } elseif (in_array((string) $val, ['Actif', 'Inactif', 'Vérifié', 'Standard', 'Suspendu', 'En attente'])) {
                        $align = Alignment::HORIZONTAL_CENTER;
                    }

                    $ws->getStyle("{$colLetter}{$rowNum}")->applyFromArray([
                        'font' => ['size' => 10, 'color' => ['rgb' => self::C_TEXT_DARK], 'name' => 'Calibri'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => $align, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
                    ]);
                }
                $ws->getRowDimension($rowNum)->setRowHeight(18);
                $r++;
            }
        }

        // 6. PIED DE PAGE DU RAPPORT
        $r++;
        $ws->mergeCells("A{$r}:{$lastColLetter}{$r}");
        $ws->setCellValue("A{$r}", 'ProConnect RDC  ·  Plateforme Officielle  ·  Document Confidentiel à usage interne  ·  Total ' . count($data->rows) . ' enregistrement(s)');
        $ws->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => self::C_TEXT_MUTED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension($r)->setRowHeight(18);

        // 7. LARGEURS AUTOMATIQUES DES COLONNES
        for ($i = 1; $i <= $colCount; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $ws->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Figer les volets au-dessus du tableau
        $freezeRow = $tableHeaderRow + 1;
        $ws->freezePane("A{$freezeRow}");

        return $spreadsheet;
    }
}
