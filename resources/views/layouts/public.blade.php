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
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ mobileNavOpen: false }" @keydown.escape.window="mobileNavOpen = false">

    {{-- Header — même design que la page d'accueil (welcome.blade.php) pour une identité
         visuelle cohérente sur tout le site. Le bouton de géolocalisation n'a pas été repris
         ici : son comportement dépend d'éléments (#current-city, #current-address) qui
         n'existent que sur la page d'accueil, donc il ne servirait à rien sur les autres pages. --}}
    <header class="fixed inset-x-0 top-0 z-50 bg-white shadow-lg">
        <div class="flag-stripe"></div>
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 group shrink-0 lg:mr-8">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden bg-white flex items-center justify-center
                                group-hover:scale-105 transition-transform duration-300 shadow-lg shrink-0">
                        <img src="/assets/img/logo.png?v=1.2" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Pro<span class="text-rdc-blue">Connect</span></h1>
                        <p class="text-[10px] text-gray-600 hidden xs:block truncate">Plateforme de services & emplois</p>
                    </div>
                </a>

                {{-- Nav Links --}}
                <nav class="hidden lg:flex space-x-8 items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300
                              relative after:absolute after:bottom-0 after:left-0 after:h-0.5
                              after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                        Accueil
                    </a>
                    <a href="{{ route('public.services.index') }}" class="font-medium transition-colors duration-300
                              relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-rdc-blue after:transition-all hover:after:w-full
                              {{ request()->routeIs('public.services*') ? 'text-rdc-blue font-bold after:w-full' : 'text-gray-700 hover:text-rdc-blue after:w-0' }}">
                        Services
                    </a>
                    @if(config('features.recruitment_enabled'))
                        <a href="{{ route('public.jobs.index') }}" class="font-medium transition-colors duration-300
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-rdc-blue after:transition-all hover:after:w-full
                                  {{ request()->routeIs('public.jobs*') ? 'text-rdc-blue font-bold after:w-full' : 'text-gray-700 hover:text-rdc-blue after:w-0' }}">
                            Offres d'emploi
                        </a>
                    @endif
                    <a href="{{ route('public.artisans.index') }}" class="font-medium transition-colors duration-300
                              relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-rdc-blue after:transition-all hover:after:w-full
                              {{ request()->routeIs('public.artisans*') ? 'text-rdc-blue font-bold after:w-full' : 'text-gray-700 hover:text-rdc-blue after:w-0' }}">
                        Artisans
                    </a>
                    <a href="{{ route('contact') }}" class="font-medium transition-colors duration-300
                              relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-rdc-blue after:transition-all hover:after:w-full
                              {{ request()->routeIs('contact') ? 'text-rdc-blue font-bold after:w-full' : 'text-gray-700 hover:text-rdc-blue after:w-0' }}">
                        Contact
                    </a>
                </nav>

                {{-- Boutons CTA et Auth --}}
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @guest
                        <div class="hidden lg:flex items-center space-x-2 sm:space-x-3">
                            <a href="{{ route('login') }}" class="px-3 sm:px-5 py-2 sm:py-2.5 text-rdc-blue text-sm sm:text-base font-semibold hover:text-rdc-blue-dark
                                      transition-colors duration-300 border border-rdc-blue/30
                                      rounded-lg hover:border-rdc-blue/50">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark
                                      text-white text-sm sm:text-base font-semibold rounded-lg hover:shadow-lg
                                      transition-all duration-300 hover:scale-105">
                                Inscription
                            </a>
                        </div>
                    @endguest

                    @auth
                        <div class="hidden lg:flex items-center space-x-4">
                            {{-- Cloche de notifications (tous les utilisateurs connectés) --}}
                            @php
                                $pubUnreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)->count();
                            @endphp
                            <a href="{{ route('user.notifications.index') }}"
                               class="relative p-2 text-gray-500 hover:text-rdc-blue transition-colors rounded-lg hover:bg-blue-50"
                               title="Notifications">
                                <i class="fas fa-bell text-xl"></i>
                                @if($pubUnreadCount > 0)
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-rdc-red text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white">
                                        {{ $pubUnreadCount > 9 ? '9+' : $pubUnreadCount }}
                                    </span>
                                @endif
                            </a>

                            {{-- Dropdown Menu (adapté au rôle réel) --}}
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none py-2">
                                    <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" class="w-10 h-10 rounded-full border-2 border-rdc-blue object-cover">
                                    <span class="font-bold text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block transition-transform" :class="{'rotate-180': open}"></i>
                                </button>

                                <div x-show="open" style="display: none;"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                     class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 z-50 origin-top-right">
                                    <div class="p-2 space-y-1">
                                        {{-- En-tête nom/email --}}
                                        <div class="px-4 py-2 border-b border-gray-100 mb-2">
                                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                        </div>

                                        {{-- Tableau de bord : dynamique selon le rôle --}}
                                        <a href="{{ route(auth()->user()->dashboard_route) }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rdc-blue rounded-lg transition-colors">
                                            <i class="fas fa-gauge w-5 text-center"></i> Tableau de bord
                                        </a>

                                        {{-- Liens client/artisan/recruiter : uniquement pour role === 'user' --}}
                                        @if(auth()->user()->role === 'user')
                                            <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rdc-blue rounded-lg transition-colors">
                                                <i class="fas fa-user w-5 text-center"></i> Mon profil
                                            </a>
                                            @if(config('features.recruitment_enabled'))
                                                <a href="{{ route('user.applications.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rdc-blue rounded-lg transition-colors">
                                                    <i class="fas fa-file-alt w-5 text-center"></i> Mes candidatures
                                                </a>
                                            @endif
                                            <a href="{{ route('user.service-requests.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rdc-blue rounded-lg transition-colors">
                                                <i class="fas fa-clipboard-list w-5 text-center"></i> Mes demandes
                                            </a>
                                            <a href="{{ route('user.messages.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rdc-blue rounded-lg transition-colors">
                                                <i class="fas fa-envelope w-5 text-center"></i> Messages
                                            </a>
                                        @endif

                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-sign-out-alt w-5 text-center"></i> Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth

                    {{-- Mobile Menu Toggle --}}
                    <button @click="mobileNavOpen = true" class="lg:hidden p-2 text-gray-700 hover:text-rdc-blue transition-colors" aria-label="Ouvrir le menu">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="mobileNavOpen" x-transition.opacity
         @click="mobileNavOpen = false"
         class="fixed inset-0 bg-gray-900/50 z-[60] lg:hidden" style="display: none;"></div>

    {{-- Mobile Sidebar Drawer --}}
    <aside :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white z-[70] transform lg:hidden transition-transform duration-300 ease-in-out flex flex-col shadow-2xl">

        <div class="px-6 py-6 border-b border-gray-100 flex items-center justify-between shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full overflow-hidden shadow-sm">
                    <img src="/assets/img/logo.png?v=1.2" alt="ProConnect" class="w-full h-full object-contain">
                </div>
                <h1 class="text-lg font-bold text-gray-900">Pro<span class="text-rdc-blue">Connect</span></h1>
            </a>
            <button @click="mobileNavOpen = false" class="text-gray-400 hover:text-gray-600 p-1" aria-label="Fermer le menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
            @auth
                <div class="flex items-center gap-3 px-3 pb-4 mb-2 border-b border-gray-100">
                    <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" class="w-11 h-11 rounded-full border-2 border-rdc-blue object-cover shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            @endauth

            <a href="{{ route('home') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                <i class="fas fa-home w-5 text-center text-gray-400"></i> Accueil
            </a>
            <a href="{{ route('public.services.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.services*') ? 'bg-rdc-blue/10 text-rdc-blue' : 'text-gray-700 hover:bg-rdc-blue/5 hover:text-rdc-blue' }}">
                <i class="fas fa-tools w-5 text-center {{ request()->routeIs('public.services*') ? 'text-rdc-blue' : 'text-gray-400' }}"></i> Services
            </a>
            @if(config('features.recruitment_enabled'))
                <a href="{{ route('public.jobs.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.jobs*') ? 'bg-rdc-blue/10 text-rdc-blue' : 'text-gray-700 hover:bg-rdc-blue/5 hover:text-rdc-blue' }}">
                    <i class="fas fa-briefcase w-5 text-center {{ request()->routeIs('public.jobs*') ? 'text-rdc-blue' : 'text-gray-400' }}"></i> Offres d'emploi
                </a>
            @endif
            <a href="{{ route('public.artisans.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('public.artisans*') ? 'bg-rdc-blue/10 text-rdc-blue' : 'text-gray-700 hover:bg-rdc-blue/5 hover:text-rdc-blue' }}">
                <i class="fas fa-user-gear w-5 text-center {{ request()->routeIs('public.artisans*') ? 'text-rdc-blue' : 'text-gray-400' }}"></i> Artisans
            </a>
            <a href="{{ route('contact') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('contact') ? 'bg-rdc-blue/10 text-rdc-blue' : 'text-gray-700 hover:bg-rdc-blue/5 hover:text-rdc-blue' }}">
                <i class="fas fa-headset w-5 text-center {{ request()->routeIs('contact') ? 'text-rdc-blue' : 'text-gray-400' }}"></i> Contact / Support
            </a>

            @auth
                <div class="border-t border-gray-100 my-3"></div>

                <a href="{{ route(auth()->user()->dashboard_route) }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                    <i class="fas fa-gauge w-5 text-center text-gray-400"></i> Tableau de bord
                </a>
                @php
                    $pubMobileUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <a href="{{ route('user.notifications.index') }}" @click="mobileNavOpen = false" class="flex items-center justify-between px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                    <span class="flex items-center gap-3"><i class="fas fa-bell w-5 text-center text-gray-400"></i> Notifications</span>
                    @if($pubMobileUnreadCount > 0)
                        <span class="w-5 h-5 bg-rdc-red text-white text-[10px] font-black flex items-center justify-center rounded-full">{{ $pubMobileUnreadCount > 9 ? '9+' : $pubMobileUnreadCount }}</span>
                    @endif
                </a>

                @if(auth()->user()->role === 'user')
                    <a href="{{ route('user.profile') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-user w-5 text-center text-gray-400"></i> Mon profil
                    </a>
                    @if(config('features.recruitment_enabled'))
                        <a href="{{ route('user.applications.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                            <i class="fas fa-file-alt w-5 text-center text-gray-400"></i> Mes candidatures
                        </a>
                    @endif
                    <a href="{{ route('user.service-requests.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-clipboard-list w-5 text-center text-gray-400"></i> Mes demandes
                    </a>
                    <a href="{{ route('user.messages.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-700 font-medium hover:bg-rdc-blue/5 hover:text-rdc-blue transition-colors">
                        <i class="fas fa-envelope w-5 text-center text-gray-400"></i> Messages
                    </a>
                @endif
            @endauth
        </nav>

        <div class="p-4 border-t border-gray-100 shrink-0">
            @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            @else
                <div class="space-y-2">
                    <a href="{{ route('register') }}" @click="mobileNavOpen = false" class="flex items-center justify-center px-4 py-3 bg-rdc-blue text-white text-sm font-bold rounded-xl hover:bg-rdc-blue-dark transition-all shadow-sm">
                        Créer un compte
                    </a>
                    <a href="{{ route('login') }}" @click="mobileNavOpen = false" class="flex items-center justify-center px-4 py-3 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                        Connexion
                    </a>
                </div>
            @endauth
        </div>
    </aside>

    {{-- Main Content (pt-24 clears the fixed header; flash messages, when present,
         sit inside it so the header-clearing offset is never applied twice) --}}
    <main class="min-h-screen pt-24">
        @if(session('success') || session('error'))
        <div class="container mx-auto px-4 pb-4">
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

        @yield('content')
    </main>

    {{-- Footer — même design (structure, réseaux sociaux, newsletter) que la page d'accueil --}}
    <footer class="bg-gradient-to-b from-gray-900 to-rdc-dark-blue text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                {{-- Logo et description --}}
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center">
                            <img src="/assets/img/logo.png?v=1.2" alt="ProConnect Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Pro<span class="text-rdc-blue">Connect</span></h2>
                            <p class="text-sm text-gray-400">Fierté congolaise</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        La plateforme de référence pour trouver des artisans qualifiés
                        et des opportunités d'emploi dans toute la République Démocratique du Congo.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-rdc-blue
                                           flex items-center justify-center transition-colors
                                           duration-300" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-blue-400
                                           flex items-center justify-center transition-colors
                                           duration-300" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-rdc-red
                                           flex items-center justify-center transition-colors
                                           duration-300" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-blue-700
                                           flex items-center justify-center transition-colors
                                           duration-300" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                {{-- Liens Services --}}
                <div>
                    <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                        <i class="fas fa-tools mr-3 text-rdc-yellow"></i>
                        Services
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('public.artisans.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Trouver un artisan
                            </a></li>
                        <li><a href="{{ route('public.services.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Catégories de services
                            </a></li>
                        <li><a href="{{ route('public.artisans.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Artisans vérifiés
                            </a></li>
                        <li><a href="{{ route('public.artisans.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Évaluations & avis
                            </a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Demande personnalisée
                            </a></li>
                    </ul>
                </div>

                @if(config('features.recruitment_enabled'))
                {{-- Liens Emplois --}}
                <div>
                    <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                        <i class="fas fa-briefcase mr-3 text-rdc-yellow"></i>
                        Emplois
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('public.jobs.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Offres d'emploi
                            </a></li>
                        <li><a href="{{ route('public.jobs.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Postuler en ligne
                            </a></li>
                        <li><a href="{{ route('public.jobs.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                CV en ligne
                            </a></li>
                        <li><a href="{{ route('public.jobs.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Conseils carrière
                            </a></li>
                        <li><a href="{{ route('public.jobs.index') }}" class="text-gray-400 hover:text-rdc-yellow
                                           transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                Alertes emploi
                            </a></li>
                    </ul>
                </div>
                @endif

                {{-- Contact & Newsletter --}}
                <div>
                    <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                        <i class="fas fa-envelope mr-3 text-rdc-yellow"></i>
                        Restez informé
                    </h3>
                    <p class="text-gray-400 mb-6 text-sm">
                        Inscrivez-vous à notre newsletter pour recevoir les dernières
                        offres et actualités ProConnect.
                    </p>
                    @if(session('success'))
                        <div class="mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-900">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex">
                            <input name="email" type="email" value="{{ old('email') }}" placeholder="Votre email" class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700
                                          rounded-l-lg focus:outline-none focus:border-rdc-yellow
                                          text-white placeholder-gray-500">
                            <button type="submit" class="px-4 py-3 bg-rdc-yellow text-gray-900 font-semibold
                                           rounded-r-lg hover:bg-yellow-500 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-rose-200">{{ $message }}</p>
                        @enderror
                    </form>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-phone mr-3 text-rdc-yellow"></i>
                            <span>+243 81 234 5678</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-envelope mr-3 text-rdc-yellow"></i>
                            <span>contact@proconnect.cd</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-map-marker-alt mr-3 text-rdc-yellow"></i>
                            <span>Gombe, Kinshasa, RDC</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="pt-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-500 text-sm">
                            &copy; {{ date('Y') }} ProConnect. Tous droits réservés.
                            <span class="text-rdc-yellow">🇨🇩 Fièrement congolais</span>
                        </p>
                    </div>
                    <div class="flex space-x-6 text-sm text-gray-500">
                        <a href="{{ route('privacy') }}" class="hover:text-rdc-yellow transition-colors">
                            Politique de confidentialité
                        </a>
                        <a href="{{ route('terms') }}" class="hover:text-rdc-yellow transition-colors">
                            Conditions d'utilisation
                        </a>
                        <a href="{{ route('legal') }}" class="hover:text-rdc-yellow transition-colors">
                            Mentions légales
                        </a>
                        <a href="{{ route('sitemap') }}" class="hover:text-rdc-yellow transition-colors">
                            Plan du site
                        </a>
                    </div>
                </div>
            </div>

            {{-- Retour en haut --}}
            <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-rdc-blue
                           to-rdc-blue-dark text-white rounded-full shadow-2xl
                           hover:shadow-rdc-blue/30 hover:scale-110 transition-all
                           duration-300 flex items-center justify-center z-40 hidden">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Bouton retour en haut (même comportement que sur la page d'accueil)
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.classList.remove('hidden');
                        backToTopBtn.classList.add('flex');
                    } else {
                        backToTopBtn.classList.add('hidden');
                        backToTopBtn.classList.remove('flex');
                    }
                });

                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
