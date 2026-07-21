@extends($layout)

@section('title', 'Mes Demandes | ProConnect')
@section('header_title', 'Mes demandes')
@section('header_subtitle', 'Suivez l\'état de vos demandes de services.')

@section($contentSection)
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium">
        <i class="fas fa-check-circle shrink-0"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm font-medium">
        <i class="fas fa-times-circle shrink-0"></i>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Filter Pills --}}
    <div class="flex flex-wrap gap-2">
        @php
            $statusFilters = [
                ''           => ['label' => 'Toutes',     'count' => $stats['total']],
                'pending'    => ['label' => 'En attente', 'count' => $stats['pending']],
                'accepted'   => ['label' => 'Acceptées',  'count' => $stats['accepted']],
                'rejected'   => ['label' => 'Refusées',   'count' => $stats['rejected']],
                'completed'  => ['label' => 'Terminées',  'count' => $stats['completed']],
                'cancelled'  => ['label' => 'Annulées',   'count' => $stats['cancelled'] ?? 0],
            ];
            $currentStatus = request('status', '');
        @endphp

        @foreach($statusFilters as $val => $filter)
        <a href="{{ route('user.service-requests.index', array_merge(request()->except('status', 'page'), $val ? ['status' => $val] : [])) }}"
           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
                  {{ $currentStatus === $val
                     ? 'bg-[#16a3b0] text-white border-[#16a3b0]'
                     : 'bg-white text-slate-600 border-slate-200 hover:border-[#16a3b0] hover:text-[#16a3b0]' }}">
            {{ $filter['label'] }}
            <span class="text-xs {{ $currentStatus === $val ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }} px-1.5 py-0.5 rounded-full font-bold">
                {{ $filter['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('user.service-requests.index') }}" class="flex gap-3">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" placeholder="Rechercher une demande…" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all">
        </div>
        <button type="submit" class="px-5 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors">
            Rechercher
        </button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('user.service-requests.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-medium text-sm rounded-lg hover:bg-slate-200 transition-colors">
            Réinitialiser
        </a>
        @endif
    </form>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($serviceRequests as $req)
        @php
            $statusConfig = [
                'pending'   => ['color' => 'amber',   'label' => 'En attente'],
                'accepted'  => ['color' => 'emerald', 'label' => 'Acceptée'],
                'rejected'  => ['color' => 'red',     'label' => 'Refusée'],
                'completed' => ['color' => 'blue',    'label' => 'Terminée'],
                'cancelled' => ['color' => 'slate',   'label' => 'Annulée'],
            ];
            $sc = $statusConfig[$req->status] ?? ['color' => 'slate', 'label' => ucfirst($req->status)];
        @endphp

        <div class="bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-300 hover:shadow-sm transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                {{-- Icon --}}
                <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                    @if($req->service?->service_image)
                        <img src="{{ Storage::url($req->service->service_image) }}" alt="" class="w-full h-full object-cover rounded-xl">
                    @else
                        <i class="fas fa-tools text-slate-400"></i>
                    @endif
                </div>

                {{-- Main Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="font-semibold text-slate-900 text-sm">{{ $req->requested_service_name ?? 'Demande de service' }}</h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($sc['color'] === 'amber')   bg-amber-50 text-amber-700 border border-amber-200
                            @elseif($sc['color'] === 'emerald') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($sc['color'] === 'red')  bg-red-50 text-red-700 border border-red-200
                            @elseif($sc['color'] === 'blue') bg-blue-50 text-blue-700 border border-blue-200
                            @else bg-slate-100 text-slate-600 border border-slate-200
                            @endif">
                            {{ $req->status_label ?? $sc['label'] }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        @if($req->service?->artisan)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-user-hard-hat text-[#16a3b0]"></i>
                            {{ $req->service->artisan->name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar text-slate-400"></i>
                            {{ $req->created_at->format('d M Y') }}
                        </span>
                        @if($req->city)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-slate-400"></i>
                            {{ $req->city }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle text-slate-400"></i>
                            {{ $req->urgency_label }}
                        </span>
                        @if(in_array($req->status, ['in_progress']) && $req->accepted_at)
                        <span class="flex items-center gap-1 text-emerald-600 font-medium" data-accepted-at="{{ $req->accepted_at->toIso8601String() }}" id="timer-badge-{{ $req->id }}">
                            <i class="fas fa-stopwatch"></i><span>00:00:00</span>
                        </span>
                        @endif
                        @if($req->status === 'completed' && $req->accepted_at && $req->completed_at)
                        @php $d = $req->completed_at->diff($req->accepted_at); @endphp
                        <span class="flex items-center gap-1 text-blue-600 font-medium">
                            <i class="fas fa-clock"></i>
                            {{ $d->h > 0 ? "{$d->h}h {$d->i}min" : "{$d->i}min" }}
                        </span>
                        @endif
                    </div>
                    @if($req->description)
                    <p class="mt-1.5 text-xs text-slate-400 line-clamp-1">{{ $req->description }}</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    @if(in_array($req->status, ['accepted', 'in_progress', 'completed']) && ($req->artisan_id || $req->service?->artisan_id))
                    @php $artisanId = $req->artisan_id ?? $req->service->artisan_id; @endphp
                    <a href="{{ route('user.messages.start.user', $artisanId) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#16a3b0] text-white text-xs font-medium rounded-lg hover:bg-[#138b96] transition-colors">
                        <i class="fas fa-comments"></i> Discuter
                    </a>
                    @endif

                    @if($req->status === 'completed' && $req->user_id === auth()->id())
                    <a href="{{ route('user.service-requests.show', $req->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition-colors">
                        <i class="fas fa-star"></i> {{ $req->rating ? 'Évaluation' : 'Évaluer' }}
                    </a>
                    @endif

                    @if($req->status === 'pending')
                    <form action="{{ route('user.service-requests.cancel', $req->id) }}" method="POST"
                          onsubmit="return confirm('Annuler cette demande ?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('user.service-requests.show', $req->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                <i class="fas fa-inbox"></i>
            </div>
            <h3 class="font-semibold text-slate-800 mb-1">Aucune demande trouvée</h3>
            <p class="text-sm text-slate-500 mb-5">
                @if(request()->hasAny(['search', 'status']))
                    Essayez de modifier vos filtres.
                @else
                    Vous n'avez pas encore envoyé de demande de service.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'status']))
            <a href="{{ route('user.services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors">
                <i class="fas fa-search"></i> Explorer les services
            </a>
            @endif
        </div>
        @endforelse

        @if($serviceRequests->hasPages())
        <div class="pt-2">{{ $serviceRequests->links() }}</div>
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
