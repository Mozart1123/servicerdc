@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="glass-panel p-8 md:p-10 rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">
    
    <!-- Background Gradient Blob -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-slate-900 mb-2 font-heading">Bon retour !</h2>
        <p class="text-slate-500 text-sm">Entrez vos identifiants pour accéder à votre espace.</p>
    </div>

    <!-- Error Messages -->
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

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-3.5 text-slate-400 pointer-events-none">
                <i class="fas fa-envelope"></i>
            </div>
            <input type="email" id="email" name="email" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                   placeholder="Email" value="{{ old('email') }}" required autofocus>
            <label for="email" class="premium-label ml-5 peer-placeholder-shown:text-slate-400 peer-focus:text-rdc-blue">
                Adresse Email
            </label>
        </div>

        <!-- Password Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-3.5 text-slate-400 pointer-events-none">
                <i class="fas fa-lock"></i>
            </div>
            <input type="password" id="password" name="password" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                   placeholder="Mot de passe" required>
            <label for="password" class="premium-label ml-5 peer-placeholder-shown:text-slate-400 peer-focus:text-rdc-blue">
                Mot de passe
            </label>
            <button type="button" class="toggle-password absolute right-4 top-3.5 text-slate-400 hover:text-rdc-blue transition-colors cursor-pointer z-10">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <!-- Remember & Forgot Password -->
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer group select-none">
                <div class="relative">
                    <input type="checkbox" name="remember" class="peer sr-only">
                    <div class="w-5 h-5 border-2 border-slate-300 rounded bg-white peer-checked:bg-rdc-blue peer-checked:border-rdc-blue transition-all shadow-sm"></div>
                    <i class="fas fa-check absolute top-0.5 left-0.5 text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </div>
                <span class="text-slate-500 group-hover:text-slate-700 transition-colors font-medium">Se souvenir de moi</span>
            </label>
            
            <a href="{{ route('password.request') }}" class="text-rdc-blue hover:text-rdc-blue-dark transition-colors font-semibold hover:underline decoration-rdc-blue/30 underline-offset-4">
                Mot de passe oublié ?
            </a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
            <span class="relative z-10 flex items-center justify-center gap-2">
                Se connecter
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </span>
            <!-- Shine Effect -->
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
        </button>

        <!-- Divider -->
        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white/80 backdrop-blur-sm px-3 text-slate-400 font-semibold tracking-wider">Ou continuer avec</span>
            </div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ url('auth/google') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all group shadow-sm hover:shadow-md">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                <span class="text-sm font-semibold text-slate-600 group-hover:text-slate-900">Google</span>
            </a>
            <a href="{{ url('auth/facebook') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all group shadow-sm hover:shadow-md">
                <i class="fab fa-facebook text-[#1877F2] text-lg group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-semibold text-slate-600 group-hover:text-slate-900">Facebook</span>
            </a>
        </div>
    </form>

    <!-- Footer Register Link -->
    <div class="mt-8 text-center text-sm text-slate-500">
        Pas encore inscrit ? 
        <a href="{{ route('register') }}" class="text-rdc-dark-blue font-bold hover:text-rdc-blue transition-colors ml-1 relative inline-block group">
            Créer un compte
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-rdc-yellow transition-all duration-300 group-hover:w-full"></span>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const input = this.previousElementSibling.previousElementSibling; // Input is 2 siblings back because of label
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                icon.classList.add('text-rdc-blue');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                icon.classList.remove('text-rdc-blue');
            }
        });
    });
</script>
@endpush