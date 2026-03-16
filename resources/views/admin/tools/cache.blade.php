@extends('layouts.admin')

@section('title', 'Gestion du Cache')
@section('header_title', 'Performance & Rapidité')
@section('page_title', 'Optimisation Cache')
@section('page_subtitle', 'Gérez les couches de persistance de données pour assurer une fluidité maximale aux utilisateurs.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    loading: false,
    clearCache(type = 'all') {
        if (this.loading) return;
        this.loading = true;
        fetch('{{ route('admin.tools.cache.clear') }}', {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            if (data.success) {
                alert('Cache ' + type + ' vidé avec succès !');
            }
        });
    }
}">
    <!-- Cache Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
        <!-- Global Flush -->
        <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm flex flex-col items-center text-center group">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 text-rdc-blue rounded-2xl sm:rounded-3xl flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 shadow-inner group-hover:bg-blue-500 group-hover:text-white transition-all transform group-hover:-rotate-12">
                <i class="fas fa-broom"></i>
            </div>
            <h4 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight mb-2">Flush Global</h4>
            <p class="text-[9px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-widest mb-6 sm:mb-8">Efface vues, config et routes</p>
            <button @click="clearCache('all')" :disabled="loading" class="w-full py-4 bg-slate-900 text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest hover:bg-rdc-blue transition-all active:scale-95 disabled:opacity-50">
                <span x-text="loading ? 'Vuidage...' : 'Tout Vider'">Tout Vider</span>
            </button>
        </div>

        <!-- Selective Flushes -->
        <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm">
            <h4 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest mb-6 sm:mb-8">Actions Ciblées</h4>
            <div class="space-y-2 sm:space-y-3">
                <button @click="clearCache('view')" :disabled="loading" class="w-full px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50 hover:bg-slate-100 rounded-xl sm:rounded-2xl text-[8px] sm:text-[9px] font-black text-slate-900 uppercase tracking-widest flex items-center justify-between transition-all active:scale-[0.98] disabled:opacity-50">
                    Vues Blade <i class="fas fa-angle-right text-[10px]"></i>
                </button>
                <button @click="clearCache('route')" :disabled="loading" class="w-full px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50 hover:bg-slate-100 rounded-xl sm:rounded-2xl text-[8px] sm:text-[9px] font-black text-slate-900 uppercase tracking-widest flex items-center justify-between transition-all active:scale-[0.98] disabled:opacity-50">
                    Routes & URL <i class="fas fa-angle-right text-[10px]"></i>
                </button>
                <button @click="clearCache('config')" :disabled="loading" class="w-full px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50 hover:bg-slate-100 rounded-xl sm:rounded-2xl text-[8px] sm:text-[9px] font-black text-slate-900 uppercase tracking-widest flex items-center justify-between transition-all active:scale-[0.98] disabled:opacity-50">
                    Config PHP <i class="fas fa-angle-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Live Performance Feed -->
    <div class="bg-slate-900 p-8 sm:p-12 rounded-[2.5rem] sm:rounded-[4rem] text-white shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 sm:gap-12 text-center md:text-left">
            <div class="flex-1 space-y-4 sm:space-y-6">
                <div>
                    <span class="px-2.5 py-1 bg-rdc-blue text-white text-[7px] sm:text-[8px] font-black uppercase tracking-[0.2em] rounded-md sm:rounded-lg">Live</span>
                    <h3 class="text-xl sm:text-3xl font-black mt-3 sm:mt-4 uppercase tracking-tighter">Latence : <span class="text-rdc-blue">-42%</span></h3>
                </div>
                <p class="text-white/40 text-[10px] sm:text-sm font-medium max-w-sm mx-auto md:mx-0 leading-relaxed">Infrastructure Redis HQ : les données sont servies instantanément depuis la RAM.</p>
            </div>
            <div class="shrink-0">
                <div class="w-32 h-32 sm:w-40 sm:h-40 border-4 sm:border-8 border-white/5 rounded-full flex items-center justify-center relative">
                    <div class="absolute inset-0 border-4 sm:border-8 border-rdc-blue rounded-full" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);"></div>
                    <span class="text-3xl sm:text-4xl font-black">99<span class="text-[10px] sm:text-xs text-white/40">%</span></span>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black text-white/30 uppercase tracking-widest mt-3 sm:mt-4">Hit Rate Global</p>
            </div>
        </div>
    </div>
</div>
@endsection
