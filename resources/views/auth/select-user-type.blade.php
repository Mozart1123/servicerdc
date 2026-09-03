@extends('layouts.auth')

@section('title', 'Choisissez votre profil')

@section('content')
<div class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">
    
    <!-- Background Gradient Blob -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-6 sm:mb-8 text-center">
        <div class="w-16 h-16 bg-rdc-blue/10 rounded-2xl flex items-center justify-center text-rdc-blue text-2xl mx-auto mb-4">
            <i class="fas fa-user-gear"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1.5 sm:mb-2 font-heading">Bienvenue, {{ Auth::user()->name }} !</h2>
        <p class="text-slate-500 text-xs sm:text-sm">Pour finaliser votre compte, choisissez quel type d'utilisateur vous êtes sur <span class="text-rdc-blue font-semibold">ProConnect</span>.</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-rdc-red/20 rounded-xl flex items-start animate-fade-in shadow-sm w-full" role="alert">
            <i class="fas fa-exclamation-circle text-rdc-red mt-0.5 mr-3 flex-shrink-0"></i>
            <ul class="text-rdc-red text-[10px] sm:text-xs font-medium list-none space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('auth.select-user-type.store') }}" class="space-y-6">
        @csrf

        <!-- User Type Selection Cards -->
        <div class="grid grid-cols-1 {{ config('features.recruitment_enabled') ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-4">

            <!-- Client Option -->
            <label class="relative cursor-pointer group">
                <input type="radio" name="user_type" value="client" class="peer sr-only" required {{ old('user_type') == 'client' ? 'checked' : '' }}>
                <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50/70 text-center transition-all peer-checked:border-rdc-blue peer-checked:bg-rdc-blue/10 group-hover:bg-white group-hover:shadow-md flex flex-col items-center justify-center space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 peer-checked:bg-rdc-blue peer-checked:text-white transition-colors">
                        <i class="fas fa-user-tie text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-slate-700 peer-checked:text-rdc-blue uppercase tracking-wider">Client</span>
                        <span class="block text-[10px] text-slate-400 mt-0.5">Commander des services ou poster des offres</span>
                    </div>
                </div>
            </label>

            <!-- Artisan Option -->
            <label class="relative cursor-pointer group">
                <input type="radio" name="user_type" value="artisan" class="peer sr-only" {{ old('user_type') == 'artisan' ? 'checked' : '' }}>
                <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50/70 text-center transition-all peer-checked:border-rdc-yellow peer-checked:bg-rdc-yellow/10 group-hover:bg-white group-hover:shadow-md flex flex-col items-center justify-center space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 peer-checked:bg-rdc-yellow peer-checked:text-white transition-colors">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-slate-700 peer-checked:text-rdc-yellow uppercase tracking-wider">Artisan</span>
                        <span class="block text-[10px] text-slate-400 mt-0.5">Proposer vos prestations & réaliser des demandes</span>
                    </div>
                </div>
            </label>

            @if(config('features.recruitment_enabled'))
                <!-- Recruiter Option -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="user_type" value="recruiter" class="peer sr-only" {{ old('user_type') == 'recruiter' ? 'checked' : '' }}>
                    <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50/70 text-center transition-all peer-checked:border-rdc-red peer-checked:bg-rdc-red/10 group-hover:bg-white group-hover:shadow-md flex flex-col items-center justify-center space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 peer-checked:bg-rdc-red peer-checked:text-white transition-colors">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-rdc-red uppercase tracking-wider">Recruteur</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Publier des offres d'emploi et recruter</span>
                        </div>
                    </div>
                </label>
            @endif

        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-black rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
            <span class="relative z-10 flex items-center justify-center gap-2 text-sm sm:text-base uppercase tracking-widest">
                Valider mon profil
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </span>
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
        </button>

    </form>
</div>
@endsection
