@extends('layouts.user')

@section('header_title', 'Tableau de bord - Chercheur d\'emploi')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Welcome Header -->
    <div class="relative" data-aos="fade-down">
        <div class="absolute inset-0 bg-blue-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-3xl shadow-inner">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="flex-1 w-full text-center md:text-left">
                <h2 class="text-2xl font-black text-slate-900 uppercase">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Suivez vos candidatures et trouvez votre prochain emploi</p>
            </div>
            <div class="flex gap-4 w-full md:w-auto mt-4 md:mt-0">
                <a href="{{ route('user.jobs.index') }}" class="flex-1 text-center md:flex-none px-8 py-5 bg-rdc-blue text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                    Parcourir les offres
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="100">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-paper-plane"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ collect($myApplications ?? [])->count() ?? ($stats['applied_jobs_count'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Candidatures envoyées</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="200">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ collect($myApplications ?? [])->where('status', 'pending')->count() ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Candidatures en attente</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="300">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-building"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['total_jobs'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Offres disponibles</p>
            </div>
        </div>
    </div>

    <!-- Main Views -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        
        <!-- Mes Candidatures -->
        <div class="xl:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-4" data-aos="fade-up">
                <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Historique des Candidatures</h3>
                <a href="{{ route('user.applications.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir tout</a>
            </div>

            <div class="space-y-4">
                @if(isset($myApplications) && $myApplications->count() > 0)
                    @foreach($myApplications->take(4) as $app)
                    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition-all" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-2xl text-slate-400">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="font-bold text-slate-900">{{ $app->jobOffer->title ?? 'Poste supprimé' }}</h4>
                            <p class="text-xs text-slate-500 mt-1"><i class="fas fa-building mr-1"></i> {{ $app->jobOffer->company_name ?? 'Entreprise' }} • <i class="fas fa-clock mr-1"></i> Il y a {{ $app->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-center md:text-right mt-4 md:mt-0">
                            @if($app->status == 'pending')
                                <span class="inline-block px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-200">En attente</span>
                            @elseif($app->status == 'accepted')
                                <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full border border-emerald-200">Acceptée</span>
                            @elseif($app->status == 'rejected')
                                <span class="inline-block px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-200">Refusée</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-full border border-slate-200">{{ ucfirst($app->status) }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm text-center" data-aos="fade-up">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-3xl text-slate-300 mb-4">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase">Historique Vide</h4>
                        <p class="text-sm text-slate-500 mt-2">Vous n'avez pas encore postulé à des offres d'emploi (données réelles).</p>
                        <a href="{{ route('user.jobs.index') }}" class="inline-block mt-4 px-6 py-3 bg-rdc-blue text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition">Chercher un emploi</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recommandations -->
        <div class="space-y-6" data-aos="fade-left">
            <h3 class="text-xl font-heading font-black text-slate-900 uppercase px-4">Pour vous</h3>
            <div class="bg-slate-900 rounded-[3.5rem] p-8 text-white shadow-2xl relative overflow-hidden h-full flex flex-col">
                <div class="absolute top-0 right-0 p-6 opacity-10 text-6xl">
                    <i class="fas fa-briefcase"></i>
                </div>
                
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 relative z-10">Offres Suggérées</h4>
                
                <div class="space-y-4 relative z-10">
                    @if(isset($recentJobs) && method_exists($recentJobs, 'take') && $recentJobs->count() > 0)
                        @foreach($recentJobs->take(4) as $rJob)
                        <div class="border-b border-slate-800 pb-4 last:border-0 hover:bg-white/5 p-2 rounded-lg transition-all">
                            <h5 class="font-bold text-emerald-400 hover:text-emerald-300 transition line-clamp-1"><a href="{{ route('user.jobs.show', $rJob->id) }}">{{ $rJob->title }}</a></h5>
                            <p class="text-xs text-slate-400 mt-1"><i class="fas fa-map-marker-alt"></i> {{ $rJob->location ?? 'Non spécifié' }}</p>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-slate-400 text-center">Aucune offre récente pour le moment.</p>
                    @endif
                </div>

                <div class="mt-auto pt-6">
                     <a href="{{ route('user.jobs.index') }}" class="block w-full py-4 text-center bg-white/10 hover:bg-white/20 text-white font-black rounded-2xl text-[10px] uppercase tracking-[0.2em] transition-all">
                        Voir plus d'offres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
