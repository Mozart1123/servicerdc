@extends('layouts.public')

@section('title', 'Annuaire des Artisans')
@section('meta_description', 'Trouvez des artisans qualifiés en République Démocratique du Congo.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-slate-900">Annuaire des Artisans</h1>
        <p class="text-slate-500 mt-2">Découvrez des professionnels qualifiés et expérimentés en RDC.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('public.artisans.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, spécialité..."
                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
            </div>
            <div class="flex gap-3">
                <input type="text" name="city" value="{{ request('city') }}" placeholder="Ville..."
                       class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
                <button type="submit" class="px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-xl text-sm hover:bg-[#1E9CB5] transition-all shadow-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Results Count --}}
    <p class="text-sm text-slate-400 font-medium mb-6">{{ $artisans->total() }} artisan(s) trouvé(s)</p>

    {{-- Artisans Grid --}}
    @if($artisans->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hard-hat text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-500">Aucun artisan trouvé</h3>
            <p class="text-sm text-slate-400 mt-1">Modifiez vos critères de recherche.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($artisans as $artisan)
                <a href="{{ route('public.artisans.show', $artisan->id) }}"
                   class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden group text-center p-6">

                    {{-- Avatar --}}
                    <div class="relative mb-4 inline-block">
                        @if($artisan->profile_photo)
                            <img src="{{ Storage::url($artisan->profile_photo) }}"
                                 class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-300" alt="{{ $artisan->name }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($artisan->name) }}&background=29B6D1&color=fff&size=160"
                                 class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-300" alt="{{ $artisan->name }}">
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 border-2 border-white rounded-full"></div>
                    </div>

                    {{-- Info --}}
                    <div class="flex items-center justify-center gap-2">
                        <h3 class="font-bold text-slate-900 text-sm group-hover:text-[#29B6D1] transition-colors">{{ $artisan->name }}</h3>
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
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                                <i class="fas {{ $level->level_icon }}"></i>
                                {{ $level->level_label }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-[#29B6D1] font-semibold mt-0.5">{{ $artisan->profession ?? 'Artisan' }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $artisan->city ?? 'RDC' }}
                    </p>

                    {{-- Stats --}}
                    <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-slate-50">
                        <div class="text-center">
                            <div class="text-sm font-black text-slate-900">{{ $artisan->services_count ?? 0 }}</div>
                            <div class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Services</div>
                        </div>
                        @if($artisan->bio)
                        <div class="text-center">
                            <div class="text-sm font-black text-slate-900">✓</div>
                            <div class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Profil</div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 px-4 py-2 bg-[#29B6D1]/10 text-[#29B6D1] text-xs font-bold rounded-xl group-hover:bg-[#29B6D1] group-hover:text-white transition-all">
                        Voir le profil
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $artisans->links() }}
        </div>
    @endif
</div>
@endsection
