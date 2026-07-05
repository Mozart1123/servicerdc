@extends('layouts.admin')

@section('title', 'Tableau de bord financier')
@section('header', 'Tableau de bord financier')

@section('content')
<div class="space-y-6">

    <!-- 1. K-PAY WALLET -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-wallet text-rdc-blue"></i> Portefeuille K-PAY
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Solde en direct depuis l'API officielle</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        Connecté
                    </span>
                </div>
            </div>

            @if(isset($wallet['error']))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
                        <div>
                            <h4 class="font-semibold">Connexion au portefeuille K-PAY impossible</h4>
                            <p class="text-sm opacity-90 mt-1">{{ $wallet['error'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-red-500/20 text-sm space-y-1 text-red-300">
                        <p class="font-medium">Points de vérification :</p>
                        <ul class="list-disc list-inside space-y-1 ml-1">
                            <li>Vérifiez que <code class="bg-red-900/40 px-1 rounded">KPAY_API_KEY</code> et <code class="bg-red-900/40 px-1 rounded">KPAY_SECRET_KEY</code> dans votre <code class="bg-red-900/40 px-1 rounded">.env</code> sont correctes.</li>
                            <li>Récupérez vos clés depuis votre tableau de bord sur <a href="https://admin.kpay.site" target="_blank" class="underline">admin.kpay.site</a>.</li>
                            <li>Après toute modification du <code class="bg-red-900/40 px-1 rounded">.env</code>, exécutez <code class="bg-red-900/40 px-1 rounded">php artisan config:clear</code>.</li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($wallet['wallets'] ?? [] as $w)
                    <div class="bg-gray-800/50 rounded-lg p-5 border border-gray-700/50 backdrop-blur-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-rdc-blue bg-rdc-blue/10 px-2 py-1 rounded">{{ $w['currency'] }}</span>
                            @if($w['reservedBalance'] > 0)
                                <span class="text-xs text-yellow-400"><i class="fa-solid fa-lock mr-1"></i>{{ number_format($w['reservedBalance']) }} réservé</span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-xs mb-1">Solde Total</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($w['balance']) }}</p>
                        <div class="mt-3 pt-3 border-t border-gray-700/50 flex justify-between items-center">
                            <span class="text-xs text-gray-500">Disponible</span>
                            <span class="text-sm font-semibold text-green-400">{{ number_format($w['availableBalance']) }}</span>
                        </div>
                    </div>
                    @empty
                        <div class="col-span-3 text-center text-gray-400 py-4">Aucun portefeuille trouvé.</div>
                    @endforelse
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.finances.withdraw') }}" class="inline-flex items-center gap-2 bg-rdc-blue hover:bg-rdc-blue-dark text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                        <i class="fa-solid fa-money-bill-transfer"></i> Initier un retrait
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. REVENUE STATISTICS -->
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-8 mb-4">Statistiques des Revenus</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Aujourd'hui -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenus Aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['revenue_today'], 2) }} $</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fa-solid fa-calendar-day text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Cette semaine -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenus Semaine</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['revenue_week'], 2) }} $</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="fa-solid fa-calendar-week text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ce mois -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenus Mois</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['revenue_month'], 2) }} $</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="fa-solid fa-calendar-days text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenus Globaux</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_revenue'], 2) }} $</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                    <i class="fa-solid fa-vault text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SUBSCRIPTIONS & TRANSACTIONS OVERVIEW -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Subscriptions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Abonnements Premium</h3>
            <div class="flex gap-4">
                <div class="flex-1 bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-100 dark:border-green-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Actifs</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['active_subs'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 bg-red-50 dark:bg-red-900/10 rounded-lg p-4 border border-red-100 dark:border-red-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-red-600 dark:text-red-400">
                            <i class="fa-solid fa-times-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expirés</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['expired_subs'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">État des Transactions</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-gray-600 dark:text-gray-300">Réussies</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stats['success_payments'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                        <span class="text-gray-600 dark:text-gray-300">En attente</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stats['pending_payments'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-gray-600 dark:text-gray-300">Échouées</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stats['failed_payments'] }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
