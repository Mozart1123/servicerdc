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
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <div class="overflow-x-hidden">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[45%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Service</th>
                        <th class="hidden lg:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie</th>
                        <th class="w-[20%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Prix</th>
                        <th class="hidden min-[480px]:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Localisation</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="w-[35%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($services as $service)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center font-black shadow-sm shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-screwdriver-wrench text-[10px] sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate leading-tight">{{ $service->title }}</p>
                                    <p class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate mt-0.5">ID: #{{ $service->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-8 py-6">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                {{ $service->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                            <span class="text-[10px] sm:text-sm font-black text-slate-900 font-mono">{{ number_format($service->price, 2) }}$</span>
                        </td>
                        <td class="hidden min-[480px]:table-cell px-8 py-6">
                            <div class="flex items-center gap-2 text-slate-500 text-[10px] sm:text-xs font-bold">
                                <i class="fas fa-location-dot opacity-30"></i>
                                <span class="truncate max-w-[120px]">{{ $service->location }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell px-8 py-6 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($service->is_verified)
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest ml-1">Vérifié</span>
                                @else
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-slate-300 rounded-full"></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Standard</span>
                                @endif
                            </div>
                        </td>
                        <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                            <div class="flex items-center justify-end gap-1.5 sm:gap-3">
                                <a href="{{ route('admin.services.edit', $service) }}" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-lg sm:rounded-xl hover:text-rdc-blue hover:border-rdc-blue transition-all shadow-sm">
                                    <i class="fas fa-pen text-[9px] sm:text-xs"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce service ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-lg sm:rounded-xl hover:text-rdc-red hover:border-rdc-red transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[9px] sm:text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20">
                            <div class="flex flex-col items-center justify-center text-center min-h-[300px]">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-3xl mb-4 shadow-inner">
                                    <i class="fas fa-screwdriver-wrench"></i>
                                </div>
                                <h4 class="text-base font-black text-slate-400 uppercase tracking-widest">Aucun service actif</h4>
                                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">La plateforme est actuellement vide.</p>
                                <a href="{{ route('admin.services.create') }}" class="mt-8 px-8 py-3 bg-rdc-blue text-white font-black rounded-2xl text-[9px] uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:scale-105 transition-all">
                                    Publier le premier service
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(optional($services)->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
            {{ optional($services)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
