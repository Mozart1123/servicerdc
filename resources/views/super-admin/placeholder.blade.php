@extends('layouts.super-admin')

@section('header_title', 'Module en Développement Master')

@section('content')
<div class="min-h-[600px] flex flex-col items-center justify-center text-center p-12 bg-white rounded-[3rem] shadow-sm border border-slate-100">
    <div class="w-32 h-32 rounded-full bg-amber-500/10 flex items-center justify-center mb-8 relative">
        <div class="absolute inset-0 rounded-full border-4 border-amber-500/20 border-t-amber-500 animate-spin"></div>
        <i class="fas fa-microchip text-4xl text-amber-600"></i>
    </div>
    <h2 class="text-3xl font-heading font-extrabold text-slate-900 mb-4 tracking-tight uppercase">Initialisation du Module Master...</h2>
    <p class="text-slate-500 max-w-md mx-auto mb-8 font-medium">Cette section de la console divine est en cours de déploiement hyper-sécurisé. Veuillez patienter pendant la synchronisation des données.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full max-w-2xl px-4">
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center gap-2">
            <span class="text-[9px] font-mono text-slate-400 uppercase">Status</span>
            <span class="text-xs font-bold text-amber-500">SYNCHRONISATION</span>
        </div>
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center gap-2">
            <span class="text-[9px] font-mono text-slate-400 uppercase">Sécurité</span>
            <span class="text-xs font-bold text-emerald-500">NIVEAU 10</span>
        </div>
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center gap-2">
            <span class="text-[9px] font-mono text-slate-400 uppercase">Node</span>
            <span class="text-xs font-bold text-rdc-blue">HQ-KIN-01</span>
        </div>
    </div>

    <div class="mt-12">
        <a href="{{ route('super-admin.dashboard') }}" class="px-8 py-3 bg-slate-900 text-white text-xs font-bold rounded-xl shadow-lg hover:bg-rdc-blue transition-all uppercase tracking-widest">
            Retourner au Master Control
        </a>
    </div>
</div>
@endsection
