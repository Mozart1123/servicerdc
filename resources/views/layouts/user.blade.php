<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tableau de bord') | ServiceRDC</title>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#003366',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; borderRadius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        .nav-item-active {
            background: linear-gradient(90deg, rgba(0, 127, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            border-left: 3px solid #007FFF;
            color: #007FFF;
        }

        .nav-item {
            border-left: 3px solid transparent;
            color: #64748B;
        }
        
        .nav-item:hover {
            color: #0F172A;
            background: rgba(248, 250, 252, 0.8);
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden glass-backdrop"></div>

    <!-- Sidebar -->
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 w-80 glass-sidebar z-40 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none bg-white">
        
        <!-- [PROFIL] Section -->
        <div class="px-8 py-10 border-b border-slate-100 bg-slate-50/30">
            <div class="flex items-center gap-4">
                <div class="relative flex-shrink-0 group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-rdc-blue to-blue-400 rounded-2xl blur opacity-25 group-hover:opacity-100 transition duration-500"></div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=007FFF&color=fff&size=128" 
                         class="relative w-14 h-14 rounded-2xl border-2 border-white shadow-sm object-cover" alt="Profile">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-heading font-black text-slate-900 text-base truncate">{{ auth()->user()->name ?? 'Utilisateur' }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 bg-blue-50 text-rdc-blue text-[9px] font-black uppercase tracking-widest rounded-md border border-blue-100">
                            Citoyen Vérifié
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-6 py-8 space-y-10 custom-scrollbar">
            
            <!-- [DASHBOARD] -->
            <div>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Dashboard
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.dashboard') ? 'bg-rdc-blue text-white shadow-xl shadow-blue-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-border-all text-lg {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Vue d'ensemble
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-bolt text-lg text-slate-400 group-hover:text-amber-500"></i>
                            Activité récente
                        </a>
                    </li>
                </ul>
            </div>

            <!-- [SERVICES/EMPLOIS] -->
            <div>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Services & Emplois
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.services.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.services.index') ? 'bg-rdc-blue text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-wand-magic-sparkles text-lg {{ request()->routeIs('user.services.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Découvrir Services
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.jobs.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.jobs.index') ? 'bg-rdc-blue text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-briefcase text-lg {{ request()->routeIs('user.jobs.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Trouver un Job
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.applications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-box-archive text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Historique
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.favorites') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-heart text-lg text-slate-400 group-hover:text-red-500"></i>
                            Favoris
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.new') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-plus-circle text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Nouveau
                        </a>
                    </li>
                </ul>
            </div>

            <!-- [MESSAGERIE] -->
            <div>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Messagerie
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-inbox text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Boîte de réception
                            <span class="ml-auto bg-rdc-red text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-lg shadow-lg shadow-red-500/20">2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-comments text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Conversations
                        </a>
                    </li>
                </ul>
            </div>

            <!-- [PARAMÈTRES] -->
            <div>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Paramètres
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-user-cog text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Profil
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.security') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-shield-halved text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Sécurité
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-bell text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Notifications
                        </a>
                    </li>
                </ul>
            </div>

            <!-- [AIDE & SUPPORT] -->
            <div>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    Aide & Support
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.help') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-question-circle text-lg text-slate-400 group-hover:text-rdc-blue"></i>
                            Centre d'aide
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.report') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                            <i class="fas fa-triangle-exclamation text-lg text-slate-400 group-hover:text-red-500"></i>
                            Signaler un problème
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- [BAS] - Footer Actions -->
        <div class="p-6 border-t border-slate-100 mt-auto">
            <div class="space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-rdc-blue transition-all group">
                    <i class="fas fa-house text-lg text-slate-300 group-hover:text-rdc-blue"></i>
                    Retour accueil
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold text-slate-500 hover:bg-red-50 hover:text-rdc-red transition-all group">
                        <i class="fas fa-power-off text-lg text-slate-300 group-hover:text-rdc-red"></i>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-80 flex flex-col min-h-screen transition-all duration-300">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-rdc-blue p-2 -ml-2">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Page Title -->
            <h1 class="text-xl font-heading font-bold text-slate-800 hidden sm:block">
                @yield('header_title', 'Tableau de bord')
            </h1>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="relative hidden md:block group">
                    <input type="text" placeholder="Rechercher..." 
                           class="w-64 pl-10 pr-4 py-2 bg-slate-100 border-none rounded-full text-sm focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white transition-all">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                </div>

                <!-- Notifications -->
                <a href="{{ route('user.notifications.index') }}" class="relative p-2 text-slate-400 hover:text-rdc-blue transition-colors rounded-full hover:bg-blue-50">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rdc-red rounded-full border-2 border-white"></span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
        
    </div>

</body>
</html>
