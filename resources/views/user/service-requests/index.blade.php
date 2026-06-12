@extends('layouts.user')

@section('title', 'Mes Demandes de Services | ProConnect')
@section('header_title', 'Mes Demandes de Services')

@section('content')
<div class="space-y-8 pb-10">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl" data-aos="fade-down">
        <i class="fas fa-check-circle text-xl shrink-0"></i>
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl" data-aos="fade-down">
        <i class="fas fa-times-circle text-xl shrink-0"></i>
        <p class="font-semibold">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4" data-aos="fade-up">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate', 'icon' => 'fa-list'],
            ['label' => 'En attente', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => 'fa-clock'],
            ['label' => 'Acceptées', 'value' => $stats['accepted'], 'color' => 'emerald', 'icon' => 'fa-check-circle'],
            ['label' => 'Refusées', 'value' => $stats['rejected'], 'color' => 'red', 'icon' => 'fa-times-circle'],
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
    <form method="GET" action="{{ route('user.service-requests.index') }}" class="flex flex-col md:flex-row gap-4" data-aos="fade-up" data-aos-delay="100">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher une demande…" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <select name="status" class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-rdc-blue">
            <option value="">Tous les statuts</option>
            @foreach(['pending' => 'En attente', 'accepted' => 'Acceptée', 'rejected' => 'Refusée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('user.service-requests.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition text-center">Réinitialiser</a>
        @endif
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

                {{-- Service Image --}}
                <div class="w-16 h-16 rounded-2xl bg-{{ $c }}-50 border border-{{ $c }}-100 flex items-center justify-center shrink-0 overflow-hidden">
                    @if($req->service?->service_image)
                        <img src="{{ Storage::url($req->service->service_image) }}" alt="" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <i class="fas fa-tools text-{{ $c }}-400 text-xl"></i>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="font-bold text-lg text-slate-900">{{ $req->requested_service_name ?? 'Demande de service' }}</h4>
                        <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                            {{ $req->status_label }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500 font-medium">
                        @if($req->service?->artisan)
                        <span><i class="fas fa-user-hard-hat mr-1 text-rdc-blue"></i>{{ $req->service->artisan->name }}</span>
                        @endif
                        <span><i class="fas fa-calendar mr-1"></i>{{ $req->created_at->format('d M Y') }}</span>
                        @if($req->city)
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $req->city }}</span>
                        @endif
                        <span><i class="fas fa-exclamation-triangle mr-1"></i>{{ $req->urgency_label }}</span>
                        @if($req->status === 'accepted' && $req->accepted_at)
                        <span class="text-emerald-600 font-bold" data-accepted-at="{{ $req->accepted_at->toIso8601String() }}" id="timer-badge-{{ $req->id }}">
                            <i class="fas fa-stopwatch mr-1"></i><span>00:00:00</span>
                        </span>
                        @endif
                        @if($req->status === 'completed' && $req->accepted_at && $req->completed_at)
                        @php $d = $req->completed_at->diff($req->accepted_at); @endphp
                        <span class="text-blue-600 font-bold">
                            <i class="fas fa-clock mr-1"></i>{{ $d->h > 0 ? "{$d->h}h {$d->i}min" : "{$d->i}min" }}
                        </span>
                        @endif
                    </div>
                    @if($req->description)
                    <p class="mt-2 text-sm text-slate-400 line-clamp-2">{{ $req->description }}</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    {{-- Discuss button --}}
                    @if(in_array($req->status, ['accepted', 'completed']) && ($req->artisan_id || $req->service?->artisan_id))
                    @php $artisanId = $req->artisan_id ?? $req->service->artisan_id; @endphp
                    <a href="{{ route('user.messages.start.user', $artisanId) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rdc-blue text-white text-sm font-bold rounded-xl hover:bg-rdc-blue-dark transition">
                        <i class="fas fa-comments"></i> Discuter
                    </a>
                    @endif

                    {{-- Rate button --}}
                    @if($req->status === 'completed' && $req->user_id === auth()->id())
                    <a href="{{ route('user.service-requests.show', $req->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition">
                        <i class="fas fa-star"></i> {{ $req->rating ? 'Voir evaluation' : 'Evaluer' }}
                    </a>
                    @endif

                    {{-- Cancel button --}}
                    @if($req->status === 'pending')
                    <form action="{{ route('user.service-requests.cancel', $req->id) }}" method="POST"
                          onsubmit="return confirm('Annuler cette demande ?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </form>
                    @endif

                    {{-- View detail --}}
                    <a href="{{ route('user.service-requests.show', $req->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-12 rounded-2xl border border-slate-100 text-center" data-aos="fade-up">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-4xl mx-auto mb-4">
                <i class="fas fa-inbox"></i>
            </div>
            <h3 class="font-bold text-lg text-slate-800">Aucune demande</h3>
            <p class="text-slate-500 mt-2">Vous n'avez pas encore envoyé de demande de service.</p>
            <a href="{{ route('user.services.index') }}" class="inline-block mt-5 px-8 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">
                Explorer les services
            </a>
        </div>
        @endforelse

        @if($serviceRequests->hasPages())
        <div class="pt-4">{{ $serviceRequests->links() }}</div>
        @endif
    </div>
</div>

<script>
(function() {
    const badges = document.querySelectorAll('[data-accepted-at]');
    if (!badges.length) return;
    function updateBadges() {
        const now = Date.now();
        badges.forEach(badge => {
            const acceptedAt = new Date(badge.dataset.acceptedAt).getTime();
            const diff = Math.floor((now - acceptedAt) / 1000);
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            badge.querySelector('span:last-child').textContent = h + ':' + m + ':' + s;
        });
    }
    updateBadges();
    setInterval(updateBadges, 1000);
})();
</script>
@endsection
