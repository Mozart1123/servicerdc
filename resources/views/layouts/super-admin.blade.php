<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOD MODE | MAÎTRE ABSOLU | SRDC-DIVINE</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Icons & Stylings -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#0F172A',
                        'divine-gold': '#FFD700',
                        'cosmic-purple': '#A855F7',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
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
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(0.98); }
        }
        .animate-pulse-soft { animation: pulse-soft 2s infinite; }

        .divine-glow {
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
        }

        .gold-shimmer {
            background: linear-gradient(90deg, #F59E0B, #FBBF24, #F59E0B);
            background-size: 200% 100%;
            animation: shimmer 2s infinite linear;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .power-bar-fill {
            background: linear-gradient(90deg, #3B82F6, #10B981, #F59E0B);
            transition: width 1s ease-in-out;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="godMode">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 w-80 glass-sidebar z-50 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none overflow-hidden">
        
        <!-- [ENTÊTE ULTIME] -->
        <div class="p-8 border-b border-warning-100 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="flex flex-col gap-5 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-amber-400 to-yellow-600 flex items-center justify-center text-3xl shadow-xl shadow-amber-500/30 divine-glow relative">
                        <i class="fas fa-crown text-white"></i>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 animate-pulse"></div>
                    </div>
                    <div>
                        <h2 class="font-heading font-extrabold text-sm leading-tight tracking-widest text-amber-400 uppercase">MAÎTRE ABSOLU</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">SUPER ADMIN - HQ</span>
                        </div>
                    </div>
                </div>

                <!-- Jauge de Pouvoir -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-amber-500">⚡ Pouvoir</span>
                        <span x-text="powerLevel + '%'"></span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-700/50 rounded-full overflow-hidden p-[1px]">
                        <div class="h-full power-bar-fill rounded-full" :style="'width: ' + powerLevel + '%'"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-white/10">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-mono text-slate-400 uppercase tracking-tighter">UID: GOD-MODE-001</span>
                        <span class="text-[9px] font-mono text-emerald-400 uppercase tracking-tighter">ALL SYSTEMS UNLOCKED</span>
                    </div>
                    <div class="px-2 py-0.5 bg-amber-500/10 border border-amber-500/30 rounded text-[8px] font-bold text-amber-500 uppercase">OFFLINE MODE: NO</div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">
            
            <!-- SECTION 1 : CONTRÔLE ABSOLU DES UTILISATEURS -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-blue-500 uppercase tracking-widest font-heading flex items-center justify-between">
                    <span>1. Contrôle Utilisateurs</span>
                    <i class="fas fa-users text-[8px]"></i>
                </div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="super-admin.divine.tracking" icon="fas fa-map-marked-alt" label="Carte Temps Réel" badge="LIVE" variant="default" />
                    <x-super-admin-nav-item route="#" icon="fas fa-search-dollar" label="Recherche Omnisciente" variant="default" />
                    <x-super-admin-nav-item route="#" icon="fas fa-eye" label="Vision Utilisateur" variant="default" />
                    <x-super-admin-nav-item route="#" icon="fas fa-brain" label="Lire les Pensées" variant="default" />
                    <x-super-admin-nav-item route="super-admin.users.index" icon="fas fa-user-edit" label="Modifier n'importe qui" variant="gold" />
                    <x-super-admin-nav-item route="super-admin.divine.impersonate" icon="fas fa-user-secret" label="Impersonation Ultime" variant="gold" />
                    <x-super-admin-nav-item route="#" icon="fas fa-bullhorn" label="Message à Tous" badge="PUSH" variant="gold" />
                    <x-super-admin-nav-item route="#" icon="fas fa-ban" label="Apocalypse (Ban Global)" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 2 : CONTRÔLE TOTAL DU CONTENU -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-emerald-500 uppercase tracking-widest font-heading flex items-center justify-between">
                    <span>2. Contrôle Contenu</span>
                    <i class="fas fa-file-invoice text-[8px]"></i>
                </div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="#" icon="fas fa-pen-nib" label="Édition Universelle" variant="default" />
                    <x-super-admin-nav-item route="#" icon="fas fa-code" label="Modifier HTML/CSS Live" variant="gold" />
                    <x-super-admin-nav-item route="#" icon="fas fa-ghost" label="Censure Totale" variant="danger" />
                    <x-super-admin-nav-item route="#" icon="fas fa-magic" label="Échange Magique" variant="gold" />
                </ul>
            </section>

            <!-- SECTION 3 : CONTRÔLE TOTAL DES SERVICES -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-amber-500 uppercase tracking-widest font-heading">3. Contrôle Services</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="super-admin.services" icon="fas fa-tools" label="Gestion Divine Services" />
                    <x-super-admin-nav-item route="#" icon="fas fa-hard-hat" label="Contrôle des Artisans" />
                    <x-super-admin-nav-item route="#" icon="fas fa-chart-pie" label="Manipulation Marché" variant="gold" />
                </ul>
            </section>

            <!-- SECTION 5 : CONTRÔLE TOTAL FINANCIER -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-yellow-500 uppercase tracking-widest font-heading">5. Contrôle Financier</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="#" icon="fas fa-vault" label="Trésorerie Absolue" badge="$$$" />
                    <x-super-admin-nav-item route="#" icon="fas fa-money-bill-wave" label="Créer de l'argent" variant="gold" />
                    <x-super-admin-nav-item route="#" icon="fas fa-file-invoice-dollar" label="Facturation Divine" />
                    <x-super-admin-nav-item route="#" icon="fas fa-exchange-alt" label="Remboursements Divins" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 6 : CONTRÔLE TOTAL DU SYSTÈME -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-red-500 uppercase tracking-widest font-heading">6. Contrôle Système</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="super-admin.system.console" icon="fas fa-server" label="Admin Serveur (Root)" variant="danger" />
                    <x-super-admin-nav-item route="super-admin.database.tables" icon="fas fa-database" label="BDD Divine (Direct)" variant="danger" />
                    <x-super-admin-nav-item route="super-admin.security.firewall" icon="fas fa-shield-alt" label="Sécurité Absolue" />
                    <x-super-admin-nav-item route="#" icon="fas fa-unlock-alt" label="Casser Mots de Passe" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 8 : CONTRÔLE TOTAL DES ADMINS -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-slate-900 uppercase tracking-widest font-heading">8. Contrôle Admins</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="super-admin.users.index" icon="fas fa-crown" label="Hiérarchie Divine" />
                    <x-super-admin-nav-item route="super-admin.logs" icon="fas fa-history" label="Audit Universel" />
                    <x-super-admin-nav-item route="#" icon="fas fa-gavel" label="Juger les Admins" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 9 : CONTRÔLE TOTAL DU CODE -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-indigo-500 uppercase tracking-widest font-heading">9. Contrôle Code</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="#" icon="fas fa-file-code" label="Édition Source" variant="danger" />
                    <x-super-admin-nav-item route="#" icon="fas fa-rocket" label="Déploiement Divin" variant="gold" />
                    <x-super-admin-nav-item route="#" icon="fas fa-bug" label="Debug Universel" />
                </ul>
            </section>

            <!-- SECTION 10 : CONTRÔLE TOTAL DU TEMPS -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-purple-500 uppercase tracking-widest font-heading">10. Contrôle Temps</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="#" icon="fas fa-undo-alt" label="Revenir dans le Temps" variant="cosmic" />
                    <x-super-admin-nav-item route="#" icon="fas fa-fast-forward" label="Avancer (Simulation)" variant="cosmic" />
                    <x-super-admin-nav-item route="#" icon="fas fa-calendar-alt" label="Modifier l'Histoire" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 12 : POUVOIRS COSMIQUES -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-pink-500 uppercase tracking-widest font-heading">12. Pouvoirs Cosmiques</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="super-admin.divine.powers" icon="fas fa-sparkles" label="Création Ex-Nihilo" variant="cosmic" />
                    <x-super-admin-nav-item route="#" icon="fas fa-meteor" label="Destruction Totale" variant="danger" />
                    <x-super-admin-nav-item route="#" icon="fas fa-crystal-ball" label="Voyance Digitale" variant="cosmic" />
                    <x-super-admin-nav-item route="#" icon="fas fa-atom" label="Big Bang (Reset)" variant="panic" />
                </ul>
            </section>

            <!-- SECTION 14 : MODES SPÉCIAUX -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest font-heading">14. Modes Spéciaux</div>
                <ul class="space-y-1">
                    <x-super-admin-nav-item route="#" icon="fas fa-ghost" label="Mode Furtif" variant="default" badge="OFF" />
                    <x-super-admin-nav-item route="#" icon="fas fa-mask" label="Mode Incognito" variant="default" />
                    <x-super-admin-nav-item route="#" icon="fas fa-skull" label="Confirmation Nucléaire" variant="danger" />
                </ul>
            </section>

            <!-- SECTION 15 : COMMANDES DIVINES -->
            <section>
                <div class="px-4 mb-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest font-heading">15. Commandes Divines</div>
                <ul class="space-y-1 text-[10px] font-mono p-4 bg-slate-50 rounded-2xl border border-slate-100 italic text-slate-500">
                    <li>Ctrl+Shift+G : God Mode</li>
                    <li>Ctrl+Shift+D : Destruction</li>
                    <li>##GODMODE## : Activate</li>
                </ul>
            </section>

            <div class="h-10"></div>
        </nav>
        
        <!-- [BAS] - CONTRÔLE FINAL -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/80 backdrop-blur-md">
            <div class="grid grid-cols-2 gap-2 mb-4">
                <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-all gap-1 shadow-lg shadow-red-600/20">
                    <i class="fas fa-bomb text-xs"></i>
                    <span class="text-[8px] font-bold uppercase tracking-tighter">Destruction</span>
                </button>
                <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-all gap-1 shadow-lg shadow-amber-500/20">
                    <i class="fas fa-sync text-xs"></i>
                    <span class="text-[8px] font-bold uppercase tracking-tighter">Réinitialiser</span>
                </button>
                <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-900 text-white hover:bg-black transition-all gap-1 shadow-lg shadow-black/20">
                    <i class="fas fa-lock text-xs"></i>
                    <span class="text-[8px] font-bold uppercase tracking-tighter">Verrouiller</span>
                </button>
                <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-rdc-blue text-white hover:bg-rdc-blue-dark transition-all gap-1 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-phone-alt text-xs"></i>
                    <span class="text-[8px] font-bold uppercase tracking-tighter">Créateur</span>
                </button>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-xl text-[10px] font-extrabold uppercase tracking-widest text-slate-600 hover:bg-red-50 hover:text-rdc-red hover:shadow-sm transition-all group border border-transparent hover:border-red-100">
                    <i class="fas fa-power-off text-slate-400 group-hover:text-rdc-red"></i>
                    Quitter l'Univers
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-80 flex flex-col min-h-screen transition-all duration-300">
        
        <!-- Top Navbar -->
        <header class="h-24 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-50 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex flex-col">
                    <h1 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                        @yield('header_title', 'Master System Control')
                        <div class="flex items-center gap-1 group">
                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full animate-pulse-soft">GOD MODE ACTIF</span>
                            <span class="px-2 py-0.5 bg-slate-900 text-white text-[10px] font-bold rounded-full opacity-0 group-hover:opacity-100 transition-opacity">LVL ∞</span>
                        </div>
                    </h1>
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-[10px] font-mono text-slate-400 flex items-center gap-1.5 uppercase tracking-tighter">
                            <i class="fas fa-fingerprint text-emerald-500"></i> BIOMÉTRIE VÉRIFIÉE
                        </span>
                        <div class="h-3 w-px bg-slate-200"></div>
                        <span class="text-[10px] font-mono text-slate-400 flex items-center gap-1.5 uppercase tracking-tighter">
                            <i class="fas fa-satellite text-rdc-blue"></i> UPLINK: HQ-ORBITAL-STATION
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="relative hidden xl:block group">
                    <input type="text" placeholder="Recherche omnisciente..." 
                           class="w-80 pl-12 pr-4 py-3 bg-slate-100 border-none rounded-2xl text-xs focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white transition-all font-medium">
                    <i class="fas fa-search absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                </div>

                <!-- Profile -->
                <div class="flex items-center gap-4 pl-6 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-extrabold text-slate-900 tracking-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mt-0.5">Architecte Suprême</p>
                    </div>
                    <div class="relative group cursor-pointer">
                        <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-rdc-blue rounded-2xl blur opacity-20 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=000&color=F59E0B&size=128" 
                             class="relative w-12 h-12 rounded-2xl shadow-xl border-2 border-white" alt="Super Admin">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="fas fa-crown text-[6px] text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-10 bg-slate-50/50">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('godMode', () => ({
                sidebarOpen: false,
                powerLevel: 100,
                init() {
                    setInterval(() => {
                        this.powerLevel = 95 + Math.floor(Math.random() * 6);
                    }, 3000);
                }
            }))
        })
    </script>
    @stack('scripts')
</body>
</html>
