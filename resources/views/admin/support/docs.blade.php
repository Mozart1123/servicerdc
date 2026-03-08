@extends('layouts.admin')

@section('title', 'Gestion Documentation')
@section('header_title', 'Base de Connaissances HQ')
@section('page_title', 'Manuels & Tutoriels')
@section('page_subtitle', 'Rédigez et organisez les guides d\'utilisation pour les artisans et les clients de ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px] flex flex-col items-center justify-center text-center p-12">
        <div class="w-32 h-32 bg-slate-50 text-slate-200 rounded-[2.5rem] flex items-center justify-center text-5xl mb-8 border border-slate-50 shadow-inner">
            <i class="fas fa-book-open"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-4">Système de Documentation Centralisé</h3>
        <p class="text-slate-400 max-w-lg mx-auto font-medium mb-10">Ce module vous permet de gérer les articles du centre d'aide. L'infrastructure de recherche multi-langues est en cours d'indexation.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-2xl">
            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-left group hover:border-rdc-blue transition-all cursor-pointer">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 group-hover:text-rdc-blue transition-colors">Guide Artisan</h4>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">14 Articles publiés</p>
            </div>
            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-left group hover:border-rdc-blue transition-all cursor-pointer">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 group-hover:text-rdc-blue transition-colors">Aide Client</h4>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">08 Articles publiés</p>
            </div>
        </div>
        
        <button class="mt-12 px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/20">Ajouter un Manuel</button>
    </div>
</div>
@endsection
