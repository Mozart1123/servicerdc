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

    <div class="w-full">
        
        <!-- Main Content -->
        <div class="space-y-8">
            
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

                    {{-- Stats + Bouton rapide --}}
                    <div class="border-y border-slate-100 py-8">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
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

                        {{-- Bouton postuler rapide (haut) --}}
                        @if($userApplication)
                            <div class="flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-emerald-700">Candidature déposée</p>
                                    <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest">
                                        @php
                                            $statusLabel = match($userApplication->status) {
                                                'approved','accepted' => 'Acceptée',
                                                'rejected'  => 'Non retenue',
                                                'interview' => 'Entretien prévu',
                                                'hired'     => 'Embauché(e) !',
                                                default     => 'En attente de revue',
                                            };
                                        @endphp
                                        {{ $statusLabel }}
                                    </p>
                                </div>
                                <a href="{{ route('user.applications.index') }}" class="text-[10px] font-black text-emerald-600 hover:text-emerald-800 uppercase tracking-widest transition whitespace-nowrap">
                                    Voir →
                                </a>
                            </div>
                        @else
                            <a href="{{ route('user.jobs.apply.form', $job->id) }}"
                               class="inline-flex items-center justify-center gap-3 w-full py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20">
                                <i class="fas fa-paper-plane"></i>
                                Postuler maintenant
                            </a>
                        @endif
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

                        {{-- À propos de l'entreprise --}}
                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                                <span class="w-8 h-1 bg-slate-300 rounded-full"></span>
                                À propos de l'entreprise
                            </h3>
                            <div class="flex flex-col sm:flex-row gap-6 items-start">
                                <div class="w-20 h-20 rounded-3xl bg-slate-50 border border-slate-100 p-4 shrink-0">
                                    @if($job->company_logo)
                                        <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fas fa-building text-3xl text-slate-200"></i>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-900 text-lg mb-2">{{ $job->company_name }}</h5>
                                    @if($job->employer && $job->employer->company_description)
                                        <p class="text-slate-600 text-sm leading-relaxed mb-4">{{ $job->employer->company_description }}</p>
                                    @else
                                        <p class="text-slate-500 text-sm italic mb-4">Aucune description détaillée n'a été fournie par cette entreprise.</p>
                                    @endif
                                    
                                    @if($job->employer)
                                        <a href="{{ route('user.recruiters.show', $job->employer->id) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#16a3b0] hover:text-[#128a96] transition-colors">
                                            Voir toutes les offres de {{ $job->company_name }} <i class="fas fa-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>


                        {{-- Bouton postuler bas (après lecture complète) --}}
                        <div class="pt-8 border-t border-slate-100">
                            @if($userApplication)
                                @php
                                    $st = match($userApplication->status) {
                                        'approved','accepted' => ['bg'=>'bg-emerald-500/10','text'=>'text-emerald-600','border'=>'border-emerald-200','icon'=>'fa-check-circle','label'=>'Acceptée'],
                                        'rejected'  => ['bg'=>'bg-red-50','text'=>'text-red-500','border'=>'border-red-100','icon'=>'fa-times-circle','label'=>'Non retenue'],
                                        'interview' => ['bg'=>'bg-blue-50','text'=>'text-blue-600','border'=>'border-blue-100','icon'=>'fa-calendar-check','label'=>'Entretien prévu'],
                                        'hired'     => ['bg'=>'bg-purple-50','text'=>'text-purple-600','border'=>'border-purple-100','icon'=>'fa-trophy','label'=>'Embauché(e) !'],
                                        default     => ['bg'=>'bg-amber-50','text'=>'text-amber-600','border'=>'border-amber-100','icon'=>'fa-clock','label'=>'En attente de revue'],
                                    };
                                @endphp
                                <div class="flex items-center gap-4 p-5 {{ $st['bg'] }} border {{ $st['border'] }} rounded-2xl">
                                    <i class="fas {{ $st['icon'] }} text-xl {{ $st['text'] }}"></i>
                                    <div class="flex-1">
                                        <p class="font-black text-slate-800 text-sm">Vous avez déjà postulé à cette offre</p>
                                        <p class="text-[10px] font-bold uppercase tracking-widest {{ $st['text'] }} mt-0.5">Statut : {{ $st['label'] }}</p>
                                    </div>
                                    @if(in_array($userApplication->status, ['approved', 'accepted', 'interview']))
                                    <a href="{{ route('user.messages.start.user', $job->employer_id) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#16a3b0] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#128a96] transition-all whitespace-nowrap">
                                        <i class="fas fa-comments"></i> Discuter
                                    </a>
                                    @else
                                    <a href="{{ route('user.applications.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:border-slate-300 transition-all whitespace-nowrap">
                                        Mes candidatures
                                    </a>
                                    @endif
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                    <div class="flex-1">
                                        <p class="font-black text-slate-800 text-base">Vous êtes intéressé(e) par ce poste ?</p>
                                        <p class="text-slate-400 text-sm mt-1">Envoyez votre candidature avec votre CV ProConnect ou un document personnalisé.</p>
                                    </div>
                                    <a href="{{ route('user.jobs.apply.form', $job->id) }}"
                                       class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20 shrink-0">
                                        <i class="fas fa-paper-plane"></i>
                                        Postuler maintenant
                                    </a>
                                </div>
                            @endif
                        </div>
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

    </div>
</div>
@endsection
