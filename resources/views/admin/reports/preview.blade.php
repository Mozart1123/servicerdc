@extends('layouts.admin')

@section('title', 'Rapport & Export — ' . $report->title)
@section('header_title', 'Rapports & Exports Métier')
@section('page_title', $report->title)
@section('page_subtitle', $report->subtitle)

@section('content')
<div class="space-y-8 pb-24" x-data="{
    filterOpen: false,
    reportType: '{{ $report->type }}'
}">

    <!-- Barre supérieure de contrôle & filtres -->
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
        <form method="GET" action="{{ route('admin.reports.preview') }}" id="reportFilterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 items-end">
                
                <!-- Type de rapport -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fas fa-layer-group text-rdc-blue"></i> Type de Rapport
                    </label>
                    <select name="type" @change="document.getElementById('reportFilterForm').submit()" 
                            class="w-full text-xs font-black text-slate-800 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all cursor-pointer">
                        @foreach($availableTypes as $tKey => $tName)
                            <option value="{{ $tKey }}" {{ $report->type === $tKey ? 'selected' : '' }}>
                                {{ $tName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date début -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-rdc-blue"></i> Période du
                    </label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all">
                </div>

                <!-- Date fin -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fas fa-calendar-check text-rdc-blue"></i> Au
                    </label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all">
                </div>

                <!-- Statut spécifique -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fas fa-filter text-rdc-blue"></i> Statut
                    </label>
                    <select name="status" class="w-full text-xs font-black text-slate-800 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all cursor-pointer">
                        <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Tous les statuts</option>
                        @if($report->type === 'services')
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactifs uniquement</option>
                        @elseif($report->type === 'users')
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Actifs</option>
                            <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                        @elseif($report->type === 'missions')
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Complétées</option>
                            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Annulées</option>
                        @elseif($report->type === 'transactions')
                            <option value="succeeded" {{ ($filters['status'] ?? '') === 'succeeded' ? 'selected' : '' }}>Validées / Réussies</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>Échouées</option>
                            <option value="refunded" {{ ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' }}>Remboursées</option>
                        @elseif($report->type === 'reviews')
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                            <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejetés</option>
                        @endif
                    </select>
                </div>

            </div>

            <!-- Boutons de filtrage rapide -->
            <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="submit" class="px-6 py-3 bg-slate-900 text-white text-xs font-black rounded-xl hover:bg-rdc-blue transition-all shadow-md flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> Actualiser le rapport
                    </button>
                    <a href="{{ route('admin.reports.preview', ['type' => $report->type]) }}" class="px-5 py-3 bg-slate-100 text-slate-600 text-xs font-black rounded-xl hover:bg-slate-200 transition-all">
                        Réinitialiser
                    </a>
                </div>

                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center sm:text-right">
                    <span>{{ $report->periodLabel }}</span> &middot;
                    <span>{{ $report->rowCount() }} ligne(s) trouvée(s)</span>
                </div>
            </div>
        </form>
    </div>

    <!-- Section Cartes KPI -->
    @if(!empty($report->kpis))
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
        @foreach($report->kpis as $kpi)
            <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                        <i class="{{ $kpi['icon'] ?? 'fas fa-chart-bar' }} text-rdc-blue"></i>
                    </div>
                    @if(!empty($kpi['badge']))
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-black rounded-full uppercase tracking-tighter">
                            {{ $kpi['badge'] }}
                        </span>
                    @endif
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider truncate">{{ $kpi['label'] }}</p>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono tracking-tight">{{ $kpi['value'] }}</h3>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Panneau principal : Actions d'export & Tableau prévisualisation -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Barre d'actions d'export au sommet du tableau -->
        <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/50">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Prévisualisation du Rapport</h3>
                </div>
                <p class="text-xs text-slate-400 font-bold mt-1">
                    Généré à la demande pour l'opérateur <span class="text-slate-700 font-black">{{ $report->generatedBy }}</span> &middot; {{ $report->generatedAt }}
                </p>
            </div>

            <!-- Les 3 boutons d'export cibles -->
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $exportParams = array_merge($filters, ['type' => $report->type]);
                @endphp

                <!-- Excel -->
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'excel'])) }}" 
                   class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                    <i class="fas fa-file-excel text-sm"></i>
                    <span>Exporter Excel (.xlsx)</span>
                </a>

                <!-- PDF -->
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'pdf'])) }}" 
                   class="px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-lg shadow-rose-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf text-sm"></i>
                    <span>Exporter PDF (.pdf)</span>
                </a>

                <!-- Word -->
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'word'])) }}" 
                   class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-lg shadow-blue-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                    <i class="fas fa-file-word text-sm"></i>
                    <span>Exporter Word (.docx)</span>
                </a>
            </div>
        </div>

        <!-- Tableau des données prévisualisées -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        @foreach($report->headers as $idx => $header)
                            @php
                                $align = 'text-left';
                                if ($idx === 0 || in_array($header, ['Statut', 'Statut Modération', 'Certification', 'Type Profil', 'Rôle Système', 'Date', 'Date Début', 'Date Création', 'Date Déposition', 'Date Inscription', 'Date & Heure', 'Note', 'Devise'])) {
                                    $align = 'text-center';
                                } elseif (str_contains($header, 'Prix') || str_contains($header, 'CDF') || str_contains($header, 'Montant') || str_contains($header, 'Commission') || str_contains($header, '$')) {
                                    $align = 'text-right';
                                }
                            @endphp
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest {{ $align }} whitespace-nowrap">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($report->rows as $rIdx => $row)
                        <tr class="hover:bg-blue-50/20 transition-colors group">
                            @foreach($row as $cIdx => $val)
                                @php
                                    $align = 'text-left';
                                    $valStr = (string)$val;
                                    $isFirst = ($cIdx === 0 || str_starts_with($valStr, '#'));
                                    $isMonetary = str_contains($valStr, 'CDF') || str_contains($valStr, '$') || str_contains($valStr, 'USD');
                                    $isStars = str_contains($valStr, '★');

                                    if ($isFirst || in_array($valStr, ['Actif', 'Inactif', 'Vérifié', 'Standard', 'Suspendu', 'En attente', 'En cours', 'Complétée', 'Annulée', 'Validé', 'Succès', 'Échoué', 'Remboursé', 'Approuvé', 'Rejeté', 'Artisan', 'Client', 'Admin', 'Super Admin']) || $isStars) {
                                        $align = 'text-center';
                                    } elseif ($isMonetary) {
                                        $align = 'text-right';
                                    }
                                @endphp
                                <td class="px-6 py-4 text-xs font-medium text-slate-800 {{ $align }} whitespace-nowrap">
                                    @if($isFirst)
                                        <span class="font-mono text-slate-400 font-bold">{{ $val }}</span>
                                    @elseif(in_array($valStr, ['Actif', 'Vérifié', 'Complétée', 'Validé', 'Approuvé', 'Succès']))
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-black uppercase rounded-full tracking-wider">
                                            {{ $val }}
                                        </span>
                                    @elseif(in_array($valStr, ['Inactif', 'Suspendu', 'Annulée', 'Échoué', 'Rejeté', 'Remboursé']))
                                        <span class="px-3 py-1 bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-black uppercase rounded-full tracking-wider">
                                            {{ $val }}
                                        </span>
                                    @elseif(in_array($valStr, ['En attente', 'En cours']))
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black uppercase rounded-full tracking-wider">
                                            {{ $val }}
                                        </span>
                                    @elseif($isStars)
                                        <span class="text-amber-500 font-bold tracking-wider">{{ $val }}</span>
                                    @elseif($isMonetary)
                                        <span class="font-mono font-bold text-slate-900">{{ $val }}</span>
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($report->headers) }}" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider">Aucune donnée trouvée</h4>
                                    <p class="text-xs text-slate-400 max-w-sm">Aucun enregistrement ne correspond aux filtres temporels ou de statut sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pied du tableau avec rappel des exports -->
        <div class="p-6 sm:p-8 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs font-bold text-slate-500">
                Total : <strong class="text-slate-900">{{ $report->rowCount() }}</strong> enregistrement(s) dans ce rapport
            </span>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'excel'])) }}" 
                   class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:border-emerald-500 hover:text-emerald-600 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-excel text-emerald-600"></i> Excel
                </a>
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'pdf'])) }}" 
                   class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:border-rose-500 hover:text-rose-600 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-pdf text-rose-600"></i> PDF
                </a>
                <a href="{{ route('admin.reports.export-file', array_merge($exportParams, ['format' => 'word'])) }}" 
                   class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-word text-blue-600"></i> Word
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
