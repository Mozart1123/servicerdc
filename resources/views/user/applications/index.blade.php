@extends('layouts.user')

@section('title', 'Historique des Candidatures')
@section('header_title', 'Historique')

@section('content')
<div class="space-y-8">
    <!-- Application Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Total Envoyés</div>
            <div class="text-3xl font-heading font-extrabold text-slate-900">32</div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-amber-400">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">À l'étude</div>
            <div class="text-3xl font-heading font-extrabold text-slate-900">12</div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-400">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Entretiens</div>
            <div class="text-3xl font-heading font-extrabold text-slate-900">4</div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-red-400">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Réfusés</div>
            <div class="text-3xl font-heading font-extrabold text-slate-900">6</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="relative w-full md:w-96 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
            <input type="text" placeholder="Rechercher une candidature..." 
                   class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-4 focus:ring-blue-100 transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 font-medium">Filtrer par:</span>
            <select class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-rdc-blue transition-all">
                <option>Tous les statuts</option>
                <option>À l'étude</option>
                <option>Accepté</option>
                <option>Réfusé</option>
            </select>
        </div>
    </div>

    <!-- Detailed List -->
    <div class="space-y-4">
        <!-- Item 1 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <!-- Company Logo -->
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-rdc-blue text-2xl font-bold border border-blue-100 shrink-0">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-lg font-bold text-slate-900 truncate">Administrateur Systèmes Senior</h4>
                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase rounded-full border border-amber-100">À l'étude</span>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="far fa-building"></i> Orange RDC
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt"></i> Postulé le 15 Janv 2024
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button class="px-5 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all">
                        Voir l'offre
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Item 2 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <!-- Company Logo -->
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 text-2xl font-bold border border-green-100 shrink-0">
                    <i class="fas fa-university"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-lg font-bold text-slate-900 truncate">Analyste Financier</h4>
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-full border border-emerald-100">Entretien prévu</span>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="far fa-building"></i> Rawbank
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt"></i> Postulé le 08 Janv 2024
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button class="px-5 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all">
                        Voir l'offre
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Item 3 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all group opacity-75 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <!-- Company Logo -->
                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-rdc-red text-2xl font-bold border border-red-100 shrink-0">
                    <i class="fas fa-plane"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-lg font-bold text-slate-900 truncate">Ingénieur Aéronautique</h4>
                        <span class="px-2.5 py-0.5 bg-red-50 text-rdc-red text-[10px] font-bold uppercase rounded-full border border-red-100">Réfusé</span>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="far fa-building"></i> CAA (Compagnie Africaine d'Aviation)
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt"></i> Postulé le 20 Dec 2023
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button class="px-5 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all">
                        Voir l'offre
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
