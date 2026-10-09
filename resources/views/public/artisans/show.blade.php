@extends('layouts.public')

@section('title', $artisan->name . ' — Artisan ProConnect')
@section('meta_description', Str::limit($artisan->bio ?? 'Profil artisan sur ProConnect RDC', 160))

@section('content')
@php
    $realisationsJson = $realisations->map(function ($r) {
        return [
            'id' => $r->id,
            'image_url' => $r->image_url,
            'caption' => $r->caption ?? '',
        ];
    })->values()->toJson();
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{
        tab: 'services',
        lightboxOpen: false,
        currentIndex: 0,
        zoomLevel: 1,
        realisations: {{ $realisationsJson }},
        touchStartX: 0,
        touchStartY: 0,
        openLightbox(index) {
            this.currentIndex = index;
            this.zoomLevel = 1;
            this.lightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeLightbox() {
            this.lightboxOpen = false;
            this.zoomLevel = 1;
            document.body.style.overflow = '';
        },
        nextImage() {
            if (this.realisations.length > 0) {
                this.currentIndex = (this.currentIndex + 1) % this.realisations.length;
                this.zoomLevel = 1;
            }
        },
        prevImage() {
            if (this.realisations.length > 0) {
                this.currentIndex = (this.currentIndex - 1 + this.realisations.length) % this.realisations.length;
                this.zoomLevel = 1;
            }
        },
        toggleZoom() {
            this.zoomLevel = this.zoomLevel === 1 ? 2 : 1;
        },
        handleTouchStart(e) {
            this.touchStartX = e.touches[0].clientX;
            this.touchStartY = e.touches[0].clientY;
        },
        handleTouchEnd(e) {
            const diffX = e.changedTouches[0].clientX - this.touchStartX;
            const diffY = e.changedTouches[0].clientY - this.touchStartY;
            if (diffY > 80 && Math.abs(diffX) < 60) {
                this.closeLightbox();
                return;
            }
            if (Math.abs(diffX) > 40) {
                if (diffX > 0) { this.prevImage(); }
                else { this.nextImage(); }
            }
        }
     }"
     @keydown.window.escape="if(lightboxOpen) closeLightbox()"
     @keydown.window.arrow-right="if(lightboxOpen) nextImage()"
     @keydown.window.arrow-left="if(lightboxOpen) prevImage()">

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
            @if($artisan->cover_photo)
                <div class="h-56 sm:h-60 relative overflow-hidden">
                    <img src="{{ Storage::url($artisan->cover_photo) }}" alt="Couverture de {{ $artisan->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="h-56 sm:h-60 relative overflow-hidden" style="background: linear-gradient(115deg, #29B6D1 0%, #1E9CB5 45%, #090D16 100%);">
                    <div class="absolute inset-0 opacity-15" style="background-image: repeating-linear-gradient(115deg, #fff 0px, #fff 2px, transparent 2px, transparent 26px);"></div>
                    <i class="fas fa-hammer absolute" style="right:20px; bottom:-18px; font-size:150px; color:rgba(255,255,255,0.12);"></i>
                </div>
            @endif

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
                        <div class="flex items-center gap-2.5 flex-wrap mt-1.5">
                            <span class="text-sm text-[#29B6D1] font-bold">{{ $artisan->main_profession }}</span>
                            @php $ratingInfo = $artisan->rating_summary; @endphp
                            @if($ratingInfo['is_new'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-slate-100 text-slate-600 rounded-full text-xs font-bold">
                                    <i class="fas fa-sparkles text-amber-500"></i> Nouveau
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-full text-xs font-bold">
                                    <i class="fas fa-star text-amber-400 text-xs"></i> {{ $ratingInfo['badge'] }}
                                </span>
                            @endif
                            @if($artisan->response_time_badge)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/60 rounded-full text-xs font-semibold">
                                    <i class="fas fa-bolt text-blue-500 text-[10px]"></i> {{ $artisan->response_time_badge }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap w-full sm:w-auto pb-1.5">
                        <button type="button"
                                @click="tab = 'services'; $nextTick(() => document.getElementById('artisan-tabs')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#29B6D1] text-white text-sm font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/25 whitespace-nowrap">
                            <i class="fas fa-paper-plane"></i>Demander un service
                        </button>
                        @auth
                            <a href="{{ route('user.messages.start.user', $artisan->id) }}"
                               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 bg-white text-[#29B6D1] text-sm font-bold rounded-2xl border border-[#29B6D1]/30 hover:border-[#29B6D1]/50 transition-all whitespace-nowrap">
                                <i class="fas fa-envelope"></i>Contacter
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 bg-white text-[#29B6D1] text-sm font-bold rounded-2xl border border-[#29B6D1]/30 hover:border-[#29B6D1]/50 transition-all whitespace-nowrap">
                                <i class="fas fa-sign-in-alt"></i>Contacter
                            </a>
                        @endauth

                        @auth
                            @if(auth()->user()->isClient())
                                <button type="button"
                                        data-favorite-toggle
                                        data-url="{{ route('user.artisans.favorite.toggle', $artisan->id) }}"
                                        data-favorited="{{ $isFavorited ? '1' : '0' }}"
                                        aria-label="Ajouter aux favoris"
                                        class="inline-flex items-center justify-center w-[50px] h-[50px] rounded-2xl border transition-all shrink-0 {{ $isFavorited ? 'bg-red-50 border-red-200 text-red-500' : 'bg-white border-slate-100 text-slate-300 hover:text-red-400 hover:border-red-100' }}">
                                    <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" aria-label="Connexion pour ajouter aux favoris"
                               class="inline-flex items-center justify-center w-[50px] h-[50px] rounded-2xl border border-slate-100 text-slate-300 hover:text-red-400 hover:border-red-100 transition-all shrink-0">
                                <i class="far fa-heart"></i>
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
        <div id="artisan-tabs" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-1.5 flex items-center gap-0.5 sm:gap-1 overflow-x-auto">
            <button type="button" @click="tab = 'apropos'"
                    :class="tab === 'apropos' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-[11px] sm:text-[13px] font-bold whitespace-nowrap transition-colors">
                À propos
            </button>
            <button type="button" @click="tab = 'services'"
                    :class="tab === 'services' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-[11px] sm:text-[13px] font-bold whitespace-nowrap transition-colors">
                Services <span class="opacity-60">({{ $services->count() }})</span>
            </button>
            <button type="button" @click="tab = 'realisations'"
                    :class="tab === 'realisations' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-[11px] sm:text-[13px] font-bold whitespace-nowrap transition-colors">
                Réalisations
            </button>
            <button type="button" @click="tab = 'avis'"
                    :class="tab === 'avis' ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl text-[11px] sm:text-[13px] font-bold whitespace-nowrap transition-colors">
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

            @php
                $hasPracticalInfo = $artisan->years_experience
                    || !empty($artisan->languages)
                    || $artisan->intervention_zone
                    || !is_null($artisan->home_service)
                    || $artisan->address;
            @endphp
            @if($hasPracticalInfo)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 mt-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-5">Informations pratiques</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @if($artisan->years_experience)
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#29B6D1]/10 text-[#29B6D1] flex items-center justify-center shrink-0">
                                    <i class="fas fa-briefcase text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Expérience</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $artisan->years_experience }} an{{ $artisan->years_experience > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        @endif

                        @if(!empty($artisan->languages))
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#29B6D1]/10 text-[#29B6D1] flex items-center justify-center shrink-0">
                                    <i class="fas fa-language text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Langues parlées</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($artisan->languages as $lang)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">{{ $lang }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($artisan->intervention_zone)
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#29B6D1]/10 text-[#29B6D1] flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-marked-alt text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Zone d'intervention</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $artisan->intervention_zone }}</p>
                                </div>
                            </div>
                        @endif

                        @if(!is_null($artisan->home_service))
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#29B6D1]/10 text-[#29B6D1] flex items-center justify-center shrink-0">
                                    <i class="fas fa-house-user text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Déplacement à domicile</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $artisan->home_service ? 'Oui' : 'Non' }}</p>
                                </div>
                            </div>
                        @endif

                        @if($artisan->address)
                            <div class="flex items-start gap-3 sm:col-span-2">
                                <div class="w-9 h-9 rounded-lg bg-[#29B6D1]/10 text-[#29B6D1] flex items-center justify-center shrink-0">
                                    <i class="fas fa-location-dot text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Adresse</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $artisan->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
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
                                        @if($service->pricing_type === 'quote' || empty($service->price))
                                            Sur devis
                                        @elseif($service->pricing_type === 'starting_from')
                                            À partir de ${{ number_format((float)$service->price, 0) }}
                                        @else
                                            ${{ number_format((float)$service->price, 0) }}
                                        @endif
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
            <h2 class="text-lg font-bold text-slate-900 mb-5">
                Réalisations
                <span class="text-sm font-semibold text-slate-400 ml-2">({{ $realisations->count() }})</span>
            </h2>

            @if($realisations->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
                    <i class="fas fa-images text-3xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Aucune réalisation pour l'instant.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                    @foreach($realisations as $realisation)
                        <div class="group bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden cursor-pointer hover:shadow-lg transition-all"
                             @click="openLightbox({{ $loop->index }})">
                            <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                <img src="{{ $realisation->image_url }}" alt="{{ $realisation->caption ?? 'Réalisation de ' . $artisan->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                    <span class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center shadow">
                                        <i class="fas fa-expand text-sm"></i>
                                    </span>
                                </div>
                            </div>
                            @if($realisation->caption)
                                <p class="px-3 py-2 text-xs font-semibold text-slate-600 truncate group-hover:text-rdc-blue transition-colors">{{ $realisation->caption }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
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

    {{-- Visionneuse plein écran pour réalisations (Point 7) --}}
    <div x-show="lightboxOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-md flex flex-col items-center justify-between p-4 sm:p-6 select-none"
         @touchstart="handleTouchStart($event)"
         @touchend="handleTouchEnd($event)">

        {{-- Top bar: counter + actions (zoom, close) --}}
        <div class="w-full flex items-center justify-between text-white z-10">
            <div class="text-xs sm:text-sm font-bold tracking-wider text-white/80">
                <span x-text="currentIndex + 1"></span> / <span x-text="realisations.length"></span>
            </div>
            <div class="flex items-center gap-2">
                {{-- Zoom button --}}
                <button type="button" @click.stop="toggleZoom()"
                        title="Zoomer"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fas" :class="zoomLevel > 1 ? 'fa-search-minus' : 'fa-search-plus'"></i>
                </button>
                {{-- Close button (Croix visible) --}}
                <button type="button" @click="closeLightbox()"
                        title="Fermer (Échap ou glisser vers le bas)"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        {{-- Center: Image with zoom and navigation arrows --}}
        <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden my-2"
             @click.self="closeLightbox()">
            
            {{-- Previous button --}}
            <button type="button"
                    x-show="realisations.length > 1"
                    @click.stop="prevImage()"
                    class="absolute left-2 sm:left-4 z-20 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center transition-all border border-white/20 backdrop-blur-sm"
                    title="Précédent">
                <i class="fas fa-chevron-left text-base sm:text-lg"></i>
            </button>

            {{-- Main Image --}}
            <template x-if="realisations.length > 0">
                <img :src="realisations[currentIndex]?.image_url"
                     :alt="realisations[currentIndex]?.caption || 'Réalisation'"
                     class="max-h-[80vh] max-w-full object-contain transition-transform duration-200 cursor-zoom-in"
                     :style="`transform: scale(${zoomLevel});`"
                     @click.stop="toggleZoom()">
            </template>

            {{-- Next button --}}
            <button type="button"
                    x-show="realisations.length > 1"
                    @click.stop="nextImage()"
                    class="absolute right-2 sm:right-4 z-20 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center transition-all border border-white/20 backdrop-blur-sm"
                    title="Suivant">
                <i class="fas fa-chevron-right text-base sm:text-lg"></i>
            </button>
        </div>

        {{-- Bottom bar: Caption + Swipe info --}}
        <div class="w-full text-center text-white z-10 max-w-xl mx-auto">
            <template x-if="realisations[currentIndex]?.caption">
                <p class="text-sm font-medium text-white/90 drop-shadow mb-1" x-text="realisations[currentIndex]?.caption"></p>
            </template>
            <p class="text-[10px] text-white/40 uppercase tracking-widest hidden sm:block">
                Flèches gauche/droite pour naviguer • Échap pour fermer
            </p>
            <p class="text-[10px] text-white/40 uppercase tracking-widest sm:hidden">
                Glissez pour naviguer • Glissez vers le bas pour fermer
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.querySelector('[data-favorite-toggle]');
    if (!btn) return;

    var csrf = document.querySelector('meta[name="csrf-token"]');
    csrf = csrf ? csrf.content : '';

    btn.addEventListener('click', function () {
        btn.disabled = true;

        fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            var icon = btn.querySelector('i');
            if (data.favorited) {
                btn.dataset.favorited = '1';
                btn.classList.remove('bg-white', 'border-slate-100', 'text-slate-300');
                btn.classList.add('bg-red-50', 'border-red-200', 'text-red-500');
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                btn.dataset.favorited = '0';
                btn.classList.remove('bg-red-50', 'border-red-200', 'text-red-500');
                btn.classList.add('bg-white', 'border-slate-100', 'text-slate-300');
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        })
        .catch(function () {
            // Échec silencieux — l'état visuel du cœur ne change simplement pas.
        })
        .finally(function () {
            btn.disabled = false;
        });
    });
});
</script>
@endpush
@endsection
