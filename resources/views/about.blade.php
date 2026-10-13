@extends('layouts.public')

@section('title', 'À propos')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-16 sm:py-20 space-y-14">

    <header class="text-center max-w-3xl mx-auto">
        <p class="text-xs font-black uppercase tracking-widest text-rdc-blue mb-3">À propos</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-5">ProConnect relie les clients aux artisans de confiance en RDC</h1>
        <p class="text-slate-600 leading-relaxed">
            Trouver un bon plombier, peintre, électricien ou photographe près de chez soi ne devrait pas dépendre du bouche-à-oreille.
            ProConnect rassemble des artisans notés par leurs clients et vous aide à choisir le bon, au bon prix, dans votre commune.
        </p>
    </header>

    <section>
        <h2 class="text-xl font-bold text-slate-900 mb-6 text-center">Comment ça marche</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach([
                ['fa-pen-to-square', 'Décrivez votre besoin', 'Le service, quelques photos, votre commune, la date et votre budget : une minute suffit.'],
                ['fa-users-viewfinder', 'Recevez des propositions', 'Votre demande est envoyée aux artisans les plus adaptés, choisis selon leur métier, leur commune, leur note et leur disponibilité.'],
                ['fa-handshake', 'Choisissez et suivez', 'Comparez prix, avis et réalisations, discutez dans la messagerie, puis suivez la mission jusqu\'à la fin.'],
            ] as [$icon, $title, $text])
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="w-12 h-12 rounded-xl bg-rdc-blue/10 text-rdc-blue flex items-center justify-center text-xl mb-4">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">{{ $title }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-slate-50 rounded-2xl p-6">
            <h2 class="font-bold text-slate-900 mb-3"><i class="fas fa-user text-rdc-blue mr-2"></i>Pour les clients</h2>
            <ul class="space-y-2 text-sm text-slate-600">
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Artisans vérifiés et notés par d'autres clients</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Propositions comparables avant de vous engager</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Échanges dans la messagerie, numéros protégés jusqu'à votre choix</li>
            </ul>
        </div>
        <div class="bg-slate-50 rounded-2xl p-6">
            <h2 class="font-bold text-slate-900 mb-3"><i class="fas fa-hammer text-amber-500 mr-2"></i>Pour les artisans</h2>
            <ul class="space-y-2 text-sm text-slate-600">
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Des demandes de clients dans vos communes</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Un profil professionnel avec vos réalisations</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Une bonne note et une réponse rapide pour plus de visibilité</li>
            </ul>
        </div>
    </section>

    <section class="text-center">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('public.services.index') }}" class="px-6 py-3 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md">
                Trouver un artisan
            </a>
            @guest
                <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50">
                    Créer un compte
                </a>
            @endguest
        </div>
    </section>
</div>
@endsection
