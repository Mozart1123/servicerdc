@extends('layouts.admin')

@section('title', 'Maintenance Système')
@section('header_title', 'Disponibilité Infrastructure')
@section('page_title', 'Panneau de Contrôle')
@section('page_subtitle', 'Basculez le système en mode maintenance pour les mises à jour majeures ou les réparations d\'urgence.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white p-12 rounded-[4rem] border border-slate-100 shadow-sm text-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        
        <div class="relative z-10">
            <div class="w-32 h-32 bg-slate-900 text-white rounded-[2.5rem] flex items-center justify-center text-5xl mb-10 mx-auto shadow-2xl transition-transform group-hover:rotate-12">
                <i class="fas fa-hammer"></i>
            </div>
            
            <h3 class="text-3xl font-black text-slate-900 uppercase tracking-tight mb-4">Mode Maintenance</h3>
            <p class="text-slate-400 max-w-xl mx-auto font-medium text-lg leading-relaxed mb-12">Actionnez l'interrupteur pour bloquer l'accès aux interfaces utilisateurs et afficher une page d'attente professionnelle. L'accès administrateur restera actif pour vous.</p>
            
            <div class="flex flex-col items-center gap-6">
                <!-- Premium Switcher -->
                <div class="flex items-center gap-6 bg-slate-50 p-6 rounded-[3rem] border border-slate-100">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Opérationnel</span>
                    <button class="w-24 h-12 bg-slate-200 rounded-full relative p-1 transition-all duration-500 overflow-hidden">
                        <div class="absolute left-1 top-1 w-10 h-10 bg-white rounded-full shadow-lg transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-10 font-black text-[10px]">OFF</div>
                    </button>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-amber-500 transition-colors">Maintenance</span>
                </div>
                
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-tighter italic">Dernière maintenance effectuée il y a 14 jours (v2.4.1)</p>
            </div>
        </div>
    </div>

    <!-- Scheduled Tasks -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm p-10">
        <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8">Tâches Planifiées</h4>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-rdc-blue transition-colors">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400 group-hover:text-rdc-blue shadow-sm">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Backup SQL Quotidien</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Prochaine exécution: Aujourd'hui à 00:00</p>
                    </div>
                </div>
                <button class="px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl">Lancer maintenant</button>
            </div>
        </div>
    </div>
</div>
@endsection
