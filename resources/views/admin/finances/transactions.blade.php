@extends('layouts.admin')

@section('title', 'Flux Financiers')
@section('header_title', 'Gestion des Transactions')
@section('page_title', 'Régie Financière')
@section('page_subtitle', 'Suivez l\'ensemble des flux monétaires et paiements transitant sur ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Financial Overview -->
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
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 bg-slate-50/30">
            <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Historique des Transactions</h3>
            <div class="flex select-none">
                <button class="px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl w-full sm:w-auto">Exporter CSV</button>
            </div>
        </div>
        <div class="overflow-hidden w-full">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-1/5 sm:w-auto px-2 sm:px-8 py-3 sm:py-6 text-[7px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center sm:text-left">Réf.</th>
                        <th class="w-1/4 sm:w-auto px-2 sm:px-8 py-3 sm:py-6 text-[7px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center sm:text-left">Client/Artisan</th>
                        <th class="w-[15%] sm:w-auto px-2 sm:px-8 py-3 sm:py-6 text-[7px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center sm:text-left">Montant</th>
                        <th class="w-1/5 sm:w-auto px-2 sm:px-8 py-3 sm:py-6 text-[7px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center sm:text-left">Méthode</th>
                        <th class="w-1/5 sm:w-auto px-2 sm:px-8 py-3 sm:py-6 text-[7px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center sm:text-left">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-2 sm:px-8 py-4 sm:py-6 font-mono text-[8px] sm:text-xs font-black text-slate-400 uppercase text-center sm:text-left truncate">#TRX-88219</td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <p class="text-[9px] sm:text-sm font-black text-slate-900 leading-tight truncate">Jean Mukendi</p>
                            <p class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate mt-0.5 sm:mt-0">Artisan Plombier</p>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 font-black text-[10px] sm:text-base text-slate-900 text-center sm:text-left">125$</td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 justify-center sm:justify-start">
                                <div class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 items-center justify-center p-1.5 grayscale shrink-0">
                                    <i class="fas fa-mobile-screen-button text-slate-400"></i>
                                </div>
                                <span class="text-[8px] sm:text-[10px] font-black text-slate-900 uppercase">M-Pesa</span>
                            </div>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <span class="inline-block px-1.5 sm:px-3 py-1 bg-emerald-50 text-emerald-600 text-[7px] sm:text-[9px] font-black uppercase rounded-md sm:rounded-lg">Succès</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-2 sm:px-8 py-4 sm:py-6 font-mono text-[8px] sm:text-xs font-black text-slate-400 uppercase text-center sm:text-left truncate">#TRX-88220</td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <p class="text-[9px] sm:text-sm font-black text-slate-900 leading-tight truncate">Marie Kalala</p>
                            <p class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate mt-0.5 sm:mt-0">Électricien</p>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 font-black text-[10px] sm:text-base text-slate-900 text-center sm:text-left">45.50$</td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 justify-center sm:justify-start">
                                <div class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 items-center justify-center p-1.5 grayscale shrink-0">
                                    <i class="fas fa-credit-card text-slate-400"></i>
                                </div>
                                <span class="text-[8px] sm:text-[10px] font-black text-slate-900 uppercase">Airtel</span>
                            </div>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <span class="inline-block px-1.5 sm:px-3 py-1 bg-amber-50 text-amber-600 text-[7px] sm:text-[9px] font-black uppercase rounded-md sm:rounded-lg">Attente</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
