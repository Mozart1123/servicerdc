@extends('layouts.public')

@section('title', $artisan->name . ' — Artisan ProConnect')
@section('meta_description', Str::limit($artisan->bio ?? 'Profil artisan sur ProConnect RDC', 160))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: 'services' }">

    {{-- Breadcrumb --}}
    <nav class="text-xs font-bold text-slate-400 mb-8 flex items-center gap-2">
        <a href="{{ route('public.artisans.index') }}" class="hover:text-[#29B6D1] transition-colors">Artisans</a>
        <i class="fas fa-chevron-right text-[9px]"></i>
        <span class="text-slate-600">{{ $artisan->name }}</span>
    </nav>

    <div class="space-y-6">

        {{-- Profile Card --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            {{-- Cover --}}
            <div class="h-56 sm:h-60 relative overflow-hidden" style="background: linear-gradient(115deg, #29B6D1 0%, #1E9CB5 45%, #090D16 100%);">
                <div class="absolute inset-0 opacity-15" style="background-image: repeating-linear-gradient(115deg, #fff 0px, #fff 2px, transparent 2px, transparent 26px);"></div>
                <i class="fas fa-hammer absolute" style="right:20px; bottom:-18px; font-size:150px; color:rgba(255,255,255,0.12);"></i>
            </div>

            <div class="px-6 sm:px-8 pb-8 -mt-14 relative">
                <div class="flex items-end gap-5 flex-wrap">
                    @if($artisan->profile_photo)
                        <img src="{{ Storage::url($artisan->profile_photo) }}"
                             class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-lg shrink-0" alt="{{ $artisan->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($artisan->name) }}&background=29B6D1&color=fff&size=220"
                             class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-lg shrink-0" alt="{{ $artisan->name }}">
                    @endif

                    <div class="flex-1 min-w-[240px] pb-1.5">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl font-bold text-slate-900">{{ $artisan->name }}</h1>
                            @include('partials.verified-badge', ['user' => $artisan])
                            @if($artisan->artisanLevel && $artisan->artisanLevel->level !== 'nouveau')
                                @php
                                    $level = $artisan->artisanLevel;
                                    $badgeClass = match($level->level) {
                                        'actif' => 'bg-slate-100 text-slate-600',
                                        'verifie' => 'bg-[#29B6D1]/10 text-[#29B6D1]',
                                        'elite' => 'bg-amber-100 text-amber-600',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badgeClass }}">
                                    <i class="fas {{ $level->level_icon }}"></i>
                                    {{ $level->level_label }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-[#29B6D1] font-semibold mt-1">{{ $artisan->profession ?? 'Artisan' }}</p>
                    </div>

                    <div class="flex items-center gap-2.5 pb-1.5">
                        <button type="button" @click="tab = 'services'"
                                class="inline-flex items-center gap-2 px-5 py-3 bg-[#29B6D1] text-white text-sm font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/25 whitespace-nowrap">
                            <i class="fas fa-paper-plane"></i>Demander un service
                        </button>
                        @auth
                            <a href="{{ route('user.messages.start.user', $artisan->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-3 bg-white text-[#29B6D1] text-sm font-bold rounded-2xl border border-[#29B6D1]/30 hover:border-[#29B6D1]/50 transition-all whitespace-nowrap">
                                <i class="fas fa-envelope"></i>Contacter
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 px-4 py-3 bg-white text-[#29B6D1] text-sm font-bold rounded-2xl border border-[#29B6D1]/30 hover:border-[#29B6D1]/50 transition-all whitespace-nowrap">
                                <i class="fas fa-sign-in-alt"></i>Contacter
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Stats — ligne principale --}}
                <div class="flex items-center gap-3.5 flex-wrap mt-5 text-[13px] text-slate-500 font-medium">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-slate-400"></i>{{ $artisan->city ?? 'RDC' }}
                    </span>

                    @if($artisan->artisanLevel && $artisan->artisanLevel->average_rating > 0)
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="inline-flex items-center gap-0.5">
                            @php $avg = (float) $artisan->artisanLevel->average_rating; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($avg >= $i)
                                    <i class="fas fa-star text-amber-400 text-xs"></i>
                                @elseif($avg > $i - 1)
                                    <i class="fas fa-star-half-alt text-amber-400 text-xs"></i>
                                @else
                                    <i class="far fa-star text-slate-200 text-xs"></i>
                                @endif
                            @endfor
                            <span class="font-bold text-slate-700 ml-1">{{ number_format($avg, 1) }}</span>
                            <span>({{ $reviewsCount }} avis)</span>
                        </span>
                    @endif

                    @if($artisan->artisanLevel && $artisan->artisanLevel->total_missions > 0)
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-slate-400"></i>{{ $artisan->artisanLevel->total_missions }} missions réalisées
                        </span>
                    @endif
                </div>

                {{-- Stats — ligne secondaire --}}
                <div class="flex items-center gap-2.5 flex-wrap mt-2.5 text-xs text-slate-400 font-semibold">
                    <span>{{ $services->count() }} service{{ $services->count() > 1 ? 's' : '' }} publié{{ $services->count() > 1 ? 's' : '' }}</span>
                    <span class="w-[3px] h-[3px] rounded-full bg-slate-200"></span>
                    <span>Membre depuis {{ $artisan->created_at->format('M Y') }}</span>
                    <span class="w-[3px] h-[3px] rounded-full bg-slate-200"></span>
                    <span class="inline-flex items-center gap-1.5">
                        Statut :
                        @if($artisan->status === 'active')
                            <span class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-black rounded uppercase">Actif</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-black rounded uppercase">{{ ucfirst($artisan->status) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-1.5 flex items-center gap-1 overflow-x-auto">
            <button type="button" @click="tab = 'apropos'"
                    :class="tab === 'apropos' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-[13px] font-bold whitespace-nowrap transition-colors">
                À propos
            </button>
            <button type="button" @click="tab = 'services'"
                    :class="tab === 'services' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-[13px] font-bold whitespace-nowrap transition-colors">
                Services <span class="opacity-60">({{ $services->count() }})</span>
            </button>
            <button type="button" @click="tab = 'realisations'"
                    :class="tab === 'realisations' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-[13px] font-bold whitespace-nowrap transition-colors">
                Réalisations
            </button>
            <button type="button" @click="tab = 'avis'"
                    :class="tab === 'avis' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-[13px] font-bold whitespace-nowrap transition-colors">
                Avis <span class="opacity-60">({{ $reviewsCount }})</span>
            </button>
        </div>

        {{-- Tab : À propos --}}
        <div x-show="tab === 'apropos'" style="display: none;">
            @if($artisan->bio)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-3">À propos</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $artisan->bio }}</p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
                    <i class="fas fa-user text-3xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Cet artisan n'a pas encore complété sa présentation.</p>
                </div>
            @endif
        </div>

        {{-- Tab : Services --}}
        <div x-show="tab === 'services'" style="display: none;">
            <h2 class="text-lg font-bold text-slate-900 mb-5">
                Services proposés
                <span class="text-sm font-semibold text-slate-400 ml-2">({{ $services->count() }})</span>
            </h2>

            @if($services->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
                    <i class="fas fa-tools text-3xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Cet artisan n'a pas encore publié de services.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($services as $service)
                        <a href="{{ route('public.services.show', $service->id) }}"
                           class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden group">
                            <div class="h-36 bg-gradient-to-br from-[#29B6D1]/10 to-slate-50 overflow-hidden">
                                @if($service->service_image)
                                    <img src="{{ Storage::url($service->service_image) }}" alt="{{ $service->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-tools text-3xl text-[#29B6D1]/30"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-slate-900 text-sm group-hover:text-[#29B6D1] transition-colors line-clamp-2">{{ $service->title }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-slate-400 font-medium">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $service->location ?? $artisan->city ?? 'RDC' }}
                                    </span>
                                    <span class="text-sm font-black text-[#29B6D1]">
                                        @if($service->price) ${{ number_format($service->price, 0) }} @else Sur devis @endif
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tab : Réalisations --}}
        <div x-show="tab === 'realisations'" style="display: none;">
            <h2 class="text-lg font-bold text-slate-900 mb-5">Mes réalisations</h2>
            <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
                <i class="fas fa-images text-3xl text-slate-200 mb-3"></i>
                <p class="text-sm text-slate-400 font-medium">Aucune réalisation pour l'instant.</p>
            </div>
        </div>

        {{-- Tab : Avis --}}
        <div x-show="tab === 'avis'" style="display: none;">
            <h2 class="text-lg font-bold text-slate-900 mb-5">
                Avis
                <span class="text-sm font-semibold text-slate-400 ml-2">({{ $reviewsCount }})</span>
            </h2>

            @if($reviews->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
                    <i class="fas fa-comment-dots text-3xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Aucun avis pour l'instant.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $review->client?->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->client?->name ?? 'Client').'&background=e2e8f0&color=475569' }}"
                                         class="w-9 h-9 rounded-full object-cover" alt="{{ $review->client?->name }}">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $review->client?->name ?? 'Client ProConnect' }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $review->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }} text-xs"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($review->feedback)
                                <p class="text-sm text-slate-600 leading-relaxed mt-3">{{ $review->feedback }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
