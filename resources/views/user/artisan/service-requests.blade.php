@extends('layouts.user')

@section('title', 'Demandes reçues | ProConnect')
@section('header_title', 'Demandes de Services Reçues')

@section('content')
<div class="space-y-8 pb-10">

    {{-- Flash Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
    @if(session($type))
    <div class="flex items-center gap-3 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-5 py-4 rounded-2xl" data-aos="fade-down">
        <i class="fas fa-circle-info text-xl shrink-0"></i>
        <p class="font-semibold">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4" data-aos="fade-up">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate', 'icon' => 'fa-list'],
            ['label' => 'En attente', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => 'fa-clock'],
            ['label' => 'Acceptées', 'value' => $stats['accepted'], 'color' => 'emerald', 'icon' => 'fa-check'],
            ['label' => 'Refusées', 'value' => $stats['rejected'], 'color' => 'red', 'icon' => 'fa-times'],
            ['label' => 'Terminées', 'value' => $stats['completed'], 'color' => 'blue', 'icon' => 'fa-flag-checkered'],
        ] as $stat)
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-{{ $stat['color'] }}-400">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('user.artisan.service-requests.index') }}" class="flex flex-col md:flex-row gap-4" data-aos="fade-up" data-aos-delay="100">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher…" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <select name="status" class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-rdc-blue">
            <option value="">Tous</option>
            @foreach(['pending' => 'En attente', 'accepted' => 'Acceptée', 'rejected' => 'Refusée', 'completed' => 'Terminée'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
    </form>

    {{-- List --}}
    <div class="space-y-4">
        @forelse($serviceRequests as $req)
        @php
            $colors = ['pending' => 'amber', 'accepted' => 'emerald', 'rejected' => 'red', 'completed' => 'blue', 'cancelled' => 'slate'];
            $c = $colors[$req->status] ?? 'slate';
        @endphp
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-6" data-aos="fade-up">
            <div class="flex flex-col md:flex-row gap-4">

                {{-- Client Avatar --}}
                <div class="shrink-0">
                    <img src="{{ $req->user->photo_url }}" alt="{{ $req->user->name }}"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-slate-100 shadow-sm">
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="font-bold text-lg text-slate-900">{{ $req->requested_service_name ?? 'Demande' }}</h4>
                        <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                            {{ $req->status_label }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500 font-medium">
                        <span><i class="fas fa-user mr-1 text-rdc-blue"></i>{{ $req->user->name }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ $req->created_at->format('d M Y') }}</span>
                        @if($req->city)
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $req->city }}</span>
                        @endif
                        <span><i class="fas fa-fire mr-1 text-orange-400"></i>{{ $req->urgency_label }}</span>
                        <span><i class="fas fa-dollar-sign mr-1"></i>{{ $req->budget_range }}</span>
                    </div>
                    @if($req->description)
                    <p class="mt-2 text-sm text-slate-400 line-clamp-2">{{ $req->description }}</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($req->status === 'pending')
                    <form action="{{ route('user.service-requests.accept', $req->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 transition shadow-sm">
                            <i class="fas fa-check"></i> Accepter
                        </button>
                    </form>
                    <form action="{{ route('user.service-requests.reject', $req->id) }}" method="POST"
                          onsubmit="return confirm('Refuser cette demande ?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                            <i class="fas fa-times"></i> Refuser
                        </button>
                    </form>
                    @endif

                    @if($req->status === 'accepted')
                    <form action="{{ route('user.service-requests.start', $req->id) }}" method="POST"
                          onsubmit="return confirm('Démarrer cette mission maintenant ?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition shadow-sm">
                            <i class="fas fa-play"></i> Démarrer
                        </button>
                    </form>
                    <a href="{{ route('user.messages.start.user', $req->user_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rdc-blue text-white text-sm font-bold rounded-xl hover:bg-rdc-blue-dark transition">
                        <i class="fas fa-comments"></i> Discuter
                    </a>
                    <form action="{{ route('user.service-requests.complete', $req->id) }}" method="POST"
                          onsubmit="return confirm('Marquer ce service comme terminé ?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 text-sm font-bold rounded-xl hover:bg-blue-100 border border-blue-200 transition">
                            <i class="fas fa-flag-checkered"></i> Terminé
                        </button>
                    </form>
                    <form action="{{ route('user.service-requests.cancel', $req->id) }}" method="POST"
                          onsubmit="return confirm('Annuler cette mission ?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                            <i class="fas fa-ban"></i> Annuler
                        </button>
                    </form>
                    @endif

                    @if($req->status === 'completed')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-bold rounded-xl border border-emerald-200">
                        <i class="fas fa-star"></i> En attente d'avis client
                    </span>
                    <a href="{{ route('user.messages.start.user', $req->user_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                        <i class="fas fa-comments"></i> Contacter
                    </a>
                    @endif

                    @if($req->status === 'cancelled')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-500 text-sm font-bold rounded-xl border border-slate-200">
                        <i class="fas fa-ban"></i> Annulée
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-12 rounded-2xl border border-slate-100 text-center" data-aos="fade-up">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-4xl mx-auto mb-4">
                <i class="fas fa-inbox"></i>
            </div>
            <h3 class="font-bold text-lg text-slate-800">Aucune demande reçue</h3>
            <p class="text-slate-500 mt-2">Les clients n'ont pas encore envoyé de demandes pour vos services.</p>
        </div>
        @endforelse

        @if($serviceRequests->hasPages())
        <div class="pt-4">{{ $serviceRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
