@extends('layouts.admin')

@section('title', 'Analytique Avancé')
@section('header_title', 'HQ Business Intelligence')
@section('page_title', 'Exploration des Données')
@section('page_subtitle', 'Analysez les tendances de croissance et le comportement des utilisateurs sur ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="{ activeTab: 'growth' }">
    <!-- Analysis Tabs -->
    <div class="flex items-center gap-2 bg-white/50 backdrop-blur-md p-1.5 sm:p-2 rounded-xl sm:rounded-2xl w-full sm:w-fit border border-slate-100 shadow-sm mx-auto overflow-x-auto no-scrollbar">
        <button @click="activeTab = 'growth'" :class="activeTab === 'growth' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="flex-1 sm:flex-none px-4 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest transition-all text-nowrap">Croissance</button>
        <button @click="activeTab = 'behavior'" :class="activeTab === 'behavior' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="flex-1 sm:flex-none px-4 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest transition-all text-nowrap">Comportement</button>
        <button @click="activeTab = 'markets'" :class="activeTab === 'markets' ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900'" class="flex-1 sm:flex-none px-4 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest transition-all text-nowrap">Marchés</button>
    </div>

    <!-- Growth Analytics -->
    <div x-show="activeTab === 'growth'" class="space-y-6 sm:space-y-8 p-0.5 sm:p-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8 sm:mb-10">
                    <h4 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest">Nouveaux Artisans</h4>
                    <span class="text-[8px] sm:text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 sm:px-3 py-1 rounded-lg">+12.4%</span>
                </div>
                <!-- Chart Placeholder -->
                <div class="h-48 sm:h-64 bg-slate-50 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 flex items-end justify-center gap-1.5 sm:gap-2 p-4 sm:p-8">
                    @foreach([30, 45, 25, 60, 80, 55, 90, 70, 85, 100] as $h)
                        <div class="flex-1 bg-rdc-blue/10 rounded-full group hover:bg-rdc-blue transition-all" style="height: <?php echo $h; ?>%;"></div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8 sm:mb-10">
                    <h4 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest">Acquisition</h4>
                    <span class="text-[8px] sm:text-[10px] font-black text-blue-500 bg-blue-50 px-2 sm:px-3 py-1 rounded-lg">PWA</span>
                </div>
                <div class="relative w-32 h-32 sm:w-48 sm:h-48 mx-auto flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="50%" cy="50%" r="40%" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-50" />
                        <circle cx="50%" cy="50%" r="40%" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="251" stroke-dashoffset="60" class="text-rdc-blue" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl sm:text-3xl font-black text-slate-900">76<span class="text-[10px] sm:text-xs">%</span></span>
                    </div>
                </div>
                <p class="text-center mt-6 sm:mt-8 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Installation via PWA</p>
            </div>
        </div>
    </div>
    
    <div class="bg-slate-900 p-8 sm:p-12 rounded-[2.5rem] sm:rounded-[4rem] text-white shadow-2xl relative overflow-hidden text-center">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-rdc-blue/20 rounded-full blur-[100px]"></div>
        <h3 class="text-xl sm:text-3xl font-black mb-3 sm:mb-4 uppercase tracking-tighter">Business Intelligence</h3>
        <p class="text-white/40 text-[10px] sm:text-sm font-medium max-w-xl mx-auto mb-8 sm:mb-10 leading-relaxed">Générez des rapports PDF détaillés pour vos réunions stratégiques.</p>
        <button class="w-full sm:w-auto px-10 sm:px-12 py-4 sm:py-5 bg-white text-slate-900 font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest hover:bg-rdc-blue hover:text-white transition-all shadow-xl active:scale-95">Rapport Semestriel</button>
    </div>
</div>
@endsection
