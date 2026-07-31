@extends('layouts.public')

@section('title', $serviceType->title . ' | ProConnect')
@section('meta_description', 'Découvrez les artisans et tarifs pour ' . $serviceType->title . ' en RDC.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-[#29B6D1] transition-colors">
            Accueil
        </a>
        @if($serviceType->category)
            <span class="text-slate-300 mx-2 text-xs">/</span>
            <a href="{{ route('public.categories.service-types', $serviceType->category->id) }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-[#29B6D1] transition-colors">
                {{ $serviceType->category->name }}
            </a>
        @endif
        <span class="text-slate-300 mx-2 text-xs">/</span>
        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">
            {{ $serviceType->title }}
        </span>
    </div>

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-slate-900">{{ $serviceType->title }}</h1>
        <p class="text-slate-500 mt-2">Découvrez les offres de nos artisans qualifiés pour cette prestation.</p>
    </div>

    {{-- Results Count --}}
    <p class="text-sm text-slate-400 font-medium mb-6">{{ $services->total() }} offre(s) trouvée(s)</p>

    {{-- Services Grid --}}
    @if($services->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                <i class="fas fa-tools"></i>
            </div>
            <h3 class="text-base font-black text-slate-400 uppercase tracking-widest">Aucun artisan disponible</h3>
            <p class="text-xs text-slate-300 font-bold uppercase mt-2">Aucune offre active ne correspond actuellement à ce service en RDC.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($services as $service)
                <a href="{{ route('public.services.show', $service->id) }}"
                   class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden group flex flex-col h-full">
                    
                    {{-- Image --}}
                    <div class="h-44 bg-gradient-to-br from-[#29B6D1]/10 to-[#29B6D1]/5 overflow-hidden relative shrink-0">
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

                    {{-- Info --}}
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm leading-snug mb-1 line-clamp-2 group-hover:text-[#29B6D1] transition-colors">
                                {{ $service->title }}
                            </h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $service->location ?? $service->city ?? 'RDC' }}
                            </p>
                        </div>

                        <div>
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
                                <span class="text-sm font-black text-[#29B6D1]">
                                    À partir de
                                </span>
                                <span class="text-base font-black text-slate-900">
                                    {{ $service->price == 0 ? 'Gratuit' : number_format($service->price, 2) . '$' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
