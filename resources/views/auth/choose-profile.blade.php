@extends('layouts.auth')

@section('title', 'Créer un compte — Choisissez votre profil')
{{-- Deux cartes côte à côte : plus large que les formulaires de connexion --}}
@section('auth_width', 'max-w-3xl')

@php
    // Les deux profils partagent la même carte : seuls le contenu et les couleurs changent.
    $profiles = [
        [
            'type'     => 'client',
            'label'    => 'Profil',
            'title'    => 'Client',
            'icon'     => 'fa-user-tie',
            'tagline'  => 'Trouvez un artisan de confiance près de chez vous.',
            'benefits' => ['Artisans vérifiés et notés', 'Demande publiée en une minute', 'Suivi de vos missions'],
            'cta'      => 'Continuer en tant que client',
            // Cyan ProConnect ; bouton un ton plus profond pour un texte blanc lisible (contraste ≥ 4,5:1).
            'card'     => 'border-cyan-200/80 bg-gradient-to-b from-cyan-50/80 to-white hover:border-[#20B8D0] hover:shadow-[0_18px_40px_-18px_rgba(32,184,208,0.55)]',
            'iconBox'  => 'border-cyan-100 text-[#20B8D0]',
            'labelCls' => 'text-[#0E97AF]',
            'button'   => 'bg-gradient-to-r from-[#0E97AF] to-[#0B7F94] text-white hover:from-[#0B8CA3] hover:to-[#096F82] focus-visible:ring-cyan-300',
        ],
        [
            'type'     => 'artisan',
            'label'    => 'Professionnel',
            'title'    => 'Artisan',
            'icon'     => 'fa-hammer',
            'tagline'  => 'Recevez de nouveaux clients dans votre quartier.',
            'benefits' => ['Profil professionnel avec vos réalisations', 'Demandes adaptées à votre métier', 'Une bonne note pour plus de visibilité'],
            'cta'      => "Continuer en tant qu'artisan",
            // Orange artisan ; texte bleu nuit sur l'orange pour rester lisible.
            'card'     => 'border-amber-200/80 bg-gradient-to-b from-amber-50/80 to-white hover:border-[#FF9800] hover:shadow-[0_18px_40px_-18px_rgba(255,152,0,0.55)]',
            'iconBox'  => 'border-amber-100 text-[#FF9800]',
            'labelCls' => 'text-[#C26A00]',
            'button'   => 'bg-gradient-to-r from-[#FFA31A] to-[#FF9800] text-[#111B32] hover:from-[#FF9800] hover:to-[#F08A00] focus-visible:ring-amber-300',
        ],
    ];
@endphp

@section('content')
<div class="relative rounded-3xl border border-slate-100 bg-white p-5 shadow-[0_24px_60px_-20px_rgba(17,27,50,0.22)] sm:p-8">

    {{-- En-tête --}}
    <header class="mb-7 text-center sm:mb-8">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-50 text-2xl text-[#20B8D0] shadow-sm">
            <i class="fas fa-users" aria-hidden="true"></i>
        </div>
        <h2 class="font-heading text-2xl font-bold text-[#111B32] sm:text-3xl">Rejoindre ProConnect</h2>
        <p class="mt-2 text-sm text-slate-500 sm:text-base">Choisissez votre type de compte pour commencer</p>
    </header>

    {{-- Cartes de profil : côte à côte dès qu'elles ont la place (tablette, et à partir de 1200 px).
         Entre 1024 et 1199 px le panneau de gauche réduit la colonne : empilées. Plage exclusive,
         pour ne pas dépendre de l'ordre des règles générées par Tailwind. --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:max-[1199px]:grid-cols-1">
        @foreach($profiles as $profile)
            <article class="group flex h-full flex-col rounded-2xl border p-5 transition-all duration-300 hover:-translate-y-0.5 sm:p-6 {{ $profile['card'] }}">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border bg-white text-2xl shadow-sm transition-transform duration-300 group-hover:scale-105 {{ $profile['iconBox'] }}">
                        <i class="fas {{ $profile['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.12em] {{ $profile['labelCls'] }}">{{ $profile['label'] }}</p>
                        <h3 class="font-heading text-2xl font-bold leading-tight text-[#111B32]">{{ $profile['title'] }}</h3>
                    </div>
                </div>

                {{-- Espaces insécables : les guillemets ne restent jamais seuls en bout de ligne --}}
                <p class="mt-5 text-[15px] leading-relaxed text-slate-600">«&nbsp;{{ $profile['tagline'] }}&nbsp;»</p>

                {{-- flex-1 : la liste occupe l'espace libre, les deux boutons s'alignent en bas --}}
                <ul class="mt-5 flex-1 space-y-3">
                    @foreach($profile['benefits'] as $benefit)
                        <li class="flex items-start gap-3 text-sm leading-snug text-slate-600">
                            <i class="fas fa-circle-check mt-0.5 shrink-0 text-base text-emerald-500" aria-hidden="true"></i>
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register', ['type' => $profile['type']]) }}"
                   class="mt-6 flex w-full items-center justify-between gap-3 rounded-xl px-5 py-3.5 text-left text-[15px] font-bold leading-snug shadow-md transition-all duration-200 hover:shadow-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-offset-2 {{ $profile['button'] }}">
                    <span>{{ $profile['cta'] }}</span>
                    <i class="fas fa-arrow-right shrink-0 transition-transform duration-200 group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
            </article>
        @endforeach
    </div>

    {{-- Séparateur --}}
    <div class="relative mb-5 py-2">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-[10px] uppercase">
            <span class="bg-white px-3 font-semibold tracking-wider text-slate-400">Ou s'inscrire avec</span>
        </div>
    </div>

    {{-- Inscription Google --}}
    <a href="{{ url('auth/google') }}"
       class="group mb-6 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-cyan-200">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5" alt="">
        <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900">Continuer avec Google</span>
    </a>

    {{-- Connexion --}}
    <div class="text-center text-sm text-slate-500">
        Déjà membre ?
        <a href="{{ route('login') }}" class="group relative ml-1 inline-block font-bold text-rdc-dark-blue transition-colors hover:text-rdc-blue">
            Se connecter
            <span class="absolute bottom-0 left-0 h-0.5 w-0 bg-rdc-yellow transition-all duration-300 group-hover:w-full"></span>
        </a>
    </div>
</div>
@endsection
