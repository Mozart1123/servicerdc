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
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
