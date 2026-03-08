@extends('layouts.admin')

@section('title', 'Gestion Géo-Spatiale')
@section('header_title', 'HQ Geo-Fencing')
@section('page_title', 'Zones de Service')
@section('page_subtitle', 'Déchargez-vous du déploiement géographique en gérant les périmètres d\'action par province.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px] flex flex-col md:flex-row">
        <!-- Provinces List -->
        <div class="w-full md:w-80 border-r border-slate-50 p-8 space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Provinces Actives</h4>
            <div class="space-y-2">
                <button class="w-full px-6 py-4 bg-rdc-blue text-white rounded-2xl font-black text-[10px] uppercase tracking-widest text-left flex items-center justify-between">
                    Kinshasa <i class="fas fa-check-circle"></i>
                </button>
                <button class="w-full px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest text-left hover:text-rdc-blue transition-all">
                    Lubumbashi
                </button>
                <button class="w-full px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest text-left">
                    Goma
                </button>
            </div>
            <button class="mt-8 w-full border-2 border-dashed border-slate-100 p-4 rounded-2xl text-[10px] font-black text-slate-300 uppercase tracking-widest hover:border-rdc-blue hover:text-rdc-blue transition-all">
                + Ajouter une zone
            </button>
        </div>
        
        <!-- Map View Mock -->
        <div class="flex-1 bg-slate-50 p-8 flex flex-col">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-50 text-rdc-blue rounded-xl flex items-center justify-center">
                        <i class="fas fa-location-crosshairs"></i>
                    </div>
                    <span class="text-sm font-black text-slate-900 uppercase">Kinshasa - Gombe (Périmètre Alpha)</span>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase rounded-lg">Système Actif</span>
                    <button class="w-8 h-8 rounded-lg border border-slate-100 text-slate-400 hover:text-rdc-red"><i class="fas fa-trash-can text-xs"></i></button>
                </div>
            </div>
            
            <!-- Map Placeholder -->
            <div class="flex-1 bg-slate-200 rounded-[2.5rem] relative overflow-hidden border-4 border-white shadow-inner flex items-center justify-center">
                <div class="absolute inset-0 bg-[url('https://api.mapbox.com/styles/v1/mapbox/light-v10/static/15.266, -4.441,12/800x600?access_token=pk.xxx')] bg-cover opacity-50"></div>
                <div class="relative z-10 p-10 bg-white/90 backdrop-blur-md rounded-3xl border border-white text-center shadow-2xl max-w-sm">
                    <i class="fas fa-map-marked-alt text-4xl text-rdc-blue mb-4"></i>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-2">Moteur Cartographique HQ</h4>
                    <p class="text-[11px] text-slate-500 font-medium">L'intégration Google Maps Enterprise est en cours de validation de quota. L'aperçu statique est temporairement désactivé.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
