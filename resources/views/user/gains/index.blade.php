@extends('layouts.user')

@section('header_title', 'Mes Gains')

@section('content')
<div class="space-y-10 pb-20 max-w-5xl mx-auto">

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HEADER                                                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="relative">
        <div class="absolute inset-0 bg-emerald-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-inner">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase">Mes Gains</h2>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Solde, historique et demandes de retrait</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ALERTES SESSION                                            --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-6 py-4">
            <i class="fas fa-check-circle text-lg"></i>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- CARTES DE SOLDE                                            --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Solde disponible --}}
        <div class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm flex flex-col gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Solde disponible</span>
            <div class="text-3xl font-black text-emerald-600">
                {{ number_format($availableBalance, 2) }} <span class="text-base font-bold text-slate-400">$</span>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">{{ $missionsPendingCount }} mission(s) en attente de paiement</p>
        </div>

        {{-- Total déjà payé --}}
        <div class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm flex flex-col gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total reçu</span>
            <div class="text-3xl font-black text-slate-700">
                {{ number_format($totalEarned, 2) }} <span class="text-base font-bold text-slate-400">$</span>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Missions entièrement payées</p>
        </div>

        {{-- Statut demande en cours --}}
        <div class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm flex flex-col gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Demande en cours</span>
            @if($pendingRequest)
                <div class="text-2xl font-black text-amber-500">{{ number_format((float)$pendingRequest->amount, 2) }} $</div>
                <p class="text-[10px] text-amber-500 font-bold uppercase tracking-wide">En attente de traitement</p>
            @else
                <div class="text-2xl font-black text-slate-300">—</div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Aucune demande active</p>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- FORMULAIRE DE DEMANDE DE RETRAIT                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="relative">
        <div class="absolute inset-0 bg-rdc-blue/5 rounded-[3rem] blur-3xl opacity-40"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[2.5rem] shadow-sm">
            <h3 class="text-base font-black text-slate-900 uppercase tracking-wide mb-1">
                <i class="fas fa-paper-plane text-rdc-blue mr-2"></i>Demander un retrait
            </h3>
            <p class="text-xs text-slate-400 font-bold mb-6 uppercase tracking-widest">
                Solde disponible : <span class="text-emerald-600">{{ number_format($availableBalance, 2) }} $</span>
            </p>

            @if($pendingRequest)
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl px-6 py-4">
                    <i class="fas fa-clock text-lg"></i>
                    <div>
                        <p class="text-sm font-black">Une demande est déjà en cours</p>
                        <p class="text-xs mt-1">Votre demande de <strong>{{ number_format((float)$pendingRequest->amount, 2) }} $</strong> est en attente de traitement. Vous pourrez soumettre une nouvelle demande une fois celle-ci traitée.</p>
                    </div>
                </div>

            @elseif($availableBalance <= 0)
                <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 text-slate-500 rounded-2xl px-6 py-4">
                    <i class="fas fa-info-circle text-lg"></i>
                    <p class="text-sm font-bold">Votre solde disponible est de 0 $. Complétez des missions pour générer des gains.</p>
                </div>

            @else
                <form method="POST" action="{{ route('user.gains.request') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">
                                Montant à retirer (max {{ number_format($availableBalance, 2) }} $) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                step="0.01"
                                min="1"
                                max="{{ $availableBalance }}"
                                value="{{ old('amount') }}"
                                required
                                placeholder="Ex: 50.00"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"
                            >
                            @error('amount')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">
                                Numéro Mobile Money <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="mobile_money_number"
                                id="mobile_money_number"
                                value="{{ old('mobile_money_number') }}"
                                required
                                placeholder="Ex: +243 81 000 0000"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"
                            >
                            @error('mobile_money_number')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">
                            <i class="fas fa-shield-alt text-emerald-500 mr-1"></i>
                            Le montant demandé ne peut pas dépasser votre solde disponible
                        </p>
                        <button
                            type="submit"
                            class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-95"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Soumettre la demande
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MISSIONS EN ATTENTE DE PAIEMENT                            --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($pendingMissions->isNotEmpty())
    <div class="bg-white border border-slate-100 p-6 rounded-[2rem] shadow-sm">
        <h3 class="text-base font-black text-slate-900 uppercase tracking-wide mb-5">
            <i class="fas fa-clock text-amber-500 mr-2"></i>Missions en attente de paiement
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Mission</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Montant brut</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Commission</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Vous recevez</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($pendingMissions as $mission)
                    <tr>
                        <td class="py-3 font-bold text-slate-800">{{ $mission->title ?? 'Mission #' . $mission->id }}</td>
                        <td class="py-3 font-bold text-slate-600">{{ number_format((float)$mission->amount, 2) }} $</td>
                        <td class="py-3 text-red-500 font-bold">— {{ number_format((float)$mission->commission_amount, 2) }} $</td>
                        <td class="py-3 font-black text-emerald-600">{{ number_format((float)$mission->amount - (float)$mission->commission_amount, 2) }} $</td>
                        <td class="py-3 text-slate-400 text-xs">{{ $mission->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200">
                        <td colspan="3" class="pt-3 text-xs font-black text-slate-400 uppercase tracking-wide">Total disponible</td>
                        <td class="pt-3 font-black text-emerald-600 text-base">{{ number_format($availableBalance, 2) }} $</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HISTORIQUE DES DEMANDES                                    --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-slate-100 p-6 rounded-[2rem] shadow-sm">
        <h3 class="text-base font-black text-slate-900 uppercase tracking-wide mb-5">
            <i class="fas fa-history text-slate-400 mr-2"></i>Historique des demandes de retrait
        </h3>

        @if($payoutRequests->isEmpty())
            <div class="text-center py-12">
                <div class="text-4xl text-slate-200 mb-3"><i class="fas fa-file-invoice-dollar"></i></div>
                <p class="text-slate-400 font-bold text-sm">Aucune demande de retrait pour le moment.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-100">
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">#</th>
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Montant</th>
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Numéro</th>
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Statut</th>
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Date demande</th>
                            <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-3">Traitement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($payoutRequests as $req)
                        <tr>
                            <td class="py-3 text-slate-400 text-xs font-bold">#{{ $req->id }}</td>
                            <td class="py-3 font-black text-slate-800">{{ number_format((float)$req->amount, 2) }} $</td>
                            <td class="py-3 text-slate-600 font-bold text-xs">{{ $req->mobile_money_number }}</td>
                            <td class="py-3">
                                @php
                                    $colors = ['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'red'];
                                    $color = $colors[$req->status] ?? 'slate';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide bg-{{ $color }}-100 text-{{ $color }}-700">
                                    @if($req->status === 'pending') <i class="fas fa-clock"></i>
                                    @elseif($req->status === 'approved') <i class="fas fa-check"></i>
                                    @else <i class="fas fa-times"></i>
                                    @endif
                                    {{ $req->status_label }}
                                </span>
                                @if($req->rejection_reason)
                                    <p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $req->rejection_reason }}</p>
                                @endif
                            </td>
                            <td class="py-3 text-slate-400 text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-slate-400 text-xs">
                                {{ $req->processed_at ? $req->processed_at->format('d/m/Y H:i') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
