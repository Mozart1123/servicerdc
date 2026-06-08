@extends('layouts.user')

@section('header_title', 'Conseiller de Carrière Intelligent')

@section('content')
<div class="space-y-12 pb-24">
    <!-- Premium Header: The Visionary Dashboard -->
    <div class="relative rounded-[4rem] overflow-hidden bg-rdc-dark-blue p-12 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 -left-1/4 w-1/2 h-full bg-rdc-blue rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-0 -right-1/4 w-1/2 h-full bg-blue-400 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-12">
            <div class="max-w-2xl space-y-6">
                <div class="inline-flex items-center gap-3 px-5 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-200">Mode Analyse Automatique Activé</span>
                </div>
                <h1 class="text-5xl font-heading font-black leading-[1.1] uppercase tracking-tighter">
                    Votre <span class="text-rdc-blue">Carrière</span> basée sur vos <span class="text-emerald-400">Actions</span>
                </h1>
                <p class="text-lg text-blue-100/70 font-medium max-w-xl leading-relaxed">
                    Nous avons analysé vos candidatures, vos services et votre profil pour générer ces recommandations personnalisées.
                </p>
                <div class="pt-4">
                    <form action="{{ route('user.career-advisor.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="group flex items-center gap-4 px-8 py-4 bg-white text-rdc-dark-blue rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                            <i class="fas fa-sync-alt animate-spin-slow group-hover:rotate-180 transition-transform duration-500"></i>
                            Synchroniser mon activité
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- AI Interaction HUD -->
            <div class="hidden lg:block w-72 h-72 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-48 h-48 bg-white/5 backdrop-blur-2xl rounded-[3rem] border border-white/20 flex flex-col items-center justify-center shadow-inner">
                        <div class="text-4xl font-black text-white mb-1">{{ count($user->skills ?? []) }}</div>
                        <span class="text-[9px] font-black text-blue-200 uppercase tracking-widest text-center px-4">Compétences Détectées</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500 text-white p-6 rounded-3xl flex items-center gap-4 shadow-xl animate-bounce-short">
            <i class="fas fa-check-circle text-2xl"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        
        <!-- DATA SOURCES & PROFILE -->
        <div class="xl:col-span-4 space-y-8">
            <div class="bg-white/80 backdrop-blur-xl border border-white shadow-xl rounded-[3.5rem] p-10">
                <div class="mb-10">
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase flex items-center gap-4 mb-2">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg">
                            <i class="fas fa-microchip"></i>
                        </div>
                        Profil IA
                    </h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sources de données analysées</p>
                </div>

                <div class="space-y-8">
                    <!-- SOURCE: APPLICATIONS -->
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-6 group hover:bg-white hover:shadow-lg transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-rdc-blue shadow-inner group-hover:bg-rdc-blue group-hover:text-white transition-all">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-900 uppercase">Candidatures</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $user->jobApplications->count() }} Interactions</div>
                        </div>
                    </div>

                    <!-- SOURCE: SERVICES -->
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-6 group hover:bg-white hover:shadow-lg transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-amber-500 shadow-inner group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-900 uppercase">Services & Jobs</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $user->services->count() }} Prestations</div>
                        </div>
                    </div>

                    <!-- DETECTED SKILLS -->
                    <div class="space-y-6 pt-4">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-2">Compétences Identifiées</label>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->skills ?? [] as $skill)
                                <span class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-[9px] font-black text-slate-700 uppercase shadow-sm">
                                    {{ $skill }}
                                </span>
                            @empty
                                <div class="w-full text-[10px] font-bold text-slate-400 text-center py-4 border-2 border-dashed border-slate-200 rounded-2xl">
                                    Aucune compétence détectée
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <button onclick="document.getElementById('manual-config').classList.toggle('hidden')" class="w-full text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">
                            Ajuster Manuellement <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MANUAL FALLBACK (Hidden by default) -->
            <div id="manual-config" class="hidden animate-fade-in">
                <div class="bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
                    <form action="{{ route('user.career-advisor.update') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ajustement Manuel</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($allSkills as $skill)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="skills[]" value="{{ $skill }}" 
                                               class="hidden peer" {{ in_array($skill, $user->skills ?? []) ? 'checked' : '' }}>
                                        <span class="px-4 py-2 rounded-xl border border-slate-100 bg-slate-50 text-[9px] font-black uppercase text-slate-500 peer-checked:bg-rdc-blue peer-checked:text-white inline-block">
                                            {{ $skill }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="w-full py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest">
                            Appliquer les Changements
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN RESULTS FEED -->
        <div class="xl:col-span-8 space-y-12">
            <div class="flex items-center justify-between px-6">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-[1.5rem] bg-emerald-500 text-white flex items-center justify-center text-xl shadow-2xl rotate-3">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-heading font-black text-slate-900 uppercase tracking-tight">Opportunités Prédictives</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Basé sur {{ count($recommendations) }} correspondances</p>
                    </div>
                </div>
            </div>

            @if($recommendations->isEmpty())
                <div class="bg-white border-4 border-dashed border-slate-100 rounded-[4rem] p-24 text-center">
                    <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center text-5xl text-slate-200 mx-auto mb-10">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="max-w-md mx-auto space-y-4">
                        <h4 class="text-xl font-black text-slate-900 uppercase">Analyse Reclise</h4>
                        <p class="text-slate-400 font-medium pb-4">Cliquez sur <span class="font-black text-rdc-blue">Synchroniser mon activité</span> pour que l'IA puisse scanner vos données.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-10">
                    @foreach($recommendations as $rec)
                        <div class="group relative bg-white border border-slate-100 p-10 rounded-[4rem] shadow-sm hover:shadow-2xl transition-all duration-500">
                            <div class="flex flex-col md:flex-row gap-12">
                                <!-- MATCH VISUALIZER -->
                                <div class="shrink-0 flex flex-col items-center justify-center space-y-4">
                                    <div class="relative w-32 h-32 flex items-center justify-center">
                                        <svg class="w-full h-full transform -rotate-90">
                                            <circle cx="64" cy="64" r="58" stroke="rgba(226, 232, 240, 0.5)" stroke-width="10" fill="transparent" />
                                            <circle cx="64" cy="64" r="58" stroke="url(#gradient-{{ $rec->id }})" stroke-width="12" fill="transparent" 
                                                    stroke-dasharray="364.4" stroke-dashoffset="{{ 364.4 - (364.4 * $rec->match_score / 100) }}" 
                                                    stroke-linecap="round" />
                                            <defs>
                                                <linearGradient id="gradient-{{ $rec->id }}" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" stop-color="#10B981" />
                                                    <stop offset="100%" stop-color="#3B82F6" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-3xl font-black text-slate-900">{{ round($rec->match_score) }}%</span>
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Match IA</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 space-y-8">
                                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-bold text-rdc-blue uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full">{{ $rec->careerPath->industry }}</span>
                                            </div>
                                            <h4 class="text-3xl font-heading font-black text-slate-900 uppercase tracking-tighter leading-none group-hover:text-rdc-blue transition-colors">
                                                {{ $rec->careerPath->title }}
                                            </h4>
                                        </div>
                                        <div class="bg-emerald-50 border border-emerald-100 rounded-3xl p-5 shrink-0 text-center">
                                            <div class="text-[8px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-1">Salaire Est.</div>
                                            <div class="text-xl font-black text-slate-900">{{ $rec->careerPath->salary_range }}</div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100">
                                        <p class="text-base text-slate-700 font-medium leading-relaxed">
                                            {{ $rec->analysis }}
                                        </p>
                                    </div>

                                    <!-- JOB OPPORTUNITIES HUD -->
                                    <div class="space-y-5">
                                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] flex items-center gap-2 px-2">
                                            <i class="fas fa-briefcase text-rdc-blue"></i>
                                            Offres Disponibles
                                        </h5>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @forelse($rec->jobs as $job)
                                                <a href="{{ route('user.jobs.show', $job->id) }}" class="flex items-center gap-5 p-5 bg-white border border-slate-100 rounded-3xl hover:border-rdc-blue hover:shadow-xl transition-all">
                                                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-xl text-slate-300">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-[11px] font-black text-slate-900 uppercase truncate">{{ $job->title }}</div>
                                                        <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $job->location ?? 'Kinshasa' }}</div>
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="col-span-full py-4 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                                                    <span class="text-[9px] font-black text-slate-300 uppercase">Aucune offre directe</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin-slow { animation: spin-slow 15s linear infinite; }
    .animate-bounce-short { animation: bounce 1s ease-in-out; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
</style>
@endsection