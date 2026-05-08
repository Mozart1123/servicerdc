@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden backdrop-blur-3xl shadow-2xl border border-white/60">
    
    <!-- Background Gradient Blob -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-100/30 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <!-- Header -->
    <div class="mb-6 sm:mb-8 text-center lg:text-left">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1.5 sm:mb-2 font-heading">Créer un compte</h2>
        <p class="text-slate-500 text-xs sm:text-sm">Rejoignez la communauté <span class="text-rdc-blue font-semibold">ServiceRDC</span>.</p>
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

    <form method="POST" action="{{ route('register') }}" class="space-y-4 sm:space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Full Name -->
            <div class="premium-input-group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <input type="text" id="name" name="name" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                       placeholder="Prénom & Nom" value="{{ old('name') }}" required autofocus>
                <label for="name" class="premium-label">
                    Prénom & Nom
                </label>
            </div>

            <!-- Phone -->
            <div class="premium-input-group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-phone text-sm"></i>
                </div>
                <input type="tel" id="phone" name="phone" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                       placeholder="Téléphone" value="{{ old('phone') }}" required>
                <label for="phone" class="premium-label">
                    Téléphone
                </label>
            </div>
        </div>

        <!-- Email Field -->
        <div class="premium-input-group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <i class="fas fa-envelope text-sm"></i>
            </div>
            <input type="email" id="email" name="email" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                   placeholder="Email" value="{{ old('email') }}" required>
            <label for="email" class="premium-label">
                Adresse Email
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Password Field -->
            <div class="premium-input-group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-lock text-sm"></i>
                </div>
                <input type="password" id="password" name="password" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                       placeholder="Pass" required>
                <label for="password" class="premium-label">
                    Mot de passe
                </label>
                <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rdc-blue transition-colors cursor-pointer z-10">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <!-- Confirm Field -->
            <div class="premium-input-group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-shield-halved text-sm"></i>
                </div>
                <input type="password" id="password_confirmation" name="password_confirmation" class="premium-input placeholder-transparent peer focus:ring-4 focus:ring-blue-100" 
                       placeholder="Confirmer" required>
                <label for="password_confirmation" class="premium-label">
                    Confirmer
                </label>
            </div>
        </div>

        <!-- User Type Selection -->
        <div class="space-y-3">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Je suis un(e)...</label>
            <div class="grid grid-cols-3 gap-2">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="user_type" value="client" class="peer sr-only" required {{ old('user_type') == 'client' ? 'checked' : '' }}>
                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-rdc-blue peer-checked:bg-rdc-blue/10 group-hover:bg-white group-hover:shadow-sm">
                        <i class="fas fa-user-tie mb-1 text-sm block text-slate-400 peer-checked:text-rdc-blue"></i>
                        <span class="text-[9px] font-bold text-slate-500 peer-checked:text-rdc-blue uppercase tracking-tighter">Client</span>
                    </div>
                </label>
                <label class="relative cursor-pointer group">
                    <input type="radio" name="user_type" value="artisan" class="peer sr-only" {{ old('user_type') == 'artisan' ? 'checked' : '' }}>
                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-rdc-yellow peer-checked:bg-rdc-yellow/10 group-hover:bg-white group-hover:shadow-sm">
                        <i class="fas fa-tools mb-1 text-sm block text-slate-400 peer-checked:text-rdc-yellow"></i>
                        <span class="text-[9px] font-bold text-slate-500 peer-checked:text-rdc-yellow uppercase tracking-tighter">Artisan</span>
                    </div>
                </label>
                <label class="relative cursor-pointer group">
                    <input type="radio" name="user_type" value="job_seeker" class="peer sr-only" {{ old('user_type') == 'job_seeker' ? 'checked' : '' }}>
                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-rdc-red peer-checked:bg-rdc-red/10 group-hover:bg-white group-hover:shadow-sm">
                        <i class="fas fa-briefcase mb-1 text-sm block text-slate-400 peer-checked:text-rdc-red"></i>
                        <span class="text-[9px] font-bold text-slate-500 peer-checked:text-rdc-red uppercase tracking-tighter">Emploi</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-start ml-1 py-1">
            <label class="flex items-center gap-3 cursor-pointer group select-none">
                <div class="relative shrink-0">
                    <input type="checkbox" name="terms" required class="peer sr-only">
                    <div class="w-5 h-5 border-2 border-slate-300 rounded bg-white peer-checked:bg-rdc-blue peer-checked:border-rdc-blue transition-all shadow-sm"></div>
                    <i class="fas fa-check absolute top-0.5 left-0.5 text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </div>
                <span class="text-[11px] sm:text-xs text-slate-500 font-medium">J'accepte les <a href="#" class="text-rdc-blue font-bold hover:underline">conditions d'utilisation</a></span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-black rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
            <span class="relative z-10 flex items-center justify-center gap-2 text-sm sm:text-base uppercase tracking-widest">
                S'inscrire
                <i class="fas fa-id-card group-hover:translate-x-1 transition-transform"></i>
            </span>
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
        </button>

        <!-- Divider -->
        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-[10px] uppercase">
                <span class="bg-white/80 backdrop-blur-sm px-3 text-slate-400 font-semibold tracking-wider">Ou</span>
            </div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ url('auth/google') }}" class="flex items-center justify-center gap-2 px-3 py-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all group shadow-sm hover:shadow-md">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900">Google</span>
            </a>
            <a href="{{ url('auth/facebook') }}" class="flex items-center justify-center gap-2 px-3 py-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all group shadow-sm hover:shadow-md">
                <i class="fab fa-facebook text-[#1877F2] text-lg group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900">Facebook</span>
            </a>
        </div>
    </form>

    <div class="mt-6 text-center text-[13px] sm:text-sm text-slate-500">
        Déjà membre ? 
        <a href="{{ route('login') }}" class="text-rdc-dark-blue font-bold hover:text-rdc-blue transition-colors ml-1 relative inline-block group">
            Se connecter
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-rdc-yellow transition-all duration-300 group-hover:w-full"></span>
        </a>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function () {
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