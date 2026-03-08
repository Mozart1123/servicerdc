@extends('layouts.admin')

@section('title', 'Gestion des Offres d\'Emploi')
@section('header_title', 'Modération des Emplois')
@section('page_title', 'Centre de Recrutement')
@section('page_subtitle', 'Publiez et gérez les opportunités de carrière pour booster l\'économie locale.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-blue/10 rounded-full blur-3xl"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Offres Actives</p>
            <div class="flex items-end justify-between relative z-10">
                <h3 class="text-4xl font-heading font-black">{{ $jobs->total() }}</h3>
                <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-rdc-blue">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Derniers 30 jours</p>
            <div class="flex items-end justify-between">
                <h3 class="text-4xl font-heading font-black text-slate-900">+12</h3>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <i class="fas fa-arrow-trend-up text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl group">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Candidatures reçues</p>
            <div class="flex items-end justify-between">
                <h3 class="text-4xl font-heading font-black text-slate-900">1.2k</h3>
                <div class="w-12 h-12 bg-blue-50 text-rdc-blue rounded-2xl flex items-center justify-center group-hover:bg-rdc-blue group-hover:text-white transition-all">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white/50 backdrop-blur-sm p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="relative w-full md:w-96 group">
            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
            <input type="text" placeholder="Poste, entreprise, ville..." 
                   class="w-full pl-12 pr-4 py-3 bg-white border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
        </div>
        
        <a href="{{ route('admin.jobs.create') }}" class="w-full md:w-auto px-10 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200 hover:bg-rdc-blue transition-all text-center">
            <i class="fas fa-plus mr-2"></i> Publier une Offre
        </a>
    </div>

    <!-- Jobs List -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Offre d'Emploi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Entreprise</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type / Salaire</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Applications</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Gérer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jobs as $job)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div>
                                <p class="text-sm font-black text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $job->title }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><i class="fas fa-location-dot mr-1"></i> {{ $job->location }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 p-1 overflow-hidden shadow-inner">
                                    @if($job->logo_url)
                                        <img src="{{ $job->logo_url }}" class="w-full h-full object-contain" alt="">
                                    @else
                                        <i class="fas fa-building text-slate-200 text-lg"></i>
                                    @endif
                                </div>
                                <span class="text-xs font-black text-slate-700 uppercase">{{ $job->company_name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div>
                                <span class="text-[9px] font-black px-2 py-0.5 bg-blue-50 text-rdc-blue rounded uppercase tracking-tighter">{{ $job->contract_type }}</span>
                                <p class="text-xs font-bold text-slate-400 mt-1 italic">{{ $job->salary_range ?? 'Non spécifié' }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-slate-900">{{ $job->applications_count ?? 0 }}</span>
                                <i class="fas fa-user-group text-[10px] text-slate-300"></i>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $job->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-rdc-red' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $job->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rdc-red' }}"></span>
                                {{ $job->status === 'active' ? 'Publiée' : 'Expirée' }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rdc-blue hover:shadow-xl transition-all">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rdc-red hover:shadow-xl transition-all">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 text-4xl mb-4">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-400 uppercase tracking-widest">Aucune offre d'emploi</h4>
                                <p class="text-sm text-slate-300 mt-2 font-medium">Lancez la première opportunité de carrière dès maintenant.</p>
                                <a href="{{ route('admin.jobs.create') }}" class="mt-8 px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20">Publier ma première offre</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jobs->hasPages())
        <div class="px-8 py-8 bg-slate-50/30">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
