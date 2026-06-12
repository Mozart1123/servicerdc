@extends('layouts.user')

@section('title', 'Demande de service | ProConnect')
@section('header_title', 'Détail de la demande')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-10">

    {{-- Flash Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
    @if(session($type))
    <div class="flex items-center gap-3 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-5 py-4 rounded-2xl">
        <i class="fas fa-circle-info text-xl shrink-0"></i>
        <p class="font-semibold">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" data-aos="fade-up">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-16 h-16 rounded-2xl bg-rdc-blue/10 flex items-center justify-center shrink-0 overflow-hidden">
                @if($serviceRequest->service?->service_image)
                    <img src="{{ Storage::url($serviceRequest->service->service_image) }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <i class="fas fa-tools text-rdc-blue text-2xl"></i>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-slate-900">{{ $serviceRequest->requested_service_name ?? 'Demande de service' }}</h2>
                    @php $c = match($serviceRequest->status) {'accepted'=>'emerald','rejected'=>'red','completed'=>'blue','cancelled'=>'slate',default=>'amber'}; @endphp
                    <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                        {{ $serviceRequest->status_label }}
                    </span>
                </div>
                <p class="text-sm text-slate-400">Envoyée le {{ $serviceRequest->created_at->format('d M Y à H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Live Timer / Elapsed Time Card --}}
    @if($serviceRequest->status === 'accepted' && $serviceRequest->accepted_at)
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl shadow-xl shadow-emerald-200 p-6 text-white" data-aos="fade-up" id="timer-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-100 mb-1">Service en cours</p>
                <p class="text-sm font-bold text-emerald-50">Le chronomètre est lancé</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-stopwatch text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p id="live-timer" class="font-mono text-5xl font-black tracking-wider text-white">00:00:00</p>
            <p class="text-xs text-emerald-100 mt-2 uppercase tracking-widest">Heures : Minutes : Secondes</p>
        </div>
    </div>
    <script>
    (function() {
        const acceptedAt = new Date('{{ $serviceRequest->accepted_at->toIso8601String() }}').getTime();
        const timerEl = document.getElementById('live-timer');
        function updateTimer() {
            const now = Date.now();
            const diff = Math.floor((now - acceptedAt) / 1000);
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            timerEl.textContent = h + ':' + m + ':' + s;
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    })();
    </script>
    @endif

    @if($serviceRequest->status === 'completed' && $serviceRequest->accepted_at && $serviceRequest->completed_at)
    @php
        $diff = $serviceRequest->completed_at->diff($serviceRequest->accepted_at);
        $elapsedStr = $diff->h > 0 ? "{$diff->h}h {$diff->i}min" : "{$diff->i}min {$diff->s}s";
    @endphp
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-xl shadow-blue-200 p-6 text-white" data-aos="fade-up">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-100 mb-1">Service terminé</p>
                <p class="text-sm font-bold text-blue-50">Durée totale du service</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-flag-checkered text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p class="font-mono text-5xl font-black tracking-wider text-white">{{ $elapsedStr }}</p>
            <p class="text-xs text-blue-100 mt-2 uppercase tracking-widest">Du {{ $serviceRequest->accepted_at->format('H:i') }} au {{ $serviceRequest->completed_at->format('H:i') }}</p>
        </div>
    </div>
    @endif

    {{-- Details --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-2 gap-5" data-aos="fade-up">
        @foreach([
            ['label' => 'Service lié', 'value' => $serviceRequest->service?->title, 'icon' => 'fa-tools'],
            ['label' => 'Artisan', 'value' => $serviceRequest->artisan?->name ?? $serviceRequest->service?->artisan?->name, 'icon' => 'fa-user-hard-hat'],
            ['label' => 'Ville', 'value' => $serviceRequest->city, 'icon' => 'fa-map-marker-alt'],
            ['label' => 'Urgence', 'value' => $serviceRequest->urgency_label, 'icon' => 'fa-exclamation-triangle'],
            ['label' => 'Budget', 'value' => $serviceRequest->budget_range, 'icon' => 'fa-money-bill-wave'],
            ['label' => 'Téléphone', 'value' => $serviceRequest->phone, 'icon' => 'fa-phone'],
        ] as $detail)
        @if($detail['value'])
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $detail['label'] }}</p>
            <p class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas {{ $detail['icon'] }} text-rdc-blue w-4"></i>
                {{ $detail['value'] }}
            </p>
        </div>
        @endif
        @endforeach

        @if($serviceRequest->description)
        <div class="sm:col-span-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Description</p>
            <p class="text-slate-700 leading-relaxed">{{ $serviceRequest->description }}</p>
        </div>
        @endif

        @if($serviceRequest->admin_response)
        <div class="sm:col-span-2 bg-blue-50 rounded-xl p-4 border border-blue-100">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Réponse reçue</p>
            <p class="text-blue-800 leading-relaxed">{{ $serviceRequest->admin_response }}</p>
        </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3" data-aos="fade-up">
        @if(in_array($serviceRequest->status, ['accepted', 'completed']) && isset($conversation))
        <a href="{{ route('user.messages.index', ['id' => $conversation->id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-200">
            <i class="fas fa-comments"></i> Discuter avec l'artisan
        </a>
        @endif

        @if($serviceRequest->status === 'accepted' && $serviceRequest->artisan_id === auth()->id())
        <form action="{{ route('user.service-requests.complete', $serviceRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-check-circle"></i> Marquer comme termine
            </button>
        </form>
        @endif

        @if($serviceRequest->status === 'pending' && $serviceRequest->user_id === auth()->id())
        <form action="{{ route('user.service-requests.cancel', $serviceRequest->id) }}" method="POST"
              onsubmit="return confirm('Etes-vous sur de vouloir annuler cette demande ?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                <i class="fas fa-times"></i> Annuler la demande
            </button>
        </form>
        @endif

        <a href="{{ route('user.service-requests.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    {{-- Rating Section --}}
    @if($serviceRequest->status === 'completed' && $serviceRequest->user_id === auth()->id())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" data-aos="fade-up">
        @if($serviceRequest->rating)
            {{-- Already rated --}}
            <div class="text-center">
                <h3 class="text-lg font-bold text-slate-900 mb-3">Votre evaluation</h3>
                <div class="flex justify-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-xl {{ $i <= $serviceRequest->rating->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                    @endfor
                </div>
                @if($serviceRequest->rating->comment)
                <p class="text-slate-600 italic">"{{ $serviceRequest->rating->comment }}"</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">Evalue le {{ $serviceRequest->rating->created_at->format('d M Y') }}</p>
            </div>
        @else
            {{-- Rating form --}}
            <form action="{{ route('user.service-requests.rate', $serviceRequest->id) }}" method="POST">
                @csrf
                <h3 class="text-lg font-bold text-slate-900 mb-1">Evaluer l'artisan</h3>
                <p class="text-sm text-slate-500 mb-5">Le service est termine. Partagez votre experience !</p>

                <div class="flex justify-center gap-2 mb-6" id="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                        <i class="fas fa-star text-3xl text-slate-200 peer-checked:text-amber-400 hover:text-amber-300 transition" data-star="{{ $i }}"></i>
                    </label>
                    @endfor
                </div>

                <textarea name="comment" rows="3" placeholder="Laissez un commentaire (optionnel)..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none resize-none mb-4"></textarea>

                <div class="text-center">
                    <button type="submit" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition shadow-lg shadow-amber-200">
                        <i class="fas fa-star mr-2"></i> Envoyer mon evaluation
                    </button>
                </div>
            </form>

            <script>
            document.querySelectorAll('#star-rating input').forEach(input => {
                input.addEventListener('change', function() {
                    const val = parseInt(this.value);
                    document.querySelectorAll('#star-rating i[data-star]').forEach(star => {
                        star.classList.toggle('text-amber-400', parseInt(star.dataset.star) <= val);
                        star.classList.toggle('text-slate-200', parseInt(star.dataset.star) > val);
                    });
                });
            });
            </script>
        @endif
    </div>
    @endif
</div>
@endsection
