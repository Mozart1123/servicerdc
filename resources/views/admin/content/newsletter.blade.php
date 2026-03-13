@extends('layouts.admin')

@section('title', 'Gestion Newsletter')
@section('header_title', 'Engagement Communautaire')
@section('page_title', 'Campagnes E-mail')
@section('page_subtitle', 'Gérez vos listes de diffusion et concevez des newsletters percutantes pour vos abonnés.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Newsletter Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:border-slate-200">
            <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 sm:mb-2 truncate">Total Abonnés</p>
            <h4 class="text-lg sm:text-2xl font-black text-slate-900">12,482</h4>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:border-emerald-100">
            <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 sm:mb-2 truncate">Taux Clic</p>
            <h4 class="text-lg sm:text-2xl font-black text-emerald-500">18.4%</h4>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:border-red-100">
            <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 sm:mb-2 truncate">Désabonn.</p>
            <h4 class="text-lg sm:text-2xl font-black text-rdc-red">0.4%</h4>
        </div>
        <div class="bg-slate-900 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-xl text-white">
            <p class="text-[7px] sm:text-[9px] font-black text-white/40 uppercase tracking-widest mb-1 sm:mb-2 truncate">Dernier Envoi</p>
            <h4 class="text-lg sm:text-2xl font-black truncate">Hier</h4>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Lists -->
        <div class="lg:col-span-1 bg-white p-6 sm:p-8 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm">
            <h4 class="text-[10px] sm:text-xs font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8">Listes</h4>
            <div class="space-y-3 sm:space-y-4">
                <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-slate-900 uppercase">Utilisateurs</p>
                        <p class="text-[8px] sm:text-[9px] font-bold text-slate-400">8.2k abonnés</p>
                    </div>
                    <i class="fas fa-users text-slate-200 text-sm"></i>
                </div>
                <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-slate-900 uppercase">Artisans</p>
                        <p class="text-[8px] sm:text-[9px] font-bold text-slate-400">4.1k abonnés</p>
                    </div>
                    <i class="fas fa-screwdriver-wrench text-slate-200 text-sm"></i>
                </div>
            </div>
            <button class="w-full mt-4 sm:mt-6 py-3.5 sm:py-4 border-2 border-dashed border-slate-100 rounded-xl sm:rounded-2xl text-[8px] sm:text-[9px] font-black text-slate-300 uppercase tracking-widest hover:border-rdc-blue hover:text-rdc-blue transition-all">Nouveau</button>
        </div>

        <!-- Campaign Creator Mock -->
        <div class="lg:col-span-2 bg-slate-900 p-8 sm:p-10 rounded-[2.5rem] sm:rounded-[4rem] text-white shadow-2xl flex flex-col items-center justify-center text-center relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rdc-blue/10 rounded-full blur-[100px]"></div>
            <div class="w-14 h-14 sm:w-20 sm:h-20 bg-white/5 rounded-2xl sm:rounded-3xl flex items-center justify-center text-2xl sm:text-4xl mb-4 sm:mb-6 border border-white/10 shrink-0">
                <i class="fas fa-pen-nib"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tighter mb-4">Éditeur Campagne</h3>
            <p class="text-white/40 text-[10px] sm:text-sm font-medium max-w-sm mb-6 sm:mb-10 leading-relaxed">Concevez des emails responsive avec notre éditeur intuitif.</p>
            <button class="px-8 sm:px-10 py-3.5 sm:py-4 bg-rdc-blue text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all">Créer</button>
        </div>
    </div>
</div>
@endsection
