@extends('layouts.user')

@section('header_title', 'Statistiques des visiteurs')

@section('content')
<div class="space-y-6 pb-20">
    <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-sm text-center" data-aos="zoom-in">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-50 text-blue-500 rounded-full mb-6">
            <i class="fas fa-eye text-4xl"></i>
        </div>
        <h2 class="text-3xl font-black text-slate-900 mb-4">Qui a consulté votre profil ?</h2>
        <p class="text-slate-500 max-w-lg mx-auto text-lg mb-8">Découvrez quelles entreprises et recruteurs sont intéressés par votre profil. Plus vous complétez votre profil, plus vous apparaitrez dans les recherches.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100">
                <span class="block text-4xl font-black text-rdc-blue mb-2">0</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Vues cette semaine</span>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100">
                <span class="block text-4xl font-black text-emerald-500 mb-2">0</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Apparitions recherche</span>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100">
                <span class="block text-4xl font-black text-amber-500 mb-2">0</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Entreprises</span>
            </div>
        </div>

        <div class="mt-10 bg-amber-50 border border-amber-100 rounded-2xl p-6 inline-block text-left text-amber-700">
            <h4 class="font-bold flex items-center gap-2 mb-2"><i class="fas fa-crown"></i> Fonctionnalité Premium</h4>
            <p class="text-sm">Consultez en détail le nom des entreprises qui recherchent des talents avec vos compétences en souscrivant à ProConnect Premium.</p>
        </div>
    </div>
</div>
@endsection
