@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
    <div class="glass-card rounded-3xl p-8 lg:p-10 shadow-2xl">
        <!-- header -->
        <div class="mb-10 text-center lg:text-left">
            <h2 class="text-3xl font-bold text-white mb-2">Créer un compte</h2>
            <p class="text-gray-400">Rejoignez la communauté <span class="text-[#007FFF] font-semibold">ServiceRDC</span>
                dès aujourd'hui.</p>
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

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Nom complet</label>
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                            placeholder="Jean Kabongo">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Téléphone</label>
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                            placeholder="+243 ...">
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Adresse email</label>
                <div class="relative group">
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                        placeholder="nom@exemple.com">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2 ml-1">Mot de passe</label>
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-12 pr-12 py-4 rounded-2xl input-premium focus:outline-none"
                            placeholder="••••••••">
                        <button type="button"
                            class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-300 mb-2 ml-1">Confirmation</label>
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-[#007FFF] transition-colors">
                            <i class="fas fa-shield-check"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full pl-12 pr-4 py-4 rounded-2xl input-premium focus:outline-none"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- User Type -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-3 ml-1">Je suis un(e)...</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="user_type" value="client" class="peer sr-only" required {{ old('user_type') == 'client' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-2xl border border-white/10 bg-white/5 text-center transition-all peer-checked:border-[#007FFF] peer-checked:bg-[#007FFF]/10 group-hover:bg-white/10">
                            <i class="fas fa-user-tie mb-2 text-xl block text-gray-400 peer-checked:text-[#007FFF]"></i>
                            <span class="text-xs font-semibold text-gray-300 peer-checked:text-white">Client</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="user_type" value="artisan" class="peer sr-only" {{ old('user_type') == 'artisan' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-2xl border border-white/10 bg-white/5 text-center transition-all peer-checked:border-[#F7D618] peer-checked:bg-[#F7D618]/10 group-hover:bg-white/10">
                            <i class="fas fa-tools mb-2 text-xl block text-gray-400 peer-checked:text-[#F7D618]"></i>
                            <span class="text-xs font-semibold text-gray-300 peer-checked:text-white">Artisan</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="user_type" value="job_seeker" class="peer sr-only" {{ old('user_type') == 'job_seeker' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-2xl border border-white/10 bg-white/5 text-center transition-all peer-checked:border-[#CE1021] peer-checked:bg-[#CE1021]/10 group-hover:bg-white/10">
                            <i class="fas fa-briefcase mb-2 text-xl block text-gray-400 peer-checked:text-[#CE1021]"></i>
                            <span class="text-xs font-semibold text-gray-300 peer-checked:text-white">Emploi</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-start ml-1">
                <label class="relative flex items-center cursor-pointer group">
                    <input type="checkbox" name="terms" required class="peer sr-only">
                    <div
                        class="w-5 h-5 border-2 border-gray-600 rounded-md peer-checked:bg-[#007FFF] peer-checked:border-[#007FFF] transition-all flex items-center justify-center">
                        <i
                            class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                    </div>
                    <span class="ml-3 text-sm text-gray-400">J'accepte les <a href="#"
                            class="text-[#007FFF] hover:underline">conditions</a></span>
                </label>
            </div>

            <button type="submit"
                class="w-full py-4 px-6 btn-primary text-white font-bold rounded-2xl shadow-xl hover:shadow-[#007FFF]/20 transition-all flex items-center justify-center group">
                <span>S'inscrire sur ServiceRDC</span>
                <i class="fas fa-id-card ml-2 group-hover:rotate-12 transition-transform"></i>
            </button>

            <!-- Social Divider -->
            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-slate-900 px-3 text-gray-500 font-semibold tracking-wider">Ou s'inscrire avec</span>
                </div>
            </div>

            <!-- Social Register -->
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ url('auth/google') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-semibold text-gray-300">Google</span>
                </a>
                <a href="{{ url('auth/facebook') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group">
                    <i class="fab fa-facebook text-[#1877F2] text-lg"></i>
                    <span class="text-sm font-semibold text-gray-300">Facebook</span>
                </a>
            </div>
        </form>

        <div class="mt-8 text-center text-gray-400 text-sm">
            Déjà membre ? <a href="{{ route('login') }}" class="text-[#007FFF] font-semibold hover:underline">Se
                connecter</a>
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