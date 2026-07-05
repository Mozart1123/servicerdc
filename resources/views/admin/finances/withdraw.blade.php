@extends('layouts.admin')

@section('title', 'Retirer des fonds')
@section('header', 'Retirer des fonds')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.finances.withdrawals') }}" class="text-sm text-gray-500 hover:text-rdc-blue flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Historique des retraits
        </a>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-check-circle"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-rdc-blue"></i> Initier un retrait K-PAY
            </h2>
            <p class="text-sm text-gray-500 mt-1">Transférez les fonds de ProConnect vers un compte Mobile Money.</p>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">Solde Disponible K-PAY</span>
                <span class="text-2xl font-bold text-green-500">{{ number_format($wallet['available'] ?? 0, 2) }} {{ $wallet['currency'] ?? 'USD' }}</span>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.finances.withdraw.process') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant à retirer (USD)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                        <input type="number" step="0.01" min="1" max="{{ $wallet['available'] ?? 99999 }}" name="amount" id="amount" required class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue">
                    </div>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fournisseur Mobile Money</label>
                    <select name="provider" id="provider" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue">
                        <option value="">Sélectionnez un fournisseur</option>
                        <option value="AIRTEL_COD">Airtel Money (RDC)</option>
                        <option value="ORANGE_COD">Orange Money (RDC)</option>
                        <option value="VODACOM_MPESA_COD">M-Pesa (RDC)</option>
                        <!-- Add other countries if needed -->
                    </select>
                    @error('provider')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de téléphone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-gray-400"></i>
                        </div>
                        <input type="text" name="phone_number" id="phone_number" placeholder="ex: 243xxxxxxxxx" required class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Inclure le code pays sans le "+" (ex: 243 pour la RDC)</p>
                    @error('phone_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-rdc-blue hover:bg-rdc-blue-dark text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Confirmer le retrait
                    </button>
                    <p class="text-xs text-center text-gray-500 mt-3">
                        <i class="fa-solid fa-shield-halved"></i> Transaction sécurisée via l'API officielle K-PAY
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
