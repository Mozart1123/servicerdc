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
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Historique des Transactions</h3>
            <div class="flex gap-2">
                <button class="px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl">Exporter CSV</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Référence</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Client / Artisan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Montant</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Méthode</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6 font-mono text-xs font-black text-slate-400 uppercase">#TRX-88219</td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-900">Jean Mukendi</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Vers: Artisan Plombier</p>
                        </td>
                        <td class="px-8 py-6 font-black text-slate-900">125.00$</td>
                        <td class="px-8 py-6 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center p-1.5 grayscale shrink-0">
                                <i class="fas fa-mobile-screen-button text-slate-400"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-900 uppercase">M-Pesa</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase rounded-lg">Succès</span>
                        </td>
                    </tr>
                    <!-- Add a few more mock rows if needed -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
