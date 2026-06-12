@extends('layouts.user')

@section('title', 'Mes Avis | ProConnect')
@section('header_title', 'Mes Avis & Evaluations')

@section('content')
<div class="space-y-8 pb-10">

    {{-- Summary Card --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8" data-aos="fade-up">
        <div class="flex flex-col md:flex-row items-center gap-8">

            {{-- Average Score --}}
            <div class="text-center shrink-0">
                <p class="text-6xl font-black text-slate-900">{{ number_format($avgRating, 1, ',', '') }}</p>
                <div class="flex items-center justify-center gap-1 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            <i class="fas fa-star text-amber-400 text-xl"></i>
                        @elseif($i - 0.5 <= $avgRating)
                            <i class="fas fa-star-half-alt text-amber-400 text-xl"></i>
                        @else
                            <i class="far fa-star text-slate-200 text-xl"></i>
                        @endif
                    @endfor
                </div>
                <p class="text-sm font-bold text-slate-400 mt-2">{{ $totalReviews }} avis au total</p>
            </div>

            {{-- Distribution --}}
            <div class="flex-1 w-full">
                @foreach($ratingDistribution as $star => $count)
                @php
                    $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                @endphp
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-xs font-bold text-slate-500 w-8 text-right">{{ $star }} <i class="fas fa-star text-amber-400 text-[10px]"></i></span>
                    <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-slate-400 w-8">{{ $count }}</span>
                </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- Reviews List --}}
    <div class="space-y-4">
        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 px-2">Tous les avis</h3>

        @forelse($allReviews as $review)
        @php
            $statusColors = [
                'approved' => 'emerald',
                'pending'  => 'amber',
                'rejected' => 'red',
            ];
            $sc = $statusColors[$review->status] ?? 'slate';
        @endphp
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-6" data-aos="fade-up">
            <div class="flex flex-col md:flex-row gap-4">

                {{-- Client Avatar --}}
                <div class="shrink-0">
                    <img src="{{ $review->client->photo_url ?? asset('img/default-avatar.png') }}"
                         alt="{{ $review->client->name ?? 'Client' }}"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-slate-100 shadow-sm">
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="font-bold text-slate-900">{{ $review->client->name ?? 'Client' }}</h4>
                        <span class="px-2.5 py-0.5 bg-{{ $sc }}-50 text-{{ $sc }}-600 text-xs font-bold uppercase rounded-full border border-{{ $sc }}-200">
                            {{ $review->status_label }}
                        </span>
                        @if($review->source === 'mission')
                            <span class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-bold uppercase rounded-full border border-purple-200">Mission</span>
                        @else
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase rounded-full border border-blue-200">Service</span>
                        @endif
                    </div>

                    {{-- Stars --}}
                    <div class="flex items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }} text-sm"></i>
                        @endfor
                        <span class="text-xs font-bold text-slate-400 ml-2">{{ $review->rating }}/5</span>
                    </div>

                    {{-- Service & Date --}}
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 font-medium mb-2">
                        <span><i class="fas fa-briefcase mr-1 text-rdc-blue"></i>{{ $review->service_name }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($review->date)->format('d M Y') }}</span>
                    </div>

                    {{-- Comment --}}
                    @if($review->comment)
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                    @else
                        <p class="text-sm text-slate-300 italic">Aucun commentaire</p>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="bg-white p-12 rounded-2xl border border-slate-100 text-center" data-aos="fade-up">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-4xl mx-auto mb-4">
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3 class="font-bold text-lg text-slate-800">Aucun avis pour le moment</h3>
            <p class="text-slate-500 mt-2">Terminez des missions pour recevoir vos premières évaluations clients.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
