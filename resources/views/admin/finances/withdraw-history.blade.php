@extends('layouts.admin')

@section('title', 'Historique des retraits')
@section('header', 'Historique des retraits')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-rdc-blue"></i> Historique des Retraits
        </h2>
        <a href="{{ route('admin.finances.withdraw') }}" class="bg-rdc-blue hover:bg-rdc-blue-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Nouveau retrait
        </a>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-check-circle"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Référence</th>
                        <th scope="col" class="px-6 py-3">Montant</th>
                        <th scope="col" class="px-6 py-3">Fournisseur & Numéro</th>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $withdrawal->reference_id }}
                                @if($withdrawal->kpay_reference)
                                    <div class="text-xs text-gray-400 mt-1" title="K-PAY Reference">K-PAY: {{ $withdrawal->kpay_reference }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ number_format($withdrawal->amount, 2) }} $
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $withdrawal->provider }}</div>
                                <div class="text-xs text-gray-500">{{ $withdrawal->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($withdrawal->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                        <i class="fa-solid fa-check"></i> Complété
                                    </span>
                                @elseif($withdrawal->status === 'pending' || $withdrawal->status === 'processing')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800">
                                        <i class="fa-solid fa-spinner fa-spin"></i> En attente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                        <i class="fa-solid fa-xmark"></i> Échoué
                                    </span>
                                    @if($withdrawal->notes)
                                        <div class="text-xs text-red-500 mt-1 truncate max-w-xs" title="{{ $withdrawal->notes }}">
                                            {{ $withdrawal->notes }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-money-bill-transfer text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <p>Aucun retrait n'a été effectué pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
