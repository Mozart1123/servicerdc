@extends('layouts.admin')

@section('title', 'Gestion des Services')
@section('header_title', 'Modération des Services')
@section('page_title', 'Exploration des Services')
@section('page_subtitle', 'Gérez, vérifiez et modérez tous les services publiés sur la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative flex-1 md:w-80 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                <input type="text" placeholder="Rechercher un service..." 
                       class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
            </div>
            <button class="p-3 bg-slate-50 text-slate-400 rounded-2xl hover:text-rdc-blue transition-all">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        
        <a href="{{ route('admin.services.create') }}" class="w-full md:w-auto px-8 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all text-center">
            <i class="fas fa-plus mr-2"></i> Ajouter un Service
        </a>
    </div>

    <!-- Services Table Card -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Service</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Prix départ</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Localisation</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($services as $service)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center font-black shadow-inner">
                                    <i class="fas fa-screwdriver-wrench"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $service->title }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Artisan ID: #{{ $service->artisan_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                {{ $service->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-slate-900">{{ number_format($service->price, 2) }}$</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold">
                                <i class="fas fa-location-dot opacity-30"></i>
                                {{ $service->location }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                @if($service->is_verified)
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Vérifié</span>
                                @else
                                    <span class="w-2 h-2 bg-slate-300 rounded-full"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Standard</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.services.edit', $service) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rdc-blue hover:border-rdc-blue transition-all shadow-sm">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Supprimer ce service ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rdc-red hover:border-rdc-red transition-all shadow-sm">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 text-4xl mb-4">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-400 uppercase tracking-widest">Aucun service trouvé</h4>
                                <p class="text-sm text-slate-300 mt-2 font-medium">Commencez par créer le premier service d'élite.</p>
                                <a href="{{ route('admin.services.create') }}" class="mt-6 px-8 py-3 bg-rdc-blue text-white font-black rounded-xl text-[10px] uppercase tracking-widest">Créer un service</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($services->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
