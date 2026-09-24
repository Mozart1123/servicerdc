<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $report->title }}</title>
    <style>
        @page {
            margin: 28px 30px 40px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* En-tête */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-logo {
            height: 48px;
            max-width: 180px;
            display: block;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 900;
            color: #0d2137;
            letter-spacing: -0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-title span {
            color: #29b6d1;
        }
        .brand-subtitle {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .report-meta {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }
        .report-meta .doc-type {
            font-size: 11px;
            font-weight: 800;
            color: #0d2137;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            background-color: #e0f2fe;
            color: #0369a1;
            font-size: 8px;
            font-weight: 800;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .divider-bar {
            height: 3px;
            background: #29b6d1;
            margin-bottom: 14px;
            border-radius: 2px;
        }

        /* Titre du rapport */
        .report-heading {
            margin-bottom: 14px;
        }
        .report-title {
            font-size: 14px;
            font-weight: 800;
            color: #0d2137;
            margin: 0 0 2px 0;
        }
        .report-sub {
            font-size: 9px;
            color: #64748b;
            margin: 0;
        }

        /* Cartes KPI */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 16px;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .kpi-label {
            font-size: 7.5px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 13px;
            font-weight: 900;
            color: #0d2137;
        }
        .kpi-badge {
            font-size: 7px;
            color: #29b6d1;
            font-weight: 800;
            margin-top: 2px;
        }

        /* Tableau principal */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        .data-table th {
            background-color: #0d2137;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 7px;
            text-align: left;
            border: 1px solid #0d2137;
        }
        .data-table th.text-center { text-align: center; }
        .data-table th.text-right { text-align: right; }

        .data-table td {
            padding: 5px 7px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .data-table tr.zebra {
            background-color: #f8fafc;
        }
        .data-table td.text-center { text-align: center; }
        .data-table td.text-right { text-align: right; }

        .tag-active {
            color: #166534;
            font-weight: 800;
        }
        .tag-inactive {
            color: #991b1b;
            font-weight: 800;
        }
        .tag-verified {
            color: #0369a1;
            font-weight: 800;
        }

        /* Pied de page */
        .footer-note {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- Pied de page automatique -->
    <div class="footer-note">
        ProConnect RDC &middot; Document confidentiel &middot; Généré par {{ $report->generatedBy }} le {{ $report->generatedAt }} &middot; Page <span class="page-number"></span>
    </div>

    <!-- En-tête officiel -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <table style="border-collapse:collapse; margin:0; padding:0;">
                    <tr>
                        @if(!empty($logoBase64))
                        <td style="vertical-align:middle; padding-right:8px;">
                            <img class="brand-logo" src="data:image/png;base64,{{ $logoBase64 }}" alt="ProConnect Logo">
                        </td>
                        @endif
                        <td style="vertical-align:middle;">
                            <h1 class="brand-title">PRO<span>CONNECT</span></h1>
                            <div class="brand-subtitle">Plateforme Professionnelle des Métiers &amp; Services en RDC</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;" class="report-meta">
                <div class="doc-type">{{ $report->title }}</div>
                <div><strong>Période :</strong> {{ $report->periodLabel }}</div>
                <div><strong>Date :</strong> {{ $report->generatedAt }} &middot; <strong>Opérateur :</strong> {{ $report->generatedBy }}</div>
                <div style="margin-top: 3px;"><span class="badge-pill">Rapport Certifié</span></div>
            </td>
        </tr>
    </table>

    <div class="divider-bar"></div>

    <!-- Synthèse & KPIs -->
    @if(!empty($report->kpis))
    <table class="kpi-table">
        <tr>
            @foreach($report->kpis as $kpi)
            <td class="kpi-card">
                <div class="kpi-label">{{ $kpi['label'] }}</div>
                <div class="kpi-value">{{ $kpi['value'] }}</div>
                @if(!empty($kpi['badge']))
                    <div class="kpi-badge">{{ $kpi['badge'] }}</div>
                @endif
            </td>
            @endforeach
        </tr>
    </table>
    @endif

    <!-- Tableau de données -->
    <table class="data-table">
        <thead>
            <tr>
                @foreach($report->headers as $idx => $header)
                    @php
                        $align = 'left';
                        if ($idx === 0 || in_array($header, ['Statut', 'Certification', 'Type Profil', 'Rôle Système', 'Date Création', 'Date Inscription'])) {
                            $align = 'text-center';
                        } elseif (str_contains($header, 'Prix') || str_contains($header, 'CDF') || str_contains($header, 'Montant')) {
                            $align = 'text-right';
                        }
                    @endphp
                    <th class="{{ $align }}">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($report->rows as $rIdx => $row)
                <tr class="{{ $rIdx % 2 === 1 ? 'zebra' : '' }}">
                    @foreach($row as $cIdx => $val)
                        @php
                            $align = 'left';
                            if ($cIdx === 0 || str_starts_with((string)$val, '#') || in_array((string)$val, ['Actif', 'Inactif', 'Vérifié', 'Standard', 'Suspendu', 'En attente', 'Artisan', 'Client', 'Admin', 'Super Admin'])) {
                                $align = 'text-center';
                            } elseif (str_contains((string)$val, 'CDF')) {
                                $align = 'text-right';
                            }
                        @endphp
                        <td class="{{ $align }}">
                            @if($val === 'Actif' || $val === 'Vérifié')
                                <span class="tag-active">{{ $val }}</span>
                            @elseif($val === 'Inactif' || $val === 'Suspendu')
                                <span class="tag-inactive">{{ $val }}</span>
                            @else
                                {{ $val }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($report->headers) }}" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">
                        Aucun enregistrement ne correspond aux critères sélectionnés.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
