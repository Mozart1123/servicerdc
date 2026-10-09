@extends('layouts.user')

@section('title', 'Mes Avis')
@section('page_title', 'Mes Avis et Évaluations')

@section('content')
<div class="space-y-10 pb-20 max-w-5xl mx-auto">
    
    <!-- Top Nav -->
    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors">
        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
    </a>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total Avis</div>
            <div class="text-4xl font-heading font-black text-slate-900">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 shadow-sm">
            <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3">En Attente</div>
            <div class="text-4xl font-heading font-black text-amber-600">{{ $stats['pending'] }}</div>
        </div>

        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 shadow-sm">
            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3">Approuvés</div>
            <div class="text-4xl font-heading font-black text-emerald-600">{{ $stats['approved'] }}</div>
        </div>

        <div class="bg-red-50 rounded-2xl p-6 border border-red-100 shadow-sm">
            <div class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-3">Rejetés</div>
            <div class="text-4xl font-heading font-black text-red-600">{{ $stats['rejected'] }}</div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($reviews as $review)
        <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                
                <!-- Review Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-2xl text-slate-400">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-slate-900 text-lg">{{ $review->artisan?->name ?? 'Artisan ProConnect' }}</h4>
                            @if($review->mission)
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Mission #{{ str_pad($review->mission_id, 5, '0', STR_PAD_LEFT) }} — {{ $review->mission->title }}</p>
                            @elseif($review->serviceRequest)
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Demande #{{ str_pad($review->service_request_id, 5, '0', STR_PAD_LEFT) }} — {{ $review->serviceRequest->requested_service_name ?? $review->serviceRequest->service?->title ?? 'Prestation' }}</p>
                            @else
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Prestation #{{ str_pad($review->id, 5, '0', STR_PAD_LEFT) }}</p>
                            @endif
                            <p class="text-xs text-slate-400 mt-1">{{ $review->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="text-amber-500 text-2xl mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="fas fa-star opacity-20"></i>
                            @endif
                        @endfor
                    </div>

                    <!-- Feedback -->
                    @if($review->feedback)
                    <p class="text-slate-700 italic bg-slate-50 p-4 rounded-xl border border-slate-100">
                        "{{ $review->feedback }}"
                    </p>
                    @endif
                </div>

                <!-- Status Badge -->
                <div class="md:text-right">
                    @if($review->status === 'pending')
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 rounded-2xl text-xs font-bold uppercase tracking-widest shadow-sm">
                            <i class="fas fa-clock animate-spin"></i> En attente
                        </span>
                        <p class="text-[9px] text-slate-500 mt-2">En cours de modération par l'admin</p>
                    @elseif($review->status === 'approved')
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-2xl text-xs font-bold uppercase tracking-widest shadow-sm">
                            <i class="fas fa-check-circle"></i> Approuvé
                        </span>
                        <p class="text-[9px] text-slate-500 mt-2">Visible sur le profil de l'artisan</p>
                    @elseif($review->status === 'rejected')
                        <div>
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-2xl text-xs font-bold uppercase tracking-widest shadow-sm">
                                <i class="fas fa-ban"></i> Rejeté
                            </span>
                            @if($review->rejection_reason)
                            <div class="mt-3 p-3 bg-red-50 border border-red-100 rounded-xl">
                                <p class="text-[9px] font-bold text-red-600 uppercase tracking-widest mb-1">Raison :</p>
                                <p class="text-xs text-red-700">{{ $review->rejection_reason }}</p>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 border border-slate-100 shadow-sm text-center">
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="font-black text-slate-900 text-lg mb-2">Tu n'as pas encore laissé d'avis.</h3>
            <p class="text-slate-500 text-sm max-w-md mx-auto mb-6">Après avoir fait appel à un artisan et finalisé une prestation, vos évaluations et avis apparaîtront ici.</p>
            <a href="{{ route('user.service-requests.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-rdc-blue hover:bg-rdc-blue-dark text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                <i class="fas fa-list-check"></i> Voir mes demandes
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
    <div class="flex justify-center mt-8">
        {{ $reviews->links() }}
    </div>
    @endif

</div>
@endsection
