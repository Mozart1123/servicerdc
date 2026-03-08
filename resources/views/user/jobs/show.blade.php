@extends('layouts.user')

@section('title', $job->title)
@section('header_title', 'Détails de l\'offre')

@section('content')
<div class="space-y-8">
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm animate-fade-in" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm animate-fade-in" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Back Button -->
    <div>
        <a href="{{ route('user.jobs.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-rdc-blue font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux offres</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Job Header Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="bg-gradient-to-r from-rdc-blue to-blue-600 px-8 py-10 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-heading font-extrabold mb-3">{{ $job->title }}</h1>
                            <div class="flex flex-wrap items-center gap-4 text-white/90">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building"></i>
                                    <span class="font-bold">{{ $job->company_name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $job->location }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($job->logo_url)
                            <img src="{{ $job->logo_url }}" class="w-20 h-20 rounded-xl bg-white p-2 shadow-lg" alt="Logo">
                        @else
                            <div class="w-20 h-20 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                <i class="fas fa-briefcase text-3xl text-white/80"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Job Details -->
                <div class="px-8 py-6 space-y-6">
                    <!-- Quick Info -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Type de contrat</p>
                            <p class="font-heading font-extrabold text-slate-900">{{ $job->contract_type }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Catégorie</p>
                            <p class="font-heading font-extrabold text-slate-900">{{ $job->category }}</p>
                        </div>
                        @if($job->salary_range)
                            <div class="bg-slate-50 rounded-xl p-4">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Salaire</p>
                                <p class="font-heading font-extrabold text-rdc-blue">{{ $job->salary_range }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="text-lg font-heading font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-align-left text-rdc-blue"></i>
                            Description du poste
                        </h3>
                        <div class="prose prose-slate max-w-none">
                            <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $job->description }}</p>
                        </div>
                    </div>

                    <!-- Requirements -->
                    @if($job->requirements)
                        <div>
                            <h3 class="text-lg font-heading font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-list-check text-rdc-blue"></i>
                                Exigences
                            </h3>
                            <div class="prose prose-slate max-w-none">
                                <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $job->requirements }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Jobs -->
            @if($relatedJobs->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
                    <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-sparkles text-rdc-blue"></i>
                        Autres opportunités
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($relatedJobs as $relatedJob)
                            <a href="{{ route('user.jobs.show', $relatedJob->id) }}" 
                               class="block p-4 rounded-xl border border-slate-200 hover:border-rdc-blue hover:shadow-md transition-all group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $relatedJob->title }}</h4>
                                        <p class="text-sm text-slate-500 mt-1">{{ $relatedJob->company_name }} • {{ $relatedJob->location }}</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-slate-400 group-hover:text-rdc-blue group-hover:translate-x-1 transition-all"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Application Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sticky top-24">
                @if($userApplication)
                    <!-- Already Applied -->
                    <div class="text-center py-6">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-4xl text-rdc-blue"></i>
                        </div>
                        <h3 class="font-heading font-extrabold text-xl text-slate-900 mb-2">Candidature envoyée</h3>
                        <p class="text-slate-600 mb-4">Vous avez déjà postulé à cette offre</p>
                        
                        <!-- Application Status -->
                        @if($userApplication->status === 'pending')
                            <div class="px-4 py-3 bg-amber-50 rounded-xl border border-amber-200 mb-4">
                                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-1">Statut</p>
                                <p class="font-heading font-extrabold text-amber-600">
                                    <i class="fas fa-clock mr-2"></i>En attente
                                </p>
                            </div>
                        @elseif($userApplication->status === 'accepted' || $userApplication->status === 'approved')
                            <div class="px-4 py-3 bg-emerald-50 rounded-xl border border-emerald-200 mb-4">
                                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-1">Statut</p>
                                <p class="font-heading font-extrabold text-emerald-600">
                                    <i class="fas fa-check-circle mr-2"></i>Acceptée
                                </p>
                            </div>
                        @elseif($userApplication->status === 'rejected')
                            <div class="px-4 py-3 bg-red-50 rounded-xl border border-red-200 mb-4">
                                <p class="text-xs font-bold text-red-700 uppercase tracking-wide mb-1">Statut</p>
                                <p class="font-heading font-extrabold text-red-600">
                                    <i class="fas fa-times-circle mr-2"></i>Rejetée
                                </p>
                            </div>
                        @endif

                        <a href="{{ route('user.applications.index') }}" 
                           class="block w-full px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">
                            Voir mes candidatures
                        </a>
                    </div>
                @else
                    <!-- Apply Form -->
                    <h3 class="font-heading font-extrabold text-xl text-slate-900 mb-6 text-center">Postuler maintenant</h3>
                    
                    <form method="POST" action="{{ route('user.jobs.apply', $job->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <!-- Cover Letter -->
                        <div>
                            <label for="cover_letter" class="block text-sm font-bold text-slate-700 mb-2">
                                Lettre de motivation
                            </label>
                            <textarea 
                                name="cover_letter" 
                                id="cover_letter" 
                                rows="5" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-all resize-none"
                                placeholder="Pourquoi êtes-vous le candidat idéal pour ce poste ?"></textarea>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label for="resume_url" class="block text-sm font-bold text-slate-700 mb-2">
                                CV (facultatif)
                            </label>
                            <input 
                                type="file" 
                                name="resume_url" 
                                id="resume_url" 
                                accept=".pdf,.doc,.docx"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-all">
                            <p class="text-xs text-slate-500 mt-2">PDF, DOC ou DOCX (max 2MB)</p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full px-6 py-4 bg-gradient-to-r from-rdc-blue to-blue-600 text-white font-heading font-extrabold text-lg rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Envoyer ma candidature
                        </button>
                    </form>
                @endif
            </div>

            <!-- Job Info -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                <h4 class="font-heading font-extrabold text-slate-900 mb-4">Informations</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-slate-600">
                        <i class="fas fa-calendar-plus w-5 text-slate-400"></i>
                        <span>Publié le {{ $job->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($job->deadline)
                        <div class="flex items-center gap-3 text-slate-600">
                            <i class="fas fa-calendar-xmark w-5 text-slate-400"></i>
                            <span>Date limite: {{ $job->deadline->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-3 text-slate-600">
                        <i class="fas fa-users w-5 text-slate-400"></i>
                        <span>{{ $job->applications->count() }} candidature(s)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>
@endsection
