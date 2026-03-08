@extends('layouts.user')

@section('header_title', 'Tableau de bord')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Quick Actions / Search Bar -->
    <div class="relative">
        <div class="absolute inset-0 bg-rdc-blue/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1 w-full relative">
                <i class="fas fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="QUEL SERVICE OU EMPLOI RECHERCHEZ-VOUS ?" 
                       class="w-full pl-16 pr-8 py-5 bg-slate-50 border-none rounded-3xl text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
            </div>
            <div class="flex gap-4 w-full md:w-auto">
                <button class="flex-1 md:flex-none px-8 py-5 bg-rdc-blue text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                    Rechercher
                </button>
            </div>
        </div>
    </div>

    <!-- Main Discovery Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">
        
        <!-- 1. SERVICES DISPONIBLES -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Services Populaires</h3>
                </div>
                <a href="{{ route('user.services.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir tout</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @php
                    $services = [
                        ['title' => 'Ménage & Nettoyage', 'count' => '42 offres', 'icon' => 'fas fa-broom', 'color' => 'blue'],
                        ['title' => 'Plomberie & Réparation', 'count' => '18 offres', 'icon' => 'fas fa-wrench', 'color' => 'amber'],
                        ['title' => 'Transport & Livraison', 'count' => '35 offres', 'icon' => 'fas fa-truck', 'color' => 'emerald'],
                        ['title' => 'Cours à Domicile', 'count' => '12 offres', 'icon' => 'fas fa-book-open', 'color' => 'purple'],
                    ];
                @endphp
                @foreach($services as $s)
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $s['color'] }}-50 text-{{ $s['color'] }}-500 flex items-center justify-center text-xl mb-4 group-hover:bg-{{ $s['color'] }}-500 group-hover:text-white transition-all shadow-inner">
                        <i class="{{ $s['icon'] }}"></i>
                    </div>
                    <h4 class="font-heading font-black text-slate-900 text-sm uppercase tracking-tight">{{ $s['title'] }}</h4>
                    <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">{{ $s['count'] }} à proximité</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 2. TROUVER DES EMPLOIS -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-rdc-blue text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Opportunités d'Emploi</h3>
                </div>
                <a href="{{ route('user.jobs.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir tout</a>
            </div>

            <div class="space-y-4">
                @php
                    $jobs = [
                        ['title' => 'Développeur FullStack Laravel', 'company' => 'Vodacom RDC', 'loc' => 'Kinshasa, Gombe', 'salary' => '2,500$'],
                        ['title' => 'Responsable Marketing', 'company' => 'Orange RDC', 'loc' => 'Kinshasa, 30 Juin', 'salary' => '1,800$'],
                    ];
                @endphp
                @foreach($jobs as $j)
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex items-center gap-6">
                    <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-2xl text-slate-300 group-hover:bg-rdc-blue group-hover:text-white transition-all shadow-inner">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-heading font-black text-slate-900 text-sm uppercase group-hover:text-rdc-blue transition-colors">{{ $j['title'] }}</h4>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"><i class="fas fa-building mr-1.5 opacity-50"></i>{{ $j['company'] }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"><i class="fas fa-location-dot mr-1.5 opacity-50"></i>{{ $j['loc'] }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-900">{{ $j['salary'] }}</p>
                        <button class="mt-2 px-4 py-1.5 bg-slate-900 text-white text-[9px] font-black rounded-lg uppercase tracking-widest hover:bg-rdc-blue transition-all shadow-lg">Postuler</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Secondary Context: Activity & Suggestions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-10">
        
        <!-- Activity Timeline -->
        <div class="lg:col-span-2 bg-white rounded-[3.5rem] p-10 shadow-sm border border-slate-100">
            <h3 class="text-lg font-heading font-black text-slate-900 uppercase mb-10 flex items-center gap-3">
                <i class="fas fa-bolt text-amber-500"></i>
                Activité Récente
            </h3>
            
            <div class="relative pl-8 border-l-2 border-slate-50 space-y-10">
                <div class="relative">
                    <div class="absolute -left-[41px] top-1 w-5 h-5 bg-emerald-500 border-4 border-white rounded-full shadow-lg shadow-emerald-500/20"></div>
                    <p class="text-sm font-bold text-slate-900">Vous avez postulé à l'offre <span class="text-rdc-blue underline">"Senior Designer"</span></p>
                    <p class="text-[10px] text-slate-400 mt-1 font-mono uppercase tracking-widest">Il y a 2 heures • VODACOM RDC</p>
                </div>
                <div class="relative">
                    <div class="absolute -left-[41px] top-1 w-5 h-5 bg-blue-500 border-4 border-white rounded-full shadow-lg shadow-blue-500/20"></div>
                    <p class="text-sm font-bold text-slate-900">Nouveau service ajouté aux favoris: <span class="text-amber-600 underline">"Plomberie Express"</span></p>
                    <p class="text-[10px] text-slate-400 mt-1 font-mono uppercase tracking-widest">Hier à 14:30 • CATÉGORIE: DEPANNAGE</p>
                </div>
            </div>
        </div>

        <!-- Professional Completion HUD -->
        <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-rdc-blue/10 rounded-full blur-3xl group-hover:bg-rdc-blue/20 transition-all"></div>
            
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8">Statut du Citoyen Pro</h4>
            
            <div class="space-y-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full border-4 border-white/5 relative">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="48" cy="48" r="42" stroke="rgba(255,255,255,0.05)" stroke-width="6" fill="transparent" />
                            <circle cx="48" cy="48" r="42" stroke="#007FFF" stroke-width="6" fill="transparent" stroke-dasharray="264" stroke-dashoffset="39.6" class="transition-all duration-1000" />
                        </svg>
                        <span class="absolute text-2xl font-black">85%</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-medium text-slate-300 text-center leading-relaxed italic">"Votre profil est presque complet. Ajoutez un portfolio pour augmenter vos chances de conversion de 40%."</p>
                    <button class="w-full py-4 bg-white text-slate-900 font-black rounded-2xl text-[10px] uppercase tracking-[0.2em] shadow-xl hover:scale-105 transition-all">
                        Optimiser ma Réalité
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .animate-pulse-soft { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
</style>
@endsection
