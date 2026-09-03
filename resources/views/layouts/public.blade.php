<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ProConnect') | Plateforme de services & emplois RDC</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    <meta name="description" content="@yield('meta_description', 'Trouvez des artisans qualifiés et des offres d\'emploi en République Démocratique du Congo.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Poppins', sans-serif; }
        .flag-stripe { height: 4px; background: linear-gradient(90deg, #007A5E 33.3%, #F7D000 33.3% 66.6%, #CE1020 66.6%); }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ mobileNavOpen: false }" @keydown.escape.window="mobileNavOpen = false">

    {{-- Header --}}
    <header class="fixed inset-x-0 top-0 z-50 bg-white shadow-md">
        <div class="flag-stripe"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-full overflow-hidden shadow-md group-hover:scale-105 transition-transform">
                        <img src="/assets/img/logo.png" alt="ProConnect" class="w-full h-full object-contain">
                    </div>
                    <span class="text-lg font-bold text-slate-900">Pro<span class="text-[#29B6D1]">Connect</span></span>
                </a>

                {{-- Nav Links --}}
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('public.services.index') }}" class="hover:text-[#29B6D1] transition-colors {{ request()->routeIs('public.services*') ? 'text-[#29B6D1] font-bold' : '' }}">Services</a>
                    @if(config('features.recruitment_enabled'))
                        <a href="{{ route('public.jobs.index') }}" class="hover:text-[#29B6D1] transition-colors {{ request()->routeIs('public.jobs*') ? 'text-[#29B6D1] font-bold' : '' }}">Offres d'emploi</a>
                    @endif
                    <a href="{{ route('public.artisans.index') }}" class="hover:text-[#29B6D1] transition-colors {{ request()->routeIs('public.artisans*') ? 'text-[#29B6D1] font-bold' : '' }}">Artisans</a>
                </nav>

                {{-- Auth Buttons (desktop only — mobile uses the drawer below) --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        {{-- Cloche de notifications (tous les utilisateurs connectés) --}}
                        @php
                            $pubUnreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                ->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('user.notifications.index') }}"
                           class="relative p-2 text-slate-400 hover:text-[#29B6D1] transition-colors rounded-lg hover:bg-blue-50"
                           title="Notifications">
                            <i class="fas fa-bell text-lg"></i>
                            @if($pubUnreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[8px] font-black flex items-center justify-center rounded-full border-2 border-white animate-pulse">
                                    {{ $pubUnreadCount > 9 ? '9+' : $pubUnreadCount }}
                                </span>
                            @endif
                        </a>

                        {{-- Dropdown Menu (adapté au rôle réel) --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none py-2">
                                <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" class="w-10 h-10 rounded-full border-2 border-[#29B6D1] object-cover">
                                <span class="font-bold text-slate-700 hidden sm:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-slate-400 hidden sm:block transition-transform" :class="{'rotate-180': open}"></i>
                            </button>

                            <div x-show="open" style="display: none;"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                 class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 z-50 origin-top-right">
                                <div class="p-2 space-y-1">
                                    {{-- En-tête nom/email --}}
                                    <div class="px-4 py-2 border-b border-slate-100 mb-2">
                                        <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                    </div>

                                    {{-- Tableau de bord : dynamique selon le rôle --}}
                                    <a href="{{ route(auth()->user()->dashboard_route) }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                        <i class="fas fa-gauge w-5 text-center"></i> Tableau de bord
                                    </a>

                                    {{-- Liens client/artisan/recruiter : uniquement pour role === 'user' --}}
                                    @if(auth()->user()->role === 'user')
                                        <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-user w-5 text-center"></i> Mon profil
                                        </a>
                                        @if(config('features.recruitment_enabled'))
                                            <a href="{{ route('user.applications.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                                <i class="fas fa-file-alt w-5 text-center"></i> Mes candidatures
                                            </a>
                                        @endif
                                        <a href="{{ route('user.service-requests.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-clipboard-list w-5 text-center"></i> Mes demandes
                                        </a>
                                        <a href="{{ route('user.messages.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-envelope w-5 text-center"></i> Messages
                                        </a>
                                    @endif

                                    <div class="border-t border-slate-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 text-center"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-[#29B6D1] transition-colors">Connexion</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-[#29B6D1] text-white text-sm font-bold rounded-xl hover:bg-[#1E9CB5] transition-all shadow-sm">S'inscrire</a>
                    @endauth
                </div>

                {{-- Mobile Menu Toggle --}}
                <button @click="mobileNavOpen = true" class="md:hidden p-2 text-slate-600 hover:text-[#29B6D1] transition-colors" aria-label="Ouvrir le menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="mobileNavOpen" x-transition.opacity
         @click="mobileNavOpen = false"
         class="fixed inset-0 bg-slate-900/50 z-[60] md:hidden" style="display: none;"></div>

    {{-- Mobile Sidebar Drawer --}}
    <aside :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white z-[70] transform md:hidden transition-transform duration-300 ease-in-out flex flex-col shadow-2xl">

        <div class="px-6 py-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full overflow-hidden shadow-sm">
                    <img src="/assets/img/logo.png" alt="ProConnect" class="w-full h-full object-contain">
                </div>
                <span class="text-lg font-bold text-slate-900">Pro<span class="text-[#29B6D1]">Connect</span></span>
            </a>
            <button @click="mobileNavOpen = false" class="text-slate-400 hover:text-slate-600 p-1" aria-label="Fermer le menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
            @auth
                <div class="flex items-center gap-3 px-3 pb-4 mb-2 border-b border-slate-100">
                    <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" class="w-11 h-11 rounded-full border-2 border-[#29B6D1] object-cover shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            @endauth

            <a href="{{ route('home') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                <i class="fas fa-home w-5 text-center text-slate-400"></i> Accueil
            </a>
            <a href="{{ route('public.services.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.services*') ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1]' }}">
                <i class="fas fa-tools w-5 text-center {{ request()->routeIs('public.services*') ? 'text-[#29B6D1]' : 'text-slate-400' }}"></i> Services
            </a>
            @if(config('features.recruitment_enabled'))
                <a href="{{ route('public.jobs.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.jobs*') ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1]' }}">
                    <i class="fas fa-briefcase w-5 text-center {{ request()->routeIs('public.jobs*') ? 'text-[#29B6D1]' : 'text-slate-400' }}"></i> Offres d'emploi
                </a>
            @endif
            <a href="{{ route('public.artisans.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.artisans*') ? 'bg-[#29B6D1]/10 text-[#29B6D1]' : 'text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1]' }}">
                <i class="fas fa-user-gear w-5 text-center {{ request()->routeIs('public.artisans*') ? 'text-[#29B6D1]' : 'text-slate-400' }}"></i> Artisans
            </a>

            @auth
                <div class="border-t border-slate-100 my-3"></div>

                <a href="{{ route(auth()->user()->dashboard_route) }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                    <i class="fas fa-gauge w-5 text-center text-slate-400"></i> Tableau de bord
                </a>
                @php
                    $pubMobileUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <a href="{{ route('user.notifications.index') }}" @click="mobileNavOpen = false" class="flex items-center justify-between px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                    <span class="flex items-center gap-3"><i class="fas fa-bell w-5 text-center text-slate-400"></i> Notifications</span>
                    @if($pubMobileUnreadCount > 0)
                        <span class="w-5 h-5 bg-red-500 text-white text-[10px] font-black flex items-center justify-center rounded-full">{{ $pubMobileUnreadCount > 9 ? '9+' : $pubMobileUnreadCount }}</span>
                    @endif
                </a>

                @if(auth()->user()->role === 'user')
                    <a href="{{ route('user.profile') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                        <i class="fas fa-user w-5 text-center text-slate-400"></i> Mon profil
                    </a>
                    @if(config('features.recruitment_enabled'))
                        <a href="{{ route('user.applications.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                            <i class="fas fa-file-alt w-5 text-center text-slate-400"></i> Mes candidatures
                        </a>
                    @endif
                    <a href="{{ route('user.service-requests.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                        <i class="fas fa-clipboard-list w-5 text-center text-slate-400"></i> Mes demandes
                    </a>
                    <a href="{{ route('user.messages.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:text-[#29B6D1] transition-colors">
                        <i class="fas fa-envelope w-5 text-center text-slate-400"></i> Messages
                    </a>
                @endif
            @endauth
        </nav>

        <div class="p-4 border-t border-slate-100 shrink-0">
            @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            @else
                <div class="space-y-2">
                    <a href="{{ route('register') }}" @click="mobileNavOpen = false" class="flex items-center justify-center px-4 py-3 bg-[#29B6D1] text-white text-sm font-bold rounded-xl hover:bg-[#1E9CB5] transition-all shadow-sm">
                        Créer un compte
                    </a>
                    <a href="{{ route('login') }}" @click="mobileNavOpen = false" class="flex items-center justify-center px-4 py-3 border border-slate-200 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-all">
                        Connexion
                    </a>
                </div>
            @endauth
        </div>
    </aside>

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        @if(session('success'))
            <div class="px-5 py-3 bg-green-50 border border-green-100 rounded-xl text-green-700 font-semibold text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="px-5 py-3 bg-red-50 border border-red-100 rounded-xl text-red-700 font-semibold text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif
    </div>
    @endif

    {{-- Main Content --}}
    <main class="min-h-screen pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white mt-20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-3">Pro<span class="text-[#29B6D1]">Connect</span></h3>
                    <p class="text-slate-400 text-sm leading-relaxed">La plateforme moderne qui connecte artisans, recruteurs et clients en République Démocratique du Congo.</p>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-widest mb-4 text-slate-300">Explorer</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('public.services.index') }}" class="hover:text-white transition-colors">Services</a></li>
                        @if(config('features.recruitment_enabled'))
                            <li><a href="{{ route('public.jobs.index') }}" class="hover:text-white transition-colors">Offres d'emploi</a></li>
                        @endif
                        <li><a href="{{ route('public.artisans.index') }}" class="hover:text-white transition-colors">Annuaire des artisans</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-widest mb-4 text-slate-300">Compte</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        @guest
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Connexion</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Inscription</a></li>
                        @else
                            <li><a href="{{ route('user.profile') }}" class="hover:text-white transition-colors">Mon profil</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-700 mt-10 pt-6 text-center text-xs text-slate-500">
                © {{ date('Y') }} ProConnect RDC. Tous droits réservés.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
