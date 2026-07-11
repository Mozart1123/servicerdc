@extends('layouts.public')

@section('title', $artisan->name . ' — Artisan ProConnect')
@section('meta_description', Str::limit($artisan->bio ?? 'Profil artisan sur ProConnect RDC', 160))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs font-bold text-slate-400 mb-8 flex items-center gap-2">
        <a href="{{ route('public.artisans.index') }}" class="hover:text-[#29B6D1] transition-colors">Artisans</a>
        <i class="fas fa-chevron-right text-[9px]"></i>
        <span class="text-slate-600">{{ $artisan->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left / Services --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Profile Card --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                {{-- Cover --}}
                <div class="h-28 bg-gradient-to-r from-[#29B6D1] to-[#090D16]"></div>
                <div class="px-6 pb-6 -mt-10">
                    <div class="flex items-end gap-4">
                        @if($artisan->profile_photo)
                            <img src="{{ Storage::url($artisan->profile_photo) }}"
                                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-lg" alt="{{ $artisan->name }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($artisan->name) }}&background=29B6D1&color=fff&size=160"
                                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-lg" alt="{{ $artisan->name }}">
                        @endif
                        <div class="mb-1 flex-1">
                            <h1 class="text-xl font-bold text-slate-900">{{ $artisan->name }}</h1>
                            <p class="text-sm text-[#29B6D1] font-semibold">{{ $artisan->profession ?? 'Artisan' }}</p>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $artisan->city ?? 'RDC' }}
                            </p>
                        </div>
                    </div>

                    @if($artisan->bio)
                    <div class="mt-5 pt-5 border-t border-slate-50">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">À propos</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $artisan->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Services --}}
            <div>
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
        </div>

        {{-- Right / Contact Sidebar --}}
        <div class="space-y-5">

            {{-- Stats --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Statistiques</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 font-medium">Services publiés</span>
                        <span class="font-black text-slate-900">{{ $services->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 font-medium">Membre depuis</span>
                        <span class="font-bold text-slate-700 text-sm">{{ $artisan->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 font-medium">Statut</span>
                        <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase">Actif</span>
                    </div>
                </div>
            </div>

            {{-- Contact CTA --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Contacter</h3>
                @auth
                    <a href="{{ route('user.messages.start.user', $artisan->id) }}"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm">
                        <i class="fas fa-envelope mr-2"></i>Envoyer un message
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm mb-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion pour contacter
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full text-center px-5 py-3 bg-slate-50 text-slate-700 font-bold rounded-2xl hover:bg-slate-100 transition-all text-sm">
                        Créer un compte
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
