@extends('layouts.admin')

@section('title', 'Missions & Avis')
@section('header_title', 'Missions & Avis Clients')
@section('page_title', 'Missions')
@section('page_subtitle', 'Suivez toutes les missions et consultez les avis clients.')

@section('content')
<div class="space-y-8">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <!-- Total -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-slate-100 text-slate-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-list text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Total</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <!-- En cours -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-spinner text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">En cours</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $stats['in_progress'] }}</h3>
            </div>
        </div>

        <!-- Terminées -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-check-circle text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Terminées</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $stats['completed'] }}</h3>
            </div>
        </div>

        <!-- Avec Avis -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-star text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Avec Avis</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $stats['with_review'] }}</h3>
            </div>
        </div>

        <!-- Note Moy. -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-chart-bar text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Note Moy.</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $stats['avg_rating'] }}/5</h3>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher par nom, artisan, client..." value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <select name="status" class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none">
            <option value="">Tous les statuts</option>
            @foreach(['pending' => 'En attente', 'in_progress' => 'En cours', 'completed' => 'Terminées', 'cancelled' => 'Annulées'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
    </form>

    <!-- Missions Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Mission</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Artisan</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Début</th>
                        <th class="px-6 py-4">Fin</th>
                        <th class="px-6 py-4">Avis</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($missions as $mission)
                    @php
                        $colors = ['pending' => 'slate', 'in_progress' => 'amber', 'completed' => 'emerald', 'cancelled' => 'red'];
                        $c = $colors[$mission->status] ?? 'slate';
                    @endphp
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-sm text-slate-900">{{ \Str::limit($mission->title, 30) }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">#{{ str_pad($mission->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $mission->client->photo_url }}" class="w-8 h-8 rounded-lg object-cover border border-slate-100" alt="">
                                <span class="text-xs font-bold text-slate-700">{{ $mission->client->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $mission->artisan->photo_url }}" class="w-8 h-8 rounded-lg object-cover border border-slate-100" alt="">
                                <span class="text-xs font-bold text-slate-700">{{ $mission->artisan->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-{{ $c }}-50 text-{{ $c }}-600 text-[10px] font-black uppercase rounded-full border border-{{ $c }}-200">
                                {{ $mission->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                            {{ $mission->start_date ? $mission->start_date->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                            {{ $mission->end_date ? $mission->end_date->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($mission->rating)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-[10px] {{ $i <= $mission->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                @endfor
                                <span class="text-[10px] font-bold text-slate-500 ml-1">{{ $mission->rating }}/5</span>
                            </div>
                            @if($mission->feedback)
                            <p class="text-[10px] text-slate-400 mt-1 truncate max-w-[150px]" title="{{ $mission->feedback }}">"{{ \Str::limit($mission->feedback, 40) }}"</p>
                            @endif
                            @else
                            <span class="text-[10px] text-slate-300 font-bold uppercase">Pas encore</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.missions.show', $mission->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-50 text-slate-600 text-[10px] font-black uppercase rounded-lg hover:bg-rdc-blue hover:text-white transition">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-medium text-sm">Aucune mission</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($missions->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $missions->links() }}</div>
        @endif
    </div>
</div>
@endsection
