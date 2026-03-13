@extends('layouts.admin')

@section('title', 'Gestion Actualités')
@section('header_title', 'Centre de Presse HQ')
@section('page_title', 'Édition de Contenu')
@section('page_subtitle', 'Publiez des articles, des mises à jour système et des annonces officielles pour la communauté.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-5 sm:p-6 rounded-[2rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="relative w-full sm:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors text-xs"></i>
            <input type="text" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold outline-none">
        </div>
        <button class="w-full sm:w-auto px-6 sm:px-10 py-3.5 sm:py-4 bg-rdc-blue text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-lg shadow-blue-500/10 active:scale-95 transition-all">
            <i class="fas fa-plus mr-2 text-[8px] sm:text-xs"></i> Nouveau
        </button>
    </div>

    <!-- News Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- News Card -->
        <div class="bg-white rounded-[2rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-500">
            <div class="h-40 sm:h-48 bg-slate-100 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1573164713714-d95e4309a66d?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="">
                <div class="absolute top-4 left-4">
                    <span class="px-2.5 py-1 bg-white/90 backdrop-blur-md text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-lg shadow-sm">Officiel</span>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <p class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 sm:mb-3">12 Féb 2026</p>
                <h4 class="text-sm sm:text-md font-black text-slate-900 mb-3 sm:mb-4 group-hover:text-rdc-blue transition-colors leading-tight">Lancement version 2.5</h4>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium line-clamp-2 mb-4 sm:mb-6 leading-relaxed">Nous sommes fiers d'annoncer la mise à jour majeure...</p>
                <div class="flex items-center justify-between pt-4 sm:pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-slate-900 border-2 border-white shadow-sm"></div>
                        <span class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase">Admin HQ</span>
                    </div>
                    <div class="flex gap-1.5 sm:gap-2">
                        <button class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-slate-50 text-slate-400 hover:text-rdc-blue transition-all"><i class="fas fa-pen text-[9px] sm:text-[10px]"></i></button>
                        <button class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-slate-50 text-slate-400 hover:text-rdc-red transition-all"><i class="fas fa-trash text-[9px] sm:text-[10px]"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
