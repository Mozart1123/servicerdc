<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | ServiceRDC - @yield('title', 'Console de Gestion')</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Stylings -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                        'rdc-dark-blue': '#0F172A',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .animate-pulse-soft { animation: pulse-soft 2s infinite; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 w-80 glass-sidebar z-50 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none overflow-hidden">
        
        <!-- [ENTÊTE ADMIN] -->
        <div class="p-8 border-b border-slate-100 bg-gradient-to-b from-white to-slate-50/50 relative">
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="absolute top-4 right-4 lg:hidden p-2 text-slate-400 hover:text-slate-600 bg-white rounded-lg shadow-sm border border-slate-100 transition-colors">
                <i class="fas fa-times"></i>
            </button>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=007FFF&color=fff&size=128" 
                         class="w-16 h-16 rounded-2xl border-2 border-white shadow-lg object-cover" alt="Profile">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full animate-pulse"></div>
                </div>
                <div>
                    <h2 class="font-heading font-black text-slate-900 text-sm uppercase tracking-tight truncate w-40">{{ auth()->user()->name }}</h2>
                    <div class="flex flex-col gap-1 mt-1">
                        <span class="text-[9px] font-black text-rdc-blue uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fas fa-crown"></i> Admin Principal
                        </span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fas fa-building"></i> ServiceRDC Admin
                        </span>
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-1.5 animate-pulse">
                            <i class="fas fa-check-circle"></i> Système actif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">
            
            <!-- [DASHBOARD] -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] font-heading flex items-center gap-2">
                    <i class="fas fa-grid-2 text-[8px]"></i> DASHBOARD
                </div>
                <ul class="space-y-1">
                    <x-admin-nav-item route="admin.dashboard" icon="fas fa-chart-bar" label="Vue globale" />
                    <x-admin-nav-item route="admin.stats" icon="fas fa-chart-line" label="Stats temps réel" />
                    <x-admin-nav-item route="admin.alerts" icon="fas fa-triangle-exclamation" label="Alertes système" badge="5" badgeColor="bg-rdc-red" />
                </ul>
            </section>
            
            <ul class="space-y-1">
                <!-- [GESTION UTILISATEURS] -->
                <x-admin-dropdown-nav icon="fas fa-users-gear" label="Utilisateurs" :activePrefixes="['admin.users', 'admin.users-mgmt']">
                    <x-admin-dropdown-item route="admin.users.index" label="Tous les utilisateurs" />
                    <x-admin-dropdown-item route="admin.users-mgmt.pending" label="En attente" badge="12" />
                    <x-admin-dropdown-item route="admin.users-mgmt.flagged" label="Signalés/Suspendus" />
                    <x-admin-dropdown-item route="admin.users-mgmt.docs" label="Vérification KYC" />
                </x-admin-dropdown-nav>
                
                <!-- [MODÉRATION & RESSOURCES] -->
                <x-admin-dropdown-nav icon="fas fa-shield-halved" label="Modération & Opérations" :activePrefixes="['admin.moderation', 'admin.categories', 'admin.jobs']">
                    <x-admin-dropdown-item route="admin.moderation.services" label="Services signalés" />
                    <x-admin-dropdown-item route="admin.moderation.reviews" label="Évaluations à modérer" />
                    <x-admin-dropdown-item route="admin.categories.index" label="Catégories système" />
                    <x-admin-dropdown-item route="admin.jobs.index" label="Offres d'emploi" />
                </x-admin-dropdown-nav>

                <!-- [FINANCES] -->
                <x-admin-dropdown-nav icon="fas fa-wallet" label="Gestion Financière" :activePrefixes="['admin.finances']">
                    <x-admin-dropdown-item route="admin.finances.transactions" label="Transactions" />
                    <x-admin-dropdown-item route="admin.finances.commissions" label="Commissions" />
                    <x-admin-dropdown-item route="admin.finances.invoicing" label="Facturation" />
                </x-admin-dropdown-nav>

                <!-- [CONTENU & COMMUNICATION] -->
                <x-admin-dropdown-nav icon="fas fa-bullhorn" label="Communication" :activePrefixes="['admin.content']">
                    <x-admin-dropdown-item route="admin.content.news" label="Actualités/Blog" />
                    <x-admin-dropdown-item route="admin.content.newsletter" label="Newsletter" />
                    <x-admin-dropdown-item route="admin.content.push" label="Alertes Push" />
                </x-admin-dropdown-nav>

                <!-- [RAPPORTS & ANALYTICS] -->
                <x-admin-dropdown-nav icon="fas fa-chart-pie" label="Rapports Data" :activePrefixes="['admin.reports-hq']">
                    <x-admin-dropdown-item route="admin.reports-hq.analytics" label="Analytics globaux" />
                    <x-admin-dropdown-item route="admin.reports-hq.financial" label="Rapports financiers" />
                    <x-admin-dropdown-item route="admin.reports-hq.export" label="Exports massifs" />
                </x-admin-dropdown-nav>

                <!-- [SUPPORT CLIENT] -->
                <x-admin-dropdown-nav icon="fas fa-headset" label="Support ServiceRDC" :activePrefixes="['admin.support-hq']">
                    <x-admin-dropdown-item route="admin.support-hq.tickets" label="Tickets ouverts" />
                    <x-admin-dropdown-item route="admin.support-hq.docs" label="Base de connaissances" />
                    <x-admin-dropdown-item route="admin.support-hq.suggestions" label="Boîte à idées" />
                </x-admin-dropdown-nav>

                <!-- [CONFIGURATION TECHNIQUE] -->
                <x-admin-dropdown-nav icon="fas fa-gears" label="Configuration Core" :activePrefixes="['admin.settings', 'admin.settings-hq', 'admin.tools']">
                    <x-admin-dropdown-item route="admin.settings.index" label="Paramètres généraux" />
                    <x-admin-dropdown-item route="admin.settings-hq.geo" label="Zones de service" />
                    <x-admin-dropdown-item route="admin.settings-hq.api" label="Intégrations (API)" />
                    <div class="h-px bg-slate-200 my-1 mx-2"></div>
                    <x-admin-dropdown-item route="admin.tools.maintenance" label="Mode maintenance" badge="OFF" badgeColor="bg-slate-400" />
                    <x-admin-dropdown-item route="admin.tools.cache" label="Purge cache" />
                    <x-admin-dropdown-item route="admin.tools.logs" label="Logs erreurs" />
                </x-admin-dropdown-nav>
            </ul>

            <div class="h-10"></div>
        </nav>
        
        <!-- [BAS] - Footer Actions -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/80 backdrop-blur-sm">
            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-black text-slate-600 hover:bg-white hover:text-rdc-blue hover:shadow-sm transition-all mb-1 group uppercase tracking-widest">
                <i class="fas fa-user w-5 text-center text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                Mon compte
            </a>
            
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-black text-slate-600 hover:bg-white hover:text-rdc-blue hover:shadow-sm transition-all mb-1 group uppercase tracking-widest">
                <i class="fas fa-repeat w-5 text-center text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                Basculer en mode user
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-black text-slate-600 hover:bg-red-50 hover:text-rdc-red hover:shadow-sm transition-all group uppercase tracking-widest">
                    <i class="fas fa-door-open w-5 text-center text-slate-400 group-hover:text-rdc-red transition-colors"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-80 flex flex-col min-h-screen transition-all duration-300">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-50 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="hidden sm:flex items-center gap-2">
                    <h1 class="text-xl font-heading font-bold text-slate-900 truncate">
                        @yield('header_title', 'Tableau de bord')
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="relative hidden xl:block group">
                    <input type="text" placeholder="Recherche administrative..." 
                           class="w-72 pl-10 pr-4 py-2.5 bg-slate-100 border-none rounded-full text-sm focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white transition-all">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                </div>

                <!-- Notifications -->
                <button class="relative p-2.5 text-slate-400 hover:text-rdc-blue transition-colors rounded-full hover:bg-blue-50">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rdc-red rounded-full border-2 border-white"></span>
                </button>
                
                <!-- Profile Toggle -->
                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <div class="hidden md:block text-right">
                        <p class="text-xs font-extrabold text-slate-900 leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] font-bold text-rdc-blue uppercase mt-1 tracking-widest">HQ Station</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=007FFF&color=fff" 
                         class="w-10 h-10 rounded-xl shadow-lg border-2 border-white ring-1 ring-slate-100" alt="Admin">
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-8">
            <!-- Breadcrumbs / Top Actions -->
            <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight">@yield('page_title', 'Vue d\'ensemble')</h2>
                    <p class="text-slate-500 text-sm mt-1">@yield('page_subtitle', 'Bienvenue dans votre centre de contrôle ServiceRDC.')</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl shadow-sm hover:border-rdc-blue hover:text-rdc-blue transition-all flex items-center gap-2">
                        <i class="fas fa-file-export"></i> Exporter
                    </button>
                    <button class="px-5 py-2.5 bg-rdc-blue text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:bg-rdc-blue-dark transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Nouveau Rapport
                    </button>
                </div>
            </div>

            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="p-8 border-t border-slate-100 bg-white/50 text-center">
            <p class="text-xs font-bold text-slate-400">© 2024 <span class="text-rdc-blue">ServiceRDC</span> Administrative HQ. Tous droits réservés.</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
