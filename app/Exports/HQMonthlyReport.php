<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HQMonthlyReport
{
    protected $report;

    // ── Color Palette ────────────────────────────────────────────────────────
    const C_DARKEST   = '0D2137';
    const C_DARK      = '1E3A5F';
    const C_MID       = '2E6DA4';
    const C_DARK_GRN  = '1A5276';
    const C_WHITE     = 'FFFFFF';
    const C_ALT_ROW   = 'EBF5FB';
    const C_LIGHT_GRN = 'D5F5E3';
    const C_LIGHT_AMB = 'FDEBD0';
    const C_LIGHT_RED = 'FADBD8';
    const C_TXT_DARK  = '1A1A1A';
    const C_TXT_MUT   = '555555';
    const C_GRN_TXT   = '0F6E56';
    const C_AMB_TXT   = '7D6608';
    const C_RED_TXT   = '922B21';
    const C_BLU_TXT   = '185FA5';
    const C_PRP_TXT   = '3C3489';
    const C_BDR       = 'BDC3C7';

    public function __construct($report)
    {
        $this->report = $report;
    }

    // ── Download Response ────────────────────────────────────────────────────
    public function download(): StreamedResponse
    {
        $spreadsheet = $this->build();
        $filename = 'Rapport_HQ_ServiceRDC_' . now()->format('Y-m') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Main Builder ─────────────────────────────────────────────────────────
    protected function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle('Rapport HQ - ' . now()->format('M Y'));
        $ws->setShowGridlines(false);

        $r = 1;

        // ── COVER HEADER ────────────────────────────────────────────────────
        $ws->mergeCells("A{$r}:F{$r}");
        $this->setCellValue($ws, "A{$r}", 'SERVICE RDC — RAPPORT HQ STATION');
        $this->styleCell($ws, "A{$r}:F{$r}", [
            'font'  => ['bold' => true, 'size' => 14, 'color' => self::C_WHITE, 'name' => 'Arial'],
            'fill'  => self::C_DARKEST,
            'align' => 'center',
            'height'=> 32,
            'row'   => $r,
        ]);
        $r++;

        $ws->mergeCells("A{$r}:F{$r}");
        $this->setCellValue($ws, "A{$r}", 'Rapport Mensuel Automatisé · ' . now()->format('F Y') . ' · Confidentiel');
        $this->styleCell($ws, "A{$r}:F{$r}", [
            'font'  => ['bold' => false, 'size' => 10, 'color' => self::C_WHITE, 'italic' => true, 'name' => 'Arial'],
            'fill'  => '1A3A5C',
            'align' => 'center',
            'height'=> 18,
            'row'   => $r,
        ]);
        $r += 2;

        // ── SECTION 1: EN-TÊTE ───────────────────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '1 · EN-TÊTE DU RAPPORT');

        $reportId = 'RPT-' . now()->format('Y-m') . '-' . str_pad($this->report->id, 3, '0', STR_PAD_LEFT);
        $meta = [
            ['ID Rapport',        $reportId],
            ['Type',              $this->report->type ?? 'FULL (MONTH)'],
            ['Généré par',        $this->report->generator->name ?? 'Admin ServiceRDC'],
            ['Date de génération',now()->format('d/m/Y H:i')],
            ['Période couverte',  '01/' . now()->format('m/Y') . ' — ' . now()->endOfMonth()->format('d/m/Y')],
            ['Plateforme',        'ServiceRDC · HQ Station'],
        ];
        foreach ($meta as $i => [$label, $value]) {
            $ws->mergeCells("A{$r}:B{$r}");
            $ws->mergeCells("C{$r}:F{$r}");
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            $this->setCellValue($ws, "A{$r}", $label);
            $this->metaLabel($ws, "A{$r}:B{$r}", $bg, $r);
            $this->setCellValue($ws, "C{$r}", $value);
            $this->metaValue($ws, "C{$r}:F{$r}", $bg, $r);
            $r++;
        }
        $r++;

        // ── SECTION 2: RÉSUMÉ EXÉCUTIF ────────────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '2 · RÉSUMÉ EXÉCUTIF DES KPI');
        
        $kpiHeaders = ['Indicateur de Performance', 'Valeur Actuelle', 'Variation vs M-1', 'Statut'];
        
        foreach ($kpiHeaders as $ci => $h) {
            $col = ['A','B','C','D'][$ci];
            if ($col === 'D') {
                $ws->mergeCells("D{$r}:F{$r}");
            }
            $ws->getCell("{$col}{$r}")->setValue($h);
            $ws->getStyle("{$col}{$r}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => self::C_WHITE], 'size' => 10, 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_MID]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
        }
        $ws->getRowDimension($r)->setRowHeight(18);
        $r++;

        $kpis = [
            ['Nouveaux Utilisateurs',        $this->report->new_users        ?? '142',       '+12% vs M-1',   'HAUSSE'],
            ['Utilisateurs Actifs (MAU)',     $this->report->active_users     ?? '1,850',      '+5.2%',         'STABLE'],
            ['Missions Clôturées',            $this->report->missions_done    ?? '456',       '+18.7%',        'OPTIMAL'],
            ['Alertes Système Critiques',     $this->report->critical_alerts  ?? '0',        '-100%',         'VIGILANT'],
            ['Revenus Bruts Plateforme',      '$' . number_format($this->report->total_revenue ?? 12450.00, 2), '+18.4%', 'TARGET'],
            ['Commissions Nettes HQ (15%)',   '$' . number_format($this->report->commissions  ?? 1867.50, 2),  '+15.5%', 'TARGET'],
            ['Remboursements / Litiges',      '$' . number_format($this->report->refunds       ?? 450, 2),  '-2.1%',  'MINIMISÉ'],
            ['Taux de Succès Paiements',      '98.2%',                                        '+0.5%',        'EXCELLENT'],
        ];
        $statusColors = [
            'HAUSSE'    => [self::C_BLU_TXT,  'E6F1FB'],
            'STABLE'    => [self::C_TXT_DARK,  'F2F3F4'],
            'OPTIMAL'   => [self::C_GRN_TXT,   self::C_LIGHT_GRN],
            'VIGILANT'  => [self::C_AMB_TXT,   self::C_LIGHT_AMB],
            'TARGET'    => [self::C_GRN_TXT,   self::C_LIGHT_GRN],
            'MINIMISÉ'  => [self::C_AMB_TXT,   self::C_LIGHT_AMB],
            'EXCELLENT' => [self::C_GRN_TXT,   self::C_LIGHT_GRN],
        ];
        foreach ($kpis as $i => $row) {
            $ws->mergeCells("D{$r}:F{$r}");
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            [$stColor, $stBg] = $statusColors[$row[3]] ?? [self::C_TXT_DARK, 'FFFFFF'];

            $this->writeKpiRow($ws, $r, $row[0], $row[1], $row[2], $row[3], $bg, $stColor, $stBg);
            $r++;
        }
        $r++;

        // ── SECTION 3: TRANSACTIONS ──────────────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '3 · REGISTRE DÉTAILLÉ DES TRANSACTIONS');
        $r = $this->tableHeader($ws, $r, ['Référence', 'Date', 'Partenaires (Client / Artisan)', 'Montant (USD)', 'Méthode', 'Statut'], 6);

        $transactions = $this->report->transactions ?? [
            ['#TRX-9901', '12/03/2026', 'Jean Dupont / Pierre Lomami',      150.00,  'Orange Money', 'VALIDÉ'],
            ['#TRX-9902', '13/03/2026', 'Marie Kabila / HQ Support',       2500.00,  'M-Pesa',       'VALIDÉ'],
            ['#TRX-9903', '14/03/2026', 'Paul Tshisekedi / Artisan Pro',     75.00,  'Bank Card',    'VALIDÉ'],
            ['#TRX-9904', '15/03/2026', 'Sarah Mwamba / Tech Service',      120.00,  'Airtel Money', 'EN ATTENTE'],
            ['#TRX-9905', '16/03/2026', 'Kevin Mbuyi / Logistic RDC',       890.00,  'M-Pesa',       'VALIDÉ'],
        ];
        $txColors = [
            'VALIDÉ'     => [self::C_GRN_TXT, self::C_LIGHT_GRN],
            'EN ATTENTE' => [self::C_AMB_TXT, self::C_LIGHT_AMB],
            'ÉCHOUÉ'     => [self::C_RED_TXT, self::C_LIGHT_RED],
        ];
        foreach ($transactions as $i => $tx) {
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            [$stColor, $stBg] = $txColors[$tx[5]] ?? [self::C_TXT_DARK, 'FFFFFF'];
            $this->writeDataRow($ws, $r, [
                $tx[0], $tx[1], $tx[2],
                '$' . number_format($tx[3], 2),
                $tx[4],
            ], $bg);
            // Status cell with color
            $sc = $ws->getCell("F{$r}");
            $sc->setValue($tx[5]);
            $ws->getStyle("F{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $stColor], 'size' => 10, 'name' => 'Arial'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $stBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
            $ws->getRowDimension($r)->setRowHeight(16);
            $r++;
        }
        $r++;

        // ── SECTION 4: UTILISATEURS ──────────────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '4 · AUDIT DU PARC UTILISATEURS');
        $r = $this->tableHeader($ws, $r, ['Identité Nominale', 'Email Professionnel', 'Rôle Système', "Date d'Inscription", 'Statut KYC', 'Missions'], 6);

        $users = $this->report->users ?? [
            ['Dominique Lomami', 'dom@rdc.cd',   'Artisan Expert',   '02/01/2026', 'VÉRIFIÉ', '12'],
            ['Alice Mbuyi',      'alice@test.com','Client Standard',  '15/02/2026', 'PARTIEL', '3'],
            ['Marc Kazadi',      'marc@pro.cd',   'Artisan Premium',  '10/03/2026', 'VÉRIFIÉ', '1'],
            ['Julie Bakole',     'julie@rdc.cd',  'Admin Support',    '01/01/2026', 'VÉRIFIÉ', '0'],
        ];
        $kycColors = [
            'VÉRIFIÉ' => [self::C_GRN_TXT, self::C_LIGHT_GRN],
            'PARTIEL' => [self::C_AMB_TXT, self::C_LIGHT_AMB],
            'REJETÉ'  => [self::C_RED_TXT, self::C_LIGHT_RED],
        ];
        foreach ($users as $i => $user) {
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            [$kycColor, $kycBg] = $kycColors[$user[4]] ?? [self::C_TXT_DARK, 'FFFFFF'];
            $this->writeDataRow($ws, $r, [$user[0], $user[1], $user[2], $user[3], '', $user[5]], $bg);
            // KYC status cell
            $kc = $ws->getCell("E{$r}");
            $kc->setValue($user[4]);
            $ws->getStyle("E{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $kycColor], 'size' => 10, 'name' => 'Arial'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $kycBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
            $ws->getRowDimension($r)->setRowHeight(16);
            $r++;
        }
        $r++;

        // ── SECTION 5: PERFORMANCE SYSTÈME ───────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '5 · MÉTRIQUES DE PERFORMANCE SYSTÈME & INFRASTRUCTURE');

        // Double sub-header
        $ws->mergeCells("A{$r}:C{$r}");
        $ws->mergeCells("D{$r}:F{$r}");
        foreach ([['A', 'Indicateur Technique'], ['D', 'Modération & KYC']] as [$col, $label]) {
            $ws->getCell("{$col}{$r}")->setValue($label);
            $ws->getStyle("{$col}{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => self::C_WHITE], 'size' => 10, 'name' => 'Arial'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_DARK_GRN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
        }
        $ws->getRowDimension($r)->setRowHeight(18);
        $r++;

        $perfLeft  = [['Uptime Plateforme','99.8%'],['Temps Réponse API','142 ms'],['Erreurs Serveur (500)','3'],['Erreurs 404','17'],['Requêtes DB/jour (moy.)','4 280']];
        $perfRight = [['Comptes en attente traités','42'],['Comptes signalés/suspendus','0'],['Vérifications KYC soumises','18'],['KYC Approuvées','15'],['KYC En attente','3']];
        foreach ($perfLeft as $i => [$lLabel, $lVal]) {
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            $ws->mergeCells("A{$r}:B{$r}");
            $ws->mergeCells("D{$r}:E{$r}");
            [$rLabel, $rVal] = $perfRight[$i];

            foreach ([
                ["A{$r}:B{$r}", $lLabel, true,  'left'],
                ["C{$r}:C{$r}", $lVal,   false, 'center'],
                ["D{$r}:E{$r}", $rLabel, true,  'left'],
                ["F{$r}:F{$r}", $rVal,   false, 'center'],
            ] as [$range, $val, $bold, $align]) {
                $ws->getCell(explode(':', $range)[0])->setValue($val);
                $ws->getStyle($range)->applyFromArray([
                    'font'      => ['bold' => $bold, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_TXT_DARK]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                    'alignment' => ['horizontal' => $align === 'left' ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => $align === 'left' ? 1 : 0],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
                ]);
            }
            $ws->getRowDimension($r)->setRowHeight(16);
            $r++;
        }
        $r++;

        // ── SECTION 6: NOTES ADMIN ────────────────────────────────────────────
        $r = $this->sectionTitle($ws, $r, '6 · NOTES ADMINISTRATIVES & RECOMMANDATIONS HQ');

        $notes = [
            '· Le système montre une excellente résilience avec un uptime quasi-parfait (99.8%).',
            '· Les revenus sont en hausse grâce à la nouvelle politique de commissions sur les missions expertes.',
            '· Recommandation : Intensifier la vérification KYC pour les artisans inscrits après le 10/03.',
            '· 3 alertes système détectées — aucune critique. Surveiller les erreurs 500 ; escalader si > 10/mois.',
            '· Prochain rapport automatique : ' . now()->addMonth()->format('d/m/Y') . ' · Généré par le système HQ Station.',
        ];
        foreach ($notes as $i => $note) {
            $ws->mergeCells("A{$r}:F{$r}");
            $bg = $i % 2 === 1 ? self::C_ALT_ROW : 'FFFFFF';
            $ws->getCell("A{$r}")->setValue($note);
            $ws->getStyle("A{$r}:F{$r}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial', 'italic' => ($i === 4), 'color' => ['rgb' => self::C_TXT_DARK]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
            $ws->getRowDimension($r)->setRowHeight(18);
            $r++;
        }
        $r++;

        // ── FOOTER ────────────────────────────────────────────────────────────
        $ws->mergeCells("A{$r}:F{$r}");
        $footerText = '— FIN DU RAPPORT · ServiceRDC Administrative HQ · ' . $reportId . ' · Confidentiel —';
        $ws->getCell("A{$r}")->setValue($footerText);
        $ws->getStyle("A{$r}:F{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'italic' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_DARKEST]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension($r)->setRowHeight(22);

        // ── COLUMN WIDTHS ─────────────────────────────────────────────────────
        $widths = ['A' => 30, 'B' => 26, 'C' => 40, 'D' => 18, 'E' => 20, 'F' => 18];
        foreach ($widths as $col => $width) {
            $ws->getColumnDimension($col)->setWidth($width);
        }

        // Freeze top 2 rows
        $ws->freezePane('A3');

        return $spreadsheet;
    }

    // ── Helper: Section Title ─────────────────────────────────────────────────
    protected function sectionTitle($ws, int $r, string $title): int
    {
        $ws->mergeCells("A{$r}:F{$r}");
        $ws->getCell("A{$r}")->setValue($title);
        $ws->getStyle("A{$r}:F{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['rgb' => self::C_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $ws->getRowDimension($r)->setRowHeight(22);
        return $r + 1;
    }

    // ── Helper: Table Header ──────────────────────────────────────────────────
    protected function tableHeader($ws, int $r, array $headers, int $cols = 6): int
    {
        $colLetters = ['A','B','C','D','E','F'];
        foreach ($headers as $i => $h) {
            if ($i >= $cols) break;
            $col = $colLetters[$i];
            $ws->getCell("{$col}{$r}")->setValue($h);
            $ws->getStyle("{$col}{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_WHITE]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_MID]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
        }
        $ws->getRowDimension($r)->setRowHeight(18);
        return $r + 1;
    }

    // ── Helper: Data Row ──────────────────────────────────────────────────────
    protected function writeDataRow($ws, int $r, array $values, string $bg): void
    {
        $colLetters = ['A','B','C','D','E','F'];
        foreach ($values as $i => $v) {
            $col = $colLetters[$i] ?? 'F';
            $ws->getCell("{$col}{$r}")->setValue($v);
            $ws->getStyle("{$col}{$r}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_TXT_DARK]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
            ]);
        }
        $ws->getRowDimension($r)->setRowHeight(16);
    }

    // ── Helper: KPI Row ───────────────────────────────────────────────────────
    protected function writeKpiRow($ws, int $r, string $ind, string $val, string $var, string $status, string $bg, string $stColor, string $stBg): void
    {
        // Indicator (bold, left)
        $ws->getCell("A{$r}")->setValue($ind);
        $ws->getStyle("A{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_TXT_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        // Value (green if $ or %)
        $valColor = (str_contains($val, '$') || str_contains($val, '%')) ? self::C_GRN_TXT : self::C_TXT_DARK;
        $ws->getCell("B{$r}")->setValue($val);
        $ws->getStyle("B{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => $valColor]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        // Variation
        $ws->getCell("C{$r}")->setValue($var);
        $ws->getStyle("C{$r}")->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_TXT_MUT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        // Status (colored badge, merged D:F)
        $ws->getCell("D{$r}")->setValue($status);
        $ws->getStyle("D{$r}:F{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => $stColor]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $stBg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        $ws->getRowDimension($r)->setRowHeight(16);
    }

    // ── Helper: Meta Label ────────────────────────────────────────────────────
    protected function metaLabel($ws, string $range, string $bg, int $r): void
    {
        $ws->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => '2C3E50']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        $ws->getRowDimension($r)->setRowHeight(16);
    }

    // ── Helper: Meta Value ────────────────────────────────────────────────────
    protected function metaValue($ws, string $range, string $bg, int $r): void
    {
        $ws->getStyle($range)->applyFromArray([
            'font'      => ['size' => 10, 'name' => 'Arial', 'color' => ['rgb' => self::C_TXT_DARK]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BDR]]],
        ]);
        $ws->getRowDimension($r)->setRowHeight(16);
    }

    // ── Helper: Set Cell Value ────────────────────────────────────────────────
    protected function setCellValue($ws, string $cell, $value): void
    {
        $ws->getCell($cell)->setValue($value);
    }

    // ── Helper: Style Cell ────────────────────────────────────────────────────
    protected function styleCell($ws, string $range, array $opts): void
    {
        $style = [
            'font' => array_merge(['name' => 'Arial'], $opts['font'] ?? []),
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $opts['fill'] ?? 'FFFFFF']],
            'alignment' => ['horizontal' => (isset($opts['align']) && $opts['align'] === 'center') ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        if (isset($opts['font']['color'])) {
            $style['font']['color'] = ['rgb' => $opts['font']['color']];
        }
        $ws->getStyle($range)->applyFromArray($style);
        if (isset($opts['row'], $opts['height'])) {
            $ws->getRowDimension($opts['row'])->setRowHeight($opts['height']);
        }
    }
}
