@extends('layouts.user')

@section('title', 'Mes Offres d\'Emploi')
@section('header_title', 'Gestion des Offres')

@section('content')
<div class="space-y-8">
    {{-- Header with Action --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
        <div>
            <h2 class="text-2xl font-black text-slate-900 font-heading">Mes Offres d'Emploi</h2>
            <p class="text-sm text-slate-400 font-medium">Gérez vos publications et suivez les candidatures.</p>
        </div>
        <a href="{{ route('user.jobs.create') }}"
           class="flex items-center justify-center gap-2 px-6 py-3 bg-rdc-blue text-white font-black rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all">
            <i class="fas fa-plus-circle"></i>
            <span>Publier une offre</span>
        </a>
    </div>

    {{-- Job Offers Table/List --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        @if($jobOffers->count() > 0)
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Offre</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Candidatures</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($jobOffers as $job)
                        @php
                            $offerImage = null;

                            if (!empty($job->company_logo)) {
                                $offerImage = \Illuminate\Support\Str::startsWith($job->company_logo, ['http://', 'https://'])
                                    ? $job->company_logo
                                    : Storage::url($job->company_logo);
                            } elseif (!empty($job->image)) {
                                $offerImage = \Illuminate\Support\Str::startsWith($job->image, ['http://', 'https://'])
                                    ? $job->image
                                    : Storage::url($job->image);
                            } elseif (!empty($job->company?->logo)) {
                                $offerImage = \Illuminate\Support\Str::startsWith($job->company->logo, ['http://', 'https://'])
                                    ? $job->company->logo
                                    : Storage::url($job->company->logo);
                            }

                            $companyLabel = $job->company_name ?? $job->company?->name ?? $job->title;
                            $initials = collect(explode(' ', trim($companyLabel)))
                                ->take(2)
                                ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                ->implode('');

                            $badgeClasses = [
                                'bg-sky-100 text-sky-700',
                                'bg-amber-100 text-amber-700',
                                'bg-emerald-100 text-emerald-700',
                                'bg-violet-100 text-violet-700',
                                'bg-rose-100 text-rose-700',
                            ];
                            $badgeClass = $badgeClasses[$job->id % count($badgeClasses)];
                        @endphp
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if($offerImage)
                                        <img src="{{ $offerImage }}" alt="{{ $job->title }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm">
                                    @else
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[11px] font-black tracking-wide shadow-sm border border-gray-100 {{ $badgeClass }}">
                                            {{ $initials ?: strtoupper(substr($job->title, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $job->title }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium lowercase italic">{{ $job->contract_type }} • {{ $job->location }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-700 text-xs font-black">
                                    {{ $job->applications_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($job->is_expired)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-amber-100"><i class="fas fa-lock text-[9px]"></i>Expirée</span>
                                @elseif($job->status === 'active')
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-100">Actif</span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black rounded-full uppercase tracking-widest border border-slate-200">Fermé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-xs font-bold text-slate-600">{{ $job->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $job->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('user.jobs.show', $job->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('user.jobs.edit', $job->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('user.jobs.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rdc-red hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden p-3 space-y-3">
                @foreach($jobOffers as $job)
                @php
                    $offerImage = null;

                    if (!empty($job->company_logo)) {
                        $offerImage = \Illuminate\Support\Str::startsWith($job->company_logo, ['http://', 'https://'])
                            ? $job->company_logo
                            : Storage::url($job->company_logo);
                    } elseif (!empty($job->image)) {
                        $offerImage = \Illuminate\Support\Str::startsWith($job->image, ['http://', 'https://'])
                            ? $job->image
                            : Storage::url($job->image);
                    } elseif (!empty($job->company?->logo)) {
                        $offerImage = \Illuminate\Support\Str::startsWith($job->company->logo, ['http://', 'https://'])
                            ? $job->company->logo
                            : Storage::url($job->company->logo);
                    }

                    $companyLabel = $job->company_name ?? $job->company?->name ?? $job->title;
                    $initials = collect(explode(' ', trim($companyLabel)))
                        ->take(2)
                        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                        ->implode('');

                    $badgeClasses = [
                        'bg-sky-100 text-sky-700',
                        'bg-amber-100 text-amber-700',
                        'bg-emerald-100 text-emerald-700',
                        'bg-violet-100 text-violet-700',
                        'bg-rose-100 text-rose-700',
                    ];
                    $badgeClass = $badgeClasses[$job->id % count($badgeClasses)];
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($offerImage)
                            <img src="{{ $offerImage }}" alt="{{ $job->title }}" class="w-14 h-14 rounded-xl object-cover border border-gray-100 shadow-sm shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-[11px] font-black tracking-wide shadow-sm border border-gray-100 shrink-0 {{ $badgeClass }}">
                                {{ $initials ?: strtoupper(substr($job->title, 0, 2)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-black text-slate-900 text-[15px] truncate">{{ $job->title }}</p>
                                @if($job->is_expired)
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-amber-100"><i class="fas fa-lock text-[9px]"></i>Expirée</span>
                                @elseif($job->status === 'active')
                                    <span class="shrink-0 px-2.5 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-100">Actif</span>
                                @else
                                    <span class="shrink-0 px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-black rounded-full uppercase tracking-widest border border-slate-200">Fermé</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate lowercase italic">{{ $job->contract_type }} • {{ $job->location }}</p>
                            <div class="flex items-center justify-between mt-2 text-[10px] text-slate-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black">{{ $job->applications_count }}</span>
                                    candidature{{ $job->applications_count > 1 ? 's' : '' }}
                                </span>
                                <span>{{ $job->created_at->format('d/m/Y') }} · {{ $job->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('user.jobs.show', $job->id) }}" class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-[10px] font-bold uppercase tracking-wide">
                            <i class="fas fa-eye text-xs"></i> Voir
                        </a>
                        <a href="{{ route('user.jobs.edit', $job->id) }}" class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wide">
                            <i class="fas fa-edit text-xs"></i> Modifier
                        </a>
                        <form action="{{ route('user.jobs.destroy', $job->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Confirmer la suppression ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-red-50 text-red-500 text-[10px] font-bold uppercase tracking-wide">
                                <i class="fas fa-trash-alt text-xs"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            @if($jobOffers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $jobOffers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-20" data-aos="zoom-in" data-aos-delay="200">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-briefcase text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Aucune offre publiée</h3>
                <p class="text-slate-400 text-sm mt-1 max-w-xs mx-auto">Commencez par publier votre première offre d'emploi pour recruter des talents.</p>
                <a href="{{ route('user.jobs.create') }}" class="mt-8 inline-flex items-center gap-2 bg-rdc-blue text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Publier ma première offre</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
