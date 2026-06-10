@extends('layouts.admin')

@section('title', 'Détail Mission & Avis')
@section('page_title', 'Mission #' . $mission->id)
@section('page_subtitle', 'Détails de la mission et avis client')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Mission Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">{{ $mission->title }}</h3>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                    $statusLabels = [
                        'pending' => 'En attente',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$mission->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $statusLabels[$mission->status] ?? $mission->status }}
                </span>
            </div>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Service</p>
                <p class="text-sm font-semibold text-slate-800">{{ $mission->service->title ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Montant</p>
                <p class="text-sm font-semibold text-slate-800">{{ number_format($mission->amount ?? 0, 0, ',', '.') }} $</p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Client</p>
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mission->client->name ?? 'C') }}&background=29B6D1&color=fff&size=32" class="w-8 h-8 rounded-lg" alt="">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $mission->client->name ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-400">{{ $mission->client->email ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Artisan</p>
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mission->artisan->name ?? 'A') }}&background=F0B800&color=fff&size=32" class="w-8 h-8 rounded-lg" alt="">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $mission->artisan->name ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-400">{{ $mission->artisan->email ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date de début</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $mission->start_date ? \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y H:i') : 'Pas encore démarrée' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date de fin</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $mission->end_date ? \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y H:i') : 'En cours' }}
                </p>
            </div>
            @if($mission->description)
            <div class="md:col-span-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Description</p>
                <p class="text-sm text-slate-600">{{ $mission->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Review / Avis Client Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-star text-rdc-yellow"></i> Avis Client
            </h3>
        </div>
        <div class="px-6 py-5">
            @if($mission->rating)
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Note</p>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-lg {{ $i <= $mission->rating ? 'text-rdc-yellow' : 'text-slate-200' }}"></i>
                            @endfor
                            <span class="ml-2 text-sm font-bold text-slate-700">{{ $mission->rating }}/5</span>
                        </div>
                    </div>
                    @if($mission->feedback)
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Commentaire</p>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-sm text-slate-700 italic">"{{ $mission->feedback }}"</p>
                        </div>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-400">
                            Avis laissé le {{ $mission->updated_at ? $mission->updated_at->format('d/m/Y à H:i') : 'N/A' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-comment-dots text-4xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Le client n'a pas encore laissé d'avis.</p>
                    <p class="text-xs text-slate-300 mt-1">L'avis apparaîtra ici une fois que le client aura évalué la mission.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-start">
        <a href="{{ route('admin.missions.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl shadow-sm hover:border-rdc-blue hover:text-rdc-blue transition-all flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour aux missions
        </a>
    </div>
</div>
@endsection
