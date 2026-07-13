<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ProConnect') | Plateforme de services & emplois RDC</title>
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
<body class="bg-slate-50 text-slate-800 antialiased">

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
                    <a href="{{ route('public.jobs.index') }}" class="hover:text-[#29B6D1] transition-colors {{ request()->routeIs('public.jobs*') ? 'text-[#29B6D1] font-bold' : '' }}">Offres d'emploi</a>
                    <a href="{{ route('public.artisans.index') }}" class="hover:text-[#29B6D1] transition-colors {{ request()->routeIs('public.artisans*') ? 'text-[#29B6D1] font-bold' : '' }}">Artisans</a>
                </nav>

                {{-- Auth Buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->user_type === 'client' || auth()->user()->role === 'user')
                            {{-- Notification Bell (Client only) --}}
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

                            {{-- Client Dropdown Menu --}}
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
                                        <div class="px-4 py-2 border-b border-slate-100 mb-2">
                                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                        </div>
                                        <a href="{{ route('user.profile') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-user w-5 text-center"></i> Mon profil
                                        </a>
                                        <a href="{{ route('user.applications.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-file-alt w-5 text-center"></i> Mes candidatures
                                        </a>
                                        <a href="{{ route('user.service-requests.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-clipboard-list w-5 text-center"></i> Mes demandes
                                        </a>
                                        <a href="{{ route('user.messages.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-[#29B6D1] rounded-lg transition-colors">
                                            <i class="fas fa-envelope w-5 text-center"></i> Messages
                                        </a>
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-sign-out-alt w-5 text-center"></i> Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-slate-800 to-slate-900 text-white text-sm font-bold rounded-xl hover:shadow-lg transition-all duration-300">Dashboard Admin</a>
                            @else
                                <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-[#29B6D1] to-[#1E9CB5] text-white text-sm font-bold rounded-xl hover:shadow-lg transition-all duration-300">Dashboard</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="p-2.5 text-slate-400 hover:text-red-500 transition-colors border border-slate-200 rounded-xl hover:border-red-200" title="Déconnexion">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-[#29B6D1] transition-colors">Connexion</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-[#29B6D1] text-white text-sm font-bold rounded-xl hover:bg-[#1E9CB5] transition-all shadow-sm">S'inscrire</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

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
                        <li><a href="{{ route('public.jobs.index') }}" class="hover:text-white transition-colors">Offres d'emploi</a></li>
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
