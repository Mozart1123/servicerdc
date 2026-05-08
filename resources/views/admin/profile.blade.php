@extends('layouts.admin')

@section('title', 'Mon Profil')
@section('header_title', 'Configuration du Profil')

@section('content')
<div class="space-y-6">
    <!-- Header Hero -->
    <div class="relative bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-rdc-blue to-blue-800"></div>
        <div class="px-6 pb-6 relative">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-end gap-6 -mt-12">
                    <div class="relative group">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-white flex items-center justify-center">
                            @if(isset($user->avatar))
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-rdc-blue text-white flex items-center justify-center text-4xl font-bold font-heading">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                        <button class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-rdc-blue transition-colors shadow-sm">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                    </div>
                    <div class="mb-2">
                        <h1 class="text-2xl font-bold font-heading text-slate-900">{{ $user->name }}</h1>
                        <div class="flex items-center gap-3 mt-1 text-sm">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-rdc-blue font-medium border border-blue-100">
                                <i class="fas fa-shield-alt text-xs"></i>
                                {{ str_replace('_', ' ', Str::title($user->role)) }}
                            </span>
                            <span class="text-slate-500">
                                <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> RDC HQ Station
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 font-medium transition-colors text-sm flex-1 sm:flex-none text-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Sidebar - Menu & Stats -->
        <div class="space-y-6">
            <!-- Navigation Tabs (CSS controlled for SPA feel if needed, here just anchors or sections) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-2">
                <nav class="space-y-1">
                    <a href="#personal-info" class="flex items-center gap-3 px-4 py-3 text-slate-900 bg-slate-50 font-semibold rounded-xl transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-rdc-blue border border-slate-200">
                            <i class="fas fa-user"></i>
                        </div>
                        Informations Personnelles
                    </a>
                    <a href="#security" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 border border-slate-200">
                            <i class="fas fa-lock"></i>
                        </div>
                        Sécurité & Mot de passe
                    </a>
                    <a href="#notifications" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 border border-slate-200">
                            <i class="fas fa-bell"></i>
                        </div>
                        Préférences de Notification
                    </a>
                </nav>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Activité Système</h2>
                    <i class="fas fa-chart-line text-slate-400"></i>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 text-sm">Date d'inscription</span>
                        <span class="font-medium text-slate-900">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 text-sm">Dernière connexion</span>
                        <span class="font-medium text-slate-900">Aujourd'hui, 08:30</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 text-sm">Statut du compte</span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Actif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 text-sm">Niveau d'accès</span>
                        <span class="font-medium text-slate-900">Privilèges Administratifs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information Form -->
            <div id="personal-info" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-24">
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <h2 class="text-lg font-bold font-heading text-slate-900">Informations Personnelles</h2>
                    <p class="text-sm text-slate-500 mt-1">Mettez à jour vos informations de contact et votre profil public.</p>
                </div>
                <div class="p-5 sm:p-6">
                    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2" for="name">Nom Complet</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-slate-400"></i>
                                    </div>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-colors @error('name') border-red-300 @enderror" required>
                                </div>
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2" for="email">Adresse Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400"></i>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-colors @error('email') border-red-300 @enderror" required>
                                </div>
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2" for="phone">Numéro de Téléphone</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-slate-400"></i>
                                    </div>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-colors @error('phone') border-red-300 @enderror" placeholder="+243 ...">
                                </div>
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-rdc-blue text-white font-semibold rounded-xl hover:bg-blue-700 shadow-sm shadow-rdc-blue/30 transition-all active:scale-95 flex items-center gap-2">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Form -->
            <div id="security" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-24">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold font-heading text-slate-900">Sécurité & Mot de passe</h2>
                        <p class="text-sm text-slate-500 mt-1">Gérez la sécurité de votre compte administratif.</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                        <i class="fas fa-shield-check text-xl"></i>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2" for="password">Nouveau mot de passe</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-slate-400"></i>
                                    </div>
                                    <input type="password" id="password" name="password" class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-colors">
                                </div>
                                <p class="text-xs text-slate-500 mt-1">Laissez vide si vous ne souhaitez pas modifier.</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2" for="password_confirmation">Confirmer le mot de passe</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-slate-400"></i>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rdc-blue/20 focus:border-rdc-blue transition-colors">
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-slate-900">Authentification à Double Facteur (2FA)</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-sm">Ajoute une couche de sécurité supplémentaire à votre compte en demandant un code lors de la connexion.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mt-1">
                                <input type="checkbox" value="" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rdc-blue"></div>
                            </label>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 border border-slate-200 text-slate-700 bg-white font-semibold rounded-xl hover:bg-slate-50 transition-all active:scale-95 flex items-center gap-2">
                                Mettre à jour la sécurité
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Sessions / Activity -->
            <div id="activity" class="bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <h2 class="text-lg font-bold font-heading text-slate-900">Sessions Actives & Appareils</h2>
                    <p class="text-sm text-slate-500 mt-1">Liste des appareils récemment connectés à votre compte.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    <!-- Current Session -->
                    <div class="p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-rdc-blue flex items-center justify-center">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900 flex items-center gap-2">
                                    Windows 11 · Chrome
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">Actif</span>
                                </p>
                                <p class="text-sm text-slate-500">127.0.0.1 · Kinshasa, RDC</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other Session -->
                    <div class="p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">iPhone 14 · Safari</p>
                                <p class="text-sm text-slate-500">192.168.1.45 · Kinshasa, RDC</p>
                            </div>
                        </div>
                        <p class="text-sm text-slate-400 whitespace-nowrap">Il y a 2 jours</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Simple smooth scrolling for tabs
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
                
                // Update active state
                document.querySelectorAll('nav a').forEach(a => {
                    a.classList.remove('bg-slate-50', 'text-slate-900');
                    a.classList.add('text-slate-600');
                    a.querySelector('.bg-white').classList.remove('text-rdc-blue');
                    a.querySelector('.bg-white').classList.add('text-slate-400');
                });
                
                this.classList.add('bg-slate-50', 'text-slate-900');
                this.classList.remove('text-slate-600');
                this.querySelector('.bg-white').classList.add('text-rdc-blue');
                this.querySelector('.bg-white').classList.remove('text-slate-400');
            }
        });
    });
</script>
@endsection
