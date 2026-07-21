@extends('layouts.admin')

@section('title', 'Demandes de Retrait Artisans')
@section('header', 'Demandes de Retrait')

@section('content')
<div class="space-y-8" x-data="payoutAdmin()">

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- ALERTES SESSION                                         --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            <i class="fa-solid fa-check-circle text-lg"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
            <i class="fa-solid fa-exclamation-circle text-lg"></i>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- DEMANDES EN ATTENTE                                     --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                Demandes en attente
                @if($pendingRequests->isNotEmpty())
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs font-black">
                        {{ $pendingRequests->count() }}
                    </span>
                @endif
            </h2>
        </div>

        @if($pendingRequests->isEmpty())
            <div class="text-center py-14">
                <i class="fa-solid fa-circle-check text-4xl text-emerald-300 mb-3 block"></i>
                <p class="text-gray-400 font-semibold text-sm">Aucune demande en attente. Tout est traité !</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Artisan</th>
                            <th class="px-6 py-3">Montant demandé</th>
                            <th class="px-6 py-3">Solde dispo artisan</th>
                            <th class="px-6 py-3">Numéro Mobile Money</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Missions</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-amber-50/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">#{{ $req->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $req->artisan?->name }}</div>
                                <div class="text-xs text-gray-400">{{ $req->artisan?->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-base text-gray-900">{{ number_format((float)$req->amount, 2) }} $</span>
                            </td>
                            <td class="px-6 py-4">
                                @php $diff = $req->artisan_available_balance - (float)$req->amount; @endphp
                                <span class="font-bold {{ $req->artisan_available_balance >= (float)$req->amount ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ number_format($req->artisan_available_balance, 2) }} $
                                </span>
                                @if($req->artisan_available_balance < (float)$req->amount)
                                    <span class="block text-[10px] text-red-400 font-bold mt-0.5">⚠ Solde insuffisant</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-sm font-bold text-gray-700">{{ $req->mobile_money_number }}</td>
                            <td class="px-6 py-4 text-xs text-gray-400">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.finances.payout-requests.missions', $req->artisan_id) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-rdc-blue hover:underline">
                                    <i class="fa-solid fa-list-check"></i>
                                    {{ $req->pending_missions_count }} mission(s)
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Bouton Approuver --}}
                                    <button
                                        type="button"
                                        @click="openApprove({{ $req->id }}, '{{ $req->artisan?->name }}', '{{ number_format((float)$req->amount, 2) }}', '{{ $req->mobile_money_number }}')"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors"
                                    >
                                        <i class="fa-solid fa-check"></i> Approuver
                                    </button>

                                    {{-- Bouton Rejeter --}}
                                    <button
                                        type="button"
                                        @click="openReject({{ $req->id }}, '{{ $req->artisan?->name }}')"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold rounded-lg transition-colors"
                                    >
                                        <i class="fa-solid fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- HISTORIQUE TRAITÉ                                       --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-history text-gray-400"></i> Demandes traitées (30 dernières)
            </h2>
        </div>

        @if($processedRequests->isEmpty())
            <p class="text-center text-gray-400 font-semibold text-sm py-10">Aucun historique.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Artisan</th>
                            <th class="px-6 py-3">Montant</th>
                            <th class="px-6 py-3">Numéro</th>
                            <th class="px-6 py-3">Statut</th>
                            <th class="px-6 py-3">Traité par</th>
                            <th class="px-6 py-3">Date traitement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedRequests as $req)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-3 font-bold">#{{ $req->id }}</td>
                            <td class="px-6 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $req->artisan?->name }}</div>
                            </td>
                            <td class="px-6 py-3 font-bold text-gray-900">{{ number_format((float)$req->amount, 2) }} $</td>
                            <td class="px-6 py-3 font-mono text-xs">{{ $req->mobile_money_number }}</td>
                            <td class="px-6 py-3">
                                @if($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-check"></i> Approuvée
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fa-solid fa-times"></i> Rejetée
                                    </span>
                                    @if($req->rejection_reason)
                                        <p class="text-[10px] text-red-400 mt-1">{{ Str::limit($req->rejection_reason, 60) }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $req->processor?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-xs text-gray-400">
                                {{ $req->processed_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODALE APPROBATION (avec avertissement virement manuel) --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div
        x-show="showApproveModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-8 space-y-6" @click.outside="showApproveModal = false">

            {{-- Icône avertissement --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-2xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Confirmer l'approbation</h3>
                    <p class="text-sm text-gray-500" x-text="'Demande #' + approveId + ' — ' + approveName"></p>
                </div>
            </div>

            {{-- Rappel critique virement manuel --}}
            <div class="bg-amber-50 border-l-4 border-amber-400 rounded-xl p-4">
                <p class="text-sm font-black text-amber-800 mb-1">⚠️ Action irréversible — Lisez avant de confirmer</p>
                <p class="text-sm text-amber-700">
                    Ce bouton <strong>ne déclenche aucun virement automatique</strong>. Il marque uniquement la demande comme traitée dans le système.
                </p>
                <p class="text-sm text-amber-700 mt-2">
                    Vous devez avoir effectué le virement de <strong x-text="approveAmount + ' $'"></strong> sur le numéro <strong x-text="approvePhone"></strong> via votre accès K-PAY ou opérateur <strong>avant</strong> de cliquer sur Confirmer.
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-sm text-gray-600 dark:text-gray-300">
                <p>✓ La demande sera marquée <strong>Approuvée</strong></p>
                <p>✓ Toutes les missions de l'artisan passeront de <code class="bg-amber-100 text-amber-700 px-1 rounded text-xs">pending_payout</code> à <code class="bg-emerald-100 text-emerald-700 px-1 rounded text-xs">paid_out</code></p>
                <p>✓ L'artisan recevra une notification</p>
            </div>

            <div class="flex gap-3 justify-end">
                <button @click="showApproveModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors">
                    Annuler
                </button>
                <form :action="'/admin/finances/payout-requests/' + approveId + '/approve'" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black rounded-xl transition-colors shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-check mr-1"></i> J'ai viré les fonds — Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODALE REJET                                            --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div
        x-show="showRejectModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-8 space-y-5" @click.outside="showRejectModal = false">

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-2xl shrink-0">
                    <i class="fa-solid fa-times-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Rejeter la demande</h3>
                    <p class="text-sm text-gray-500" x-text="'Demande #' + rejectId + ' — ' + rejectName"></p>
                </div>
            </div>

            <form :action="'/admin/finances/payout-requests/' + rejectId + '/reject'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">
                        Motif du rejet <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="rejection_reason"
                        required
                        minlength="10"
                        rows="4"
                        placeholder="Expliquez clairement la raison du rejet (minimum 10 caractères)..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-red-300 outline-none resize-none"
                    ></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">L'artisan sera notifié avec ce motif. Les missions restent en attente de paiement.</p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-xl transition-colors">
                        <i class="fa-solid fa-times mr-1"></i> Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function payoutAdmin() {
    return {
        showApproveModal: false,
        showRejectModal:  false,
        approveId:    null,
        approveName:  '',
        approveAmount: '',
        approvePhone: '',
        rejectId:     null,
        rejectName:   '',

        openApprove(id, name, amount, phone) {
            this.approveId     = id;
            this.approveName   = name;
            this.approveAmount = amount;
            this.approvePhone  = phone;
            this.showApproveModal = true;
        },
        openReject(id, name) {
            this.rejectId   = id;
            this.rejectName = name;
            this.showRejectModal = true;
        },
    };
}
</script>
@endsection
