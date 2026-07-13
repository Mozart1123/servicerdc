@extends($layout)

@section('title', $job->title . ' | ProConnect')
@section('header_title', 'Détails de l\'offre')

@section($contentSection)
<div class="space-y-8 pb-20">
    
    {{-- Success/Error Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
        @if(session($type))
            <div class="bg-{{ $color }}-50 border-l-4 border-{{ $color }}-500 text-{{ $color }}-700 p-5 rounded-2xl shadow-sm flex items-center gap-4" data-aos="fade-down">
                <i class="fas {{ $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }} text-2xl"></i>
                <p class="font-bold">{{ session($type) }}</p>
            </div>
        @endif
    @endforeach

    {{-- Back Button --}}
    <div>
        <a href="{{ route('user.jobs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-100 rounded-2xl text-slate-600 hover:text-rdc-blue font-black text-[10px] uppercase tracking-widest transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux opportunités</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Job Header Card -->
            <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden" data-aos="fade-up">
                {{-- Cover Image --}}
                <div class="h-48 w-full bg-slate-900 relative overflow-hidden">
                    @if($job->cover_image)
                        <img src="{{ Storage::url($job->cover_image) }}" class="w-full h-full object-cover opacity-60">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-r from-rdc-blue to-blue-900 opacity-40"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-10">
                            <i class="fas fa-briefcase text-[10rem] text-white"></i>
                        </div>
                    @endif
                    
                    <div class="absolute -bottom-10 left-10 w-24 h-24 rounded-3xl bg-white p-3 shadow-2xl border border-slate-100 z-10">
                        @if($job->company_logo)
                            <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain" alt="Logo">
                        @else
                            <div class="w-full h-full rounded-2xl bg-slate-50 flex items-center justify-center">
                                <i class="fas fa-building text-3xl text-slate-300"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-10 pt-16 pb-10 space-y-8">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1.5 bg-rdc-blue/10 text-rdc-blue text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $job->category ?? 'Secteur non spécifié' }}</span>
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $job->contract_type }}</span>
                        </div>
                        <h1 class="text-4xl font-heading font-black text-slate-900 tracking-tight leading-tight">{{ $job->title }}</h1>
                        <div class="flex flex-wrap items-center gap-6 mt-6">
                            <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                                <i class="fas fa-building text-rdc-blue opacity-70"></i>
                                <span>{{ $job->company_name }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                                <i class="fas fa-map-marker-alt text-rdc-blue opacity-70"></i>
                                <span>{{ $job->location }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest">
                                <i class="fas fa-clock opacity-50"></i>
                                <span>{{ $job->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 border-y border-slate-100 py-8">
                        <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Salaire</p>
                            <p class="font-heading font-black text-rdc-blue text-lg">{{ $job->salary_range ?? 'À négocier' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidatures</p>
                            <p class="font-heading font-black text-slate-800 text-lg">{{ $job->applications->count() }}</p>
                        </div>
                        <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date Limite</p>
                            <p class="font-heading font-black text-slate-800 text-lg">{{ $job->deadline ? $job->deadline->format('d M Y') : 'Ouverte' }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-3">
                                <span class="w-8 h-1 bg-rdc-blue rounded-full"></span>
                                Missions et Responsabilités
                            </h3>
                            <div class="text-slate-600 leading-relaxed text-lg whitespace-pre-line">
                                {{ $job->description }}
                            </div>
                        </div>

                        @if($job->requirements)
                        <div class="pt-6">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-3">
                                <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                                Profil recherché / Exigences
                            </h3>
                            <div class="text-slate-600 leading-relaxed text-lg whitespace-pre-line">
                                {{ $job->requirements }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Jobs -->
            @if($relatedJobs->isNotEmpty())
                <div class="space-y-6">
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase tracking-tight ml-2">Opportunités Similaires</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($relatedJobs as $relatedJob)
                            <a href="{{ route('user.jobs.show', $relatedJob->id) }}" 
                               class="flex items-center gap-4 p-5 bg-white border border-slate-100 rounded-3xl hover:border-rdc-blue hover:shadow-xl transition-all group">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100 p-2">
                                    @if($relatedJob->company_logo)
                                        <img src="{{ Storage::url($relatedJob->company_logo) }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fas fa-briefcase text-slate-200 text-2xl"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 truncate group-hover:text-rdc-blue transition-colors text-sm">{{ $relatedJob->title }}</h4>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $relatedJob->company_name }} • {{ $relatedJob->location }}</p>
                                </div>
                                <i class="fas fa-chevron-right ml-auto text-slate-200 group-hover:text-rdc-blue transition-colors text-xs"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            
            <!-- Application Form Card -->
            <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden" data-aos="fade-left">
                <div class="absolute -right-20 -bottom-20 w-60 h-60 bg-rdc-blue/20 rounded-full blur-3xl"></div>
                
                @if($userApplication)
                    <div class="relative z-10 text-center py-6">
                        <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-md border border-white/5">
                            <i class="fas fa-file-signature text-4xl text-rdc-blue"></i>
                        </div>
                        <h3 class="font-heading font-black text-2xl mb-2">Candidature Déposée</h3>
                        <p class="text-slate-400 text-sm mb-8">Nous avons bien reçu votre dossier.</p>
                        
                        @php
                            $st = match($userApplication->status) {
                                'approved','accepted' => ['bg'=>'bg-emerald-500/10','text'=>'text-emerald-400','border'=>'border-emerald-500/20','icon'=>'fa-check-circle','label'=>'Acceptée'],
                                'rejected'  => ['bg'=>'bg-red-500/10','text'=>'text-red-400','border'=>'border-red-500/20','icon'=>'fa-times-circle','label'=>'Non retenue'],
                                'interview' => ['bg'=>'bg-blue-500/10','text'=>'text-blue-400','border'=>'border-blue-500/20','icon'=>'fa-calendar-check','label'=>'Entretien prévu'],
                                'hired'     => ['bg'=>'bg-purple-500/10','text'=>'text-purple-400','border'=>'border-purple-500/20','icon'=>'fa-trophy','label'=>'Embauché(e) !'],
                                default     => ['bg'=>'bg-amber-500/10','text'=>'text-amber-400','border'=>'border-amber-500/20','icon'=>'fa-clock','label'=>'En attente de revue'],
                            };
                        @endphp
                        
                        <div class="px-6 py-5 {{ $st['bg'] }} {{ $st['border'] }} rounded-3xl mb-8 border">
                             <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">État de votre dossier</p>
                             <div class="flex items-center justify-center gap-3 {{ $st['text'] }} font-heading font-black text-lg uppercase tracking-tight">
                                <i class="fas {{ $st['icon'] }}"></i>
                                <span>{{ $st['label'] }}</span>
                             </div>
                        </div>

                        @if(in_array($userApplication->status, ['approved', 'accepted', 'interview']))
                        <a href="{{ route('user.messages.start.user', $job->employer_id) }}" 
                           class="flex items-center justify-center gap-3 w-full py-5 bg-rdc-blue text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rdc-blue-dark transition-all shadow-xl shadow-blue-500/20 mb-3">
                            <i class="fas fa-comments text-lg"></i> Discuter avec le recruteur
                        </a>
                        @endif

                        <a href="{{ route('user.applications.index') }}" class="block w-full text-center text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white transition">
                            Gérer mes candidatures
                        </a>
                    </div>
                @else
                    <div class="relative z-10">
                        <h3 class="font-heading font-black text-2xl mb-8 text-center">Postuler à ce poste</h3>
                        
                        <div class="relative z-10 text-center">
                            <h3 class="font-heading font-black text-2xl mb-6">Postuler à ce poste</h3>
                            <p class="text-sm text-slate-400 leading-relaxed mb-8">Utilisez le formulaire dédié à cette offre pour envoyer votre candidature avec votre CV ProConnect ou un document personnalisé.</p>

                            @if($userApplication)
                                <a href="{{ route('user.applications.index') }}" class="inline-flex items-center justify-center gap-3 w-full py-5 bg-green-500 text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] hover:bg-green-600 transition-all shadow-xl shadow-green-500/20">
                                    <i class="fas fa-check-circle"></i> Candidature déjà déposée
                                </a>
                            @else
                                <a href="{{ route('user.jobs.apply.form', $job->id) }}" class="inline-flex items-center justify-center gap-3 w-full py-5 bg-rdc-blue text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] hover:bg-rdc-blue-dark transition-all shadow-xl shadow-blue-500/20">
                                    <i class="fas fa-paper-plane"></i> Postuler maintenant
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Employer Details -->
            <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-xl" data-aos="fade-up">
                <h4 class="font-heading font-black text-slate-900 text-lg mb-6 uppercase tracking-tight">À propos de l'employeur</h4>
                
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-3xl bg-slate-50 border border-slate-100 p-4 mb-4">
                        @if($job->company_logo)
                            <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain">
                        @else
                            <i class="fas fa-building text-3xl text-slate-200"></i>
                        @endif
                    </div>
                    <h5 class="font-bold text-slate-900 text-lg leading-none">{{ $job->company_name }}</h5>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Recruteur vérifié ProConnect</p>
                    
                    <div class="w-full h-px bg-slate-100 my-8"></div>
                    
                    <div class="space-y-4 w-full">
                        <div class="flex items-center justify-between text-xs font-bold uppercase tracking-widest">
                            <span class="text-slate-400">Offres actives</span>
                            <span class="text-rdc-blue">{{ $job->employer->jobOffers->count() ?? 1 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold uppercase tracking-widest">
                            <span class="text-slate-400">Membre depuis</span>
                            <span class="text-slate-800">{{ $job->employer->created_at->format('M Y') ?? 'Juin 2024' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
