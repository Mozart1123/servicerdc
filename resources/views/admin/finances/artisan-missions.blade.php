@extends('layouts.admin')

@section('title', 'Missions en attente — ' . $artisan->name)
@section('header', 'Missions en attente de paiement')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fa-solid fa-list-check text-rdc-blue mr-2"></i>
                Missions de {{ $artisan->name }}
            </h2>
            <p class="text-sm text-gray-400 mt-1">Missions en <code class="bg-amber-100 text-amber-700 px-1 rounded">pending_payout</code> — en attente de versement artisan</p>
        </div>
        <a href="{{ route('admin.finances.payout-requests.index') }}" class="text-sm font-bold text-rdc-blue hover:underline flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Retour aux demandes
        </a>
    </div>

    {{-- Résumé --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Nombre de missions</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $missions->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-1">Montant net dû (amount − commission)</p>
            <p class="text-3xl font-black text-emerald-600">{{ number_format($netBalance, 2) }} $</p>
        </div>
    </div>

    {{-- Table des missions --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Mission</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Montant brut</th>
                        <th class="px-6 py-3">Commission ProConnect</th>
                        <th class="px-6 py-3 text-emerald-700">Net artisan</th>
                        <th class="px-6 py-3">Complétée le</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($missions as $mission)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $mission->title ?? 'Mission #' . $mission->id }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $mission->client?->name ?? '—' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ number_format((float)$mission->amount, 2) }} $</td>
                        <td class="px-6 py-4 text-red-500 font-bold">— {{ number_format((float)$mission->commission_amount, 2) }} $</td>
                        <td class="px-6 py-4 font-black text-emerald-600">
                            {{ number_format((float)$mission->amount - (float)$mission->commission_amount, 2) }} $
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">{{ $mission->updated_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">Aucune mission en attente.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-black">
                        <td colspan="4" class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wide">Total net dû à l'artisan</td>
                        <td class="px-6 py-4 text-emerald-600 text-base">{{ number_format($netBalance, 2) }} $</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
