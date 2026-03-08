@extends('layouts.user')

@section('title', 'Centre de Notifications')
@section('header_title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header with Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-heading font-extrabold text-slate-900 mb-1">Centre de Notifications</h2>
            <p class="text-slate-500 font-medium">Vous avez <span class="text-rdc-blue font-bold">3 nouveaux</span> messages non lus.</p>
        </div>
        <button class="text-sm font-bold text-rdc-blue hover:text-blue-700 transition-colors flex items-center gap-2">
            <i class="fas fa-check-double"></i> Tout marquer comme lu
        </button>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-3 overflow-x-auto pb-2 no-scrollbar">
        <button class="px-6 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-full shadow-lg shadow-slate-200">Tous</button>
        <button class="px-6 py-2.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-full hover:border-rdc-blue hover:text-rdc-blue transition-all">Non lus</button>
        <button class="px-6 py-2.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-full hover:border-rdc-blue hover:text-rdc-blue transition-all">Emplois</button>
        <button class="px-6 py-2.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-full hover:border-rdc-blue hover:text-rdc-blue transition-all">Système</button>
    </div>

    <!-- Notification Feed -->
    <div class="space-y-4">
        <!-- New Notification -->
        <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100 shadow-sm relative group cursor-pointer hover:bg-white hover:shadow-md transition-all duration-300">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-rdc-blue rounded-r-full shadow-lg"></div>
            
            <div class="flex gap-6">
                <div class="w-12 h-12 rounded-2xl bg-white border border-blue-200 flex items-center justify-center text-rdc-blue text-xl shrink-0 shadow-sm">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-slate-900">Nouvelle Offre Correspondante</h4>
                        <span class="text-[10px] font-bold text-rdc-blue uppercase tracking-widest">Nouveau</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-3">Une nouvelle offre pour <span class="font-bold text-slate-900">"Senior Product Designer"</span> à Kinshasa vient d'être publiée.</p>
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-400">
                        <span class="flex items-center gap-1.5"><i class="far fa-clock"></i> Il y a 15 minutes</span>
                        <a href="#" class="text-rdc-blue hover:underline font-bold">Voir l'offre</a>
                    </div>
                </div>
                <button class="text-slate-300 hover:text-slate-600 transition-colors shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Normal Notification -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative group cursor-pointer hover:shadow-md transition-all duration-300">
            <div class="flex gap-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 text-xl shrink-0">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-slate-900">Candidature mise à jour</h4>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">12 Janv</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-3">Votre candidature pour le poste de <span class="font-bold text-slate-900">Analyste Financier</span> chez Rawbank a été mise à jour par le recruteur.</p>
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-400">
                        <span class="flex items-center gap-1.5"><i class="far fa-clock"></i> Hier à 14:30</span>
                        <a href="#" class="text-rdc-blue hover:underline font-bold">Voir les détails</a>
                    </div>
                </div>
                <button class="text-slate-300 hover:text-slate-600 transition-colors shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- System Notification -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative group cursor-pointer hover:shadow-md transition-all duration-300">
            <div class="flex gap-6">
                <div class="w-12 h-12 rounded-2xl bg-rdc-yellow/10 border border-yellow-200 flex items-center justify-center text-rdc-yellow text-xl shrink-0">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-slate-900">Sécurité du Compte</h4>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">10 Janv</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-3">Un nouvel appareil s'est connecté à votre compte ServiceRDC à partir de Kinshasa.</p>
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-400">
                        <span class="flex items-center gap-1.5"><i class="far fa-calendar-alt"></i> 10 Janv 2024 à 09:12</span>
                    </div>
                </div>
                <button class="text-slate-300 hover:text-slate-600 transition-colors shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State Placeholder (Hidden) -->
    <div class="hidden flex flex-col items-center justify-center py-24 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 text-4xl mb-6">
            <i class="fas fa-bell-slash"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Aucune nouvelle notification</h3>
        <p class="text-slate-500 max-w-xs">Nous vous préviendrons dès qu'il y aura du nouveau sur votre compte.</p>
    </div>
</div>
@endsection
