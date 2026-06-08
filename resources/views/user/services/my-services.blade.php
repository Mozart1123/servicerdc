@extends('layouts.user')

@section('header_title', 'Mes Services')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Header -->
    <div class="relative">
        <div class="absolute inset-0 bg-blue-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-3xl shadow-inner">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase">Mes Services</h2>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Gérez vos prestations et offres</p>
                </div>
            </div>
            
            <a href="{{ route('user.services.create') }}" class="px-8 py-5 bg-rdc-blue text-white rounded-3xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                + Ajouter un service
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['total'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Services au total</p>
            </div>
            <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-list"></i></div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-emerald-500">{{ $stats['active'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Services actifs</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-play"></i></div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-blue-500">{{ $stats['verified'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Services vérifiés</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-check-double"></i></div>
        </div>
    </div>

    <!-- Cards List -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($services as $service)
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl transition-all group flex flex-col">
                <div class="h-48 bg-slate-100 relative">
                    <!-- Image placeholder or actual image -->
                    @if($service->service_image)
                        <img src="{{ Storage::url($service->service_image) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                    @else
                        @php
                            $gallery = $service->gallery_images ?? $service->images ?? [];
                        @endphp
                        @if(is_array($gallery) && count($gallery) > 0)
                            <img src="{{ Storage::url($gallery[0]) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <i class="fas fa-image text-3xl mb-2"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Sans Image</span>
                            </div>
                        @endif
                    @endif
                    
                    <div class="absolute top-4 right-4 flex flex-col gap-2">
                        @if($service->status == 'active')
                            <span class="px-3 py-1 bg-white/90 text-emerald-600 backdrop-blur-sm text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">Actif</span>
                        @else
                            <span class="px-3 py-1 bg-white/90 text-slate-500 backdrop-blur-sm text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">Inactif</span>
                        @endif

                        @if($service->is_verified)
                            <span class="px-3 py-1 bg-rdc-blue/90 text-white backdrop-blur-sm text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg"><i class="fas fa-check-circle mr-1"></i> Vérifié</span>
                        @endif
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ $service->category->name ?? 'Catégorie' }}</div>
                        <h3 class="font-heading font-black text-slate-900 group-hover:text-rdc-blue transition-colors text-lg mb-2 line-clamp-1">{{ $service->title }}</h3>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest flex items-center"><i class="fas fa-location-dot mr-1.5 opacity-50"></i> {{ $service->location }}</p>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between">
                        <span class="text-xl font-black text-slate-900">{{ number_format($service->price, 2) }}$</span>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('user.services.edit', $service->id) }}" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-500 hover:bg-slate-200 hover:text-slate-900 transition" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{ route('user.services.show', $service->id) }}" class="w-10 h-10 bg-rdc-blue/10 rounded-xl flex items-center justify-center text-rdc-blue hover:bg-rdc-blue hover:text-white transition" title="Voir l'aperçu">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" 
                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) document.getElementById('delete-service-{{ $service->id }}').submit();"
                                    class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-service-{{ $service->id }}" action="{{ route('user.services.destroy', $service->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="xl:col-span-3 bg-slate-50 p-12 rounded-[3.5rem] text-center border border-slate-100 border-dashed flex flex-col items-center">
                <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center text-4xl text-slate-300 shadow-sm mb-6">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Aucun service proposé</h3>
                <p class="text-sm font-medium text-slate-500 mt-2 max-w-lg mb-8">Vous n'avez pas encore créé de service. Commencez par publier votre première offre pour attirer des clients.</p>
                <a href="{{ route('user.services.create') }}" class="px-8 py-5 bg-rdc-blue text-white rounded-3xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                    + Ajouter mon premier service
                </a>
            </div>
        @endforelse
    </div>
    
    <div class="mt-8 flex justify-center">
        {{ $services->links('pagination::tailwind') ?? '' }}
    </div>
</div>
@endsection
