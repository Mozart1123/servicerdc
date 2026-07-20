@extends($layout)

@section('header_title', 'Détails de la Mission')

@section($contentSection)
<div class="space-y-10 pb-20 max-w-5xl mx-auto">
    
    <!-- Top Nav -->
    <a href="{{ route('user.missions.index') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors">
        <i class="fas fa-arrow-left"></i> Retour
    </a>

    <!-- Status Header -->
    <div class="bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 to-transparent -z-0"></div>
        
        <div class="relative z-10 flex-1 text-center md:text-left">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Mission #{{ str_pad($mission->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm font-bold text-slate-500 mt-2 uppercase tracking-widest">{{ $mission->service->title ?? 'Service Supprimé' }}</p>
        </div>
        
        <div class="relative z-10 text-center md:text-right">
            @if($mission->status == 'pending')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full text-slate-400 text-3xl mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">En attente</div>
            @elseif($mission->status == 'in_progress')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-100 rounded-full text-amber-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-spinner animate-spin"></i>
                </div>
                <div class="text-[10px] font-black text-amber-500 uppercase tracking-widest">En cours</div>
            @elseif($mission->status == 'completed')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full text-emerald-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-check"></i>
                </div>
                <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Terminée</div>
            @else
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full text-red-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-times"></i>
                </div>
                <div class="text-[10px] font-black text-red-500 uppercase tracking-widest">Annulée</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Actors Info -->
        <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm flex flex-col items-center text-center">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2 w-full">Profil Artisan</div>
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-2xl text-slate-400 mb-4">
                <i class="fas fa-hard-hat"></i>
            </div>
            <h4 class="font-heading font-black text-slate-900 text-lg">{{ $mission->artisan->name ?? 'Indisponible' }}</h4>
            <p class="text-xs font-bold text-slate-500 mt-1"><i class="fas fa-phone mr-1 opacity-50"></i> {{ $mission->artisan->phone ?? 'Non renseigné' }}</p>
        </div>

        <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm flex flex-col items-center text-center">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2 w-full">Profil Client</div>
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-2xl text-slate-400 mb-4">
                <i class="fas fa-user"></i>
            </div>
            <h4 class="font-heading font-black text-slate-900 text-lg">{{ $mission->client->name ?? 'Indisponible' }}</h4>
            <p class="text-xs font-bold text-slate-500 mt-1"><i class="fas fa-phone mr-1 opacity-50"></i> {{ $mission->client->phone ?? 'Non renseigné' }}</p>
        </div>
    </div>

    <!-- Timeline / Timestamps -->
    <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">Chronologie de la Mission</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->start_date ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->start_date ? 'bg-amber-100 text-amber-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-play"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->start_date ? 'text-amber-500' : 'text-slate-400' }}">Démarrée</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->start_date ? $mission->start_date->format('d/m/Y H:i') : 'Pas encore' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->status == 'completed' ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->status == 'completed' ? 'bg-emerald-100 text-emerald-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->status == 'completed' ? 'text-emerald-500' : 'text-slate-400' }}">Terminée</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->end_date ? $mission->end_date->format('d/m/Y H:i') : 'Pas encore' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->rating ? 'bg-blue-50 border border-blue-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->rating ? 'bg-blue-100 text-blue-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->rating ? 'text-blue-500' : 'text-slate-400' }}">Avis Client</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->rating ? $mission->rating . '/5' : 'En attente' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- K-PAY COMMISSION PAYMENT (Client only, not yet paid)           --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(Auth::id() == $mission->client_id && $mission->commission_status !== 'paid')
    <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm"
         x-data="{
            phone: '',
            provider: '',
            loading: false,
            detecting: false,
            pollInterval: null,
            ref: null,
            state: 'idle', {{-- idle | pending | success | failed --}}
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
                    if (d.provider) { this.provider = d.provider; }
                } catch(e) {}
                this.detecting = false;
            },

            async pay() {
                if (!this.phone) { this.showToast('Veuillez saisir un numéro de téléphone.', 'error'); return; }
                this.loading = true;
                this.state   = 'idle';
                try {
                    const r = await fetch('/api/client/payments/initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Authorization': 'Bearer {{ session('api_token', '') }}'
                        },
                        body: JSON.stringify({
                            provider:      this.provider || 'VODACOM_MPESA_COD',
                            phone_number:  this.phone,
                            payment_type:  'mission',
                            reference_id:  {{ $mission->id }},
                        })
                    });
                    const d = await r.json();
                    if (!r.ok) { this.showToast(d.error ?? 'Erreur lors du paiement.', 'error'); this.loading = false; return; }

                    this.ref   = d.reference;
                    this.state = 'pending';
                    this.showToast('Confirmez le prompt USSD sur votre téléphone.', 'success');
                    this.startPolling();
                } catch(e) {
                    this.showToast('Erreur réseau.', 'error');
                }
                this.loading = false;
            },

            startPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                let attempts = 0;
                this.pollInterval = setInterval(async () => {
                    attempts++;
                    if (attempts > 24) { 
                        clearInterval(this.pollInterval); 
                        this.state = 'expired';
                        this.showToast('Le délai d\'attente a expiré.', 'error');
                        return; 
                    } // 2 min max
                    try {
                        const r = await fetch('/api/client/payments/status/' + this.ref, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const d = await r.json();
                        if (d.status === 'success') {
                            this.state = 'success';
                            clearInterval(this.pollInterval);
                            setTimeout(() => window.location.reload(), 2500);
                        } else if (d.status === 'failed') {
                            this.state = 'failed';
                            clearInterval(this.pollInterval);
                            this.showToast('Le paiement a échoué. Veuillez réessayer.', 'error');
                        }
                    } catch(e) {}
                }, 5000);
            }
         }">

        {{-- Toast --}}
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'"
             class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-2xl shadow-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-3"
             style="display:none">
            <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="toast.message"></span>
        </div>

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center">
                <i class="fas fa-mobile-screen-button"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Payer la commission</h3>
                <p class="text-[10px] font-bold text-slate-400">Via Mobile Money · USSD Push</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-2xl font-heading font-black text-slate-900">${{ number_format($mission->commission_amount ?? ($mission->amount * 0.15), 2) }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Commission due</p>
            </div>
        </div>

        {{-- Idle / Input state --}}
        <div x-show="state === 'idle' || state === 'failed'" x-transition class="space-y-4">
            <div>
                <label class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Numéro Mobile Money <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <input type="tel"
                           x-model="phone"
                           @input.debounce.600ms="detectProvider()"
                           placeholder="+243 9xx xxx xxx"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 outline-none pr-16">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400" x-show="detecting">
                        <i class="fas fa-circle-notch animate-spin text-rdc-blue"></i>
                    </div>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-rdc-blue uppercase" x-show="provider && !detecting" x-text="provider.split('_')[0]"></div>
                </div>
            </div>

            @if(config('app.env') === 'local')
            <p class="text-[10px] font-bold text-amber-500"><i class="fas fa-flask mr-1"></i> Test : utilisez <b>+243813456789</b> pour simuler un succès.</p>
            @endif

            <button @click="pay()" :disabled="loading || !phone"
                    class="w-full py-4 bg-slate-900 hover:bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="loading"><i class="fas fa-circle-notch animate-spin"></i></template>
                <template x-if="!loading"><i class="fas fa-lock"></i></template>
                <span x-text="loading ? 'Initiation en cours…' : 'Payer via Mobile Money'"></span>
            </button>

            <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <i class="fas fa-shield-halved mr-1 text-emerald-500"></i> Paiement sécurisé par K-PAY · Aucune redirection
            </p>
        </div>

        {{-- Pending state —waiting for USSD confirm --}}
        <div x-show="state === 'pending'" x-transition class="text-center py-6 space-y-4">
            <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center text-3xl text-amber-500 animate-pulse">
                <i class="fas fa-mobile-screen-button"></i>
            </div>
            <h4 class="font-black text-slate-900 text-base uppercase tracking-widest">Confirmez sur votre téléphone</h4>
            <p class="text-xs font-bold text-slate-400 max-w-xs mx-auto">Un prompt USSD a été envoyé au <span class="text-slate-700" x-text="phone"></span>. Entrez votre code PIN pour confirmer.</p>
            <div class="flex items-center justify-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <i class="fas fa-circle-notch animate-spin text-rdc-blue"></i>
                En attente de confirmation…
            </div>
        </div>

        {{-- Expired state --}}
        <div x-show="state === 'expired'" x-transition class="text-center py-6 space-y-4">
            <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-3xl text-slate-500">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <h4 class="font-black text-slate-900 text-base uppercase tracking-widest">Délai d'attente expiré</h4>
            <p class="text-xs font-bold text-slate-400">Nous n'avons pas reçu la confirmation de votre opérateur dans le temps imparti.</p>
            
            <button @click="state = 'idle'; phone = ''" 
                    class="w-full mt-4 py-4 bg-slate-900 hover:bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl transition-all">
                Réessayer le paiement
            </button>
        </div>

        {{-- Success state --}}
        <div x-show="state === 'success'" x-transition class="text-center py-6 space-y-4">
            <div class="w-20 h-20 mx-auto bg-emerald-100 rounded-full flex items-center justify-center text-3xl text-emerald-500">
                <i class="fas fa-check"></i>
            </div>
            <h4 class="font-black text-emerald-600 text-base uppercase tracking-widest">Paiement confirmé !</h4>
            <p class="text-xs font-bold text-slate-400">Rechargement de la page…</p>
        </div>
    </div>
    @elseif(Auth::id() == $mission->client_id && $mission->commission_status === 'paid')
    {{-- Commission already paid badge --}}
    <div class="bg-emerald-50 rounded-[2rem] px-8 py-5 border border-emerald-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-500 flex-shrink-0">
            <i class="fas fa-circle-check"></i>
        </div>
        <div>
            <p class="font-black text-emerald-700 text-sm">Commission payée</p>
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Paiement confirmé via K-PAY</p>
        </div>
    </div>
    @endif

    <!-- Actions (Status Update) -->
    @if($mission->status != 'completed' && $mission->status != 'cancelled')
    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl">
        <h3 class="text-lg font-heading font-black uppercase mb-6 flex items-center gap-2"><i class="fas fa-bolt text-amber-400"></i> Actions Rapides</h3>
        
        @if(Auth::id() == $mission->artisan_id)
        {{-- Artisan actions: start or complete --}}
        <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
            @csrf
            @method('PUT')
            
            @if($mission->status == 'pending')
            <input type="hidden" name="status" value="in_progress">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 justify-center">
                <i class="fas fa-play"></i> Démarrer la Mission
            </button>
            @elseif($mission->status == 'in_progress')
            <input type="hidden" name="status" value="completed">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 justify-center">
                <i class="fas fa-flag-checkered"></i> Marquer Terminée
            </button>
            @endif

            <button type="button" onclick="document.getElementById('cancel-form').classList.toggle('hidden')" class="w-full md:w-auto px-10 py-4 bg-red-500/20 hover:bg-red-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                Annuler
            </button>
        </form>
        <form id="cancel-form" action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="hidden mt-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="w-full px-10 py-4 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all" onsubmit="return confirm('Confirmer l\'annulation ?')">
                <i class="fas fa-ban"></i> Confirmer l'annulation
            </button>
        </form>
        @else
        {{-- Client: only cancel if not started --}}
        <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-red-500/20 hover:bg-red-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all" onsubmit="return confirm('Annuler cette mission ?')">
                <i class="fas fa-ban"></i> Annuler la Mission
            </button>
        </form>
        @endif
    </div>
    @endif

    <!-- Feedback/Evaluation -->
    @if($mission->status == 'completed')
    <div class="bg-emerald-50 rounded-[3rem] p-10 border border-emerald-100 text-center">
        <h3 class="text-xl font-heading font-black text-slate-900 uppercase mb-2">Évaluation</h3>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">La mission est terminée !</p>
        
        @if($review)
            <!-- Review existe -->
            @if($review->status === 'approved')
                <!-- Avis Approuvé -->
                <div class="bg-white p-8 rounded-3xl inline-block shadow-sm max-w-2xl">
                    <div class="text-3xl text-amber-400 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="opacity-30 fas fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase mb-3">✓ Avis approuvé et publié</p>
                    @if($review->feedback)
                        <p class="text-slate-600 italic">"{{ $review->feedback }}"</p>
                    @endif
                </div>
            @elseif($review->status === 'rejected')
                <!-- Avis Rejeté -->
                <div class="bg-red-50 p-8 rounded-3xl inline-block border border-red-100 max-w-2xl">
                    <p class="text-red-600 font-black text-sm uppercase tracking-widest mb-3">⚠ Avis rejeté</p>
                    @if($review->rejection_reason)
                        <p class="text-red-700 text-sm mb-6"><strong>Raison :</strong> {{ $review->rejection_reason }}</p>
                    @endif
                    <p class="text-slate-600 text-xs mb-6">Vous pouvez soumettre un nouvel avis</p>
                    <button type="button" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all" 
                            onclick="document.getElementById('review-form-{{ $mission->id }}').classList.toggle('hidden')">
                        <i class="fas fa-redo mr-2"></i> Resoummettre un avis
                    </button>
                    
                    <!-- Form Resoumission -->
                    <form id="review-form-{{ $mission->id }}" action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="hidden mt-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        
                        <div>
                            <select name="rating" required class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none">
                                <option value="">Nouvelle note /5</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Très bien</option>
                                <option value="3">⭐⭐⭐ Correct</option>
                                <option value="2">⭐⭐ Décevant</option>
                                <option value="1">⭐ Mauvais</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="feedback" rows="3" placeholder="Votre nouveau commentaire..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-red-500 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-lg hover:bg-red-600 transition-all">
                            Soumettre le nouvel avis
                        </button>
                    </form>
                </div>
            @else
                <!-- En attente d'approbation -->
                <div class="bg-white p-8 rounded-3xl inline-block shadow-sm max-w-2xl border border-amber-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full text-amber-500 text-2xl mb-4 animate-pulse">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <p class="text-amber-600 font-black text-sm uppercase tracking-widest mb-2">En attente de modération</p>
                    <div class="text-3xl text-amber-400 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="opacity-30 fas fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    @if($review->feedback)
                        <p class="text-slate-600 italic text-sm">"{{ $review->feedback }}"</p>
                    @endif
                    <p class="text-slate-400 text-xs mt-4">Votre avis est en cours de vérification par notre équipe d'administration.</p>
                </div>
            @endif
        @else
            <!-- Aucun avis soumis -->
            @if(Auth::id() == $mission->client_id)
                <!-- Formulaire d'évaluation pour le client -->
                <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="max-w-xl mx-auto space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-widest">Quelle note donnez-vous à cet artisan ?</label>
                        <select name="rating" required class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Sélectionnez une note...</option>
                            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                            <option value="4">⭐⭐⭐⭐ Très bien</option>
                            <option value="3">⭐⭐⭐ Correct</option>
                            <option value="2">⭐⭐ Décevant</option>
                            <option value="1">⭐ Mauvais</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-widest">Votre commentaire (optionnel)</label>
                        <textarea name="feedback" rows="4" placeholder="Partagez votre expérience avec cet artisan..." class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-lg hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-star"></i> Soumettre l'évaluation
                    </button>
                </form>
            @else
                <p class="text-slate-500 font-bold bg-white px-8 py-6 rounded-3xl inline-block shadow-sm">En attente de l'évaluation du client</p>
            @endif
        @endif
    </div>
    @endif

</div>
@endsection
