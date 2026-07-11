@extends('layouts.public')

@section('title', $job->title . ' — ' . $job->company_name)
@section('meta_description', Str::limit($job->description, 160))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs font-bold text-slate-400 mb-8 flex items-center gap-2">
        <a href="{{ route('public.jobs.index') }}" class="hover:text-[#29B6D1] transition-colors">Offres d'emploi</a>
        <i class="fas fa-chevron-right text-[9px]"></i>
        <span class="text-slate-600">{{ Str::limit($job->title, 50) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left / Main --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Cover Image --}}
            @if($job->cover_image)
            <div class="rounded-3xl overflow-hidden h-52">
                <img src="{{ Storage::url($job->cover_image) }}" alt="{{ $job->title }}" class="w-full h-full object-cover">
            </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center">
                        @if($job->company_logo)
                            <img src="{{ Storage::url($job->company_logo) }}" alt="{{ $job->company_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#29B6D1]/20 to-[#29B6D1]/5 flex items-center justify-center">
                                <i class="fas fa-building text-2xl text-[#29B6D1]/50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-slate-900 leading-tight">{{ $job->title }}</h1>
                        <p class="text-slate-500 font-medium text-sm mt-1">{{ $job->company_name }}</p>
                        <div class="flex flex-wrap gap-3 mt-3">
                            <span class="px-3 py-1 bg-[#29B6D1]/10 text-[#29B6D1] text-[10px] font-black uppercase tracking-wide rounded-full">{{ $job->contract_type }}</span>
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wide rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $job->location }}
                            </span>
                            @if($job->salary_range)
                                <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-wide rounded-full">
                                    <i class="fas fa-dollar-sign mr-1"></i>{{ $job->salary_range }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4">Description du poste</h2>
                <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
                    {!! nl2br(e($job->description)) !!}
                </div>
            </div>

            {{-- Requirements --}}
            @if($job->requirements)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4">Prérequis & Compétences</h2>
                <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
                    {!! nl2br(e($job->requirements)) !!}
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Apply CTA --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                @if($job->deadline)
                    <div class="text-xs font-bold text-orange-500 mb-3">
                        <i class="fas fa-clock mr-1"></i>Date limite : {{ $job->deadline->format('d/m/Y') }}
                    </div>
                @endif

                @auth
                    <a href="{{ route('user.jobs.show', $job->id) }}"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm mb-3">
                        <i class="fas fa-paper-plane mr-2"></i>Postuler maintenant
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm mb-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion pour postuler
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full text-center px-5 py-3 bg-slate-50 text-slate-700 font-bold rounded-2xl hover:bg-slate-100 transition-all text-sm">
                        Créer un compte
                    </a>
                @endauth

                <div class="pt-4 mt-4 border-t border-slate-50 space-y-2 text-xs text-slate-400 font-medium">
                    <div><i class="fas fa-briefcase w-4 mr-2"></i>{{ $job->contract_type }}</div>
                    <div><i class="fas fa-map-marker-alt w-4 mr-2"></i>{{ $job->location }}</div>
                    @if($job->category)
                        <div><i class="fas fa-tag w-4 mr-2"></i>{{ $job->category }}</div>
                    @endif
                    <div><i class="fas fa-calendar w-4 mr-2"></i>Publié {{ $job->created_at->diffForHumans() }}</div>
                </div>
            </div>

            {{-- Recruiter Info --}}
            @if($job->user)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Recruteur</h3>
                <div class="flex items-center gap-3">
                    @if($job->user->profile_photo)
                        <img src="{{ Storage::url($job->user->profile_photo) }}" class="w-12 h-12 rounded-2xl object-cover" alt="">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($job->user->name) }}&background=29B6D1&color=fff&size=100" class="w-12 h-12 rounded-2xl object-cover" alt="">
                    @endif
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $job->user->name }}</div>
                        <div class="text-xs text-slate-400 font-medium">{{ $job->company_name }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Jobs --}}
    @if($related->isNotEmpty())
    <div class="mt-14">
        <h2 class="text-lg font-bold text-slate-900 mb-5">Offres similaires</h2>
        <div class="space-y-3">
            @foreach($related as $rel)
                <a href="{{ route('public.jobs.show', $rel->id) }}"
                   class="flex items-center gap-4 bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md transition-all group">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center">
                        @if($rel->company_logo)
                            <img src="{{ Storage::url($rel->company_logo) }}" class="w-full h-full object-cover" alt="">
                        @else
                            <i class="fas fa-building text-slate-300"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-[#29B6D1] transition-colors truncate">{{ $rel->title }}</h4>
                        <p class="text-xs text-slate-400 font-medium">{{ $rel->company_name }} · {{ $rel->location }}</p>
                    </div>
                    <span class="text-[10px] font-black bg-[#29B6D1]/10 text-[#29B6D1] px-2 py-1 rounded-lg flex-shrink-0">{{ $rel->contract_type }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
