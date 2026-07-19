@extends('layouts.public')

@section('title', 'Offres d\'emploi | ProConnect')
@section('meta_description', 'Trouvez des opportunités d\'emploi en République Démocratique du Congo.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-slate-900">Offres d'emploi</h1>
        <p class="text-slate-500 mt-2">Trouvez votre prochain poste parmi nos offres actives.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('public.jobs.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, entreprise..."
                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#16a3b0]/20 focus:border-[#16a3b0] outline-none transition-all">
            </div>
            <div>
                <select name="contract_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#16a3b0]/20 focus:border-[#16a3b0] outline-none transition-all">
                    <option value="">Type de contrat</option>
                    @foreach($contractTypes as $type)
                        <option value="{{ $type }}" {{ request('contract_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <input type="text" name="location" value="{{ request('location') }}" placeholder="Ville..."
                       class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#16a3b0]/20 focus:border-[#16a3b0] outline-none transition-all">
                <button type="submit" class="px-5 py-3 bg-[#16a3b0] text-white font-bold rounded-xl text-sm hover:bg-[#128a96] transition-all shadow-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        @if(request()->hasAny(['search', 'contract_type', 'location']))
            <div class="mt-3 flex items-center gap-3">
                <a href="{{ route('public.jobs.index') }}" class="text-xs text-slate-400 hover:text-slate-600 font-medium transition-colors">
                    <i class="fas fa-times mr-1"></i>Réinitialiser les filtres
                </a>
            </div>
        @endif
    </form>

    {{-- Results Count --}}
    <p class="text-sm text-slate-400 font-medium mb-6">
        <span class="text-slate-700 font-bold">{{ $jobs->total() }}</span> offre(s) trouvée(s)
    </p>

    {{-- Jobs Grid --}}
    @if($jobs->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-briefcase text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-500">Aucune offre trouvée</h3>
            <p class="text-sm text-slate-400 mt-1">Modifiez vos critères de recherche.</p>
            <a href="{{ route('public.jobs.index') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-[#16a3b0] text-white text-sm font-bold rounded-xl hover:bg-[#128a96] transition-all">
                Voir toutes les offres
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($jobs as $job)
                <x-job-card :job="$job" show-route="public.jobs.show" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $jobs->links() }}
        </div>
    @endif

</div>
@endsection
