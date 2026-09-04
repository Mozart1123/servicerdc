@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">

    <!-- Background Gradient Blob -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-6 sm:mb-8 text-center lg:text-left">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1.5 sm:mb-2 font-heading">Nouveau mot de passe</h2>
        <p class="text-slate-500 text-xs sm:text-sm">Créez un mot de passe fort pour sécuriser votre compte <span class="text-rdc-blue font-semibold">ProConnect</span>.</p>
    </div>

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

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4 sm:space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email (Read-only) -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <i class="fas fa-envelope text-sm"></i>
            </div>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ $email ?? old('email') }}"
                class="premium-input placeholder-transparent peer opacity-60 cursor-not-allowed"
                placeholder="Email"
                readonly
            >
            <label for="email" class="premium-label">
                Adresse Email
            </label>
        </div>

        <!-- Password Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <input type="password" id="password" name="password" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100"
                   placeholder="Nouveau mot de passe" required>
            <label for="password" class="premium-label">
                Nouveau mot de passe
            </label>
            <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rdc-blue transition-colors cursor-pointer z-10">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <!-- Confirm Password Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <i class="fas fa-shield-halved text-sm"></i>
            </div>
            <input type="password" id="password_confirmation" name="password_confirmation" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100"
                   placeholder="Confirmer le mot de passe" required>
            <label for="password_confirmation" class="premium-label">
                Confirmer le mot de passe
            </label>
        </div>

        <button type="submit" class="w-full py-3.5 sm:py-4 px-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
            <span class="relative z-10 flex items-center justify-center gap-2 text-sm sm:text-base">
                Réinitialiser le mot de passe
                <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
            </span>
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
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
