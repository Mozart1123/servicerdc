<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') - ServiceRDC</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#003366',
                        'rdc-success': '#10B981',
                        'rdc-warning': '#F59E0B',
                        'rdc-error': '#EF4444',
                        'rdc-info': '#3B82F6',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif'],
                        'poppins': ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                    },
                    keyframes: {
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #007FFF 0%, #0066CC 50%, #003366 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
        .flag-stripe { height: 5px; background: linear-gradient(90deg, #007FFF 0%, #007FFF 33%, #F0B800 33%, #F0B800 66%, #FF4757 66%, #FF4757 100%); }
        .active-sidebar-item { background: linear-gradient(90deg, rgba(0, 127, 255, 0.1) 0%, rgba(0, 127, 255, 0.05) 100%); border-left: 4px solid #007FFF; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #007FFF; border-radius: 4px; }
    </style>
</head>

<body class="bg-gray-100 h-full" x-data="{ sidebarOpen: false }">
    <div class="flag-stripe fixed top-0 w-full z-[60]"></div>
    
    <div class="flex h-screen pt-1">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="w-72 bg-white shadow-2xl flex flex-col fixed h-screen transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
            <!-- Logo -->
            <div class="px-6 py-8 border-b border-gray-100 bg-slate-50/30">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm flex items-center justify-center p-1.5 bg-white border border-slate-100">
                        <img src="/assets/img/logo.png?v=1.1" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase">Service<span class="text-rdc-blue">RDC</span></h2>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Dashboard Citoyen</p>
                    </div>
                </a>
            </div>

            <!-- Profile & Mode Switcher -->
            <div class="p-6 border-b border-gray-100 group">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="relative flex-shrink-0 group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-rdc-blue to-blue-400 rounded-2xl blur opacity-25 group-hover:opacity-100 transition duration-500"></div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=007FFF&color=fff&size=128" 
                             class="relative w-14 h-14 rounded-2xl border-2 border-white shadow-sm object-cover" alt="Profile">
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate tracking-tight">{{ auth()->user()->name ?? 'Utilisateur' }}</h3>
                        <p class="text-[11px] text-gray-500 flex items-center truncate">
                            <i class="fas fa-map-marker-alt text-rdc-blue mr-1"></i>
                            {{ auth()->user()->city ?? 'Kinshasa, RDC' }}
                        </p>
                    </div>
                </div>

                <!-- Mode Switcher -->
                <div class="bg-gray-50 rounded-2xl p-2 border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2 italic">Mode Actif Matrix</p>
                    <div class="grid grid-cols-3 gap-1">
                        <a href="{{ route('user.switch-type', 'client') }}" 
                           class="flex flex-col items-center justify-center py-2 rounded-xl transition-all {{ session('active_user_type') === 'client' ? 'bg-rdc-blue text-white shadow-md shadow-rdc-blue/20' : 'text-gray-400 hover:bg-white hover:text-rdc-blue' }}">
                            <i class="fas fa-user-tie text-xs mb-1"></i>
                            <span class="text-[8px] font-black uppercase tracking-tighter">Client</span>
                        </a>
                        <a href="{{ route('user.switch-type', 'artisan') }}" 
                           class="flex flex-col items-center justify-center py-2 rounded-xl transition-all {{ session('active_user_type') === 'artisan' ? 'bg-rdc-yellow text-white shadow-md shadow-rdc-yellow/20' : 'text-gray-400 hover:bg-white hover:text-rdc-yellow' }}">
                            <i class="fas fa-tools text-xs mb-1"></i>
                            <span class="text-[8px] font-black uppercase tracking-tighter">Artisan</span>
                        </a>
                        <a href="{{ route('user.switch-type', 'job_seeker') }}" 
                           class="flex flex-col items-center justify-center py-2 rounded-xl transition-all {{ session('active_user_type') === 'job_seeker' ? 'bg-rdc-red text-white shadow-md shadow-rdc-red/20' : 'text-gray-400 hover:bg-white hover:text-rdc-red' }}">
                            <i class="fas fa-briefcase text-xs mb-1"></i>
                            <span class="text-[8px] font-black uppercase tracking-tighter">Emploi</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-6">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Principal</p>
                    <div class="space-y-1">
                        <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.dashboard') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-th-large {{ request()->routeIs('user.dashboard') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Tableau de bord</span>
                        </a>
                        <a href="{{ route('user.profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.profile') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-user-circle {{ request()->routeIs('user.profile') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Mon profil</span>
                        </a>
                        <a href="{{ route('user.messages.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group relative {{ request()->routeIs('user.messages.index') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-envelope {{ request()->routeIs('user.messages.index') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Messages</span>
                            <span class="ml-auto bg-rdc-red text-white text-[10px] px-2 py-0.5 rounded-full">3</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Espace 
                        @if(session('active_user_type') === 'client') Client
                        @elseif(session('active_user_type') === 'artisan') Artisan
                        @else Emploi
                        @endif
                    </p>
                    <div class="space-y-1">
                        @if(session('active_user_type') === 'client')
                            <a href="{{ route('user.services.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.services.*') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-search-plus {{ request()->routeIs('user.services.*') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Trouver un expert</span>
                            </a>
                            <a href="{{ route('user.missions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.missions.*') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-file-invoice {{ request()->routeIs('user.missions.*') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Mes commandes</span>
                            </a>
                        @elseif(session('active_user_type') === 'artisan')
                            <a href="{{ route('user.services.my') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.services.my') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-tools {{ request()->routeIs('user.services.my') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Mes services pro</span>
                            </a>
                            <a href="{{ route('user.missions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.missions.*') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-tasks {{ request()->routeIs('user.missions.*') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Missions reçues</span>
                            </a>
                        @else
                            <a href="{{ route('user.jobs.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.jobs.*') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-briefcase {{ request()->routeIs('user.jobs.*') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Offres d'emploi</span>
                            </a>
                            <a href="{{ route('user.applications.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.applications.index') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                                <i class="fas fa-paper-plane {{ request()->routeIs('user.applications.index') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                                <span class="font-medium">Mes candidatures</span>
                            </a>
                        @endif
                        
                        <a href="{{ route('user.favorites') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.favorites') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-heart {{ request()->routeIs('user.favorites') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Mes favoris</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Compte</p>
                    <div class="space-y-1">
                        <a href="{{ route('user.security') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.security') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-shield-alt {{ request()->routeIs('user.security') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Sécurité</span>
                        </a>
                        <a href="{{ route('user.report') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('user.report') ? 'active-sidebar-item text-rdc-blue' : 'text-gray-600 hover:bg-rdc-blue/5' }}">
                            <i class="fas fa-circle-exclamation {{ request()->routeIs('user.report') ? 'text-rdc-blue' : 'text-gray-400 group-hover:text-rdc-blue' }} w-5"></i>
                            <span class="font-medium">Aide & Support</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-gray-600 rounded-lg hover:bg-rdc-red/5 hover:text-rdc-red transition-colors group">
                                <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-rdc-red w-5"></i>
                                <span class="font-medium">Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Upgrade Banner -->
            <div class="m-6 p-6 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark rounded-2xl text-white shadow-xl">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-crown text-yellow-300"></i>
                    </div>
                    <div>
                        <h4 class="font-bold leading-none">Passer en PRO</h4>
                        <p class="text-[10px] text-white/70 mt-1 uppercase tracking-wider">Top visibilité</p>
                    </div>
                </div>
                <p class="text-xs text-white/90 mb-4 leading-relaxed">Multipliez vos opportunités par 5 avec le compte expert.</p>
                <button class="w-full bg-white text-rdc-blue-dark font-bold py-2.5 rounded-xl text-xs hover:bg-gray-50 transition-colors shadow-lg">
                    Découvrir l'offre
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-72 flex flex-col min-h-screen overflow-y-auto">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 px-8 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-xl">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">@yield('header_title', 'Bienvenue')</h2>
                        <p class="text-xs text-gray-500 font-medium">@yield('header_subtitle', 'Gérez vos activités en toute simplicité')</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden md:relative md:block">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Rechercher..." class="pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl w-64 focus:outline-none focus:ring-2 focus:ring-rdc-blue/30 focus:border-rdc-blue transition-all bg-gray-50/50">
                    </div>

                    <a href="{{ route('user.notifications.index') }}" class="w-11 h-11 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors relative flex items-center justify-center">
                        <i class="fas fa-bell text-gray-500"></i>
                        <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rdc-red border-2 border-white rounded-full"></span>
                    </a>

                    <button class="w-11 h-11 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white rounded-xl shadow-lg hover:shadow-rdc-blue/30 transition-all flex items-center justify-center">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-auto py-8 text-center text-xs text-gray-400 border-t border-gray-200">
                <p>&copy; {{ date('Y') }} ServiceRDC. Tous droits réservés. <span class="text-rdc-blue">Fièrement congolais 🇨🇩</span></p>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
