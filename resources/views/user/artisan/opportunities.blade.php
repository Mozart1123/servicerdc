@extends('layouts.user')

@section('header_title', 'Opportunités')

@section('content')
<div class="space-y-10 pb-20">

    <!-- Header Section -->
    <div class="relative">
        <div class="absolute inset-0 bg-blue-500/5 rounded-3xl blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-3xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center text-3xl shadow-inner border border-blue-100">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-black text-slate-900 uppercase">Opportunités</h2>
                        <span class="px-3 py-1 bg-blue-100 text-rdc-blue text-[10px] font-black uppercase tracking-wider rounded-full">
                            Marché Ouvert
                        </span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">
                        Demandes publiées sans artisan désigné — postulez en envoyant une proposition
                    </p>
                </div>
            </div>

            <div class="text-center md:text-right">
                <p class="text-2xl font-black text-slate-900">{{ $openRequests->total() ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Demandes ouvertes</p>
            </div>
        </div>
    </div>

    <!-- Info Banner (Système de matching en cours de déploiement) -->
    <div class="p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 text-base shadow-sm">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="flex-1 text-xs">
            <h4 class="font-bold text-slate-900 text-sm mb-0.5">Comment fonctionnent les opportunités ?</h4>
            <p class="text-slate-600 leading-relaxed">
                Contrairement à vos <a href="{{ route('user.artisan.service-requests.index') }}" class="font-bold text-rdc-blue underline">Demandes reçues</a> qui vous sont adressées directement, les <strong>Opportunités</strong> regroupent les besoins exprimés par des clients à proximité qui recherchent encore le bon artisan.
            </p>
        </div>
    </div>

    <!-- Liste des opportunités -->
    <div class="space-y-4">
        @forelse($openRequests as $req)
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 hover:shadow-md transition-all">
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 text-2xl shrink-0">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-slate-900 text-base">
                            {{ $req->requested_service_name ?? $req->service?->title ?? 'Demande de service' }}
                        </h4>
                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i> {{ $req->user?->name ?? 'Client' }}</span>
                            @if($req->city)
                                <span>• <i class="fas fa-location-dot mr-1 text-slate-400"></i> {{ $req->city }}</span>
                            @endif
                            @if($req->budget)
                                <span>• <i class="fas fa-money-bill mr-1 text-emerald-500 font-bold"></i> Budget : <strong>{{ $req->budget }} $</strong></span>
                            @endif
                            <span>• <i class="fas fa-clock mr-1 text-slate-400"></i> {{ $req->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    <a href="{{ route('user.service-requests.show', $req->id) }}"
                       class="px-5 py-2.5 bg-rdc-blue hover:bg-rdc-blue-dark text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        Voir le besoin & Proposer
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 border border-slate-100 shadow-sm text-center max-w-xl mx-auto">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Aucune opportunité pour le moment</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-6">
                    Les nouvelles demandes ouvertes publiées par les clients sans artisan désigné dans vos communes d'intervention apparaîtront ici. Revenez régulièrement pour découvrir de nouveaux chantiers !
                </p>
                <a href="{{ route('user.artisan.service-requests.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                    <i class="fas fa-inbox"></i> Consulter mes demandes directes
                </a>
            </div>
        @endforelse

        @if($openRequests->hasPages())
            <div class="mt-6">
                {{ $openRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
