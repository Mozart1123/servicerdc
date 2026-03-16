@extends('layouts.admin')

@section('title', 'Maintenance Système')
@section('header_title', 'Disponibilité Infrastructure')
@section('page_title', 'Panneau de Contrôle')
@section('page_subtitle', 'Basculez le système en mode maintenance pour les mises à jour majeures ou les réparations d\'urgence.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white p-6 sm:p-12 rounded-[2rem] sm:rounded-[4rem] border border-slate-100 shadow-sm text-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        
        <div class="relative z-10">
            <div class="w-20 h-20 sm:w-32 sm:h-32 bg-slate-900 text-white rounded-[1.5rem] sm:rounded-[2.5rem] flex items-center justify-center text-3xl sm:text-5xl mb-6 sm:mb-10 mx-auto shadow-2xl transition-transform group-hover:rotate-12">
                <i class="fas fa-hammer"></i>
            </div>
            
            <h3 class="text-xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight mb-3 sm:mb-4 px-4">Maintenance</h3>
            <p class="text-[11px] sm:text-lg text-slate-400 max-w-xl mx-auto font-medium leading-relaxed mb-8 sm:mb-12 px-4 italic sm:not-italic">Basculez le système en mode maintenance pour les réparations d'urgence.</p>
            
            <div class="flex flex-col items-center gap-4 sm:gap-6" x-data="{ 
                isDown: {{ $isDown ? 'true' : 'false' }},
                loading: false,
                toggle() {
                    if (this.loading) return;
                    this.loading = true;
                    fetch('{{ route('admin.tools.maintenance.toggle') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isDown = data.isDown;
                        this.loading = false;
                    });
                }
            }">
                <!-- Premium Switcher -->
                <div class="flex items-center gap-4 sm:gap-6 bg-slate-50 p-4 sm:p-6 rounded-[2rem] sm:rounded-[3rem] border border-slate-100">
                    <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400" :class="!isDown ? 'text-emerald-500' : ''">Live</span>
                    <button @click="toggle()" class="w-16 h-10 sm:w-24 sm:h-12 rounded-full relative p-1 transition-all duration-500 overflow-hidden active:scale-95" :class="isDown ? 'bg-amber-500' : 'bg-slate-200'">
                        <div class="absolute top-1 w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full shadow-lg transition-all duration-500" :class="isDown ? 'left-7 sm:left-13' : 'left-1'"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-10 font-black text-[8px] sm:text-[10px]" x-text="isDown ? 'ON' : 'OFF'">OFF</div>
                    </button>
                    <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400" :class="isDown ? 'text-amber-500' : ''">Offline</span>
                </div>
                
                <p class="text-[8px] sm:text-[10px] font-black text-slate-300 uppercase tracking-tighter italic px-4">Dernière: 14j (v2.4.1)</p>
            </div>
        </div>
    </div>

    <!-- Scheduled Tasks -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm p-6 sm:p-10">
        <h4 class="text-[10px] sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8">Tâches</h4>
        <div class="space-y-3 sm:space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 sm:p-6 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 group hover:border-rdc-blue transition-colors gap-4">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-lg sm:rounded-xl flex items-center justify-center text-slate-400 group-hover:text-rdc-blue shadow-sm shrink-0">
                        <i class="fas fa-database text-sm sm:text-base"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight truncate">Backup SQL</p>
                        <p class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate">Aujourd'hui à 00:00</p>
                    </div>
                </div>
                <button class="w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg sm:rounded-xl active:scale-95 transition-all">Lancer</button>
            </div>
        </div>
    </div>
</div>
@endsection
