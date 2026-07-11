@extends('layouts.admin')

@section('title', 'Retirer des fonds')
@section('header', 'Retirer des fonds')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.finances.withdrawals') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Historique des retraits
        </a>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-lg"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-rdc-red p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fas fa-circle-exclamation text-lg"></i>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative">
        <div class="p-8 sm:p-10 border-b border-slate-50 flex items-center gap-4 bg-slate-50/20">
            <div class="w-12 h-12 bg-blue-50 text-rdc-blue rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                <i class="fas fa-money-bill-transfer text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">Initier un retrait K-PAY</h2>
                <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Transférez les fonds de ProConnect vers un compte Mobile Money.</p>
            </div>
        </div>

        <div class="p-8 sm:p-10 bg-slate-50/30 border-b border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-[10px] sm:text-xs text-slate-400 font-black uppercase tracking-widest">Solde Disponible K-PAY</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-500 font-mono">{{ number_format($wallet['available'] ?? 0, 2) }} {{ $wallet['currency'] ?? 'USD' }}</span>
        </div>

        <div class="p-8 sm:p-10">
            <form action="{{ route('admin.finances.withdraw.process') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="amount" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Montant à retirer (USD)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-black group-focus-within:text-rdc-blue transition-colors">$</span>
                        </div>
                        <input type="number" step="0.01" min="1" max="{{ $wallet['available'] ?? 99999 }}" name="amount" id="amount" required class="w-full pl-10 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all outline-none" placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="text-rdc-red text-[10px] font-bold uppercase tracking-tight mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="provider" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Fournisseur Mobile Money</label>
                    <select name="provider" id="provider" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all outline-none cursor-pointer appearance-none">
                        <option value="">Sélectionnez un fournisseur</option>
                        <option value="AIRTEL_COD">Airtel Money (RDC)</option>
                        <option value="ORANGE_COD">Orange Money (RDC)</option>
                        <option value="VODACOM_MPESA_COD">M-Pesa (RDC)</option>
                    </select>
                    @error('provider')
                        <p class="text-rdc-red text-[10px] font-bold uppercase tracking-tight mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="phone_number" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Numéro de téléphone</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                        </div>
                        <input type="text" name="phone_number" id="phone_number" placeholder="ex: 243xxxxxxxxx" required class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all outline-none">
                    </div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-2">Inclure le code pays sans le "+" (ex: 243 pour la RDC)</p>
                    @error('phone_number')
                        <p class="text-rdc-red text-[10px] font-bold uppercase tracking-tight mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-8">
                    <button type="submit" class="w-full bg-rdc-blue hover:bg-blue-600 active:scale-95 text-white font-black py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-3 text-xs uppercase tracking-widest">
                        <i class="fas fa-paper-plane"></i> Confirmer le retrait
                    </button>
                    <p class="text-[9px] text-center text-slate-400 font-bold uppercase tracking-widest mt-6 flex items-center justify-center gap-2">
                        <i class="fas fa-shield-halved text-emerald-500"></i> Transaction sécurisée via l'API officielle K-PAY
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
