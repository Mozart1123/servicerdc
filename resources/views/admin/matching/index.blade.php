@extends('layouts.admin')

@section('title', 'Matching artisans')
@section('header_title', 'Matching artisans')
@section('page_title', 'Matching')
@section('page_subtitle', 'Pondérations du score de pertinence et aperçu des artisans retenus pour une demande publiée.')

@section('content')
<div class="space-y-8 pb-20">

    @if(session('success'))
        <div class="px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Pondérations --}}
    <form method="POST" action="{{ route('admin.matching.update') }}"
          class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm"
          x-data="{ w: @js(collect(old('weights', $weights))->map(fn ($v) => (int) $v)) , get total() { return Object.values(this.w).reduce((a, b) => a + (parseInt(b) || 0), 0); } }">
        @csrf
        @method('PUT')
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Score de pertinence (sur 100)</h2>
                <p class="text-sm text-slate-500">Points maximum attribués à chaque critère. Le total doit faire 100.</p>
            </div>
            <span class="px-3 py-1.5 rounded-full text-sm font-black"
                  :class="total === 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'">
                Total : <span x-text="total"></span> / 100
            </span>
        </div>

        @error('weights') <p class="mb-4 text-sm text-red-600 font-semibold">{{ $message }}</p> @enderror
        @foreach($errors->get('weights.*') as $messages)
            <p class="mb-2 text-sm text-red-600 font-semibold">{{ $messages[0] }}</p>
        @endforeach

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($labels as $key => $label)
                <label class="block p-4 rounded-xl border border-slate-100 bg-slate-50">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ $label }}</span>
                    <span class="flex items-center gap-2">
                        <input type="number" name="weights[{{ $key }}]" min="0" max="100" step="1" required
                               x-model.number="w.{{ $key }}"
                               class="w-24 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold focus:ring-2 focus:ring-rdc-blue outline-none">
                        <span class="text-xs text-slate-400">pts · défaut {{ $defaults[$key] }}</span>
                    </span>
                </label>
            @endforeach
        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit" :disabled="total !== 100"
                    class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-save mr-1"></i> Enregistrer
            </button>
        </div>
    </form>

    {{-- Aperçu --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Aperçu du classement</h2>
        <p class="text-sm text-slate-500 mb-4">Choisissez une demande publiée pour voir les artisans éligibles et le détail de leur score.</p>

        <form method="GET" action="{{ route('admin.matching.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <select name="need" class="flex-1 px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-rdc-blue outline-none">
                <option value="">— Choisir une demande —</option>
                @foreach($openNeeds as $need)
                    <option value="{{ $need->id }}" @selected($selected?->id === $need->id)>
                        #{{ $need->id }} · {{ $need->requested_service_name }} · {{ $need->category?->name }} · {{ $need->commune?->name }} · {{ $need->status_label }} · {{ $need->created_at->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold">Afficher</button>
        </form>

        @if($openNeeds->isEmpty())
            <p class="text-sm text-slate-400">Aucune demande publiée pour le moment.</p>
        @endif

        @if($selected)
            <div class="mb-4 text-sm text-slate-600 bg-slate-50 rounded-xl p-4">
                <strong>#{{ $selected->id }} {{ $selected->requested_service_name }}</strong> —
                {{ $selected->category?->name }} · {{ $selected->location_label }} · {{ $selected->timing_label }} · Budget {{ $selected->budget_range }}
            </div>

            @if($ranking->isEmpty())
                <p class="text-sm text-slate-500">Aucun artisan éligible : personne n'a de service actif dans cette catégorie, n'est disponible, ou tous ont déjà {{ config('matching.max_active_jobs') }} missions en cours.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="py-2 pr-3">#</th>
                                <th class="py-2 pr-3">Artisan</th>
                                <th class="py-2 pr-3">Score</th>
                                @foreach($labels as $key => $label)
                                    <th class="py-2 pr-3 whitespace-nowrap">{{ $label }}<br><span class="normal-case font-normal">/ {{ $weights[$key] }}</span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranking as $i => $candidate)
                                <tr class="border-b border-slate-50 {{ $i < 5 ? '' : 'text-slate-400' }}">
                                    <td class="py-2 pr-3 font-bold">{{ $i + 1 }}</td>
                                    <td class="py-2 pr-3">
                                        <span class="font-semibold">{{ $candidate->artisan->name }}</span>
                                        <span class="block text-[11px] text-slate-400">
                                            {{ ['same' => 'Même commune', 'neighbor' => 'Commune voisine', 'wide' => 'Zone élargie'][$candidate->proximity] }}
                                            · note ajustée {{ number_format($candidate->adjustedRating, 2, ',', '') }} ({{ $candidate->reviewCount }} avis)
                                            · {{ $candidate->completedMissions }} mission(s)
                                            @if($candidate->usualPrice) · prix habituel ${{ number_format($candidate->usualPrice, 0) }} @endif
                                        </span>
                                    </td>
                                    <td class="py-2 pr-3 font-black text-rdc-blue">{{ number_format($candidate->score, 1, ',', '') }}</td>
                                    @foreach(array_keys($labels) as $key)
                                        <td class="py-2 pr-3">{{ number_format($candidate->breakdown[$key], 1, ',', '') }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-slate-400">Les 5 premiers recevraient la première vague. Sans donnée (aucun avis, aucune demande reçue, aucune mission), un critère reçoit la moitié de ses points ; sans budget client ou sans prix connu, le budget compte 10/15.</p>
            @endif
        @endif
    </div>
</div>
@endsection
