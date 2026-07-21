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

    {{-- Live Timer / Elapsed Time Card --}}
    @if(in_array($serviceRequest->status, ['in_progress', 'completed']) && $serviceRequest->accepted_at)
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
        @if(in_array($serviceRequest->status, ['accepted', 'in_progress', 'completed']) && isset($conversation))
        <a href="{{ route('user.messages.index', ['id' => $conversation->id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-200">
            <i class="fas fa-comments"></i> Discuter avec l'artisan
        </a>
        @endif

        @if($serviceRequest->status === 'in_progress' && $serviceRequest->artisan_id === auth()->id())
        <form action="{{ route('user.service-requests.complete', $serviceRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                <i class="fas fa-check-circle"></i> Marquer comme termine
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

    {{-- Payment Choice (Client Only, when accepted and waiting for payment) --}}
    @if($serviceRequest->status === 'accepted' && $serviceRequest->user_id === auth()->id() && $serviceRequest->mission)
    @php $mission = $serviceRequest->mission; @endphp
    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm mt-6" data-aos="fade-up"
         x-data="{
            phone: '',
            provider: '',
            loading: false,
            detecting: false,
            pollInterval: null,
            ref: null,
            state: 'idle',
            toast: { show: false, message: '', type: 'success' },
            showToast(msg, type = 'success') {
                this.toast = { show: true, message: msg, type };
                setTimeout(() => this.toast.show = false, 5000);
            },
            async detectProvider() {
                if (this.phone.replace(/\D/g,'').length < 9) return;
                this.detecting = true;
                try {
                    const r = await fetch('/api/payments/predict-provider', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ phone_number: this.phone })
                    });
                    const d = await r.json();
                    if (d.provider) this.provider = d.provider;
                } catch(e) {}
                this.detecting = false;
            },
            async pay() {
                if (!this.phone) { this.showToast('Veuillez saisir un numero de telephone.', 'error'); return; }
                this.loading = true;
                try {
                    const r = await fetch('/api/client/payments/initiate', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Authorization': 'Bearer {{ session("api_token", "") }}' },
                        body: JSON.stringify({ provider: this.provider || 'VODACOM_MPESA_COD', phone_number: this.phone, payment_type: 'mission', reference_id: {{ $mission->id }} })
                    });
                    const d = await r.json();
                    if (!r.ok) { this.showToast(d.error ?? 'Erreur paiement.', 'error'); this.loading = false; return; }
                    this.ref = d.reference; this.state = 'pending';
                    this.showToast('Confirmez le prompt USSD sur votre telephone.', 'success');
                    this.startPolling();
                } catch(e) { this.showToast('Erreur reseau.', 'error'); }
                this.loading = false;
            },
            startPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                let attempts = 0;
                this.pollInterval = setInterval(async () => {
                    attempts++;
                    if (attempts > 24) { clearInterval(this.pollInterval); this.state = 'expired'; return; }
                    try {
                        const r = await fetch('/api/client/payments/status/' + this.ref, { headers: { 'Accept': 'application/json' } });
                        const d = await r.json();
                        if (d.status === 'success') { this.state = 'success'; clearInterval(this.pollInterval); setTimeout(() => window.location.reload(), 2500); }
                        else if (d.status === 'failed') { this.state = 'failed'; clearInterval(this.pollInterval); this.showToast('Paiement echoue.', 'error'); }
                    } catch(e) {}
                }, 5000);
            }
         }">

        {{-- Toast --}}
        <div x-show="toast.show" x-transition
             :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'"
             class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-2xl shadow-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-3"
             style="display:none">
            <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="toast.message"></span>
        </div>

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Demarrer le service</h3>
                <p class="text-[10px] font-bold text-slate-400">Choisissez votre mode de paiement pour lancer le chrono</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-2xl font-heading font-black text-slate-900">${{ number_format($mission->amount ?? 0, 2) }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Montant convenu</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- K-PAY Option --}}
            <div class="border-2 border-rdc-blue/20 rounded-2xl p-5">
                <h4 class="font-bold text-slate-900 mb-1"><i class="fas fa-mobile-screen-button mr-2 text-rdc-blue"></i>Paiement Mobile (K-PAY)</h4>
                <p class="text-xs text-slate-500 mb-4">Commission de {{ number_format($mission->commission_amount ?? ($mission->amount * 0.15), 2) }} $ debitee par push USSD.</p>

                <div x-show="state === 'idle' || state === 'failed'" class="space-y-3">
                    <div class="relative">
                        <input type="tel" x-model="phone" @input.debounce.600ms="detectProvider()"
                               placeholder="+243 9xx xxx xxx"
                               class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 outline-none pr-14">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-rdc-blue uppercase" x-show="provider && !detecting" x-text="provider.split('_')[0]"></div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2" x-show="detecting"><i class="fas fa-circle-notch animate-spin text-rdc-blue text-xs"></i></div>
                    </div>
                    <button @click="pay()" :disabled="loading" class="w-full relative px-6 py-3 bg-rdc-blue hover:bg-rdc-blue-dark text-white font-black rounded-xl text-[10px] uppercase tracking-widest transition-all disabled:opacity-60">
                        <span :class="{'opacity-0': loading}">Payer via K-PAY</span>
                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center"><i class="fas fa-circle-notch animate-spin"></i></div>
                    </button>
                </div>

                <div x-show="state === 'pending'" style="display:none" class="py-4 text-center">
                    <i class="fas fa-mobile-screen text-3xl text-blue-500 animate-bounce mb-2"></i>
                    <p class="text-sm font-bold text-slate-900">Verifiez votre telephone</p>
                    <p class="text-xs text-slate-500 mt-1">Prompt USSD envoye au <b x-text="phone"></b></p>
                </div>

                <div x-show="state === 'success'" style="display:none" class="py-4 text-center">
                    <i class="fas fa-check-circle text-3xl text-emerald-500 mb-2"></i>
                    <p class="text-sm font-bold text-slate-900">Paiement valide ! Demarrage...</p>
                </div>
            </div>

            {{-- Cash Option --}}
            <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-slate-900 mb-1"><i class="fas fa-money-bill-wave mr-2 text-emerald-600"></i>Paiement en especes</h4>
                    <p class="text-xs text-slate-500">Paiement direct de la main a la main. Le chrono demarre immediatement.</p>
                </div>
                <form action="{{ route('user.service-requests.pay-cash', $serviceRequest->id) }}" method="POST" class="mt-4"
                      onsubmit="return confirm('Confirmez le paiement en especes ? Le service demarrera immediatement.')">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">
                        Choisir paiement Cash
                    </button>
                </form>
            </div>
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
