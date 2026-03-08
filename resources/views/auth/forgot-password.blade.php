@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="glass-card rounded-3xl p-8 lg:p-10 shadow-2xl">
        <!-- header -->
        <div class="mb-10 text-center lg:text-left">
            <h2 class="text-3xl font-bold text-white mb-2">Mot de passe oublié ?</h2>
            <p class="text-gray-400">Pas de soucis ! Indiquez-nous votre adresse email et nous vous enverrons un lien de
                réinitialisation.</p>
        </div>

        <!-- Messages -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-2xl animate-fade-in flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <p class="text-green-200 text-sm">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl animate-fade-in">
                <ul class="text-red-200 text-sm">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Votre email</label>
                <div class="relative group">
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                        placeholder="nom@exemple.com" required autofocus>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 px-6 btn-primary text-white font-bold rounded-2xl shadow-xl hover:shadow-[#007FFF]/20 transition-all flex items-center justify-center group">
                <span>Envoyer le lien</span>
                <i
                    class="fas fa-paper-plane ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition text-sm">
                <i class="fas fa-chevron-left mr-1"></i> Retour à la connexion
            </a>
        </div>
    </div>
@endsection