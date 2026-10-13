@extends('layouts.user')

@section('header_title', 'Opportunités')

@section('content')
<div class="space-y-8 pb-20">

    @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $type => $color)
        @if(session($type))
            <div class="px-5 py-4 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 rounded-2xl text-sm font-semibold">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    <!-- Header Section -->
    <div class="bg-white border border-slate-100 p-6 sm:p-8 rounded-3xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center text-2xl border border-blue-100">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase">Opportunités</h2>
                <p class="text-xs font-bold text-slate-400 mt-1">
                    Demandes de clients choisies pour vous selon votre métier, vos communes et votre disponibilité.
                </p>
            </div>
        </div>
        <div class="text-center md:text-right">
            <p class="text-2xl font-black text-slate-900">{{ $awaitingCount }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">En attente de réponse</p>
        </div>
    </div>

    <div class="p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl flex items-start gap-4 text-xs">
        <i class="fas fa-lightbulb text-blue-500 text-lg mt-0.5"></i>
        <p class="text-slate-600 leading-relaxed">
            Contrairement à vos <a href="{{ route('user.artisan.service-requests.index') }}" class="font-bold text-rdc-blue underline">Demandes reçues</a>
            (adressées directement depuis le catalogue), ces demandes sont envoyées à quelques artisans à la fois.
            <strong>Répondez avant l'heure limite</strong> : votre réactivité améliore votre classement.
        </p>
    </div>

    <div class="space-y-4">
        @forelse($dispatches as $dispatch)
            @php
                $need = $dispatch->need;
                $isUrgent = $need->timing === 'urgent';
                $state = match ($dispatch->status) {
                    'sent'     => ['Répondre avant ' . $dispatch->deadline_at->copy()->setTimezone(config('matching.timezone'))->format('d/m H:i'), 'bg-amber-50 text-amber-700 border-amber-200'],
                    'expired'  => ['Délai dépassé', 'bg-slate-100 text-slate-500 border-slate-200'],
                    'proposed' => ['Proposition envoyée', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'declined' => ['Refusée', 'bg-slate-100 text-slate-500 border-slate-200'],
                    default    => [$dispatch->status, 'bg-slate-100 text-slate-500 border-slate-200'],
                };
            @endphp
            <a href="{{ route('user.artisan.opportunities.show', $need) }}"
               class="block bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 {{ $isUrgent ? 'bg-amber-50 text-amber-500' : 'bg-blue-50 text-rdc-blue' }}">
                            <i class="fas {{ $isUrgent ? 'fa-bolt' : 'fa-hammer' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-slate-900 truncate">{{ $need->requested_service_name }}</h4>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 mt-1">
                                @if($need->category)<span><i class="fas fa-layer-group mr-1 text-slate-400"></i>{{ $need->category->name }}</span>@endif
                                @if($need->location_label)<span><i class="fas fa-location-dot mr-1 text-slate-400"></i>{{ $need->location_label }}</span>@endif
                                @if($need->timing_label)<span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>{{ $need->timing_label }}</span>@endif
                                <span><i class="fas fa-money-bill-wave mr-1 text-emerald-500"></i>{{ $need->budget_range }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="self-start md:self-center px-3 py-1.5 rounded-full border text-[11px] font-bold whitespace-nowrap {{ $state[1] }}">{{ $state[0] }}</span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-3xl p-12 border border-slate-100 shadow-sm text-center max-w-xl mx-auto">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Aucune opportunité pour le moment</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-6">
                    Les demandes publiées par des clients correspondant à votre métier et à vos communes d'intervention apparaîtront ici.
                    Vérifiez que vous êtes « Disponible » et que vos communes sont bien cochées.
                </p>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                    <i class="fas fa-sliders"></i> Gérer ma disponibilité
                </a>
            </div>
        @endforelse

        @if($dispatches->hasPages())
            <div class="mt-6">{{ $dispatches->links() }}</div>
        @endif
    </div>
</div>
@endsection
