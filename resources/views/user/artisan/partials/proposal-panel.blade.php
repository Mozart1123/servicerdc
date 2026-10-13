{{-- Réponse de l'artisan à une demande publiée (cahier des charges §2.4). --}}
@php
    $tz = config('matching.timezone');
    $money = fn ($v) => '$' . number_format((float) $v, 0, ',', ' ');
    $inputClass = 'w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm';
    // En cas d'erreur, l'heure de visite revient en UTC : on la réaffiche à l'heure de Kinshasa.
    $oldVisitAt = old('visit_at') ? rescue(fn () => \Illuminate\Support\Carbon::parse(old('visit_at'), 'UTC')->setTimezone($tz)->format('Y-m-d\TH:i'), '') : '';
@endphp

@foreach(['success' => 'emerald', 'info' => 'blue'] as $type => $color)
    @if(session($type))
        <div class="px-5 py-4 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 rounded-2xl text-sm font-semibold">{{ session($type) }}</div>
    @endif
@endforeach
@if($errors->any())
    <div class="px-5 py-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
        </ul>
    </div>
@endif

@if($proposal)
    {{-- Réponse déjà envoyée --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-bold text-slate-900">{{ $proposal->isVisit() ? 'Votre proposition de visite' : 'Votre proposition' }}</h3>
            @php
                [$label, $classes] = match ($proposal->status) {
                    'visit_accepted' => ['Visite acceptée', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'accepted'       => ['Retenue par le client', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'rejected'       => ['Non retenue', 'bg-slate-100 text-slate-500 border-slate-200'],
                    default          => ['En attente du client', 'bg-amber-50 text-amber-700 border-amber-200'],
                };
            @endphp
            <span class="px-3 py-1 rounded-full border text-xs font-bold {{ $classes }}">{{ $label }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            @if($proposal->isVisit())
                <div><dt class="text-xs font-bold uppercase text-slate-400">Visite</dt><dd class="font-semibold text-slate-800">{{ $proposal->visit_at->copy()->setTimezone($tz)->translatedFormat('d F Y à H:i') }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-slate-400">Frais d'étude</dt><dd class="font-semibold text-slate-800">{{ (float) $proposal->study_fee > 0 ? $money($proposal->study_fee) : 'Gratuit' }}</dd></div>
            @else
                <div><dt class="text-xs font-bold uppercase text-slate-400">Prix</dt><dd class="font-semibold text-slate-800">{{ $money($proposal->price) }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-slate-400">Date d'intervention</dt><dd class="font-semibold text-slate-800">{{ $proposal->intervention_date->translatedFormat('d F Y') }}</dd></div>
            @endif
        </dl>
        @if($proposal->message)
            <p class="text-sm text-slate-600 whitespace-pre-line bg-slate-50 rounded-xl p-3">{{ $proposal->message }}</p>
        @endif

        @if($proposal->quote_sent_at)
            <div class="border-t border-slate-100 pt-4 text-sm space-y-2">
                <p class="font-bold text-slate-900">Devis envoyé le {{ $proposal->quote_sent_at->copy()->setTimezone($tz)->format('d/m/Y à H:i') }}</p>
                <p>Main d'œuvre {{ $money($proposal->quote_labor) }} · Matériaux {{ $money($proposal->quote_materials) }} · <strong>Total {{ $money($proposal->total) }}</strong></p>
                <p>Délai : {{ $proposal->quote_duration }}</p>
                <p>Paiement : {{ collect($proposal->quote_payment_steps)->map(fn ($s) => $s['label'] . ' ' . $s['percent'] . ' %')->implode(' · ') }}</p>
            </div>
        @endif
    </div>

    @if($proposal->canSendQuote())
        {{-- Devis après la visite --}}
        <form method="POST" action="{{ route('user.artisan.opportunities.quote', $need) }}"
              class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4"
              x-data="{ steps: @js(old('steps', [['label' => 'Avant le début des travaux', 'percent' => 50], ['label' => 'À la fin des travaux', 'percent' => 50]])),
                        labor: @js(old('quote_labor', '')), materials: @js(old('quote_materials', '')),
                        get totalPercent() { return this.steps.reduce((a, s) => a + (parseInt(s.percent) || 0), 0); },
                        get total() { return (parseFloat(this.labor) || 0) + (parseFloat(this.materials) || 0); } }">
            @csrf
            <h3 class="font-bold text-slate-900">Envoyer mon devis</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="block text-sm font-semibold text-slate-700">Main d'œuvre ($)
                    <input type="number" name="quote_labor" min="0" step="1" x-model="labor" required class="{{ $inputClass }} mt-1">
                </label>
                <label class="block text-sm font-semibold text-slate-700">Matériaux ($)
                    <input type="number" name="quote_materials" min="0" step="1" x-model="materials" required class="{{ $inputClass }} mt-1">
                </label>
                <label class="block text-sm font-semibold text-slate-700 sm:col-span-2">Délai de réalisation
                    <input type="text" name="quote_duration" maxlength="100" value="{{ old('quote_duration') }}" required placeholder="Ex : 3 jours" class="{{ $inputClass }} mt-1">
                </label>
            </div>
            <p class="text-sm font-bold text-slate-900">Total : $<span x-text="total.toLocaleString('fr-FR')"></span></p>

            <div>
                <p class="text-sm font-semibold text-slate-700 mb-2">Étapes de paiement <span class="font-normal text-xs" :class="totalPercent === 100 ? 'text-emerald-600' : 'text-red-500'">(<span x-text="totalPercent"></span> % / 100 %)</span></p>
                <template x-for="(step, i) in steps" :key="i">
                    <div class="flex gap-2 mb-2">
                        <input type="text" :name="`steps[${i}][label]`" x-model="step.label" maxlength="60" required class="{{ $inputClass }} flex-1">
                        <input type="number" :name="`steps[${i}][percent]`" x-model="step.percent" min="1" max="100" required class="w-24 px-3 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue outline-none text-sm">
                        <button type="button" x-show="steps.length > 1" @click="steps.splice(i, 1)" class="px-3 text-slate-400 hover:text-red-500" aria-label="Retirer l'étape"><i class="fas fa-times"></i></button>
                    </div>
                </template>
                <button type="button" x-show="steps.length < 4" @click="steps.push({ label: '', percent: 0 })" class="text-xs font-bold text-rdc-blue"><i class="fas fa-plus mr-1"></i>Ajouter une étape</button>
            </div>

            <label class="block text-sm font-semibold text-slate-700">Message <span class="font-normal text-slate-400">(facultatif)</span>
                <textarea name="quote_message" rows="3" maxlength="500" class="{{ $inputClass }} mt-1 resize-none">{{ old('quote_message') }}</textarea>
            </label>
            <button type="submit" :disabled="totalPercent !== 100 || total <= 0"
                    class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md disabled:opacity-50">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Envoyer le devis
            </button>
        </form>
    @endif

@elseif($dispatch->status === 'declined')
    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-5 text-sm text-slate-600">
        <i class="fas fa-circle-xmark text-slate-400 mr-1"></i> Vous avez refusé cette demande{{ $dispatch->decline_reason ? ' : « ' . $dispatch->decline_reason . ' »' : '' }}.
    </div>

@elseif($canRespond)
    {{-- Répondre : proposition de prix, visite ou refus --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6"
         x-data="{ tab: @js(old('type', $errors->has('reason') ? 'decline' : 'price')), reason: @js(old('reason', '')) }">
        <h3 class="font-bold text-slate-900 mb-4">Votre réponse</h3>
        <div class="grid grid-cols-3 gap-2 mb-5">
            @foreach(['price' => ['Proposer un prix', 'fa-tag'], 'visit' => ['Proposer une visite', 'fa-clipboard-list'], 'decline' => ['Refuser', 'fa-xmark']] as $key => [$label, $icon])
                <button type="button" @click="tab = '{{ $key }}'"
                        class="px-2 py-2.5 rounded-xl border-2 text-xs font-bold transition-all"
                        :class="tab === '{{ $key }}' ? 'border-rdc-blue bg-rdc-blue/5 text-rdc-blue' : 'border-slate-100 text-slate-500'">
                    <i class="fas {{ $icon }} block mb-1"></i>{{ $label }}
                </button>
            @endforeach
        </div>

        <form method="POST" action="{{ route('user.artisan.opportunities.propose', $need) }}" x-show="tab === 'price'" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="price">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="block text-sm font-semibold text-slate-700">Votre prix ($)
                    <input type="number" name="price" min="1" max="1000000" step="1" value="{{ old('price') }}" required class="{{ $inputClass }} mt-1">
                </label>
                <label class="block text-sm font-semibold text-slate-700">Date d'intervention
                    <input type="date" name="intervention_date" min="{{ now($tz)->toDateString() }}" value="{{ old('intervention_date') }}" required class="{{ $inputClass }} mt-1">
                </label>
            </div>
            <label class="block text-sm font-semibold text-slate-700">Message au client <span class="font-normal text-slate-400">(facultatif)</span>
                <textarea name="message" rows="3" maxlength="500" placeholder="Ex : Peinture fournie, 2 couches, travail propre." class="{{ $inputClass }} mt-1 resize-none">{{ old('type') === 'price' ? old('message') : '' }}</textarea>
            </label>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md">
                <i class="fas fa-paper-plane mr-1"></i> Envoyer ma proposition
            </button>
        </form>

        <form method="POST" action="{{ route('user.artisan.opportunities.propose', $need) }}" x-show="tab === 'visit'" x-cloak class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="visit">
            <p class="text-xs text-slate-500">Pour les travaux sur devis : vous passez voir le chantier, puis vous envoyez votre devis dans l'application.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="block text-sm font-semibold text-slate-700">Date et heure de la visite
                    <input type="datetime-local" name="visit_at" min="{{ now($tz)->format('Y-m-d\TH:i') }}" value="{{ $oldVisitAt }}" required class="{{ $inputClass }} mt-1">
                </label>
                <label class="block text-sm font-semibold text-slate-700">Frais d'étude ($)
                    <input type="number" name="study_fee" min="0" max="10000" step="1" value="{{ old('study_fee', 0) }}" required class="{{ $inputClass }} mt-1">
                    <span class="block text-xs font-normal text-slate-400 mt-1">0 si la visite est gratuite.</span>
                </label>
            </div>
            <label class="block text-sm font-semibold text-slate-700">Message au client <span class="font-normal text-slate-400">(facultatif)</span>
                <textarea name="message" rows="3" maxlength="500" class="{{ $inputClass }} mt-1 resize-none">{{ old('type') === 'visit' ? old('message') : '' }}</textarea>
            </label>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md">
                <i class="fas fa-paper-plane mr-1"></i> Proposer la visite
            </button>
        </form>

        <form method="POST" action="{{ route('user.artisan.opportunities.decline', $need) }}" x-show="tab === 'decline'" x-cloak class="space-y-4">
            @csrf
            <p class="text-sm font-semibold text-slate-700">Raison <span class="font-normal text-slate-400">(facultatif)</span></p>
            <div class="flex flex-wrap gap-2">
                @foreach($declineReasons as $r)
                    <button type="button" @click="reason = reason === @js($r) ? '' : @js($r)"
                            class="px-3 h-9 rounded-xl border text-xs font-bold"
                            :class="reason === @js($r) ? 'bg-slate-900 border-slate-900 text-white' : 'bg-slate-50 border-slate-200 text-slate-600'">{{ $r }}</button>
                @endforeach
            </div>
            <input type="text" name="reason" x-model="reason" maxlength="255" placeholder="Ou précisez…" class="{{ $inputClass }}">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-bold">
                Refuser la demande
            </button>
        </form>
    </div>

@else
    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-5 text-sm text-slate-600">
        <i class="fas fa-hourglass-end text-slate-400 mr-1"></i>
        Le délai de réponse est dépassé : la demande a été transmise à d'autres artisans.
    </div>
@endif
