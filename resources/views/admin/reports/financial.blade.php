@extends('layouts.admin')

@section('title', 'Rapports Financiers')
@section('header_title', 'Performance Financière')
@section('page_title', 'Analyses Financières')
@section('page_subtitle', 'Suivez les flux de trésorerie, les commissions et la santé financière de la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Financial HUD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        <div class="bg-slate-900 p-10 rounded-[3rem] shadow-3xl relative overflow-hidden group border border-slate-800">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="relative z-10 space-y-4">
                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">Chiffre d'Affaire Brut</p>
                <h2 class="text-4xl font-heading font-black text-white">{{ number_format($metrics['gross_revenue'], 2) }} <span class="text-xl text-white/40">FCFA</span></h2>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded-lg text-[8px] font-black uppercase">Volume Transactionnel</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-blue/5 rounded-full blur-3xl group-hover:bg-rdc-blue/10 transition-all"></div>
            <div class="relative z-10 space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Commissions Nettes (HQ)</p>
                <h2 class="text-4xl font-heading font-black text-rdc-blue">{{ number_format($metrics['net_commissions'], 2) }} <span class="text-xl text-slate-300">FCFA</span></h2>
                <div class="w-full bg-slate-50 h-2 rounded-full mt-4 overflow-hidden shadow-inner">
                    <div class="bg-rdc-blue h-full w-[{{ ($metrics['net_commissions'] / $metrics['gross_revenue']) * 100 }}%] rounded-full shadow-[0_0_10px_rgba(40,116,252,0.3)]"></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/10 transition-all"></div>
            <div class="relative z-10 space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Santé Financière</p>
                <div class="flex items-end gap-3">
                    <h2 class="text-5xl font-heading font-black text-slate-900">{{ $metrics['health_score'] }}<span class="text-xl text-slate-300 font-bold">%</span></h2>
                    <span class="mb-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">Optimal</span>
                </div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic mt-2">Basé sur les flux de 30 derniers jours</p>
            </div>
        </div>
    </div>

    <!-- Payout Pipeline -->
    <div class="bg-white rounded-[4rem] border border-slate-100 shadow-sm overflow-hidden relative">
        <div class="px-12 py-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div>
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Pipeline des Versements</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Montants en attente de déblocage vers les artisans</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total en Attente</p>
                    <p class="text-xl font-black text-rose-500 tracking-tighter">{{ number_format($metrics['payouts_pending'], 2) }} <span class="text-xs">FCFA</span></p>
                </div>
                <button @click="alert('Traitement des versements groupés en cours...')" class="w-14 h-14 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-2xl hover:bg-rdc-blue transition-all active:scale-95">
                    <i class="fas fa-hand-holding-dollar text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-12 text-center opacity-40">
            <i class="fas fa-file-invoice-dollar text-6xl text-slate-200 mb-6"></i>
            <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Registre Synchronisé</h4>
            <p class="text-[10px] font-bold text-slate-300 mt-2 uppercase">Tous les flux transactionnels sont audités par le HQ</p>
        </div>
    </div>
</div>
@endsection
