@extends('layouts.admin')

@section('title', 'Module en Développement')
@section('header_title', 'Configuration')

@section('content')
<div class="min-h-[60vh] sm:h-[calc(100vh-20rem)] flex flex-col items-center justify-center text-center px-6 py-12">
    <div class="relative mb-8 sm:mb-12">
        <div class="absolute inset-0 bg-rdc-blue rounded-full blur-[60px] sm:blur-[80px] opacity-20 animate-pulse"></div>
        <div class="relative w-32 h-32 sm:w-40 sm:h-40 bg-white rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl flex items-center justify-center border border-slate-100 group">
            <div class="absolute inset-2 border-2 border-dashed border-slate-100 rounded-[1rem] sm:rounded-[1.5rem] group-hover:rotate-45 transition-transform duration-700"></div>
            <i class="fas fa-microchip text-4xl sm:text-6xl text-slate-900 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>
    
    <h2 class="text-2xl sm:text-4xl font-heading font-extrabold text-slate-900 mb-4 sm:mb-6 tracking-tight leading-tight">Module en Déploiement</h2>
    <p class="text-slate-500 max-w-xl mx-auto leading-relaxed mb-8 sm:mb-10 text-sm sm:text-lg">
        Ce module de <span class="text-rdc-blue font-bold">ServiceRDC HQ</span> est en phase de synchronisation. 
        L'infrastructure est en cours de mise à jour pour assurer une sécurité maximale.
    </p>
    
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 w-full sm:w-auto">
        <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-slate-900 text-white font-bold rounded-xl sm:rounded-2xl hover:bg-rdc-blue shadow-xl shadow-slate-200 active:scale-95 transition-all flex items-center justify-center gap-3 text-xs sm:text-sm">
            <i class="fas fa-shield-alt"></i> Centre de Contrôle
        </a>
        <button class="w-full sm:w-auto px-8 py-4 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl sm:rounded-2xl hover:bg-slate-50 active:scale-95 transition-all text-xs sm:text-sm">
            Consulter les Logs
        </button>
    </div>
</div>
@endsection
