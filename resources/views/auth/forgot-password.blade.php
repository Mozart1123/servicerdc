@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">

    <!-- Background Gradient Blob -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-6 sm:mb-8 text-center lg:text-left">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1.5 sm:mb-2 font-heading">Mot de passe oublié ?</h2>
        <p class="text-slate-500 text-xs sm:text-sm">Pas de soucis ! Indiquez-nous votre adresse email et nous vous enverrons un lien de réinitialisation.</p>
    </div>

    <!-- Messages -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start animate-fade-in shadow-sm w-full" role="status">
            <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3 flex-shrink-0"></i>
            <span class="text-green-700 text-xs font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-rdc-red/20 rounded-xl flex items-start animate-fade-in shadow-sm w-full" role="alert">
            <i class="fas fa-exclamation-circle text-rdc-red mt-0.5 mr-3 flex-shrink-0"></i>
            <ul class="text-rdc-red text-xs font-medium list-none space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Email Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <i class="fas fa-envelope"></i>
            </div>
            <input type="email" id="email" name="email" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100"
                   placeholder="Email" value="{{ old('email') }}" required autofocus>
            <label for="email" class="premium-label">
                Adresse Email
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 sm:py-4 px-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
            <span class="relative z-10 flex items-center justify-center gap-2 text-sm sm:text-base">
                Envoyer le lien
                <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
            </span>
            <!-- Shine Effect -->
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
        </button>
    </form>

    <div class="mt-6 sm:mt-8 text-center text-[13px] sm:text-sm text-slate-500">
        <a href="{{ route('login') }}" class="text-rdc-dark-blue font-bold hover:text-rdc-blue transition-colors relative inline-block group">
            <i class="fas fa-chevron-left mr-1"></i> Retour à la connexion
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-rdc-yellow transition-all duration-300 group-hover:w-full"></span>
        </a>
    </div>
</div>
@endsection
