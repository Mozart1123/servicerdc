@extends('layouts.admin')

@section('title', 'Centre de Support')
@section('header_title', 'Tickets & Assistance')
@section('page_title', 'Support Utilisateurs')
@section('page_subtitle', 'Gérez les demandes d\'assistance et les tickets techniques de la communauté ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Support Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-3 sm:gap-4 group transition-all hover:border-amber-100">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-ticket"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En attente</p>
                <h4 class="text-lg sm:text-xl font-black text-slate-900">08</h4>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-3 sm:gap-4 group transition-all hover:border-blue-100">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En cours</p>
                <h4 class="text-lg sm:text-xl font-black text-slate-900">03</h4>
            </div>
        </div>
        <div class="bg-emerald-500 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-lg shadow-emerald-500/10 flex items-center gap-3 sm:gap-4 text-white group hover:scale-[1.02] transition-transform">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:rotate-12 transition-transform">
                <i class="fas fa-check"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-white/60 uppercase tracking-widest truncate">Résolus</p>
                <h4 class="text-lg sm:text-xl font-black">1.4k</h4>
            </div>
        </div>
        <div class="bg-slate-900 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-xl flex items-center gap-3 sm:gap-4 text-white group hover:scale-[1.02] transition-transform">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-clock"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-white/40 uppercase tracking-widest truncate">Moyen</p>
                <h4 class="text-lg sm:text-xl font-black">4h 12m</h4>
            </div>
        </div>
    </div>

    <!-- Tickets Feed -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
            <div class="flex items-center gap-4">
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Maintenance</h3>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded animate-pulse">Live</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative group w-full sm:w-64">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-100 rounded-xl text-xs font-bold focus:ring-4 focus:ring-rdc-blue/5 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            <!-- Ticket Item -->
            <div class="px-6 sm:px-10 py-6 sm:py-8 flex flex-col sm:flex-row items-start gap-4 sm:gap-8 group hover:bg-slate-50/50 transition-colors cursor-pointer">
                <div class="hidden sm:block w-14 h-14 rounded-2xl bg-white border border-slate-100 overflow-hidden shrink-0 shadow-sm">
                    <img src="https://i.pravatar.cc/100?u=12" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 w-full">
                    <div class="flex items-start justify-between mb-2 gap-2">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <h4 class="text-xs sm:text-sm font-black text-slate-900 leading-tight">Problème paiement M-Pesa</h4>
                            <span class="inline-block w-fit px-2 py-0.5 bg-amber-50 text-amber-600 text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-md">Urgent</span>
                        </div>
                        <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest italic shrink-0">Il y a 32m</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-slate-400 font-medium line-clamp-2 sm:line-clamp-1">Bonjour, j'ai essayé de payer pour le service premium mais la transaction a échoué...</p>
                    <div class="mt-3 sm:mt-4 flex items-center justify-between sm:justify-start gap-4">
                        <div class="flex items-center gap-2">
                            <img src="https://i.pravatar.cc/100?u=12" class="sm:hidden w-5 h-5 rounded-full object-cover shrink-0">
                            <span class="text-[9px] sm:text-[10px] font-black text-slate-900 uppercase">Awa Kassongo</span>
                        </div>
                        <span class="hidden sm:inline w-1 h-1 bg-slate-200 rounded-full"></span>
                        <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #TKT-00421</span>
                    </div>
                </div>
                <div class="hidden sm:flex flex-col items-end gap-2">
                    <span class="px-3 py-1 bg-blue-50 text-rdc-blue text-[8px] font-black uppercase tracking-widest rounded-lg">Ouvert</span>
                </div>
            </div>
            <!-- Empty state fallback logic here if needed -->
        </div>
    </div>
</div>
@endsection
