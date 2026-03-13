@extends('layouts.admin')

@section('title', 'Modération des Avis')
@section('header_title', 'Qualité de Service & Éthique')
@section('page_title', 'Évaluations à Modérer')
@section('page_subtitle', 'Gérez les retours clients pour maintenir une communauté d\'artisans fiable et respectueuse.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Reviews Analytics HUD -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En attente</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900">12</h3>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate text-nowrap">Approuvés (24h)</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900">145</h3>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 text-rdc-red rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Rejetés (30j)</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900">38</h3>
                </div>
            </div>
        </div>
        <div class="bg-slate-900 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-xl text-white group">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/10 text-white rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fas fa-star-half-stroke"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-white/40 uppercase tracking-widest truncate">Moyenne</p>
                    <h3 class="text-lg sm:text-xl font-black">4.82/5</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Moderation Feed -->
    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Flux d'Avis</h3>
            <div class="flex gap-2">
                <span class="hidden sm:inline-block px-4 py-2 bg-slate-900 text-white text-[9px] font-black uppercase rounded-xl tracking-widest">Modération active</span>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            <!-- Review Mock 1 -->
            <div class="p-6 sm:p-10 flex flex-col lg:flex-row gap-6 sm:gap-8 group hover:bg-slate-50/40 transition-all">
                <div class="flex-1">
                    <div class="flex items-center gap-3 sm:gap-4 mb-4">
                        <img src="https://ui-avatars.com/api/?name=Serge+Kasongo&background=007FFF&color=fff" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl shadow-sm shrink-0">
                        <div class="overflow-hidden">
                            <h4 class="text-[10px] sm:text-sm font-black text-slate-900 leading-tight">Serge Kasongo <span class="text-slate-300 font-medium">évalue</span> <span class="text-rdc-blue">Michel Plombier</span></h4>
                            <div class="flex items-center gap-2 sm:gap-3 mt-1">
                                <div class="flex gap-0.5 shrink-0">
                                    @foreach(range(1,5) as $i)
                                        <i class="fas fa-star text-[7px] sm:text-[10px] text-amber-400"></i>
                                    @endforeach
                                </div>
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span class="text-[7px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-tighter sm:tracking-normal truncate">Il y a 15 min</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-100">
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">"Travail impeccable ! Michel est arrivé à l'heure et a réglé ma fuite en moins de 30 minutes. Je recommande vivement pour son professionnalisme et ses tarifs honnêtes."</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[7px] sm:text-[8px] font-black uppercase rounded tracking-widest border border-emerald-100">Positif</span>
                        <span class="px-2 py-1 bg-blue-50 text-rdc-blue text-[7px] sm:text-[8px] font-black uppercase rounded tracking-widest border border-blue-100">Plomberie</span>
                    </div>
                </div>
                <div class="shrink-0 flex flex-row lg:flex-col gap-2 sm:gap-3 justify-center sm:justify-start">
                    <button class="flex-1 lg:flex-none py-3 sm:py-4 px-4 sm:px-0 lg:w-48 bg-emerald-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-xl sm:rounded-2xl shadow-lg shadow-emerald-500/10 hover:scale-105 transition-all outline-none">
                        <i class="fas fa-check mr-2"></i> Approuver
                    </button>
                    <button class="flex-1 lg:flex-none py-3 sm:py-4 px-4 sm:px-0 lg:w-48 bg-white border border-slate-100 text-rdc-red text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-red-50 transition-all outline-none">
                        <i class="fas fa-trash-can mr-2"></i> Supprimer
                    </button>
                    <button class="w-10 sm:w-14 py-3 sm:py-4 bg-slate-50 text-slate-400 rounded-xl sm:rounded-2xl hover:text-slate-900 transition-all outline-none">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Review Mock 2 (Flagged/Negative) -->
            <div class="p-6 sm:p-10 flex flex-col lg:flex-row gap-6 sm:gap-8 group hover:bg-red-50/10 transition-all bg-red-50/5">
                <div class="flex-1">
                    <div class="flex items-center gap-3 sm:gap-4 mb-4">
                        <img src="https://ui-avatars.com/api/?name=Inconnue&background=EF4444&color=fff" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl shadow-sm grayscale shrink-0">
                        <div class="overflow-hidden">
                            <h4 class="text-[10px] sm:text-sm font-black text-slate-900 leading-tight">Anonyme <span class="text-slate-300 font-medium">évalue</span> <span class="text-rdc-blue">Koffi Élec</span></h4>
                            <div class="flex items-center gap-2 sm:gap-3 mt-1">
                                <div class="flex gap-0.5 shrink-0">
                                    <i class="fas fa-star text-[7px] sm:text-[10px] text-amber-400"></i>
                                    @foreach(range(2,5) as $i)
                                        <i class="fas fa-star text-[7px] sm:text-[10px] text-slate-200"></i>
                                    @endforeach
                                </div>
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span class="text-[7px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-tighter sm:tracking-normal truncate">Il y a 2h</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border-2 border-dashed border-red-100">
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">C'est un voleur !!! Il a pris l'argent et n'est jamais revenu finir le travail. Ne l'appelez surtout pas, arnaque totale !!!</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-red-50 text-rdc-red text-[7px] sm:text-[8px] font-black uppercase rounded tracking-widest border border-red-200 animate-pulse">ALERTE IA</span>
                        <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[7px] sm:text-[8px] font-black uppercase rounded tracking-widest border border-slate-200">Signalé</span>
                    </div>
                </div>
                <div class="shrink-0 flex flex-row lg:flex-col gap-2 sm:gap-3 justify-center sm:justify-start">
                    <button class="flex-1 lg:flex-none py-3 sm:py-4 px-4 sm:px-0 lg:w-48 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-xl sm:rounded-2xl hover:bg-rdc-blue transition-all outline-none">
                        <i class="fas fa-shield-halved mr-2"></i> Enquêter
                    </button>
                    <button class="flex-1 lg:flex-none py-3 sm:py-4 px-4 sm:px-0 lg:w-48 bg-rdc-red text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-xl sm:rounded-2xl shadow-lg shadow-red-500/10 hover:scale-105 transition-all outline-none">
                        <i class="fas fa-ban mr-2"></i> Bannir
                    </button>
                    <button class="w-10 sm:w-14 py-3 sm:py-4 bg-white border border-slate-100 text-slate-400 rounded-xl sm:rounded-2xl hover:text-rdc-red transition-all outline-none">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-6 sm:px-10 py-6 sm:py-8 bg-slate-50/30 text-center">
            <button class="w-full sm:w-auto px-8 py-3.5 bg-white border border-slate-200 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">Charger plus</button>
        </div>
    </div>
</div>
@endsection
