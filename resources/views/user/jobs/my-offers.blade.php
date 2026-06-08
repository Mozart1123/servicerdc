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
            <div class="overflow-x-auto">
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
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-rdc-blue">
                                        <i class="fas fa-briefcase text-lg"></i>
                                    </div>
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
                                @if($job->status === 'active')
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
