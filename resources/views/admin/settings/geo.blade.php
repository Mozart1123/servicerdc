@extends('layouts.admin')

@section('title', 'Gestion Géo-Spatiale')
@section('header_title', 'HQ Geo-Fencing')
@section('page_title', 'Zones de Service')
@section('page_subtitle', 'Déchargez-vous du déploiement géographique en gérant les périmètres d\'action par province.')

@section('content')
<div class="space-y-6 sm:space-y-8 pb-20 px-1 sm:px-0">
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px] flex flex-col lg:flex-row">
        <!-- Provinces List -->
        <div class="w-full lg:w-80 border-b lg:border-b-0 lg:border-r border-slate-50 p-6 sm:p-8 space-y-4">
            <h4 class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 sm:mb-6">Provinces Actives</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                <button class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-rdc-blue text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-left flex items-center justify-between">
                    Kinshasa <i class="fas fa-check-circle text-xs"></i>
                </button>
                <button class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 text-slate-400 rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-left hover:text-rdc-blue active:scale-95 transition-all">
                    Lubumbashi
                </button>
                <button class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 text-slate-400 rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-left">
                    Goma
                </button>
            </div>
            <button class="mt-6 sm:mt-8 w-full border-2 border-dashed border-slate-100 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] font-black text-slate-300 uppercase tracking-widest hover:border-rdc-blue hover:text-rdc-blue active:scale-95 transition-all">
                + Ajouter zone
            </button>
        </div>
        
        <!-- Map View Mock -->
        <div class="flex-1 bg-slate-50 p-6 sm:p-8 flex flex-col min-h-[400px]">
            <div class="bg-white p-4 rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 sm:mb-8">
                <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl flex items-center justify-center shrink-0">
                        <i class="fas fa-location-crosshairs text-sm"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-black text-slate-900 uppercase truncate">Kinshasa - Gombe</span>
                </div>
                <div class="flex gap-2 w-full sm:w-auto justify-end">
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[8px] sm:text-[9px] font-black uppercase rounded-lg">Actif</span>
                    <button class="w-8 h-8 rounded-lg border border-slate-100 text-slate-400 hover:text-rdc-red active:scale-90 transition-all"><i class="fas fa-trash-can text-[10px]"></i></button>
                </div>
            </div>
            
            <!-- Map Placeholder -->
            <div class="flex-1 bg-slate-200 rounded-[1.5rem] sm:rounded-[2.5rem] relative overflow-hidden border-2 sm:border-4 border-white shadow-inner flex items-center justify-center">
                <div class="absolute inset-0 bg-[url('https://api.mapbox.com/styles/v1/mapbox/light-v10/static/15.266, -4.441,12/800x600?access_token=pk.xxx')] bg-cover opacity-50"></div>
                <div class="relative z-10 p-6 sm:p-10 bg-white/90 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-white text-center shadow-2xl max-w-[280px] sm:max-w-sm">
                    <i class="fas fa-map-marked-alt text-3xl sm:text-4xl text-rdc-blue mb-3 sm:mb-4"></i>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight mb-2">Moteur Cartographique</h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">L'intégration Google Maps Enterprise est en attente de synchronisation.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
