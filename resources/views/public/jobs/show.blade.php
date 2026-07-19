@extends('layouts.public')

@section('title', $job->title . ' — ' . $job->company_name . ' | ProConnect')
@section('meta_description', Str::limit($job->description, 160))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 overflow-x-hidden">

    {{-- Breadcrumb --}}
    <nav class="text-xs font-bold text-slate-400 mb-6 sm:mb-8 flex items-center gap-2 flex-wrap">
        <a href="{{ route('public.jobs.index') }}" class="hover:text-[#16a3b0] transition-colors">Offres d'emploi</a>
        <i class="fas fa-chevron-right text-[9px]"></i>
        <span class="text-slate-600 break-words">{{ Str::limit($job->title, 60) }}</span>
    </nav>

    <div class="space-y-6 sm:space-y-8">

        {{-- ── Job Header Card ──────────────────────────────────── --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Cover Image --}}
            <div class="h-40 sm:h-52 w-full bg-slate-900 relative overflow-hidden">
                @if($job->cover_image)
                    <img src="{{ Storage::url($job->cover_image) }}" alt="{{ $job->title }}" class="w-full h-full object-cover opacity-60">
                @else
                    <div class="absolute inset-0 bg-gradient-to-r from-[#16a3b0] to-blue-900 opacity-40"></div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-10">
                        <i class="fas fa-briefcase text-[8rem] sm:text-[10rem] text-white"></i>
                    </div>
                @endif

                {{-- Floating logo --}}
                <div class="absolute -bottom-8 sm:-bottom-10 left-4 sm:left-10 w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-white p-2.5 sm:p-3 shadow-2xl border border-slate-100 z-10">
                    @if($job->company_logo)
                        <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain" alt="{{ $job->company_name }}">
                    @else
                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-slate-50 flex items-center justify-center">
                            <span class="text-2xl sm:text-3xl font-black text-[#16a3b0]/50">{{ strtoupper(substr($job->company_name ?? 'C', 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Header Content --}}
            <div class="px-4 sm:px-10 pt-12 sm:pt-16 pb-6 sm:pb-10 space-y-5 sm:space-y-8">

                {{-- Badges + Title --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3 sm:mb-4">
                        <span class="px-3 py-1.5 bg-[#16a3b0]/10 text-[#16a3b0] text-[10px] font-black uppercase tracking-widest rounded-lg">
                            {{ $job->category ?? 'Secteur non spécifié' }}
                        </span>
                        <span class="px-3 py-1.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-lg">
                            {{ $job->contract_type }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight leading-tight break-words">
                        {{ $job->title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 sm:gap-6 mt-4">
                        <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                            <i class="fas fa-building text-[#16a3b0] opacity-70"></i>
                            <span class="break-words">{{ $job->company_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                            <i class="fas fa-map-marker-alt text-[#16a3b0] opacity-70"></i>
                            <span>{{ $job->location }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-clock opacity-50"></i>
                            <span>{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Stats mini-cards --}}
                <div class="border-y border-slate-100 py-5 sm:py-8">
                    <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-5 sm:mb-6">
                        <div class="p-3 sm:p-4 bg-slate-50/50 rounded-xl sm:rounded-2xl border border-slate-100">
                            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Salaire</p>
                            <p class="font-black text-[#16a3b0] text-sm sm:text-lg truncate">{{ $job->salary_range ?? 'À négocier' }}</p>
                        </div>
                        <div class="p-3 sm:p-4 bg-slate-50/50 rounded-xl sm:rounded-2xl border border-slate-100">
                            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidats</p>
                            <p class="font-black text-slate-800 text-sm sm:text-lg">{{ $job->applications->count() }}</p>
                        </div>
                        <div class="p-3 sm:p-4 bg-slate-50/50 rounded-xl sm:rounded-2xl border border-slate-100">
                            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Limite</p>
                            <p class="font-black text-slate-800 text-sm sm:text-lg">
                                {{ $job->deadline ? $job->deadline->format('d M Y') : 'Ouverte' }}
                            </p>
                        </div>
                    </div>

                    {{-- CTA Bouton Postuler (haut) --}}
                    @auth
                        @php $userApplication = Auth::user()->jobApplications()->where('job_offer_id', $job->id)->first(); @endphp
                        @if($userApplication)
                            <div class="flex items-center gap-3 px-4 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                                <i class="fas fa-check-circle text-emerald-500 text-lg shrink-0"></i>
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
                                <a href="{{ route('user.applications.index') }}" class="text-[10px] font-black text-emerald-600 hover:text-emerald-800 uppercase tracking-widest transition whitespace-nowrap shrink-0">
                                    Voir →
                                </a>
                            </div>
                        @else
                            <a href="{{ route('public.jobs.apply', $job->id) }}"
                               class="flex items-center justify-center gap-3 w-full py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20">
                                <i class="fas fa-paper-plane"></i>
                                Postuler maintenant
                            </a>
                        @endif
                    @else
                        <div class="space-y-3">
                            <a href="{{ route('login', ['intended' => route('public.jobs.apply', $job->id)]) }}"
                               class="flex items-center justify-center gap-3 w-full py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20">
                                <i class="fas fa-paper-plane"></i>
                                Postuler maintenant
                            </a>
                            <p class="text-center text-xs text-slate-400">
                                <a href="{{ route('register') }}" class="text-[#16a3b0] font-bold hover:underline">Créer un compte gratuit</a>
                                pour postuler à cette offre.
                            </p>
                        </div>
                    @endauth
                </div>

                {{-- Description --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-3">
                            <span class="w-8 h-1 bg-[#16a3b0] rounded-full shrink-0"></span>
                            Missions et Responsabilités
                        </h2>
                        <div class="text-slate-600 leading-relaxed text-base sm:text-lg whitespace-pre-line break-words">
                            {{ $job->description }}
                        </div>
                    </div>

                    @if($job->requirements)
                    <div class="pt-4 sm:pt-6">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-3">
                            <span class="w-8 h-1 bg-amber-400 rounded-full shrink-0"></span>
                            Profil recherché / Exigences
                        </h2>
                        <div class="text-slate-600 leading-relaxed text-base sm:text-lg whitespace-pre-line break-words">
                            {{ $job->requirements }}
                        </div>
                    </div>
                    @endif

                    {{-- À propos de l'entreprise --}}
                    <div class="pt-4 sm:pt-6 border-t border-slate-100">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5 sm:mb-6 flex items-center gap-3">
                            <span class="w-8 h-1 bg-slate-300 rounded-full shrink-0"></span>
                            À propos de l'entreprise
                        </h2>
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 items-start">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl bg-slate-50 border border-slate-100 p-3 sm:p-4 shrink-0">
                                @if($job->company_logo)
                                    <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain" alt="{{ $job->company_name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-2xl font-black text-slate-200">{{ strtoupper(substr($job->company_name ?? 'C', 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-slate-900 text-base sm:text-lg mb-2 break-words">{{ $job->company_name }}</h5>
                                @if($job->employer && $job->employer->company_description)
                                    <p class="text-slate-600 text-sm leading-relaxed mb-4 break-words">{{ $job->employer->company_description }}</p>
                                @else
                                    <p class="text-slate-500 text-sm italic mb-4">Aucune description détaillée n'a été fournie par cette entreprise.</p>
                                @endif
                                @if($job->employer)
                                    <a href="{{ route('public.jobs.index', ['search' => $job->company_name]) }}"
                                       class="inline-flex items-center gap-2 text-sm font-bold text-[#16a3b0] hover:text-[#128a96] transition-colors">
                                        Voir toutes les offres de {{ $job->company_name }} <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CTA Bouton Postuler (bas, après lecture complète) --}}
                    <div class="pt-6 sm:pt-8 border-t border-slate-100">
                        @auth
                            @if(isset($userApplication) && $userApplication)
                                @php
                                    $st = match($userApplication->status) {
                                        'approved','accepted' => ['bg'=>'bg-emerald-50','text'=>'text-emerald-600','border'=>'border-emerald-200','icon'=>'fa-check-circle','label'=>'Acceptée'],
                                        'rejected'  => ['bg'=>'bg-red-50','text'=>'text-red-500','border'=>'border-red-100','icon'=>'fa-times-circle','label'=>'Non retenue'],
                                        'interview' => ['bg'=>'bg-blue-50','text'=>'text-blue-600','border'=>'border-blue-100','icon'=>'fa-calendar-check','label'=>'Entretien prévu'],
                                        'hired'     => ['bg'=>'bg-purple-50','text'=>'text-purple-600','border'=>'border-purple-100','icon'=>'fa-trophy','label'=>'Embauché(e) !'],
                                        default     => ['bg'=>'bg-amber-50','text'=>'text-amber-600','border'=>'border-amber-100','icon'=>'fa-clock','label'=>'En attente de revue'],
                                    };
                                @endphp
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 sm:p-5 {{ $st['bg'] }} border {{ $st['border'] }} rounded-2xl">
                                    <i class="fas {{ $st['icon'] }} text-xl {{ $st['text'] }} shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="font-black text-slate-800 text-sm">Vous avez déjà postulé à cette offre</p>
                                        <p class="text-[10px] font-bold uppercase tracking-widest {{ $st['text'] }} mt-0.5">Statut : {{ $st['label'] }}</p>
                                    </div>
                                    <a href="{{ route('user.applications.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:border-slate-300 transition-all whitespace-nowrap shrink-0">
                                        Mes candidatures
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                    <div class="flex-1">
                                        <p class="font-black text-slate-800 text-base">Vous êtes intéressé(e) par ce poste ?</p>
                                        <p class="text-slate-400 text-sm mt-1">Envoyez votre candidature avec votre CV.</p>
                                    </div>
                                    <a href="{{ route('public.jobs.apply', $job->id) }}"
                                       class="inline-flex items-center justify-center gap-3 w-full sm:w-auto px-6 sm:px-8 py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20 shrink-0">
                                        <i class="fas fa-paper-plane"></i>
                                        Postuler maintenant
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="flex-1">
                                    <p class="font-black text-slate-800 text-base">Intéressé(e) par cette offre ?</p>
                                    <p class="text-slate-400 text-sm mt-1">Connectez-vous ou créez un compte pour postuler en quelques clics.</p>
                                </div>
                                <a href="{{ route('login', ['intended' => route('public.jobs.apply', $job->id)]) }}"
                                   class="inline-flex items-center justify-center gap-3 w-full sm:w-auto px-6 sm:px-8 py-4 bg-[#16a3b0] hover:bg-[#128a96] text-white rounded-2xl font-black text-sm uppercase tracking-[0.15em] transition-all shadow-lg shadow-[#16a3b0]/20 shrink-0">
                                    <i class="fas fa-paper-plane"></i>
                                    Postuler maintenant
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Jobs --}}
        @if($related->isNotEmpty())
        <div class="mt-6 sm:mt-10">
            <h2 class="text-lg font-bold text-slate-900 mb-5">Offres similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($related as $rel)
                    <x-job-card :job="$rel" show-route="public.jobs.show" />
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
