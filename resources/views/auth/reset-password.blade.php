@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="glass-card rounded-3xl p-8 lg:p-10 shadow-2xl">
    <!-- header -->
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-white mb-2">Nouveau mot de passe</h2>
        <p class="text-gray-400">Créez un mot de passe fort pour sécuriser votre compte <span class="text-[#007FFF] font-semibold">ServiceRDC</span>.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl animate-fade-in">
            <ul class="text-red-200 text-sm">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email (Read-only) -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Adresse email</label>
            <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <i class="fas fa-envelope"></i>
                </span>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ $email ?? old('email') }}"
                    class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium opacity-60 cursor-not-allowed"
                    readonly
                >
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Nouveau mot de passe</label>
            <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                    <i class="fas fa-lock"></i>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="w-full pl-12 pr-12 py-4 rounded-2xl input-premium focus:outline-none"
                    placeholder="••••••••"
                    required
                >
                <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Confirmer le mot de passe</label>
            <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                    <i class="fas fa-shield-check"></i>
                </span>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation"
                    class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                    placeholder="••••••••"
                    required
                >
            </div>
        </div>

        <button type="submit" class="w-full py-4 px-6 btn-primary text-white font-bold rounded-2xl shadow-xl hover:shadow-[#007FFF]/20 transition-all flex items-center justify-center group">
            <span>Réinitialiser le mot de passe</span>
            <i class="fas fa-check-circle ml-2 group-hover:scale-110 transition-transform"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>
@endpush