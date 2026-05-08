@extends('layouts.admin')

@section('title', 'Gestion Documentation')
@section('header_title', 'Base de Connaissances HQ')
@section('page_title', 'Manuels & Tutoriels')
@section('page_subtitle', 'Rédigez et organisez les guides d\'utilisation pour les artisans et les clients de ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px] sm:min-h-[500px] flex flex-col items-center justify-center text-center p-6 sm:p-12">
        <div class="w-20 h-20 sm:w-32 sm:h-32 bg-slate-50 text-slate-200 rounded-[1.5rem] sm:rounded-[2.5rem] flex items-center justify-center text-3xl sm:text-5xl mb-6 sm:mb-8 border border-slate-50 shadow-inner">
            <i class="fas fa-book-open"></i>
        </div>
        <h3 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight mb-3 sm:mb-4 px-4">Documentation HQ</h3>
        <p class="text-[10px] sm:text-sm text-slate-400 max-w-sm sm:max-w-lg mx-auto font-medium mb-8 sm:mb-10 px-4 leading-relaxed">Gérez les articles du centre d'aide. L'infrastructure de recherche multi-langues est en cours.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-2xl px-4">
            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl sm:rounded-3xl border border-slate-100 text-left group hover:border-rdc-blue transition-all cursor-pointer">
                <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-1 sm:mb-2 group-hover:text-rdc-blue transition-colors">Guide Artisan</h4>
                <p class="text-[8px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-tighter">14 Articles</p>
            </div>
            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl sm:rounded-3xl border border-slate-100 text-left group hover:border-rdc-blue transition-all cursor-pointer">
                <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-1 sm:mb-2 group-hover:text-rdc-blue transition-colors">Aide Client</h4>
                <p class="text-[8px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-tighter">08 Articles</p>
            </div>
        </div>
        
        <button @click="alert('Ouverture de l\'éditeur de documentation...')" class="w-full sm:w-auto mt-8 sm:mt-12 px-10 py-4 bg-rdc-blue text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">Ajouter un Article</button>
    </div>
</div>
@endsection
