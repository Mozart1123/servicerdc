<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HQ Command | ServiceRDC - @yield('title', 'Gestion Administrative')</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    
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
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
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
        * { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .flag-stripe { height: 5px; background: linear-gradient(90deg, #007FFF 0%, #007FFF 33%, #F0B800 33%, #F0B800 66%, #FF4757 66%, #FF4757 100%); }
        .glass-sidebar { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); border-right: 1px solid rgba(226, 232, 240, 0.8); }
        .active-nav-item { background: linear-gradient(90deg, rgba(0, 127, 255, 0.1) 0%, rgba(0, 127, 255, 0.05) 100%); border-left: 4px solid #007FFF; color: #007FFF !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>
<body class="h-full font-inter antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="flag-stripe fixed top-0 w-full z-[70]"></div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-slate-900/50 z-[60] lg:hidden backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 w-80 glass-sidebar z-[65] transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none overflow-hidden pt-1">
        
        <!-- [ENTÊTE ADMIN] -->
        <div class="px-8 py-10 border-b border-slate-100 bg-gradient-to-b from-white to-slate-50/50 relative">
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="absolute top-4 right-4 lg:hidden p-2 text-slate-400 hover:text-slate-600 bg-white rounded-lg shadow-sm border border-slate-100 transition-colors">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Logo Section -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 mb-8 px-2 group">
                <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm flex items-center justify-center p-1.5 bg-white border border-slate-100 group-hover:scale-105 transition-transform">
                    <img src="/assets/img/logo.png?v=1.1" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none uppercase">Service<span class="text-rdc-blue">RDC</span></h2>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Plateforme Nationale</p>
                </div>
            </a>

            <div class="flex items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=007FFF&color=fff&size=128" 
                         class="w-14 h-14 rounded-2xl border-2 border-white shadow-lg object-cover" alt="Profile">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full animate-pulse"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-black text-slate-900 text-xs uppercase truncate italic tracking-tighter">{{ auth()->user()->name }}</h2>
                    <span class="text-[8px] font-black text-rdc-blue uppercase tracking-widest mt-0.5 block italic">Manager Opérationnel</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-6 space-y-8 custom-scrollbar">
            <!-- [GESTION] -->
            <section>
                <div class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 italic">
                    <i class="fas fa-grid-2 text-[8px]"></i> CENTRE OPÉRATIONS
                </div>
                <ul class="space-y-1" x-data="{ alertCount: 0, init() { this.updateCount(); setInterval(() => this.updateCount(), 30000); document.addEventListener('alerts-updated', (e) => this.alertCount = e.detail); }, updateCount() { fetch('{{ route('admin.api.alerts.unread-count') }}').then(r => r.json()).then(d => { this.alertCount = d.count; window.dispatchEvent(new CustomEvent('alerts-updated', { detail: d.count })); }) } }">
                    <x-admin-nav-item route="admin.dashboard" icon="fas fa-chart-line" label="Vue globale" />
                    <x-admin-nav-item route="admin.stats" icon="fas fa-bolt" label="Stats temps réel" />
                    <x-admin-nav-item route="admin.alerts" icon="fas fa-triangle-exclamation" label="Alertes système" ::badge="alertCount > 0 ? alertCount : null" badgeColor="bg-rdc-red" />
                </ul>
            </section>
            
            <ul class="space-y-1">
                <!-- [GESTION UTILISATEURS] -->
                <x-admin-dropdown-nav icon="fas fa-users-gear" label="Utilisateurs" :activePrefixes="['admin.users', 'admin.users-mgmt']" 
                    x-data="{ 
                        pendingCount: 0, 
                        flaggedCount: 0,
                        init() {
                            this.fetchCounts();
                            setInterval(() => this.fetchCounts(), 30000);
                            document.addEventListener('users-updated', () => this.fetchCounts());
                        },
                        fetchCounts() {
                            fetch('{{ route('admin.users-mgmt.counts') }}')
                                .then(r => r.json())
                                .then(d => {
                                    this.pendingCount = d.pending;
                                    this.flaggedCount = d.flagged;
                                });
                        }
                    }">
                    <x-admin-dropdown-item route="admin.users.index" label="Tous les utilisateurs" />
                    <x-admin-dropdown-item route="admin.users-mgmt.pending" label="En attente" ::badge="pendingCount > 0 ? pendingCount : null" />
                    <x-admin-dropdown-item route="admin.users-mgmt.flagged" label="Signalés/Suspendus" ::badge="flaggedCount > 0 ? flaggedCount : null" badgeColor="bg-rdc-red" />
                    <x-admin-dropdown-item route="admin.users-mgmt.docs" label="Vérification KYC" />
                </x-admin-dropdown-nav>
                
                <!-- [MODÉRATION & RESSOURCES] -->
                <x-admin-dropdown-nav icon="fas fa-shield-halved" label="Modération" :activePrefixes="['admin.moderation', 'admin.categories', 'admin.jobs']"
                    x-data="{ 
                        servicesCount: 0, 
                        reviewsCount: 0,
                        jobsCount: 0,
                        init() {
                            this.fetchCounts();
                            setInterval(() => this.fetchCounts(), 45000);
                        },
                        fetchCounts() {
                            fetch('{{ route('admin.users-mgmt.counts') }}')
                                .then(r => r.json())
                                .then(d => {
                                    this.servicesCount = d.services;
                                    this.reviewsCount = d.reviews;
                                    this.jobsCount = d.jobs;
                                });
                        }
                    }">
                    <x-admin-dropdown-item route="admin.moderation.services" label="Services signalés" ::badge="servicesCount > 0 ? servicesCount : null" badgeColor="bg-amber-500" />
                    <x-admin-dropdown-item route="admin.moderation.reviews" label="Évaluations" />
                    <x-admin-dropdown-item route="admin.categories.index" label="Catégories" />
                    <x-admin-dropdown-item route="admin.jobs.index" label="Offres Job" ::badge="jobsCount > 0 ? jobsCount : null" />
                </x-admin-dropdown-nav>

                <!-- [FINANCES] -->
                <x-admin-dropdown-nav icon="fas fa-wallet" label="Finances" :activePrefixes="['admin.finances']">
                    <x-admin-dropdown-item route="admin.finances.transactions" label="Transactions" />
                    <x-admin-dropdown-item route="admin.finances.commissions" label="Commissions" />
                    <x-admin-dropdown-item route="admin.finances.invoicing" label="Facturation" />
                </x-admin-dropdown-nav>

                <!-- [CONTENU & COMMUNICATION] -->
                <x-admin-dropdown-nav icon="fas fa-bullhorn" label="Communication" :activePrefixes="['admin.content']">
                    <x-admin-dropdown-item route="admin.content.news" label="Actualités" />
                    <x-admin-dropdown-item route="admin.content.newsletter" label="Newsletter" />
                    <x-admin-dropdown-item route="admin.content.push" label="Alertes Push" />
                </x-admin-dropdown-nav>

                <!-- [CONFIGURATION] -->
                <x-admin-dropdown-nav icon="fas fa-gears" label="Configuration HQ" :activePrefixes="['admin.settings', 'admin.settings-hq', 'admin.tools']">
                    <x-admin-dropdown-item route="admin.settings.index" label="Général" />
                    <x-admin-dropdown-item route="admin.tools.logs" label="Logs système" />
                </x-admin-dropdown-nav>
            </ul>
        </nav>
        
        <!-- [BAS] -->
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
             <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-white hover:text-rdc-blue transition-all border border-transparent hover:border-slate-200 mb-2 italic">
                <i class="fas fa-repeat"></i> Mode Utilisateur
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-rdc-red hover:bg-red-50 transition-all italic">
                    <i class="fas fa-power-off"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-80 flex flex-col min-h-screen pt-1">
        <!-- Top Navbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 px-6 lg:px-10 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-50 rounded-xl">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div>
                     <h1 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">@yield('header_title', 'HQ Central')</h1>
                     <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5 italic">Operational Status: <span class="text-emerald-500">Optimum</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center bg-slate-100 rounded-full px-4 py-2 border border-slate-200 focus-within:bg-white focus-within:ring-4 focus-within:ring-rdc-blue/10 transition-all">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Recherche rapide..." class="bg-transparent border-none text-[10px] font-bold uppercase tracking-widest text-slate-800 focus:ring-0 w-48 ml-2 placeholder:text-slate-300">
                </div>
                <!-- Notifications -->
                <a href="{{ route('admin.alerts') }}" 
                   x-data="{ count: 0, init() { this.poll(); setInterval(() => this.poll(), 30000); document.addEventListener('alerts-updated', (e) => this.count = e.detail); }, poll() { fetch('{{ route('admin.api.alerts.unread-count') }}').then(r => r.json()).then(d => { this.count = d.count; window.dispatchEvent(new CustomEvent('alerts-updated', { detail: d.count })); }) } }"
                   class="relative p-2.5 text-slate-400 hover:text-rdc-blue transition-colors rounded-full hover:bg-blue-50">
                    <i class="far fa-bell text-xl"></i>
                    <template x-if="count > 0">
                        <span class="absolute top-2.5 right-2.5 w-4 h-4 bg-rdc-red text-white text-[8px] font-black flex items-center justify-center rounded-full border-2 border-white animate-bounce-short" x-text="count"></span>
                    </template>
                </a>
                
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
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-10 animate-slide-in-right">
            <!-- Breadcrumbs / Top Actions -->
            <div class="mb-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    @if(Route::currentRouteName() !== 'admin.dashboard')
                        <a href="{{ url()->previous() == url()->current() ? route('admin.dashboard') : url()->previous() }}" 
                           class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-rdc-blue hover:border-rdc-blue hover:shadow-xl transition-all shadow-sm group">
                            <i class="fas fa-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    @endif
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight italic uppercase">@yield('page_title', 'Vue d\'ensemble')</h2>
                        <p class="text-slate-400 text-[11px] font-black uppercase tracking-widest mt-1 italic">@yield('page_subtitle', 'SDRC Operational Center')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.api.logs.export') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 text-xs font-black uppercase tracking-widest rounded-2xl shadow-sm hover:border-rdc-blue hover:text-rdc-blue transition-all flex items-center gap-3 italic">
                            <i class="fas fa-file-export"></i> Exporter
                        </button>
                    </form>
                    <form action="{{ route('admin.reports.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 hover:bg-rdc-blue-dark transform hover:-translate-y-1 transition-all flex items-center gap-3 italic">
                            <i class="fas fa-plus-circle"></i> Générer Rapport
                        </button>
                    </form>
                </div>
            </div>
            @yield('content')
        </main>

        <!-- Global Notifications (Toasts) -->
        <div class="fixed top-8 right-8 z-[200] space-y-4" x-data="{ 
            notifications: [],
            add(msg, type = 'success') {
                const id = Date.now();
                this.notifications.push({ id, msg, type });
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 5000);
            }
        }" @notify.window="add($event.detail.message, $event.detail.type)">
            <template x-for="n in notifications" :key="n.id">
                <div x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="flex items-center gap-4 px-6 py-4 rounded-2xl shadow-2xl border min-w-[300px]"
                     :class="n.type === 'success' ? 'bg-white border-emerald-100 text-emerald-900' : 'bg-white border-blue-100 text-blue-900'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                         :class="n.type === 'success' ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500'">
                        <i class="fas" :class="n.type === 'success' ? 'fa-check-circle' : 'fa-info-circle'"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-40" x-text="n.type"></p>
                        <p class="text-xs font-bold" x-text="n.msg"></p>
                    </div>
                    <button @click="notifications = notifications.filter(notif => notif.id !== n.id)" class="ml-auto text-slate-300 hover:text-slate-900">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                </div>
            </template>
            
            @if(session('success'))
                <div x-init="$nextTick(() => add('{{ session('success') }}', 'success'))"></div>
            @endif
            @if(session('info'))
                <div x-init="$nextTick(() => add('{{ session('info') }}', 'info'))"></div>
            @endif
        </div>
        
        <footer class="p-8 border-t border-slate-100 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic opacity-60">© {{ date('Y') }} ServiceRDC HQ Operational Base • <span class="text-rdc-blue">Kinshasa Central</span></p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
