@extends('layouts.auth')

@section('title', 'Créer un compte — Choisissez votre profil')

@section('content')
<div class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">

    <!-- Background Gradient Blobs -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-rdc-blue/10 rounded-2xl flex items-center justify-center text-rdc-blue text-2xl mx-auto mb-4 shadow-sm">
            <i class="fas fa-users-gear"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2 font-heading">Rejoindre ProConnect</h2>
        <p class="text-slate-500 text-sm">Choisissez votre type de compte pour commencer</p>
    </div>

    <!-- Profile Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

        <!-- Client Card -->
        <div class="group relative flex flex-col justify-between p-6 rounded-2xl border-2 border-slate-200/80 bg-white/80 hover:border-rdc-blue hover:shadow-xl hover:shadow-rdc-blue/10 transition-all duration-300">
            <div>
                <!-- Icon + Title -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center text-2xl shadow-sm border border-blue-100 group-hover:scale-105 transition-transform">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-rdc-blue block">Profil</span>
                        <h3 class="text-xl font-bold text-slate-900">Client</h3>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-sm text-slate-600 font-medium mb-4 leading-relaxed">
                    « Trouvez un artisan de confiance près de chez vous. »
                </p>

                <!-- Advantages -->
                <ul class="space-y-2.5 mb-6 text-xs text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Artisans vérifiés et notés</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Demande publiée en une minute</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Suivi de vos missions</span>
                    </li>
                </ul>
            </div>

            <!-- CTA Button -->
            <a href="{{ route('register', ['type' => 'client']) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md hover:shadow-lg transition-all">
                <span>Continuer en tant que client</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Artisan Card -->
        <div class="group relative flex flex-col justify-between p-6 rounded-2xl border-2 border-slate-200/80 bg-white/80 hover:border-amber-500 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300">
            <div>
                <!-- Icon + Title -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-sm border border-amber-100 group-hover:scale-105 transition-transform">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-amber-600 block">Professionnel</span>
                        <h3 class="text-xl font-bold text-slate-900">Artisan</h3>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-sm text-slate-600 font-medium mb-4 leading-relaxed">
                    « Recevez de nouveaux clients dans votre quartier. »
                </p>

                <!-- Advantages -->
                <ul class="space-y-2.5 mb-6 text-xs text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Profil professionnel avec vos réalisations</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Demandes adaptées à votre métier</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>Une bonne note pour plus de visibilité</span>
                    </li>
                </ul>
            </div>

            <!-- CTA Button -->
            <a href="{{ route('register', ['type' => 'artisan']) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md hover:shadow-lg transition-all">
                <span>Continuer en tant qu'artisan</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

    </div>

    <!-- Divider -->
    <div class="relative py-2 mb-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-[10px] uppercase">
            <span class="bg-white/80 backdrop-blur-sm px-3 text-slate-400 font-semibold tracking-wider">Ou s'inscrire avec</span>
        </div>
    </div>

    <!-- Google Register -->
    <a href="{{ url('auth/google') }}" class="flex items-center justify-center gap-2 px-3 py-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all group shadow-sm hover:shadow-md mb-6">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
        <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900">Continuer avec Google</span>
    </a>

    <!-- Login link -->
    <div class="text-center text-sm text-slate-500">
        Déjà membre ?
        <a href="{{ route('login') }}" class="text-rdc-dark-blue font-bold hover:text-rdc-blue transition-colors ml-1 relative inline-block group">
            Se connecter
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-rdc-yellow transition-all duration-300 group-hover:w-full"></span>
        </a>
    </div>
</div>
@endsection
