@extends('layouts.admin')

@section('title', 'Modération des Avis')
@section('header_title', 'Qualité de Service & Éthique')
@section('page_title', 'Évaluations à Modérer')
@section('page_subtitle', 'Gérez les retours clients pour maintenir une communauté d\'artisans fiable et respectueuse.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Reviews Analytics HUD -->
    <!-- Reviews Analytics HUD -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group hover:-translate-y-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-500 rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl group-hover:bg-amber-500 group-hover:text-white transition-all shrink-0 shadow-sm">
                    <i class="fas fa-hourglass-half text-sm sm:text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En attente</p>
                    <h3 class="text-lg sm:text-2xl font-black text-slate-900 leading-none mt-1">12</h3>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group hover:-translate-y-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all shrink-0 shadow-sm">
                    <i class="fas fa-check-double text-sm sm:text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate text-nowrap">Approuvés (24h)</p>
                    <h3 class="text-lg sm:text-2xl font-black text-slate-900 leading-none mt-1">145</h3>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group hover:-translate-y-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 text-rdc-red rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl group-hover:bg-rdc-red group-hover:text-white transition-all shrink-0 shadow-sm">
                    <i class="fas fa-ban text-sm sm:text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Rejetés (30j)</p>
                    <h3 class="text-lg sm:text-2xl font-black text-slate-900 leading-none mt-1">38</h3>
                </div>
            </div>
        </div>
        <div class="bg-slate-900 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-xl text-white group hover:-translate-y-1 transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/10 text-white rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl group-hover:bg-rdc-blue transition-all shrink-0">
                    <i class="fas fa-star-half-stroke text-sm sm:text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[7px] sm:text-[9px] font-black text-white/40 uppercase tracking-widest truncate">Moyenne</p>
                    <h3 class="text-lg sm:text-2xl font-black leading-none mt-1">4.82<span class="text-white/30 font-bold ml-1 text-xs">/5</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Moderation Feed -->
    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-10 py-6 sm:py-9 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div class="flex items-center gap-4">
                <div class="w-2 h-2 rounded-full bg-rdc-blue animate-ping"></div>
                <h3 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-widest">Flux Direct de Modération</h3>
            </div>
            <div class="flex gap-2">
                <span class="hidden sm:inline-block px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase rounded-2xl tracking-widest shadow-xl shadow-slate-200">Session Active</span>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            <!-- Review Mock 1 -->
            <div class="p-6 sm:p-10 flex flex-col lg:flex-row gap-6 sm:gap-10 group hover:bg-slate-50/5 transition-all">
                <div class="flex-1">
                    <div class="flex items-center gap-3 sm:gap-5 mb-6">
                        <div class="relative shrink-0">
                            <img src="https://ui-avatars.com/api/?name=Serge+Kasongo&background=007FFF&color=fff" class="w-10 h-10 sm:w-14 sm:h-14 rounded-2xl sm:rounded-3xl shadow-lg group-hover:scale-105 transition-transform">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-6 sm:h-6 bg-emerald-500 border-4 border-white rounded-full"></div>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-[11px] sm:text-base font-black text-slate-900 leading-tight">Serge Kasongo <span class="text-slate-300 font-bold mx-1">→</span> <span class="text-rdc-blue underline decoration-2 underline-offset-4 decoration-rdc-blue/10 hover:decoration-rdc-blue/100 transition-all cursor-pointer">Michel Plombier</span></h4>
                            <div class="flex items-center gap-2 sm:gap-4 mt-1.5 sm:mt-2">
                                <div class="flex gap-0.5 shrink-0">
                                    @foreach(range(1,5) as $i)
                                        <i class="fas fa-star text-[8px] sm:text-[11px] text-amber-400 drop-shadow-sm"></i>
                                    @endforeach
                                </div>
                                <span class="w-1 h-1 bg-slate-200 rounded-full hidden sm:block"></span>
                                <span class="text-[8px] sm:text-[10px] font-black text-slate-300 uppercase tracking-widest">Il y a 15 minutes</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-5 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm relative group-hover:shadow-md transition-shadow overflow-hidden">
                        <i class="fas fa-quote-left absolute top-4 left-4 text-slate-100 text-4xl sm:text-6xl select-none"></i>
                        <p class="text-[11px] sm:text-sm text-slate-600 leading-relaxed font-bold relative z-10 italic">"Travail impeccable ! Michel est arrivé à l'heure et a réglé ma fuite en moins de 30 minutes. Je recommande vivement pour son professionnalisme et ses tarifs honnêtes."</p>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[8px] sm:text-[9px] font-black uppercase rounded-lg tracking-widest border border-emerald-100/50 shadow-sm">Sentiment Positif</span>
                        <span class="px-4 py-1.5 bg-blue-50 text-rdc-blue text-[8px] sm:text-[9px] font-black uppercase rounded-lg tracking-widest border border-blue-100/50 shadow-sm">Expertise Plomberie</span>
                    </div>
                </div>
                <div class="shrink-0 flex flex-row lg:flex-col gap-2 sm:gap-4 justify-center sm:justify-start pt-2">
                    <button class="flex-1 lg:flex-none py-4 sm:py-5 px-6 sm:px-0 lg:w-56 bg-white border border-slate-100 text-emerald-600 text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-2xl sm:rounded-[1.5rem] hover:bg-emerald-500 hover:text-white hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/20 transition-all outline-none flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-sm"></i> Approuver Avis
                    </button>
                    <button class="flex-1 lg:flex-none py-4 sm:py-5 px-6 sm:px-0 lg:w-56 bg-white border border-slate-100 text-rdc-red text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-2xl sm:rounded-[1.5rem] hover:bg-rdc-red hover:text-white hover:border-rdc-red hover:shadow-2xl hover:shadow-red-500/20 transition-all outline-none flex items-center justify-center gap-3">
                        <i class="fas fa-trash-can text-sm"></i> Supprimer Definitivement
                    </button>
                    <button class="w-12 sm:w-16 h-12 sm:h-auto py-4 sm:py-5 bg-slate-50 text-slate-400 rounded-2xl sm:rounded-[1.5rem] hover:bg-slate-900 hover:text-white transition-all outline-none flex items-center justify-center">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Review Mock 2 (Flagged/Negative) -->
            <div class="p-6 sm:p-10 flex flex-col lg:flex-row gap-6 sm:gap-10 group hover:bg-red-50/5 transition-all bg-red-50/[0.02]">
                <div class="flex-1">
                    <div class="flex items-center gap-3 sm:gap-5 mb-6">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-2xl sm:rounded-3xl bg-red-100 flex items-center justify-center text-red-500 text-xl sm:text-2xl shadow-inner">
                                <i class="fas fa-user-ninja"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-6 sm:h-6 bg-red-500 border-4 border-white rounded-full"></div>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-[11px] sm:text-base font-black text-slate-900 leading-tight">Utilisateur Anonyme <span class="text-slate-300 font-bold mx-1">→</span> <span class="text-rdc-blue underline decoration-2 underline-offset-4 decoration-rdc-blue/10 hover:decoration-rdc-blue/100 transition-all cursor-pointer">Koffi Élec</span></h4>
                            <div class="flex items-center gap-2 sm:gap-4 mt-1.5 sm:mt-2">
                                <div class="flex gap-0.5 shrink-0">
                                    <i class="fas fa-star text-[8px] sm:text-[11px] text-amber-400"></i>
                                    @foreach(range(2,5) as $i)
                                        <i class="fas fa-star text-[8px] sm:text-[11px] text-slate-100"></i>
                                    @endforeach
                                </div>
                                <span class="w-1 h-1 bg-slate-200 rounded-full hidden sm:block"></span>
                                <span class="text-[8px] sm:text-[10px] font-black text-slate-300 uppercase tracking-widest">Il y a 2 heures</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50/30 p-5 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] border-2 border-dashed border-red-100 relative shadow-sm group-hover:bg-red-50/50 transition-colors">
                        <i class="fas fa-triangle-exclamation absolute top-4 right-4 text-red-200 text-2xl sm:text-3xl animate-pulse"></i>
                        <p class="text-[11px] sm:text-sm text-slate-700 leading-relaxed font-bold">C'est un voleur !!! Il a pris l'argent et n'est jamais revenu finir le travail. Ne l'appelez surtout pas, arnaque totale !!!</p>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        <div class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black uppercase rounded-xl tracking-widest shadow-lg shadow-slate-200 animate-bounce">
                            <i class="fas fa-robot text-blue-400"></i> IA: Alerte Fraude (92%)
                        </div>
                        <span class="px-4 py-2 bg-red-50 text-rdc-red text-[8px] sm:text-[9px] font-black uppercase rounded-xl tracking-widest border border-red-100 shadow-sm">Signalement Manuel</span>
                        <span class="px-4 py-2 bg-slate-100 text-slate-500 text-[8px] sm:text-[9px] font-black uppercase rounded-xl tracking-widest border border-slate-200 shadow-sm">Accusation d'Escroquerie</span>
                    </div>
                </div>
                <div class="shrink-0 flex flex-row lg:flex-col gap-2 sm:gap-4 justify-center sm:justify-start pt-2">
                    <button class="flex-1 lg:flex-none py-4 sm:py-5 px-6 sm:px-0 lg:w-56 bg-slate-900 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-2xl sm:rounded-[1.5rem] hover:bg-rdc-blue hover:shadow-2xl hover:shadow-blue-500/20 transition-all outline-none flex items-center justify-center gap-3">
                        <i class="fas fa-magnifying-glass-chart text-sm"></i> Ouvrir Enquête
                    </button>
                    <button class="flex-1 lg:flex-none py-4 sm:py-5 px-6 sm:px-0 lg:w-56 bg-rdc-red text-white text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-2xl sm:rounded-[1.5rem] shadow-xl shadow-red-500/20 hover:scale-105 transition-all outline-none flex items-center justify-center gap-3">
                        <i class="fas fa-user-slash text-sm"></i> Bannir Artisan
                    </button>
                    <button class="w-12 sm:w-16 h-12 sm:h-auto py-4 sm:py-5 bg-white border border-slate-100 text-slate-300 rounded-2xl sm:rounded-[1.5rem] hover:bg-rdc-red hover:text-white transition-all outline-none flex items-center justify-center">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-6 sm:px-10 py-8 sm:py-12 bg-slate-50/30 text-center">
            <button class="w-full sm:w-auto px-12 py-5 bg-white border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-[1.5rem] hover:bg-slate-900 hover:text-white hover:shadow-2xl hover:shadow-slate-200 transition-all shadow-sm">
                Consulter Archives (4,281)
            </button>
        </div>
    </div>
    </div>
</div>
@endsection
