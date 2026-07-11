@extends('layouts.public')

@section('title', 'Offres d\'emploi')
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
                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
            </div>
            <div>
                <select name="contract_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
                    <option value="">Type de contrat</option>
                    @foreach($contractTypes as $type)
                        <option value="{{ $type }}" {{ request('contract_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <input type="text" name="location" value="{{ request('location') }}" placeholder="Ville..."
                       class="flex-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1]/20 focus:border-[#29B6D1] outline-none transition-all">
                <button type="submit" class="px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-xl text-sm hover:bg-[#1E9CB5] transition-all shadow-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Results Count --}}
    <p class="text-sm text-slate-400 font-medium mb-6">{{ $jobs->total() }} offre(s) trouvée(s)</p>

    {{-- Jobs List --}}
    @if($jobs->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-briefcase text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-500">Aucune offre trouvée</h3>
            <p class="text-sm text-slate-400 mt-1">Modifiez vos critères de recherche.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($jobs as $job)
                <a href="{{ route('public.jobs.show', $job->id) }}"
                   class="flex items-center gap-5 bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all group">

                    {{-- Company Logo --}}
                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center">
                        @if($job->company_logo)
                            <img src="{{ Storage::url($job->company_logo) }}" alt="{{ $job->company_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#29B6D1]/20 to-[#29B6D1]/5 flex items-center justify-center">
                                <i class="fas fa-building text-xl text-[#29B6D1]/50"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Job Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm group-hover:text-[#29B6D1] transition-colors">{{ $job->title }}</h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $job->company_name }}</p>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-black bg-[#29B6D1]/10 text-[#29B6D1] rounded-full uppercase tracking-wide flex-shrink-0">
                                {{ $job->contract_type }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <span class="text-xs text-slate-400 font-medium">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $job->location }}
                            </span>
                            @if($job->salary_range)
                                <span class="text-xs text-slate-400 font-medium">
                                    <i class="fas fa-dollar-sign mr-1"></i>{{ $job->salary_range }}
                                </span>
                            @endif
                            @if($job->deadline)
                                <span class="text-xs text-slate-400 font-medium">
                                    <i class="fas fa-clock mr-1"></i>Avant le {{ $job->deadline->format('d/m/Y') }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-300 font-medium">
                                Publié {{ $job->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <i class="fas fa-chevron-right text-slate-300 group-hover:text-[#29B6D1] transition-colors flex-shrink-0"></i>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
