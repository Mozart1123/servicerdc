@extends('layouts.user')

@section('header_title', 'Paiement - Plan ' . $plan->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-10 pb-20">

    <!-- Back -->
    <a href="{{ route('user.subscription.index') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors">
        <i class="fas fa-arrow-left"></i> Changer de plan
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

        {{-- === CHECKOUT FORM === --}}
        <div class="lg:col-span-3 space-y-6">

            <div class="bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm">
                <h2 class="text-2xl font-heading font-black text-slate-900 uppercase mb-2">Finaliser l'abonnement</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">Choisissez votre mode de paiement préféré</p>

                <form action="{{ route('user.subscription.subscribe') }}" method="POST" id="checkout-form" class="space-y-8">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="billing_cycle" value="{{ $billing }}">

                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                            @foreach($errors->all() as $error)
                                <p class="text-xs font-bold text-red-600"><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Payment Method: Mobile Money uniquement --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Mode de paiement <span class="text-red-500">*</span></label>

                        <input type="hidden" name="payment_method" value="mobile_money">

                        {{-- Mobile Money --}}
                        <div class="border-2 border-emerald-400 bg-emerald-50 rounded-2xl p-5 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl flex-shrink-0">
                                <i class="fas fa-mobile-screen-button"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-slate-900">Mobile Money</p>
                                <p class="text-xs font-bold text-slate-400">M-Pesa, Airtel Money, Orange Money</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 bg-emerald-500 border-emerald-500 flex items-center justify-center">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                        </div>

                        {{-- Numéro Mobile Money --}}
                        <div class="pl-4">
                            <label class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Numéro Mobile Money <span class="text-red-500">*</span></label>
                            <input type="tel" name="payment_phone" placeholder="+243 9xx xxx xxx" value="{{ old('payment_phone') }}"
                                   class="mt-2 w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 outline-none">
                            @if(config('app.env') === 'local')
                                <p class="text-[10px] font-bold text-amber-500 mt-1"><i class="fas fa-flask"></i> Mode Test : Utilisez le numéro K-Pay <b>243813456789</b> pour simuler un succès (RDC).</p>
                            @endif
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-4 border-t border-slate-100">
                        @if($plan->slug !== 'starter')
                            <button type="submit"
                                    class="w-full py-5 bg-slate-900 text-white font-black rounded-3xl text-[10px] uppercase tracking-[0.2em] shadow-2xl hover:scale-105 transition-all">
                                <i class="fas fa-lock mr-2"></i>
                                Confirmer & Payer
                                {{ $billing === 'yearly' ? '$'.number_format($plan->price_yearly, 2).'/an' : '$'.number_format($plan->price_monthly, 2).'/mois' }}
                            </button>
                        @else
                            <button type="submit"
                                    class="w-full py-5 bg-slate-800 text-white font-black rounded-3xl text-[10px] uppercase tracking-[0.2em] shadow-xl hover:scale-105 transition-all">
                                Activer le plan Starter gratuit
                            </button>
                        @endif
                        <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-4">
                            <i class="fas fa-shield-halved mr-1 text-emerald-500"></i> Paiement crypté & sécurisé
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- === ORDER SUMMARY === --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Plan Card --}}
            <div class="bg-slate-900 rounded-[3.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full
                    @if($plan->color === 'blue') bg-rdc-blue/20
                    @elseif($plan->color === 'amber') bg-amber-500/20
                    @else bg-slate-700/30
                    @endif blur-3xl"></div>

                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Récapitulatif</p>

                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-2xl mb-4">
                        <i class="fas {{ $plan->icon }}"></i>
                    </div>

                    <h3 class="text-2xl font-heading font-black text-white">{{ $plan->name }}</h3>
                    <p class="text-slate-400 font-medium text-sm mt-1">{{ $plan->description }}</p>

                    <div class="border-t border-white/10 my-6"></div>

                    <div class="space-y-3">
                        @foreach($plan->features as $feature)
                            <div class="flex items-center gap-3 text-sm">
                                <i class="fas fa-check text-emerald-400 flex-shrink-0"></i>
                                <span class="text-slate-300">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-white/10 my-6"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-bold text-sm uppercase tracking-widest">Total</span>
                        <span class="text-3xl font-black text-white">
                            @if($billing === 'yearly')
                                {{ $plan->price_yearly == 0 ? 'Gratuit' : '$'.number_format($plan->price_yearly, 2) }}
                            @else
                                {{ $plan->price_monthly == 0 ? 'Gratuit' : '$'.number_format($plan->price_monthly, 2) }}
                            @endif
                        </span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest text-right mt-1">
                        Facturation {{ $billing === 'yearly' ? 'annuelle' : 'mensuelle' }}
                    </p>
                </div>
            </div>

            {{-- Trust Badges --}}
            <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500"><i class="fas fa-shield-halved"></i></div>
                    <p class="text-xs font-bold text-slate-600">Transactions SSL cryptées 256-bit</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500"><i class="fas fa-rotate-left"></i></div>
                    <p class="text-xs font-bold text-slate-600">Remboursement sous 7 jours si insatisfaction</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500"><i class="fas fa-headset"></i></div>
                    <p class="text-xs font-bold text-slate-600">Support disponible 7j/7 pour toute question</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
