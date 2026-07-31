@extends('layouts.public')

@section('title', 'Services en ' . $category->name . ' | ProConnect')
@section('meta_description', 'Découvrez les services disponibles en ' . $category->name . ' en RDC.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-[#29B6D1] transition-colors">
            Accueil
        </a>
        <span class="text-slate-300 mx-2 text-xs">/</span>
        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">
            {{ $category->name }}
        </span>
    </div>

    {{-- Category Header --}}
    <div class="bg-gradient-to-br from-[#090D16] to-[#1E9CB5] rounded-[2.5rem] p-8 sm:p-12 text-white shadow-lg mb-12 relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5 blur-3xl"></div>
        <div class="relative z-10 max-w-2xl">
            <div class="w-14 h-14 rounded-2xl overflow-hidden mb-6 shrink-0">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}"
                         alt="{{ $category->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-white/10 flex items-center justify-center text-3xl">
                        <i class="{{ $category->icon ?? 'fas fa-tags' }}"></i>
                    </div>
                @endif
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold font-heading uppercase tracking-wide">{{ $category->name }}</h1>
            <p class="text-blue-100/80 text-sm mt-3 leading-relaxed">
                {{ $category->description ?? 'Découvrez toutes les prestations et les professionnels de ce secteur.' }}
            </p>
        </div>
    </div>

    {{-- Sub-services (ServiceTypes) list --}}
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest">Prestations disponibles</h2>
        <p class="text-xs text-slate-400 font-bold uppercase mt-1">Sélectionnez le type de service recherché</p>
    </div>

    @if($serviceTypes->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                <i class="fas fa-folder-open"></i>
            </div>
            <h3 class="text-base font-black text-slate-400 uppercase tracking-widest">Aucune prestation</h3>
            <p class="text-xs text-slate-300 font-bold uppercase mt-2">Aucun sous-service n'a été configuré pour ce domaine pour le moment.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($serviceTypes as $st)
                <a href="{{ route('public.service-types.services', $st->id) }}"
                   class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all flex items-center justify-between group">
                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-900 group-hover:text-[#29B6D1] transition-colors leading-tight">
                            {{ $st->title }}
                        </h3>
                        <p class="text-[10px] font-black text-[#29B6D1] uppercase tracking-wider">
                            {{ $st->services_count }} offres active(s)
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#29B6D1] group-hover:text-white transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
