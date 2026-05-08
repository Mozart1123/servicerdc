@extends('layouts.admin')

@section('title', 'Facturation')
@section('header_title', 'Gestion Comptable')
@section('page_title', 'Centre de Facturation')
@section('page_subtitle', 'Gérez les factures émises pour les services premium et les frais de plateforme.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    exporting: false,
    downloadAll() {
        this.exporting = true;
        window.location.href = '{{ route('admin.finances.invoicing.export') }}';
        setTimeout(() => { this.exporting = false; }, 2000);
    }
}">
    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[450px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Registre de Facturation</h3>
            <button @click="downloadAll()" :disabled="exporting" class="px-4 sm:px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase rounded-xl tracking-widest hover:bg-rdc-blue transition-all shadow-xl shadow-slate-200 disabled:opacity-50 flex items-center justify-center gap-2">
                <template x-if="!exporting">
                    <i class="fas fa-file-pdf"></i>
                </template>
                <template x-if="exporting">
                    <i class="fas fa-circle-notch animate-spin"></i>
                </template>
                <span x-text="exporting ? 'Téléchargement...' : 'Télécharger Tout'">Télécharger Tout</span>
            </button>
        </div>
        
        <div class="overflow-x-hidden">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[35%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-nowrap">N° Facture / Date</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Destinataire</th>
                        <th class="w-[30%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Montant HT</th>
                        <th class="w-[30%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($invoices ?? [] as $invoice)
                        <!-- Dynamic rows -->
                    @empty
                        @if(true) <!-- Force show mock -->
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                    <span class="font-mono text-[9px] sm:text-xs font-black text-slate-900 block truncate">#INV-2026-001</span>
                                    <span class="text-[7px] sm:text-[9px] font-bold text-slate-400 uppercase mt-1 block">Facturé le 12 Mars</span>
                                </td>
                                <td class="hidden sm:table-cell px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 text-xs">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 leading-tight">Jean-Charles Kabila</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Pack Premium Artisans</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                    <span class="text-[10px] sm:text-base font-black text-slate-900 leading-none">45.00 $</span>
                                </td>
                                <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                    <button class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-white text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-100 group-hover:scale-110">
                                        <i class="fas fa-file-pdf text-[10px] sm:text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="py-24">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-5xl mb-8 shadow-inner ring-8 ring-slate-50/50">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <h4 class="text-base sm:text-xl font-black text-slate-400 uppercase tracking-widest">Aucune Facture</h4>
                                        <p class="text-[10px] sm:text-xs text-slate-300 font-bold uppercase tracking-tight mt-3 max-w-[300px] mx-auto leading-relaxed">
                                            Votre centre de facturation est prêt. Les factures apparaîtront ici dès les premiers abonnements.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
