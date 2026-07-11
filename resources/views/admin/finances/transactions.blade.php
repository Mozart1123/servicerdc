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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <!-- Volume Total -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-chart-line text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">30 Jours</span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Volume Total</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">42.8k $</h3>
            </div>
        </div>

        <!-- Paiements Réussis -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-circle-check text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Paiements Réussis</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">98.2 <span class="text-xs text-emerald-500">%</span></h3>
            </div>
        </div>

        <!-- Remboursements -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-red-50 text-rdc-red rounded-lg sm:rounded-xl">
                    <i class="fas fa-rotate-left text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Remboursements</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">1.8 <span class="text-xs text-rdc-red">%</span></h3>
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
                        <tr>
                            <td colspan="5" class="py-24">
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
