@extends('layouts.admin')

@section('title', 'Centre de Support')
@section('header_title', 'Tickets & Assistance')
@section('page_title', 'Support Utilisateurs')
@section('page_subtitle', 'Gérez les demandes d\'assistance et les tickets techniques de la communauté ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Support Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-ticket"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">En attente</p>
                <h4 class="text-xl font-black text-slate-900">08</h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-rdc-blue rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-spinner"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">En cours</p>
                <h4 class="text-xl font-black text-slate-900">03</h4>
            </div>
        </div>
        <div class="bg-emerald-500 p-6 rounded-[2rem] shadow-lg shadow-emerald-500/20 flex items-center gap-4 text-white">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest">Résolus</p>
                <h4 class="text-xl font-black">1.4k</h4>
            </div>
        </div>
        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl flex items-center gap-4 text-white">
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Temps Moyen</p>
                <h4 class="text-xl font-black">4h 12m</h4>
            </div>
        </div>
    </div>

    <!-- Tickets Feed -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Flux de Maintenance</h3>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded animate-pulse">Live</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" placeholder="Rechercher un ticket..." class="pl-12 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-xs font-bold w-64 focus:ring-4 focus:ring-rdc-blue/5 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            <!-- Ticket Item -->
            <div class="px-10 py-8 flex items-start gap-8 group hover:bg-slate-50/50 transition-colors cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 overflow-hidden shrink-0 shadow-sm">
                    <img src="https://i.pravatar.cc/100?u=12" class="w-full h-full object-cover" alt="">
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-black text-slate-900">Problème de paiement M-Pesa</h4>
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[8px] font-black uppercase tracking-widest rounded-lg">Priorité Haute</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Il y a 32m</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium line-clamp-1">Bonjour, j'ai essayé de payer pour le service premium mais la transaction a échoué alors que j'ai été débité...</p>
                    <div class="mt-4 flex items-center gap-4">
                        <span class="text-[10px] font-black text-slate-900 uppercase">Awa Kassongo</span>
                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #TKT-00421</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 bg-blue-50 text-rdc-blue text-[8px] font-black uppercase tracking-widest rounded-lg">En attente</span>
                </div>
            </div>
            <!-- Empty state fallback logic here if needed -->
        </div>
    </div>
</div>
@endsection
