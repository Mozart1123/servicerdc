@extends('layouts.admin')

@section('title', 'Flux Financiers')
@section('header_title', 'Gestion des Transactions')
@section('page_title', 'Régie Financière')
@section('page_subtitle', 'Suivez l\'ensemble des flux monétaires et paiements transitant sur ServiceRDC.')

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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rdc-blue/20 rounded-full blur-2xl group-hover:bg-rdc-blue/30 transition-colors"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Volume Total (30j)</p>
            <h3 class="text-4xl font-heading font-black">42.8k <span class="text-xs text-rdc-blue">$</span></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Paiements Réussis</p>
            <h3 class="text-4xl font-heading font-black text-slate-900">98.2 <span class="text-xs text-emerald-500">%</span></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Remboursements</p>
            <h3 class="text-4xl font-heading font-black text-slate-900">1.8 <span class="text-xs text-rdc-red">%</span></h3>
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
        
        <div class="overflow-x-hidden">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[25%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-nowrap">Référence</th>
                        <th class="w-[35%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Client / Artisan</th>
                        <th class="w-[20%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Montant</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Méthode</th>
                        <th class="w-[20%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions ?? [] as $transaction)
                        <!-- Dynamic transaction rows would go here -->
                    @empty
                        <!-- Mock Rows for demonstration -->
                        <!-- TRX 1 -->
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <span class="font-mono text-[8px] sm:text-xs font-black text-slate-400 uppercase tracking-tighter truncate block">#TRX-88219</span>
                                <span class="text-[7px] font-bold text-slate-300 uppercase tracking-widest block mt-1">Il y a 5 min</span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                    <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center font-black shadow-sm shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                        <i class="fas fa-user text-[10px] sm:text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate leading-tight tracking-tight">Jean Mukendi</p>
                                        <p class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5 sm:mt-1 truncate opacity-70">Plombier</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                <span class="text-[11px] sm:text-base font-black text-slate-900 tracking-tighter">125.00$</span>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-6">
                                <div class="flex items-center justify-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <i class="fas fa-mobile-screen-button text-slate-400 text-xs"></i>
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">M-Pesa</span>
                                </div>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">
                                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[7px] sm:text-[9px] font-black uppercase tracking-widest">Succès</span>
                                </div>
                            </td>
                        </tr>

                        <!-- TRX 2 -->
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <span class="font-mono text-[8px] sm:text-xs font-black text-slate-400 uppercase tracking-tighter truncate block">#TRX-88220</span>
                                <span class="text-[7px] font-bold text-slate-300 uppercase tracking-widest block mt-1">Il y a 2h</span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                    <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center font-black shadow-sm shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                        <i class="fas fa-user text-[10px] sm:text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate leading-tight tracking-tight">Marie Kalala</p>
                                        <p class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5 sm:mt-1 truncate opacity-70">Electricienne</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                <span class="text-[11px] sm:text-base font-black text-slate-900 tracking-tighter">45.50$</span>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-6">
                                <div class="flex items-center justify-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <i class="fas fa-credit-card text-slate-400 text-xs"></i>
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Airtel</span>
                                </div>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 bg-amber-50 text-amber-600 rounded-full border border-amber-100">
                                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span class="text-[7px] sm:text-[9px] font-black uppercase tracking-widest">Attente</span>
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
