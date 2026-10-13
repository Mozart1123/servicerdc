{{-- Propositions des artisans, en cartes comparables (cahier des charges §2.5). --}}
@php
    $tz = config('matching.timezone');
    $money = fn ($v) => '$' . number_format((float) $v, 0, ',', ' ');
    $proximityLabels = ['same' => 'Même commune', 'neighbor' => 'Commune voisine', 'wide' => 'Plus éloigné'];
@endphp
<div class="space-y-4" data-aos="fade-up" id="propositions">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-900">Propositions reçues <span class="text-slate-400 font-semibold">({{ $comparison['cards']->count() }})</span></h3>
    </div>

    @error('proposal')
        <div class="px-5 py-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold">{{ $message }}</div>
    @enderror

    @if($comparison['cards']->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
            <i class="fas fa-hourglass-half text-slate-300 text-2xl mb-2 block"></i>
            Les propositions des artisans apparaîtront ici. Vous serez notifié dès qu'une proposition arrive.
        </div>
    @endif

    @foreach($comparison['cards'] as $card)
        @php
            $p = $card['proposal'];
            $artisan = $card['artisan'];
            $isRecommended = $comparison['recommendedId'] === $p->id;
        @endphp
        <article class="bg-white rounded-2xl border-2 {{ $isRecommended ? 'border-emerald-400 shadow-md' : 'border-slate-100 shadow-sm' }} overflow-hidden">
            @if($isRecommended)
                <div class="bg-emerald-500 text-white px-5 py-2 text-xs font-bold flex flex-wrap items-center gap-x-2">
                    <span><i class="fas fa-award mr-1"></i> Recommandé pour toi</span>
                    <span class="font-semibold text-emerald-50">· {{ $comparison['reason'] }}</span>
                </div>
            @endif

            <div class="p-5 space-y-4">
                {{-- Artisan --}}
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-slate-100 flex items-center justify-center shrink-0 text-slate-400 font-bold">
                        @if($artisan->profile_photo)
                            <img src="{{ Storage::url($artisan->profile_photo) }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr($artisan->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-bold text-slate-900">{{ $artisan->name }}</h4>
                            @if($card['verified'])
                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold"><i class="fas fa-circle-check mr-0.5"></i>Vérifié</span>
                            @endif
                        </div>
                        <p class="text-xs font-semibold text-rdc-blue">{{ $card['profession'] }}</p>
                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 mt-1">
                            @if($card['rating']['is_new'])
                                <span class="font-bold text-amber-600">Nouveau</span>
                            @else
                                <span><i class="fas fa-star text-amber-400"></i> <strong class="text-slate-700">{{ $card['rating']['average_formatted'] }}</strong> ({{ $card['rating']['count'] }} avis)</span>
                            @endif
                            @if($card['proximity'])
                                <span><i class="fas fa-location-dot text-slate-400"></i> {{ $proximityLabels[$card['proximity']] ?? '' }}</span>
                            @endif
                            @if($card['responseBadge'])
                                <span><i class="fas fa-bolt text-amber-500"></i> {{ $card['responseBadge'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Offre --}}
                <div class="rounded-xl bg-slate-50 p-4 text-sm">
                    @if(!$p->isVisit())
                        <div class="flex flex-wrap items-end justify-between gap-2">
                            <p class="text-2xl font-black text-slate-900">{{ $money($p->price) }}</p>
                            <p class="text-slate-600"><i class="fas fa-calendar-day text-rdc-blue mr-1"></i>Le {{ $p->intervention_date->translatedFormat('d F Y') }}</p>
                        </div>
                    @elseif($p->quote_sent_at)
                        <div class="flex flex-wrap items-end justify-between gap-2">
                            <p class="text-2xl font-black text-slate-900">{{ $money($p->total) }} <span class="text-xs font-bold text-slate-400">devis</span></p>
                            <p class="text-slate-600"><i class="fas fa-clock text-rdc-blue mr-1"></i>{{ $p->quote_duration }}</p>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Main d'œuvre {{ $money($p->quote_labor) }} · Matériaux {{ $money($p->quote_materials) }}</p>
                        <p class="text-xs text-slate-500">Paiement : {{ collect($p->quote_payment_steps)->map(fn ($s) => $s['label'] . ' ' . $s['percent'] . ' %')->implode(' · ') }}</p>
                        @if($p->quote_message)<p class="text-xs text-slate-600 mt-2 whitespace-pre-line">{{ $p->quote_message }}</p>@endif
                    @else
                        <p class="font-bold text-slate-900"><i class="fas fa-clipboard-list text-rdc-blue mr-1"></i>
                            {{ $p->status === 'visit_accepted' ? 'Visite acceptée — devis attendu après le passage de l\'artisan' : 'Propose une visite pour établir un devis' }}
                        </p>
                        <p class="text-slate-600 mt-1">
                            Le {{ $p->visit_at->copy()->setTimezone($tz)->translatedFormat('d F Y à H:i') }}
                            · Frais d'étude : {{ (float) $p->study_fee > 0 ? $money($p->study_fee) : 'gratuit' }}
                        </p>
                    @endif
                </div>

                @if($p->message)
                    <p class="text-sm text-slate-700 whitespace-pre-line">« {{ $p->message }} »</p>
                @endif

                @if($card['realisations']->isNotEmpty())
                    <div class="flex gap-2">
                        @foreach($card['realisations'] as $realisation)
                            <img src="{{ $realisation->image_url }}" alt="{{ $realisation->caption ?? 'Réalisation' }}" loading="lazy"
                                 class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border border-slate-100">
                        @endforeach
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if(!$p->isVisit() || $p->quote_sent_at)
                        <form method="POST" action="{{ route('user.needs.proposals.accept', $p) }}"
                              onsubmit="return confirm('Accepter cette proposition ? Le prix de {{ $money($p->total) }} sera verrouillé et les autres artisans seront prévenus.')">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold shadow-sm">
                                <i class="fas fa-check mr-1"></i> {{ $p->isVisit() ? 'Accepter le devis' : 'Accepter' }}
                            </button>
                        </form>
                    @elseif($p->status === 'pending')
                        <form method="POST" action="{{ route('user.needs.proposals.accept-visit', $p) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold shadow-sm">
                                <i class="fas fa-calendar-check mr-1"></i> Accepter la visite
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('user.messages.start.user', ['user' => $artisan->id, 'related_type' => 'service', 'related_id' => $need->id]) }}"
                       class="px-4 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold">
                        <i class="fas fa-comments mr-1"></i> Discuter
                    </a>
                    <a href="{{ route('public.artisans.show', $artisan->id) }}" target="_blank" rel="noopener"
                       class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50">
                        Voir le profil
                    </a>
                </div>
            </div>
        </article>
    @endforeach

    @if($comparison['cards']->isNotEmpty())
        <form method="POST" action="{{ route('user.needs.reject-all', $need) }}" class="text-center"
              onsubmit="return confirm('Refuser toutes les propositions ? Nous contacterons d\'autres artisans.')">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-red-600 underline underline-offset-4">
                Aucune proposition ne me convient
            </button>
        </form>
    @endif
</div>
