<?php

declare(strict_types=1);

namespace App\Services\Reports\Exporters;

use App\DTO\ReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfReportExporter
{
    public function export(ReportData $data): Response
    {
        // Orientation automatique selon le nombre de colonnes
        $orientation = count($data->headers) > 6 ? 'landscape' : 'portrait';

        // Logo en base64 pour DomPDF (les chemins relatifs ne fonctionnent pas)
        $logoBase64 = null;
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.reports.pdf', [
                'report'      => $data,
                'logoBase64'  => $logoBase64,
            ])
            ->setPaper('a4', $orientation)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)   // désactivé : on utilise base64
            ->setOption('defaultFont', 'Helvetica');

        $filename = 'ProConnect_Rapport_' . ucfirst($data->type) . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
