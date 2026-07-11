@extends('layouts.admin')

@section('title', 'Gestion des Offres d\'Emploi')
@section('header_title', 'Modération des Emplois')
@section('page_title', 'Centre de Recrutement')
@section('page_subtitle', 'Publiez et gérez les opportunités de carrière pour booster l\'économie locale.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Active Jobs -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-briefcase text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Offres Actives</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($stats['active_jobs']) }}</h3>
            </div>
        </div>

        <!-- Recent Jobs -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-arrow-trend-up text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    30 Jours
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Nouveautés</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">+{{ number_format($stats['recent_jobs']) }}</h3>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-user-tie text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Candidatures reçues</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($stats['total_applications']) }}</h3>
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
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <div class="overflow-x-hidden">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[45%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-nowrap">Offre d'Emploi</th>
                        <th class="hidden lg:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Entreprise</th>
                        <th class="w-[20%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Type</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">App.</th>
                        <th class="hidden min-[480px]:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="w-[35%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Gérer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jobs as $job)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center font-black shadow-sm shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                    <i class="fas fa-briefcase text-[10px] sm:text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate leading-tight tracking-tight">{{ $job->title }}</p>
                                    <p class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5 sm:mt-1 truncate opacity-70">
                                        <i class="fas fa-location-dot mr-1"></i> {{ $job->location }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 p-1 overflow-hidden">
                                    @if($job->logo_url)
                                        <img src="{{ $job->logo_url }}" class="w-full h-full object-contain" alt="">
                                    @else
                                        <i class="fas fa-building text-slate-200 text-lg"></i>
                                    @endif
                                </div>
                                <span class="text-xs font-black text-slate-700 uppercase">{{ $job->company_name }}</span>
                            </div>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 text-center sm:text-left">
                            <span class="inline-block text-[7px] sm:text-[9px] font-black px-1.5 sm:px-2 py-0.5 bg-blue-50 text-rdc-blue rounded uppercase tracking-tighter">{{ $job->contract_type }}</span>
                        </td>
                        <td class="hidden sm:table-cell px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-slate-900 font-mono">{{ $job->applications_count ?? 0 }}</span>
                                <span class="text-[7px] font-black text-slate-300 uppercase tracking-widest">Postulants</span>
                            </div>
                        </td>
                        <td class="hidden min-[480px]:table-cell px-8 py-6 text-center text-nowrap">
                            <div class="flex justify-center items-center">
                                @if($job->status === 'active')
                                    <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">Ouvert</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-3 py-1 bg-slate-50 rounded-full border border-slate-100 opacity-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Clos</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                            <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-lg sm:rounded-xl hover:text-rdc-blue hover:border-rdc-blue hover:shadow-lg hover:shadow-blue-500/10 transition-all shadow-sm">
                                    <i class="fas fa-pen text-[9px] sm:text-xs"></i>
                                </a>
                                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Retirer définitivement cette offre ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-lg sm:rounded-xl hover:text-rdc-red hover:border-rdc-red hover:shadow-lg hover:shadow-red-500/10 transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[9px] sm:text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20">
                            <div class="flex flex-col items-center justify-center text-center min-h-[300px]">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h4 class="text-base sm:text-lg font-black text-slate-400 uppercase tracking-widest">Bassin d'Emploi Vide</h4>
                                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2 max-w-[250px] mx-auto">Relancez l'activité économique en publiant une nouvelle offre maintenant.</p>
                                <a href="{{ route('admin.jobs.create') }}" class="mt-8 px-10 py-4 bg-slate-900 text-white rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-rdc-blue hover:shadow-xl hover:shadow-blue-500/20 transition-all">
                                    Publier une mission
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(optional($jobs)->hasPages())
        <div class="px-8 py-8 bg-slate-50/30">
            {{ optional($jobs)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
