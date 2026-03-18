<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MASTER CONTROL | ServiceRDC - Base de Commandement</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
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
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#0A0F1E',
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
        .flag-stripe { height: 6px; background: linear-gradient(90deg, #007FFF 0%, #007FFF 33%, #F0B800 33%, #F0B800 66%, #FF4757 66%, #FF4757 100%); }
        .master-sidebar { background: #0A0F1E; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .active-master-nav { background: linear-gradient(90deg, rgba(0, 127, 255, 0.2) 0%, transparent 100%); border-left: 4px solid #007FFF; color: white !important; }
        .terminal-bg { background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(20px); }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1E293B; border-radius: 10px; }
    </style>
</head>
<body class="h-full antialiased text-slate-300" x-data="{ sidebarOpen: false }">
    <div class="flag-stripe fixed top-0 w-full z-[80]"></div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-black/60 z-[70] lg:hidden backdrop-blur-md"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 w-80 master-sidebar z-[75] transform lg:translate-x-0 transition-transform duration-500 ease-in-out flex flex-col shadow-[0_0_50px_rgba(0,0,0,0.5)] pt-1">
        
        <div class="px-10 py-12 border-b border-white/5 relative overflow-hidden bg-slate-950/20">
            <div class="absolute top-0 right-0 w-32 h-32 bg-rdc-blue/10 rounded-full blur-[60px] -mr-16 -mt-16"></div>
            
            <a href="{{ url('/') }}" class="flex flex-col gap-5 relative z-10 group">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-white flex items-center justify-center shadow-2xl shadow-rdc-blue/20 divine-glow relative overflow-hidden p-2 group-hover:scale-105 transition-transform duration-500">
                        <img src="/assets/img/logo.png?v=1.1" alt="ServiceRDC Logo" class="w-full h-full object-contain">
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 animate-pulse"></div>
                    </div>
                    <div>
                        <h2 class="font-heading font-extrabold text-sm leading-tight tracking-widest text-amber-400 uppercase">MAÎTRE ABSOLU</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">SUPER ADMIN - HQ</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white leading-none tracking-tighter italic uppercase">MASTER<span class="text-rdc-blue">SYSTEM</span></h1>
                    <p class="text-[9px] font-mono font-bold text-rdc-blue uppercase tracking-[0.4em] mt-2 opacity-80 italic">Root Authority Level 0</p>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-6 space-y-10 custom-scrollbar">
            <!-- [SYSTEM CORE] -->
            <section>
                <div class="px-4 mb-5 text-[9px] font-black text-slate-500 uppercase tracking-[0.5em] italic">Infrastructure Noyau</div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-xs font-black uppercase italic tracking-widest transition-all {{ request()->routeIs('super-admin.dashboard') ? 'active-master-nav' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-microchip w-5 text-center text-rdc-blue"></i>
                            Console Système
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.users.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-xs font-black uppercase italic tracking-widest transition-all {{ request()->routeIs('super-admin.users.*') ? 'active-master-nav' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-users-gear w-5 text-center text-rdc-yellow"></i>
                            Hiérarchie Totale
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.system.console') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-xs font-black uppercase italic tracking-widest transition-all {{ request()->routeIs('super-admin.system.console') ? 'active-master-nav' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-terminal w-5 text-center text-emerald-500"></i>
                            Commandes Root
                        </a>
                    </li>
                </ul>
            </section>

            <!-- [MAINTENANCE] -->
            <section>
                <div class="px-4 mb-5 text-[9px] font-black text-slate-500 uppercase tracking-[0.5em] italic">Surveillance & BDD</div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('super-admin.database.tables') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-xs font-black uppercase italic tracking-widest transition-all {{ request()->routeIs('super-admin.database.*') ? 'active-master-nav' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-database w-5 text-center text-purple-500"></i>
                            Master Database
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.security.firewall') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-xs font-black uppercase italic tracking-widest transition-all {{ request()->routeIs('super-admin.security.*') ? 'active-master-nav' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-shield-halved w-5 text-center text-rdc-red"></i>
                            Firewall Global
                        </a>
                    </li>
                </ul>
            </section>
        </nav>
        
        <!-- [GOD LOCK] -->
        <div class="p-8 border-t border-white/5 bg-black/40">
            <div class="flex items-center gap-4 mb-6 p-4 bg-white/5 rounded-[1.5rem] border border-white/5">
                <div class="w-10 h-10 rounded-xl bg-rdc-blue/10 flex items-center justify-center text-rdc-blue">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-white uppercase italic tracking-tighter">{{ auth()->user()->name }}</p>
                    <p class="text-[8px] font-mono text-emerald-500 uppercase italic">Status: Omnipotent</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-4 px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.3em] bg-rdc-red text-white hover:bg-red-700 transition-all shadow-xl shadow-red-900/40 italic">
                    <i class="fas fa-power-off"></i> Fermer l'Univers
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-80 flex flex-col min-h-screen relative pt-1">
        <!-- Top HUD -->
        <header class="h-24 bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40 px-10 flex items-center justify-between">
            <div class="flex items-center gap-10">
                <button @click="sidebarOpen = true" class="lg:hidden p-3 text-white hover:bg-white/5 rounded-2xl">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <div class="flex flex-col">
                     <h1 class="text-2xl font-black text-white uppercase italic tracking-tighter">Command <span class="text-rdc-blue">Level ∞</span></h1>
                     <div class="flex items-center gap-4 mt-1">
                        <span class="flex items-center gap-2 text-[9px] font-mono text-emerald-500 uppercase italic">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> SYSTEM_STABLE_100%
                        </span>
                        <span class="text-[9px] font-mono text-slate-500 uppercase italic">Uptime: 99.999%</span>
                     </div>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden xl:flex items-center gap-4 px-6 py-3 bg-black/40 rounded-2xl border border-white/5 text-xs font-mono text-rdc-blue italic">
                    <i class="fas fa-cube text-xs"></i>
                    <span>ENTITY_SCANNER: ENABLED</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all cursor-pointer group">
                    <i class="fas fa-magnifying-glass group-hover:scale-125 transition-transform"></i>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8 lg:p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(0,127,255,0.05),transparent)] pointer-events-none"></div>
            <div class="relative z-10 animate-slide-in-right">
                @yield('content')
            </div>
        </main>
        
        <footer class="p-10 border-t border-white/5 bg-slate-950/50 text-center">
            <p class="text-[9px] font-mono font-bold text-slate-600 uppercase tracking-[0.5em] italic">System Core v12.4.0 • Built with Divine Authority • ServiceRDC © {{ date('Y') }}</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
