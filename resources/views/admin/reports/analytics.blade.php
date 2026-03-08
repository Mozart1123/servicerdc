@extends('layouts.admin')

@section('title', 'Analytique Avancé')
@section('header_title', 'HQ Business Intelligence')
@section('page_title', 'Exploration des Données')
@section('page_subtitle', 'Analysez les tendances de croissance et le comportement des utilisateurs sur ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="{ activeTab: 'growth' }">
    <!-- Analysis Tabs -->
    <div class="flex items-center gap-2 bg-white/50 backdrop-blur-md p-2 rounded-2xl w-fit border border-slate-100 shadow-sm mx-auto">
        <button @click="activeTab = 'growth'" :class="activeTab === 'growth' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Croissance</button>
        <button @click="activeTab = 'behavior'" :class="activeTab === 'behavior' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Comportement</button>
        <button @click="activeTab = 'markets'" :class="activeTab === 'markets' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Marchés</button>
    </div>

    <!-- Growth Analytics -->
    <div x-show="activeTab === 'growth'" class="space-y-8 p-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Nouveaux Artisans</h4>
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-3 py-1 rounded-lg">+12.4%</span>
                </div>
                <!-- Chart Placeholder -->
                <div class="h-64 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex items-end justify-center gap-2 p-8">
                    @foreach([30, 45, 25, 60, 80, 55, 90, 70, 85, 100] as $h)
                        <div class="flex-1 bg-rdc-blue/10 rounded-full group hover:bg-rdc-blue transition-all" style="height: {{ $h }}%"></div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Acquisition Mobile</h4>
                    <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-lg">Target: 80%</span>
                </div>
                <div class="relative w-48 h-48 mx-auto flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="20" fill="transparent" class="text-slate-50" />
                        <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="20" fill="transparent" stroke-dasharray="502" stroke-dashoffset="120" class="text-rdc-blue" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-slate-900">76<span class="text-xs">%</span></span>
                    </div>
                </div>
                <p class="text-center mt-8 text-[10px] font-black text-slate-400 uppercase tracking-widest">Installation via Progressive Web App</p>
            </div>
        </div>
    </div>
    
    <div class="bg-slate-900 p-12 rounded-[4rem] text-white shadow-2xl relative overflow-hidden text-center">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-rdc-blue/20 rounded-full blur-[100px]"></div>
        <h3 class="text-3xl font-black mb-4 uppercase tracking-tighter">Exportation Intelligence</h3>
        <p class="text-white/40 text-sm font-medium max-w-xl mx-auto mb-10">Générez des rapports PDF ultra-détaillés contenant les visualisations graphiques et les tableaux de données brutes pour vos réunions stratégiques.</p>
        <button class="px-12 py-5 bg-white text-slate-900 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rdc-blue hover:text-white transition-all shadow-2xl">Télécharger le Rapport Semestriel</button>
    </div>
</div>
@endsection
