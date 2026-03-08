@extends('layouts.admin')

@section('title', 'Module en Développement')
@section('header_title', 'Configuration')

@section('content')
<div class="h-[calc(100vh-20rem)] flex flex-col items-center justify-center text-center px-4">
    <div class="relative mb-12">
        <div class="absolute inset-0 bg-rdc-blue rounded-full blur-[80px] opacity-20 animate-pulse"></div>
        <div class="relative w-40 h-40 bg-white rounded-[2rem] shadow-2xl flex items-center justify-center border border-slate-100 group">
            <div class="absolute inset-2 border-2 border-dashed border-slate-100 rounded-[1.5rem] group-hover:rotate-45 transition-transform duration-700"></div>
            <i class="fas fa-microchip text-6xl text-slate-900 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>
    
    <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-6 tracking-tight">Accès Restreint : Module en Construction</h2>
    <p class="text-slate-500 max-w-xl mx-auto leading-relaxed mb-10 text-lg">
        Ce module administratif de <span class="text-rdc-blue font-bold">ServiceRDC HQ</span> est actuellement en phase de déploiement technique. 
        L'infrastructure backend est en cours de synchronisation pour assurer une sécurité maximale.
    </p>
    
    <div class="flex flex-wrap items-center justify-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="px-10 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-rdc-blue shadow-xl shadow-slate-200 transition-all flex items-center gap-3">
            <i class="fas fa-shield-alt"></i> Retour au Centre de Contrôle
        </a>
        <button class="px-10 py-4 bg-white border border-slate-200 text-slate-700 font-bold rounded-2xl hover:bg-slate-50 transition-all">
            Consulter les Logs
        </button>
    </div>
</div>
@endsection
