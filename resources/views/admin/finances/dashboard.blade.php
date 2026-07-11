@extends('layouts.admin')

@section('title', 'Tableau de bord financier')
@section('header', 'Tableau de bord financier')

@section('content')
<div class="space-y-8 pb-20">

    <!-- 1. K-PAY WALLET -->
    <div class="bg-slate-900 rounded-2xl shadow-sm border border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight">Portefeuille K-PAY</h2>
                    <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Solde en direct depuis l'API officielle</p>
                </div>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center gap-2 py-2 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Connecté
                </span>
            </div>
        </div>

        <div class="p-6">
            @if(isset($wallet['error']))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-6 rounded-2xl">
                    <div class="flex items-start gap-4">
                        <i class="fas fa-circle-exclamation text-xl mt-1"></i>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-widest">Connexion au portefeuille K-PAY impossible</h4>
                            <p class="text-xs opacity-90 mt-2 font-medium">{{ $wallet['error'] }}</p>
                            
                            <div class="mt-6 pt-6 border-t border-red-500/20 text-[10px] space-y-2 text-red-300 font-bold uppercase tracking-widest">
                                <p>Points de vérification :</p>
                                <ul class="list-disc list-inside space-y-2 ml-2">
                                    <li>Vérifiez que <code class="bg-red-900/40 px-2 py-0.5 rounded-md">KPAY_API_KEY</code> et <code class="bg-red-900/40 px-2 py-0.5 rounded-md">KPAY_SECRET_KEY</code> dans votre <code class="bg-red-900/40 px-2 py-0.5 rounded-md">.env</code> sont correctes.</li>
                                    <li>Récupérez vos clés sur <a href="https://admin.kpay.site" target="_blank" class="underline hover:text-white transition-colors">admin.kpay.site</a>.</li>
                                    <li>Exécutez <code class="bg-red-900/40 px-2 py-0.5 rounded-md">php artisan config:clear</code> après modif.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($wallet['wallets'] ?? [] as $w)
                    <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50 backdrop-blur-sm group hover:border-emerald-500/30 transition-colors">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-400/10 px-3 py-1.5 rounded-xl">{{ $w['currency'] }}</span>
                            @if($w['reservedBalance'] > 0)
                                <span class="text-[9px] font-bold text-amber-400 bg-amber-400/10 px-2 py-1 rounded-lg uppercase tracking-widest flex items-center gap-1"><i class="fas fa-lock"></i> {{ number_format($w['reservedBalance']) }} réservé</span>
                            @endif
                        </div>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Solde Total</p>
                        <p class="text-3xl sm:text-4xl font-black text-white font-mono">{{ number_format($w['balance']) }}</p>
                        <div class="mt-6 pt-6 border-t border-slate-700/50 flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Disponible</span>
                            <span class="text-sm font-black text-emerald-400 font-mono">{{ number_format($w['availableBalance']) }}</span>
                        </div>
                    </div>
                    @empty
                        <div class="col-span-3 text-center text-slate-500 py-10 font-black text-xs uppercase tracking-widest">Aucun portefeuille trouvé.</div>
                    @endforelse
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.finances.withdraw') }}" class="inline-flex items-center gap-3 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white px-6 py-3 rounded-xl font-black transition-all text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-money-bill-transfer text-sm"></i> Initier un retrait K-PAY
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. REVENUE STATISTICS -->
    <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight ml-2 mt-12 mb-6">Statistiques des Revenus</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Aujourd'hui -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-rdc-blue group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-day text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aujourd'hui</p>
            <p class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($stats['revenue_today'], 2) }}$</p>
        </div>
        
        <!-- Cette semaine -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-week text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cette Semaine</p>
            <p class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($stats['revenue_week'], 2) }}$</p>
        </div>

        <!-- Ce mois -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-days text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ce Mois</p>
            <p class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($stats['revenue_month'], 2) }}$</p>
        </div>

        <!-- Total -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-vault text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Global</p>
            <p class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($stats['total_revenue'], 2) }}$</p>
        </div>
    </div>

    <!-- 3. SUBSCRIPTIONS & TRANSACTIONS OVERVIEW -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Subscriptions -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Abonnements Premium</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 bg-emerald-50 rounded-2xl p-6 border border-emerald-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-emerald-600/70 uppercase tracking-widest">Actifs</p>
                        <p class="text-2xl font-black text-emerald-600">{{ $stats['active_subs'] }}</p>
                    </div>
                </div>
                
                <div class="flex-1 bg-red-50 rounded-2xl p-6 border border-red-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-rdc-red shrink-0">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-rdc-red/70 uppercase tracking-widest">Expirés</p>
                        <p class="text-2xl font-black text-rdc-red">{{ $stats['expired_subs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Status -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">État des Transactions</h3>
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-check"></i></div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Réussies</span>
                    </div>
                    <span class="text-lg font-black text-slate-900">{{ $stats['success_payments'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center"><i class="fas fa-clock"></i></div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">En attente</span>
                    </div>
                    <span class="text-lg font-black text-slate-900">{{ $stats['pending_payments'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-50 text-rdc-red rounded-xl flex items-center justify-center"><i class="fas fa-xmark"></i></div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Échouées</span>
                    </div>
                    <span class="text-lg font-black text-slate-900">{{ $stats['failed_payments'] }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
