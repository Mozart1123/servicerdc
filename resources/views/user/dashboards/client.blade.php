@extends($layout)

@section('title', 'Tableau de bord | ProConnect')
@section('header_title', 'Tableau de bord')
@section('header_subtitle', 'Bienvenue, ' . (auth()->user()->name ?? 'Client') . ' 👋')

@section($contentSection)
<div class="space-y-8">

    {{-- QUICK ACTIONS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('public.artisans.index') }}" class="p-4 rounded-xl bg-cyan-50 hover:bg-cyan-100 transition text-center block">
            <i class="fas fa-search text-xl text-rdc-blue mb-2"></i>
            <h3 class="font-semibold text-slate-800 text-xs">Chercher artisan</h3>
        </a>

        <a href="{{ route('user.service-requests.index') }}" class="p-4 rounded-xl bg-amber-50 hover:bg-amber-100 transition text-center block">
            <i class="fas fa-file-circle-plus text-xl text-amber-500 mb-2"></i>
            <h3 class="font-semibold text-slate-800 text-xs">Mes demandes</h3>
        </a>

        <a href="{{ route('user.favorites') }}" class="p-4 rounded-xl bg-red-50 hover:bg-red-100 transition text-center block">
            <i class="fas fa-heart text-xl text-red-500 mb-2"></i>
            <h3 class="font-semibold text-slate-800 text-xs">Mes favoris</h3>
        </a>

        <a href="{{ route('user.reviews.index') }}" class="p-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition text-center block">
            <i class="fas fa-star text-xl text-emerald-500 mb-2"></i>
            <h3 class="font-semibold text-slate-800 text-xs">Mes avis</h3>
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="border border-slate-100 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">En attente</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ $stats['pending_requests_count'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-100 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">En cours</p>
            <p class="text-2xl font-bold text-emerald-500 mt-1">{{ $stats['active_requests_count'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-100 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Terminées</p>
            <p class="text-2xl font-bold text-blue-500 mt-1">{{ $stats['completed_requests_count'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-100 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Messages non lus</p>
            <p class="text-2xl font-bold text-rdc-blue mt-1">{{ $stats['unread_messages'] ?? 0 }}</p>
        </div>
    </div>

    {{-- RECENT REQUESTS --}}
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-bold text-slate-900">Mes demandes récentes</h2>
            <a href="{{ route('user.service-requests.index') }}" class="text-rdc-blue font-semibold text-sm hover:underline">Voir tout</a>
        </div>

        <div class="space-y-3">
            @forelse($recentRequests as $request)
                @php
                    $artisanForRequest = $request->artisan ?? $request->service?->artisan;
                    $statusColor = match($request->status) {
                        'accepted'            => 'orange',
                        'in_progress'         => 'emerald',
                        'awaiting_validation' => 'amber',
                        'rejected'            => 'red',
                        'completed'           => 'blue',
                        'cancelled'           => 'slate',
                        default               => 'amber',
                    };
                @endphp
                <a href="{{ route('user.service-requests.show', $request->id) }}"
                   class="flex items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $request->requested_service_name ?? 'Demande de service' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            @if($artisanForRequest)
                                Auprès de {{ $artisanForRequest->name }} ·
                            @endif
                            {{ $request->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-2.5 py-0.5 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 text-xs font-bold uppercase rounded-full border border-{{ $statusColor }}-200 shrink-0">
                        {{ $request->status_label }}
                    </span>
                </a>
            @empty
                <div class="text-center py-10 border border-dashed border-slate-200 rounded-xl">
                    <i class="fas fa-inbox text-3xl text-slate-200 mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Vous n'avez encore envoyé aucune demande de service.</p>
                    <a href="{{ route('public.artisans.index') }}" class="inline-block mt-4 text-rdc-blue font-semibold text-sm hover:underline">
                        Trouver un artisan →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- NOTIFICATIONS --}}
    <div>
        <h2 class="text-base font-bold text-slate-900 mb-4">Notifications récentes</h2>
        <div class="space-y-3">
            @forelse($notifications as $notif)
                <div class="flex items-start gap-3 text-sm">
                    <i class="fas fa-circle text-[6px] text-rdc-blue mt-1.5"></i>
                    <p class="text-slate-600 leading-snug">{{ $notif->message ?? $notif->title ?? 'Notification' }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Aucune notification pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
