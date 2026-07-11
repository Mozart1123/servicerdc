@extends('layouts.public')

@section('title', 'Catalogue des Services')
@section('meta_description', 'Trouvez des artisans qualifiés pour tous vos besoins en RDC.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-slate-900">Services Disponibles</h1>
        <p class="text-slate-500 mt-2">Trouvez des artisans qualifiés pour tous vos besoins.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('public.services.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un service..."
                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <input type="text" name="location" value="{{ request('location') }}" placeholder="Ville..."
                       class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
                <button type="submit" class="px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-xl text-sm hover:bg-[#1E9CB5] transition-all shadow-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Results Count --}}
    <p class="text-sm text-slate-400 font-medium mb-6">{{ $services->total() }} service(s) trouvé(s)</p>

    {{-- Services Grid --}}
    @if($services->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tools text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-500">Aucun service trouvé</h3>
            <p class="text-sm text-slate-400 mt-1">Modifiez vos critères de recherche.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($services as $service)
                <a href="{{ route('public.services.show', $service->id) }}"
                   class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden group">
                    {{-- Service Image --}}
                    <div class="h-44 bg-gradient-to-br from-[#29B6D1]/10 to-[#29B6D1]/5 overflow-hidden relative">
                        @if($service->service_image)
                            <img src="{{ Storage::url($service->service_image) }}" alt="{{ $service->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @elseif($service->gallery_images && count((array)$service->gallery_images) > 0)
                            <img src="{{ Storage::url(((array)$service->gallery_images)[0]) }}" alt="{{ $service->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-tools text-4xl text-[#29B6D1]/40"></i>
                            </div>
                        @endif
                        @if($service->category)
                            <span class="absolute top-3 left-3 px-2 py-1 bg-white/90 backdrop-blur text-[10px] font-black text-slate-600 rounded-lg uppercase tracking-widest">
                                {{ $service->category->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Service Info --}}
                    <div class="p-4">
                        <h3 class="font-bold text-slate-900 text-sm leading-snug mb-1 line-clamp-2 group-hover:text-[#29B6D1] transition-colors">
                            {{ $service->title }}
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $service->location ?? $service->city ?? 'RDC' }}
                        </p>

                        {{-- Artisan --}}
                        @if($service->artisan)
                        <div class="flex items-center gap-2 mb-3 pt-3 border-t border-slate-50">
                            @if($service->artisan->profile_photo)
                                <img src="{{ Storage::url($service->artisan->profile_photo) }}" class="w-6 h-6 rounded-full object-cover" alt="">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($service->artisan->name) }}&background=29B6D1&color=fff&size=50" class="w-6 h-6 rounded-full object-cover" alt="">
                            @endif
                            <span class="text-xs text-slate-500 font-medium">{{ $service->artisan->name }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between mt-2">
                            <span class="text-base font-black text-[#29B6D1]">
                                @if($service->price)
                                    ${{ number_format($service->price, 0) }}
                                @else
                                    Sur devis
                                @endif
                            </span>
                            <span class="text-xs font-bold text-[#29B6D1] bg-[#29B6D1]/10 px-2 py-1 rounded-lg">
                                Voir <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
