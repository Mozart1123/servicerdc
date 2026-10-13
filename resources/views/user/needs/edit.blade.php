@extends($layout)

@section('title', 'Modifier ma demande | ProConnect')
@section('header_title', 'Modifier ma demande')
@section('header_subtitle', 'Possible tant qu\'aucun artisan ne vous a fait de proposition.')

@php
    $inputClass = 'w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm';
    $budgetAmount = $need->budget_type === 'amount' ? (float) $need->budget_max : '';
    $initial = [
        'timing'     => old('timing', $need->timing ?? 'this_week'),
        'budgetType' => old('budget_type', $need->budget_type ?? 'none'),
    ];
@endphp

@section($contentSection)
<div class="max-w-3xl mx-auto pb-10" x-data="@js($initial)">
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.needs.update', $need) }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-7 space-y-6">
        @csrf
        @method('PUT')

        <div class="text-sm text-slate-600 bg-slate-50 rounded-xl p-4">
            <i class="fas fa-layer-group text-rdc-blue mr-1"></i>
            {{ $need->category?->name }}{{ $need->serviceType ? ' · ' . $need->serviceType->title : '' }}
            · <i class="fas fa-location-dot text-rdc-blue mx-1"></i>{{ $need->commune?->name }}
            <p class="text-xs text-slate-400 mt-1">La catégorie et la commune ne peuvent pas changer : les artisans ont été choisis selon ces critères. Pour les changer, annulez et publiez une nouvelle demande.</p>
        </div>

        <label class="block text-sm font-semibold text-slate-700">Intitulé <span class="font-normal text-slate-400">(facultatif)</span>
            <input type="text" name="precision" maxlength="120" value="{{ old('precision', $need->requested_service_name) }}" class="{{ $inputClass }} mt-1">
        </label>

        <label class="block text-sm font-semibold text-slate-700">Description
            <textarea name="description" rows="5" maxlength="2000" required class="{{ $inputClass }} mt-1 resize-none">{{ old('description', $need->description) }}</textarea>
        </label>

        <label class="block text-sm font-semibold text-slate-700">Quartier <span class="font-normal text-slate-400">(facultatif)</span>
            <input type="text" name="quartier" maxlength="120" value="{{ old('quartier', $need->quartier) }}" class="{{ $inputClass }} mt-1">
        </label>

        <fieldset>
            <legend class="text-sm font-semibold text-slate-700 mb-2">Pour quand ?</legend>
            <input type="hidden" name="timing" :value="timing">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                @foreach(\App\Models\ServiceRequest::TIMINGS as $value => $label)
                    <button type="button" @click="timing = '{{ $value }}'"
                            class="p-3 rounded-xl border-2 text-xs font-bold transition-all"
                            :class="timing === '{{ $value }}' ? 'border-rdc-blue bg-rdc-blue/5 text-rdc-blue' : 'border-slate-100 text-slate-600'">{{ $label }}</button>
                @endforeach
            </div>
            <div x-show="timing === 'date'" x-cloak class="mt-3">
                <input type="date" name="preferred_date" min="{{ now()->toDateString() }}" max="{{ now()->addMonths(6)->toDateString() }}"
                       value="{{ old('preferred_date', $need->preferred_date?->toDateString()) }}" class="{{ $inputClass }} sm:w-64">
            </div>
        </fieldset>

        <fieldset>
            <legend class="text-sm font-semibold text-slate-700 mb-2">Budget (en dollars)</legend>
            <input type="hidden" name="budget_type" :value="budgetType">
            <div class="grid grid-cols-3 gap-2">
                @foreach(['amount' => 'Un montant', 'range' => 'Une fourchette', 'none' => "Pas d'idée"] as $value => $label)
                    <button type="button" @click="budgetType = '{{ $value }}'"
                            class="p-3 rounded-xl border-2 text-xs font-bold transition-all"
                            :class="budgetType === '{{ $value }}' ? 'border-rdc-blue bg-rdc-blue/5 text-rdc-blue' : 'border-slate-100 text-slate-600'">{{ $label }}</button>
                @endforeach
            </div>
            <div x-show="budgetType === 'amount'" x-cloak class="mt-3">
                <input type="number" name="budget_amount" min="1" max="1000000" step="1" value="{{ old('budget_amount', $budgetAmount) }}" placeholder="Montant ($)" class="{{ $inputClass }} sm:w-64">
            </div>
            <div x-show="budgetType === 'range'" x-cloak class="mt-3 flex gap-3">
                <input type="number" name="budget_min" min="0" max="1000000" step="1" value="{{ old('budget_min', $need->budget_type === 'range' ? (float) $need->budget_min : '') }}" placeholder="De ($)" class="{{ $inputClass }} sm:w-40">
                <input type="number" name="budget_max" min="1" max="1000000" step="1" value="{{ old('budget_max', $need->budget_type === 'range' ? (float) $need->budget_max : '') }}" placeholder="À ($)" class="{{ $inputClass }} sm:w-40">
            </div>
        </fieldset>

        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('user.service-requests.show', $need) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold">Annuler</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md">
                <i class="fas fa-save mr-1"></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
