@extends('layouts.admin')

@section('title', 'Flux Financiers')
@section('header_title', 'Gestion des Transactions')
@section('page_title', 'Régie Financière')
@section('page_subtitle', 'Suivez l\'ensemble des flux monétaires et paiements transitant sur ProConnect.')

@section('content')
<div class="space-y-8 pb-20" x-data="{ 
    exporting: false,
    statusFilter: '{{ request('status') }}',
    performExport() {
        this.exporting = true;
        const url = '{{ route('admin.finances.transactions.export') }}' + (this.statusFilter ? '?status=' + this.statusFilter : '');
        window.location.href = url;
        setTimeout(() => { this.exporting = false; }, 2000);
    }
}">
    <!-- HUD Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Volume Total -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-chart-line text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">30 Jours</span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Volume Total <span class="normal-case">(USD)</span></p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($totalVolume / 1000, 1) }}k $</h3>
            </div>
        </div>

        <!-- Paiements Réussis -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-circle-check text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">30 Jours</span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Paiements Réussis</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $successRate }} <span class="text-xs text-emerald-500">%</span></h3>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[450px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Flux Financiers Directs</h3>
            <button @click="performExport()" :disabled="exporting" class="px-4 sm:px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase rounded-xl tracking-widest hover:bg-rdc-blue transition-all shadow-xl shadow-slate-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <template x-if="!exporting">
                    <i class="fas fa-file-export"></i>
                </template>
                <template x-if="exporting">
                    <i class="fas fa-circle-notch animate-spin"></i>
                </template>
                <span x-text="exporting ? 'Export en cours...' : 'Exporter (.CSV)'">Exporter (.CSV)</span>
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[22%] lg:w-auto pl-4 pr-2 sm:px-4 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-nowrap">Référence</th>
                        <th class="w-[30%] lg:w-auto px-2 sm:px-4 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Client / Artisan</th>
                        <th class="w-[18%] lg:w-auto px-2 sm:px-4 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Montant</th>
                        <th class="hidden lg:table-cell lg:w-[10%] px-3 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Type</th>
                        <th class="hidden lg:table-cell lg:w-[13%] px-3 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Paiement</th>
                        <th class="hidden lg:table-cell lg:w-[12%] px-3 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Fin abo.</th>
                        <th class="w-[20%] lg:w-auto pr-4 pl-2 sm:px-4 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions ?? [] as $transaction)
                        @php
                            $statusMeta = match($transaction->status) {
                                'succeeded' => ['Réussi', 'bg-emerald-100 text-emerald-700'],
                                'pending'   => ['En attente', 'bg-amber-100 text-amber-700'],
                                'failed'    => ['Échoué', 'bg-red-100 text-red-600'],
                                'refunded'  => ['Remboursé', 'bg-slate-200 text-slate-600'],
                                default     => [ucfirst($transaction->status), 'bg-slate-100 text-slate-500'],
                            };
                            $typeLabel = match($transaction->type) {
                                'subscription'    => 'Abonnement',
                                'mission'         => 'Mission',
                                'service_request' => 'Prestation',
                                default           => ucfirst($transaction->type ?? '—'),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="pl-4 pr-2 sm:px-4 py-5 text-xs font-bold text-slate-500 truncate" title="{{ $transaction->reference_id }}">{{ $transaction->reference_id ?? ('#TRX-' . $transaction->id) }}</td>
                            <td class="px-2 sm:px-4 py-5 text-xs font-black text-slate-900 truncate">{{ $transaction->user?->name ?? 'N/A' }}</td>
                            <td class="px-2 sm:px-4 py-5 text-xs font-black text-slate-900 text-center whitespace-nowrap">{{ number_format((float) $transaction->amount, 2) }} {{ $transaction->currency }}</td>
                            <td class="hidden lg:table-cell px-3 py-5 text-xs font-bold text-slate-500 text-center truncate">{{ $typeLabel }}</td>
                            <td class="hidden lg:table-cell px-3 py-5 text-xs font-bold text-slate-500 text-center whitespace-nowrap" title="{{ $transaction->created_at->format('d/m/Y H:i') }}">{{ $transaction->created_at->format('d/m/y') }}</td>
                            <td class="hidden lg:table-cell px-3 py-5 text-xs font-bold text-slate-500 text-center whitespace-nowrap">{{ $transaction->subscription_end?->format('d/m/y') ?? '—' }}</td>
                            <td class="pr-4 pl-2 sm:px-4 py-5 text-right">
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-full {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-24">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-5xl mb-8 shadow-inner ring-8 ring-slate-50/50">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <h4 class="text-base sm:text-xl font-black text-slate-400 uppercase tracking-widest">Aucune Transaction</h4>
                                    <p class="text-[10px] sm:text-xs text-slate-300 font-bold uppercase tracking-tight mt-3 max-w-[300px] mx-auto leading-relaxed">
                                        Il n'y a pas encore de transactions enregistrées dans le système.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
