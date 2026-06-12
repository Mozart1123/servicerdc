@extends('layouts.user')

@section('header_title', 'Tableau de bord Artisan')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Welcome Artisan -->
    <div class="relative">
        <div class="absolute inset-0 bg-amber-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center text-amber-500 text-3xl shadow-inner">
                <i class="fas fa-hammer"></i>
            </div>
            <div class="flex-1 w-full text-center md:text-left">
                <h2 class="text-2xl font-black text-slate-900 uppercase">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Gérez vos services et développez votre activité</p>
            </div>
            <div class="flex gap-4 w-full md:w-auto mt-4 md:mt-0">
                <a href="{{ route('user.services.create') }}" class="flex-1 text-center md:flex-none px-8 py-5 bg-amber-500 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 transition-all">
                    + Nouveau Service
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-box-open"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['my_services_count'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mes Services</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-hard-hat"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['active_missions'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Missions Actives</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-bell"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['pending_demands_count'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Demandes en attente</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-star-half-alt"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ number_format($stats['avg_rating'] ?? 0, 1, ',', '') }} <span class="text-sm font-bold text-slate-400">({{ $stats['reviews_count'] ?? 0 }})</span></p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mes Avis</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-envelope"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['unread_notifications'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Notifications</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        
        <!-- Mes Missions Actives -->
        <div class="xl:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-4" data-aos="fade-down">
                <div class="flex items-center gap-4">
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Nouvelles demandes & Missions</h3>
                </div>
                <a href="{{ route('user.missions.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir tout</a>
            </div>

            <div class="space-y-4">
                @if(($recentDemands ?? collect())->isEmpty() && collect($artisanMissions ?? [])->isEmpty())
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center" data-aos="fade-up">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p class="text-sm text-slate-500 font-bold">Aucune demande ou mission en cours pour le moment.</p>
                    </div>
                @endif

                {{-- Nouvelles demandes --}}
                @foreach($recentDemands ?? [] as $demand)
                    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition-all group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="font-bold text-slate-900">Demande: {{ $demand->service->title ?? 'Service' }}</h4>
                            <p class="text-xs text-slate-500 mt-1"><i class="fas fa-user mr-1"></i> {{ $demand->user->name ?? 'Client' }}</p>
                            @if($demand->budget)
                                <p class="text-xs text-slate-500 mt-1"><i class="fas fa-money-bill mr-1"></i> Rémunération proposée: {{ $demand->budget }} $</p>
                            @endif
                        </div>
                        <div class="text-center md:text-right">
                            <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full mb-2">Nouvelle demande</span>
                            <div class="flex gap-2">
                                <a href="{{ route('user.service-requests.show', $demand->id) }}" class="px-4 py-2 bg-rdc-blue text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition">Répondre</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Missions en cours --}}
                @foreach($artisanMissions ?? [] as $mission)
                    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition-all group" data-aos="fade-up" data-aos-delay="{{ ($loop->index + 3) * 100 }}">
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            <i class="fas {{ $mission->status === 'completed' ? 'fa-check' : 'fa-tools' }}"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="font-bold text-slate-900">{{ $mission->service->title ?? 'Mission' }}</h4>
                            <p class="text-xs text-slate-500 mt-1"><i class="fas fa-user mr-1"></i> {{ $mission->client->name ?? 'Client' }}</p>
                        </div>
                        <div class="text-center md:text-right">
                            @if($mission->status === 'in_progress')
                                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full mb-2">En cours</span>
                            @elseif($mission->status === 'completed')
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full mb-2">Terminée</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-full mb-2">{{ ucfirst($mission->status) }}</span>
                            @endif
                            <div class="flex gap-2 justify-center md:justify-end">
                                <a href="{{ route('user.missions.show', $mission->id) }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">Détails</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Profil Artisan Completion -->
        <div class="space-y-6">
            <div class="bg-slate-900 rounded-[3.5rem] p-8 text-white shadow-2xl relative overflow-hidden group h-full flex flex-col justify-center">
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-amber-500/20 rounded-full blur-3xl group-hover:bg-amber-500/30 transition-all"></div>
                
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Profil Artisan</h4>
                
                <div class="space-y-6 relative z-10 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full border-4 border-white/5 relative mx-auto">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="40" cy="40" r="36" stroke="rgba(255,255,255,0.05)" stroke-width="6" fill="transparent" />
                            <circle cx="40" cy="40" r="36" stroke="#F59E0B" stroke-width="6" fill="transparent" stroke-dasharray="226" stroke-dashoffset="40" class="transition-all duration-1000" />
                        </svg>
                        <span class="absolute text-xl font-black">80%</span>
                    </div>

                    <p class="text-sm font-medium text-slate-300 leading-relaxed">
                        Ajoutez des photos à votre portfolio pour gagner en visibilité auprès des clients.
                    </p>
                    <button class="w-full py-4 bg-amber-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-[0.2em] shadow-xl hover:scale-105 transition-all outline-none">
                        Compléter mon profil
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
