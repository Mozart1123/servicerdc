@extends('layouts.user')

@section('header_title', 'Opportunité')

@section('content')
@php
    $deadlineLocal = $dispatch->deadline_at->copy()->setTimezone(config('matching.timezone'));
    // Prénom seulement : coordonnées masquées, les échanges passent par la messagerie.
    $clientFirstName = \Illuminate\Support\Str::of($need->user?->name ?? 'Client')->explode(' ')->first();
@endphp
<div class="max-w-3xl mx-auto space-y-6 pb-20">

    <a href="{{ route('user.artisan.opportunities.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-rdc-blue">
        <i class="fas fa-arrow-left"></i> Opportunités
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $need->requested_service_name }}</h2>
                <p class="text-sm text-slate-400">Publiée {{ $need->created_at->diffForHumans() }} par {{ $clientFirstName }}</p>
            </div>
            @if($dispatch->isAwaitingResponse())
                <span class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                    <i class="fas fa-hourglass-half mr-1"></i> Répondre avant {{ $deadlineLocal->format('d/m à H:i') }}
                </span>
            @elseif($dispatch->status === 'expired')
                <span class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-xs font-bold">Délai de réponse dépassé</span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @foreach([
                ['Catégorie', $need->category?->name . ($need->serviceType ? ' · ' . $need->serviceType->title : ''), 'fa-layer-group'],
                ['Lieu', $need->location_label, 'fa-location-dot'],
                ['Date souhaitée', $need->timing_label, 'fa-calendar-day'],
                ['Budget du client', $need->budget_range, 'fa-money-bill-wave'],
            ] as [$label, $value, $icon])
                @if(trim((string) $value) !== '')
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $label }}</p>
                    <p class="font-semibold text-slate-800 flex items-center gap-2"><i class="fas {{ $icon }} text-rdc-blue w-4"></i>{{ $value }}</p>
                </div>
                @endif
            @endforeach
        </div>

        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Description</p>
            <p class="text-sm text-slate-700 whitespace-pre-line">{{ $need->description }}</p>
        </div>

        @if($need->photo_urls)
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Photos</p>
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                @foreach($need->photo_urls as $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="block aspect-square rounded-xl overflow-hidden border border-slate-200">
                        <img src="{{ $url }}" alt="Photo de la demande" class="w-full h-full object-cover" loading="lazy">
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @include('user.artisan.partials.proposal-panel')
</div>
@endsection
