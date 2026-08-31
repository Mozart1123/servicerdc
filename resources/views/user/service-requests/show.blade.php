@extends($layout)

@section('title', 'Demande de service | ProConnect')
@section('header_title', 'Détail de la demande')

@section($contentSection)
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
                    @php $c = match($serviceRequest->status) {'accepted'=>'orange','in_progress'=>'emerald','rejected'=>'red','completed'=>'blue','cancelled'=>'slate',default=>'amber'}; @endphp
                    <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                        {{ $serviceRequest->status_label }}
                    </span>
                </div>
                <p class="text-sm text-slate-400">Envoyée le {{ $serviceRequest->created_at->format('d M Y à H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Cumulative Work-Time Card (in_progress) --}}
    @if($serviceRequest->status === 'in_progress')
    @php
        $isPaused          = $serviceRequest->isWorkPaused();
        $activeSession     = $serviceRequest->activeWorkSession;
        $totalSecondsNow   = $serviceRequest->totalWorkedSeconds();
        $baseSeconds       = $totalSecondsNow - ($activeSession ? $activeSession->durationInSeconds() : 0);
        $lastClosedSession = $serviceRequest->workSessions->whereNotNull('ended_at')->sortByDesc('ended_at')->first();
    @endphp
    <div class="{{ $isPaused ? 'bg-slate-700' : 'bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-emerald-200' }} rounded-2xl shadow-xl p-6 text-white"
         data-aos="fade-up" id="timer-card"
         data-base-seconds="{{ $baseSeconds }}"
         data-active-since="{{ $activeSession?->started_at?->toIso8601String() }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] {{ $isPaused ? 'text-slate-300' : 'text-emerald-100' }} mb-1">
                    {{ $isPaused ? 'Travail en pause' : 'Service en cours' }}
                </p>
                <p class="text-sm font-bold {{ $isPaused ? 'text-slate-200' : 'text-emerald-50' }}">
                    @if($isPaused && $lastClosedSession)
                        Mis en pause {{ $lastClosedSession->ended_at->diffForHumans() }}
                    @elseif($isPaused)
                        Le travail n'a pas encore commencé aujourd'hui
                    @else
                        Le chronomètre est lancé
                    @endif
                </p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas {{ $isPaused ? 'fa-pause' : 'fa-stopwatch' }} text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p id="live-timer" class="font-mono text-5xl font-black tracking-wider text-white">00:00:00</p>
            <p class="text-xs {{ $isPaused ? 'text-slate-300' : 'text-emerald-100' }} mt-2 uppercase tracking-widest">Temps de travail cumulé</p>
        </div>
    </div>

    @if($serviceRequest->artisan_id === auth()->id())
    <div class="flex justify-center" data-aos="fade-up">
        @if($isPaused)
        <form action="{{ route('user.service-requests.resume-work', $serviceRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl text-[11px] uppercase tracking-widest transition-all inline-flex items-center gap-2 shadow-lg shadow-emerald-200">
                <i class="fas fa-play"></i> Reprendre le travail
            </button>
        </form>
        @else
        <form action="{{ route('user.service-requests.pause-work', $serviceRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-xl text-[11px] uppercase tracking-widest transition-all inline-flex items-center gap-2">
                <i class="fas fa-pause"></i> Mettre en pause
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- Worked time per day --}}
    @if($serviceRequest->workSessions->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" data-aos="fade-up">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-days text-rdc-blue"></i> Temps travaillé par jour
        </h3>
        @php
            $joursFr = ['Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche'];
            $moisFr  = ['01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril', '05' => 'mai', '06' => 'juin', '07' => 'juillet', '08' => 'août', '09' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre'];
            $fmtDuration = function (int $seconds) {
                $h = intdiv($seconds, 3600);
                $m = intdiv($seconds % 3600, 60);
                $s = $seconds % 60;
                if ($h > 0) return sprintf('%dh %02dmin', $h, $m);
                if ($m > 0) return sprintf('%dmin %02ds', $m, $s);
                return "{$s}s";
            };
            $todayStr     = \Carbon\Carbon::today()->toDateString();
            $yesterdayStr = \Carbon\Carbon::yesterday()->toDateString();
        @endphp
        <div class="divide-y divide-slate-100">
            @foreach($serviceRequest->workSessionsByDay() as $day)
            @php
                $dateObj = \Carbon\Carbon::parse($day['date']);
                if ($day['date'] === $todayStr) {
                    $dayLabel = "Aujourd'hui";
                } elseif ($day['date'] === $yesterdayStr) {
                    $dayLabel = 'Hier';
                } else {
                    $dayLabel = $joursFr[$dateObj->format('l')] . ' ' . $dateObj->format('d') . ' ' . $moisFr[$dateObj->format('m')];
                    if ((int) $dateObj->format('Y') !== (int) now()->format('Y')) {
                        $dayLabel .= ' ' . $dateObj->format('Y');
                    }
                }
                $isTodayLive = $day['date'] === $todayStr && $activeSession;
                $todayBaseSeconds = $isTodayLive ? $day['seconds'] - $activeSession->durationInSeconds() : $day['seconds'];
            @endphp
            <div class="flex items-center justify-between py-3">
                <span class="text-sm font-semibold text-slate-700">{{ $dayLabel }}</span>
                <span class="text-sm font-black text-slate-900 font-mono"
                      @if($isTodayLive) id="day-total-today" data-base-seconds="{{ $todayBaseSeconds }}" @endif>
                    {{ $fmtDuration($day['seconds']) }}
                </span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center justify-between pt-4 mt-2 border-t-2 border-slate-100">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total</span>
            <span class="text-base font-black text-rdc-blue font-mono">{{ $fmtDuration($totalSecondsNow) }}</span>
        </div>
    </div>
    @endif

    <script>
    (function() {
        var card = document.getElementById('timer-card');
        if (!card) return;
        var timerEl    = document.getElementById('live-timer');
        var dayTotalEl = document.getElementById('day-total-today');
        var baseSeconds     = parseInt(card.dataset.baseSeconds || '0', 10);
        var dayBaseSeconds  = dayTotalEl ? parseInt(dayTotalEl.dataset.baseSeconds || '0', 10) : null;
        var activeSince = card.dataset.activeSince ? new Date(card.dataset.activeSince).getTime() : null;

        function format(totalSeconds) {
            var h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
            var m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
            var s = String(totalSeconds % 60).padStart(2, '0');
            return h + ':' + m + ':' + s;
        }

        function formatShort(totalSeconds) {
            var h = Math.floor(totalSeconds / 3600);
            var m = Math.floor((totalSeconds % 3600) / 60);
            var s = totalSeconds % 60;
            if (h > 0) return h + 'h ' + String(m).padStart(2, '0') + 'min';
            if (m > 0) return m + 'min ' + String(s).padStart(2, '0') + 's';
            return s + 's';
        }

        function updateTimer() {
            var elapsedSinceResume = activeSince ? Math.floor((Date.now() - activeSince) / 1000) : 0;
            timerEl.textContent = format(baseSeconds + elapsedSinceResume);
            if (dayTotalEl) {
                dayTotalEl.textContent = formatShort(dayBaseSeconds + elapsedSinceResume);
            }
        }

        updateTimer();
        if (activeSince) {
            setInterval(updateTimer, 1000);
        }
    })();
    </script>
    @endif

    {{-- Awaiting Client Validation Banner --}}
    @if($serviceRequest->status === 'awaiting_validation')
    <div class="bg-gradient-to-r from-amber-400 to-amber-500 rounded-2xl shadow-xl shadow-amber-200 p-6 text-white" data-aos="fade-up">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shrink-0">
                <i class="fas fa-flag-checkered text-2xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-100 mb-1">En attente de confirmation</p>
                @if($serviceRequest->user_id === auth()->id())
                    <p class="text-sm font-bold text-white">L'artisan indique que le travail est terminé. Confirmez-vous que la prestation est bien réalisée ?</p>
                @else
                    <p class="text-sm font-bold text-white">En attente de la confirmation du client avant la clôture de la mission.</p>
                @endif
            </div>
        </div>
        @if($serviceRequest->user_id === auth()->id())
        <form action="{{ route('user.service-requests.validate-completion', $serviceRequest->id) }}" method="POST" class="mt-5"
              onsubmit="return confirm('Confirmez-vous que le travail est bien terminé et que vous êtes satisfait(e) ?');">
            @csrf
            <button type="submit" class="w-full py-3.5 bg-white text-amber-600 font-black rounded-xl text-[11px] uppercase tracking-widest hover:bg-amber-50 transition-all">
                <i class="fas fa-check-circle mr-2"></i> Confirmer la fin du service
            </button>
        </form>
        @endif
    </div>
    @endif

    @if($serviceRequest->status === 'completed')
    @php
        $totalWorkedFinal = $serviceRequest->totalWorkedSeconds();
        $fmtDurationLong = function (int $seconds) {
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            return $h > 0 ? "{$h}h " . str_pad((string) $m, 2, '0', STR_PAD_LEFT) . 'min' : "{$m}min " . str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT) . 's';
        };
    @endphp
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-xl shadow-blue-200 p-6 text-white" data-aos="fade-up">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-100 mb-1">Service terminé</p>
                <p class="text-sm font-bold text-blue-50">Temps de travail total (hors pauses)</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-flag-checkered text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p class="font-mono text-5xl font-black tracking-wider text-white">{{ $fmtDurationLong($totalWorkedFinal) }}</p>
            @if($serviceRequest->accepted_at && $serviceRequest->completed_at)
            <p class="text-xs text-blue-100 mt-2 uppercase tracking-widest">Du {{ $serviceRequest->accepted_at->format('d/m/Y') }} au {{ $serviceRequest->completed_at->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Worked time per day (final, static breakdown) --}}
    @if($serviceRequest->workSessions->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" data-aos="fade-up">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-days text-rdc-blue"></i> Temps travaillé par jour
        </h3>
        @php
            $joursFr = ['Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche'];
            $moisFr  = ['01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril', '05' => 'mai', '06' => 'juin', '07' => 'juillet', '08' => 'août', '09' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre'];
            $fmtDuration = function (int $seconds) {
                $h = intdiv($seconds, 3600);
                $m = intdiv($seconds % 3600, 60);
                $s = $seconds % 60;
                if ($h > 0) return sprintf('%dh %02dmin', $h, $m);
                if ($m > 0) return sprintf('%dmin %02ds', $m, $s);
                return "{$s}s";
            };
            $todayStr     = \Carbon\Carbon::today()->toDateString();
            $yesterdayStr = \Carbon\Carbon::yesterday()->toDateString();
        @endphp
        <div class="divide-y divide-slate-100">
            @foreach($serviceRequest->workSessionsByDay() as $day)
            @php
                $dateObj = \Carbon\Carbon::parse($day['date']);
                if ($day['date'] === $todayStr) {
                    $dayLabel = "Aujourd'hui";
                } elseif ($day['date'] === $yesterdayStr) {
                    $dayLabel = 'Hier';
                } else {
                    $dayLabel = $joursFr[$dateObj->format('l')] . ' ' . $dateObj->format('d') . ' ' . $moisFr[$dateObj->format('m')];
                    if ((int) $dateObj->format('Y') !== (int) now()->format('Y')) {
                        $dayLabel .= ' ' . $dateObj->format('Y');
                    }
                }
            @endphp
            <div class="flex items-center justify-between py-3">
                <span class="text-sm font-semibold text-slate-700">{{ $dayLabel }}</span>
                <span class="text-sm font-black text-slate-900 font-mono">{{ $fmtDuration($day['seconds']) }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center justify-between pt-4 mt-2 border-t-2 border-slate-100">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total</span>
            <span class="text-base font-black text-rdc-blue font-mono">{{ $fmtDuration($totalWorkedFinal) }}</span>
        </div>
    </div>
    @endif
    @endif

    {{-- Paid Status Banner --}}
    @if($serviceRequest->payment_status === 'paid')
    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 flex items-center justify-between gap-4" data-aos="fade-up">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="font-bold text-slate-900">Prestation réglée et clôturée</h4>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 font-bold text-[10px] uppercase rounded-full">Payé</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Le montant de <b>{{ number_format($amountToPay, 2) }} $</b> a été crédité sur le portefeuille de l'artisan.
                    @if($serviceRequest->paid_at)
                    <span class="text-slate-400"> (le {{ $serviceRequest->paid_at->format('d/m/Y à H:i') }})</span>
                    @endif
                </p>
            </div>
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

        @if(in_array($serviceRequest->status, ['accepted', 'in_progress', 'awaiting_validation', 'completed']) && isset($conversation))
        <a href="{{ route('user.messages.index', ['id' => $conversation->id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-200">
            <i class="fas fa-comments"></i> Discuter avec l'artisan
        </a>
        @endif

        @if($serviceRequest->status === 'in_progress' && $serviceRequest->artisan_id === auth()->id())
        <form action="{{ route('user.service-requests.complete', $serviceRequest->id) }}" method="POST"
              onsubmit="return confirm('Signaler ce service comme terminé ? Le client devra confirmer avant que la mission soit officiellement clôturée.');">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-check-circle"></i> Signaler le travail terminé
            </button>
        </form>
        @endif

        @if(in_array($serviceRequest->status, ['pending', 'accepted']) && $serviceRequest->user_id === auth()->id())
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

    {{-- Démarrer le service (Client Only, when accepted and waiting to start) --}}
    @if($serviceRequest->status === 'accepted' && $serviceRequest->user_id === auth()->id() && $serviceRequest->mission)
    @php $mission = $serviceRequest->mission; @endphp
    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm mt-6" data-aos="fade-up">

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center">
                <i class="fas fa-play"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Demarrer le service</h3>
                <p class="text-[10px] font-bold text-slate-400">Confirmez pour lancer le chrono</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-2xl font-heading font-black text-slate-900">${{ number_format($mission->amount ?? 0, 2) }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Montant convenu</p>
            </div>
        </div>

        <div class="border border-slate-200 rounded-2xl p-5">
            <h4 class="font-bold text-slate-900 mb-1"><i class="fas fa-money-bill-wave mr-2 text-emerald-600"></i>Paiement en espèces</h4>
            <p class="text-xs text-slate-500 mb-4">Le règlement se fait directement de la main à la main avec l'artisan, en dehors de l'application.</p>
            <form action="{{ route('user.service-requests.pay-cash', $serviceRequest->id) }}" method="POST"
                  onsubmit="return confirm('Confirmez le démarrage du service ? Le chrono démarrera immédiatement.')">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-slate-900 hover:bg-rdc-blue text-white font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">
                    Démarrer le service
                </button>
            </form>
        </div>
    </div>
    @endif

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
