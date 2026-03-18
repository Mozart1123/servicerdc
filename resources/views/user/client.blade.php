<!DOCTYPE html>
<html lang="fr" x-data="{ 
    activeTab: 'dashboard', 
    mobileMenuOpen: false,
    showApplyModal: false,
    selectedJob: null,
    searchQuery: '',
    cityFilter: 'Kinshasa'
}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ServiceRDC - Espace Utilisateur Premium">
    <title>Dashboard - ServiceRDC</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'congo-blue': '#007FFF',
                        'congo-gold': '#F7D000',
                        'congo-red': '#CE1021',
                        'congo-bg': '#F0F4F5',
                        'rdc-emploi': '#14B8A6',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'display': ['Poppins', 'sans-serif'],
                    },
                    borderRadius: {
                        'xl': '1rem',
                        '2xl': '1.5rem',
                        '3xl': '2rem',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            background-color: #F0F4F5;
            color: #1A202C;
        }

        .premium-card {
            background: #FFFFFF;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .active-link {
            background-color: rgba(0, 127, 255, 0.1);
            color: #007FFF;
            border-left: 4px solid #007FFF;
        }

        .sidebar-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-item:hover:not(.active-link) {
            background-color: rgba(0, 0, 0, 0.03);
            border-left: 4px solid rgba(0, 127, 255, 0.3);
        }

        .btn-primary {
            background-color: #007FFF;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0066CC;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 127, 255, 0.2);
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="h-1 fixed top-0 left-0 right-0 z-[60] bg-gradient-to-r from-congo-blue via-congo-gold to-congo-red"></div>
    <!-- Sidebar Overlay -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" 
         @click="mobileMenuOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-72 bg-white shadow-xl z-50 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        
        <!-- Logo Area -->
        <div class="h-24 flex items-center px-8 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-congo-blue rounded-xl flex items-center justify-center text-white shadow-lg shadow-congo-blue/20">
                    <i class="fas fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Service<span class="text-congo-blue">RDC</span></h1>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">National Hub</p>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 py-8 px-4 space-y-2 overflow-y-auto custom-scrollbar">
            <div class="px-4 mb-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Menu Principal</p>
            </div>

            <!-- Vue d'ensemble -->
            <button @click="activeTab = 'dashboard'; mobileMenuOpen = false" 
               :class="activeTab === 'dashboard' ? 'active-link' : 'text-gray-500'"
               class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-th-large w-5 text-center"></i>
                <span>Vue d'ensemble</span>
            </button>

            <!-- Services & Artisans -->
            <button @click="activeTab = 'services'; mobileMenuOpen = false"
               :class="activeTab === 'services' ? 'active-link' : 'text-gray-500'"
               class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-tools w-5 text-center"></i>
                <span>Services & Artisans</span>
            </button>

            <!-- Hub d'Emplois -->
            <button @click="activeTab = 'emploi'; mobileMenuOpen = false"
               :class="activeTab === 'emploi' ? 'active-link' : 'text-gray-500'"
               class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-briefcase w-5 text-center"></i>
                <span>Hub d'Emplois</span>
            </button>

            <!-- Mes Candidatures -->
            <button @click="activeTab = 'candidatures'; mobileMenuOpen = false"
               :class="activeTab === 'candidatures' ? 'active-link' : 'text-gray-500'"
               class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-paper-plane w-5 text-center"></i>
                <span>Mes Candidatures</span>
            </button>

            <div class="px-4 pt-8 mb-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Mon Compte</p>
            </div>

            <!-- Mon Profil -->
            <button @click="activeTab = 'profile'; mobileMenuOpen = false"
               :class="activeTab === 'profile' ? 'active-link' : 'text-gray-500'"
               class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-user-circle w-5 text-center"></i>
                <span>Mon Profil</span>
            </button>

            <div class="px-4 pt-10 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Changer d'Espace
            </div>

            <div class="px-4 space-y-3">
                <a href="{{ route('user.switch-type', 'artisan') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-congo-gold/10 group transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tools text-congo-gold"></i>
                        <span class="text-xs font-black text-gray-700">ESPACE ARTISAN</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('user.switch-type', 'job_seeker') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-rdc-emploi/10 group transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-briefcase text-rdc-emploi"></i>
                        <span class="text-xs font-black text-gray-700">ESPACE RECRUTEUR</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </nav>

        <div class="p-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-12 flex items-center justify-center space-x-3 bg-congo-red/5 text-congo-red rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-congo-red hover:text-white transition-all">
                    <i class="fas fa-power-off"></i>
                    <span>DÉCONNEXION</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:ml-72 min-h-screen flex flex-col">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = true" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-500 bg-gray-100 rounded-lg">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="hidden sm:block">
                    <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest" x-text="activeTab === 'dashboard' ? 'Tableau de bord' : activeTab.charAt(0).toUpperCase() + activeTab.slice(1).replace('-', ' ')"></h2>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Global Search -->
                <div class="relative hidden md:block">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Recherche globale..." class="w-64 pl-10 pr-4 py-2.5 bg-congo-bg border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-congo-blue/20 transition-all outline-none">
                </div>

                <!-- Notifications -->
                <div class="relative">
                    <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-congo-blue hover:bg-congo-blue/5 rounded-xl transition-all">
                        <i class="fas fa-bell"></i>
                    </button>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-congo-red rounded-full ring-2 ring-white"></span>
                </div>

                <!-- User Profile Dropdown placeholder -->
                <div class="flex items-center gap-3 pl-6 border-l border-gray-100">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-black text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-congo-blue uppercase tracking-tighter">Membre Vérifié</p>
                    </div>
                    <div class="w-10 h-10 bg-congo-blue rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-congo-blue/20">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Section -->
        <main class="flex-1 p-6 lg:p-10 max-w-[1600px] mx-auto w-full">
            
            <!-- 1. VUE D'ENSEMBLE (DASHBOARD) -->
            <div x-show="activeTab === 'dashboard'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <!-- Hero Section -->
                <div class="mb-10">
                    <h1 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">
                        Bonjour, <span class="text-congo-blue">{{ explode(' ', auth()->user()->name)[0] }}</span> ! 👋
                    </h1>
                    <p class="text-gray-500 mt-2 font-medium">Bienvenue sur votre centre de contrôle ServiceRDC.</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="premium-card p-6 flex items-center gap-6">
                        <div class="w-16 h-16 bg-congo-blue/5 rounded-2xl flex items-center justify-center text-congo-blue text-2xl shadow-inner">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900">{{ $stats['applied_jobs_count'] ?? '0' }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Candidatures</p>
                        </div>
                    </div>
                    <div class="premium-card p-6 flex items-center gap-6">
                        <div class="w-16 h-16 bg-congo-gold/5 rounded-2xl flex items-center justify-center text-congo-gold text-2xl shadow-inner">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900">{{ $stats['total_services'] ?? '0' }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Services</p>
                        </div>
                    </div>
                    <div class="premium-card p-6 flex items-center gap-6">
                        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 text-2xl shadow-inner">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900">{{ $stats['active_missions'] ?? '0' }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Travaux</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Recommendations -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Jobs -->
                    <div class="premium-card p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-gray-900">Offres pour vous</h3>
                            <button @click="activeTab = 'emploi'" class="text-xs font-black text-congo-blue hover:underline">TOUT VOIR</button>
                        </div>
                        <div class="space-y-4">
                            @forelse($recentJobs ?? [] as $job)
                                <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between group hover:bg-congo-blue/5 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">{{ $job->title }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $job->location }} • {{ $job->salary ?? 'N/A' }}$</p>
                                        </div>
                                    </div>
                                    <button @click="activeTab = 'emploi'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-100 text-congo-blue group-hover:bg-congo-blue group-hover:text-white transition-all">
                                        <i class="fas fa-chevron-right text-[10px]"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-xs text-center py-8 text-gray-400 italic">Aucune offre récente.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recommended Artisans -->
                    <div class="premium-card p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-gray-900">Artisans certifiés</h3>
                            <button @click="activeTab = 'services'" class="text-xs font-black text-congo-blue hover:underline">PARCOURIR</button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @forelse($recentServices ?? [] as $service)
                                <div class="p-5 bg-gray-50 rounded-2xl text-center hover:bg-white hover:shadow-lg transition-all border border-transparent hover:border-gray-100">
                                    <div class="w-16 h-16 bg-congo-gold rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-2xl font-black">
                                        {{ substr($service->artisan->name ?? 'A', 0, 1) }}
                                    </div>
                                    <p class="font-bold text-sm text-gray-900">{{ $service->name }}</p>
                                    <p class="text-[9px] font-black text-congo-gold uppercase mt-1">{{ $service->artisan->name ?? 'Artisan' }}</p>
                                    <div class="mt-4 flex justify-center text-congo-gold text-[10px]">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-xs text-center py-8 text-gray-400 italic">Aucun artisan disponible.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SERVICES & ARTISANS -->
            <div x-show="activeTab === 'services'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-6">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900">Services & Artisans</h1>
                        <p class="text-gray-500 mt-1 font-medium">Réservez les meilleurs professionnels pour vos travaux.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative flex-1 md:w-80">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Rechercher un service..." class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none focus:ring-2 focus:ring-congo-blue/10 transition-all">
                        </div>
                        <button class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl font-bold flex items-center gap-2 text-gray-500 shadow-sm hover:bg-gray-50">
                            <i class="fas fa-filter text-xs"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Grid of Services -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($recentServices ?? [] as $service)
                        <div class="premium-card group hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                            <div class="p-8 text-center flex flex-col items-center">
                                <div class="relative mb-6">
                                    <div class="w-24 h-24 rounded-[2rem] bg-congo-bg flex items-center justify-center text-congo-blue text-3xl font-black shadow-inner group-hover:bg-congo-blue group-hover:text-white transition-all duration-500">
                                        {{ substr($service->name, 0, 1) }}{{ substr($service->name, 1, 1) }}
                                    </div>
                                    <button class="absolute -top-2 -right-2 w-10 h-10 bg-white shadow-lg rounded-xl flex items-center justify-center text-congo-gold hover:scale-110 transition-all">
                                        <i class="far fa-star"></i>
                                    </button>
                                </div>
                                <h4 class="text-xl font-black text-gray-900 mb-1">{{ $service->name }}</h4>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Expert: <span class="text-gray-600">{{ $service->artisan->name ?? 'Vérifié' }}</span></p>
                                
                                <div class="flex items-center justify-center gap-1 text-congo-gold text-[10px] mb-8">
                                    <i class="fas fa-star"></i>
                                    <span class="ml-1 text-gray-900 font-black text-xs">4.9</span>
                                </div>

                                <div class="w-full flex gap-3">
                                    <button class="flex-1 py-3 bg-congo-blue text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-congo-blue/20">DÉTAILS</button>
                                    <button class="px-4 py-3 bg-gray-50 text-gray-500 rounded-xl hover:bg-congo-blue hover:text-white transition-all"><i class="fas fa-comment-dots"></i></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center flex flex-col items-center justify-center">
                            <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 text-6xl mb-8">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-2">Aucun résultat trouvé</h3>
                            <p class="text-gray-500 mb-8 max-w-sm">Désolé, nous n'avons trouvé aucun artisan correspondant à votre recherche. Essayez d'autres mots-clés ou suggérez-nous un métier.</p>
                            <a href="#" class="px-8 py-4 bg-congo-blue text-white rounded-2xl font-black text-xs shadow-xl shadow-congo-blue/20">SUGGÉRER UN MÉTIER</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. HUB D'EMPLOIS -->
            <div x-show="activeTab === 'emploi'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-6">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900">Hub d'Emplois</h1>
                        <p class="text-gray-500 mt-1 font-medium">Les meilleures offres de la nation en un seul endroit.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select x-model="cityFilter" class="pl-12 pr-10 py-3.5 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none font-bold text-xs appearance-none focus:ring-2 focus:ring-congo-blue/10">
                                <option>Kinshasa</option>
                                <option>Lubumbashi</option>
                                <option>Goma</option>
                                <option>Bukavu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Job List -->
                <div class="space-y-6">
                    @forelse($allJobs ?? [] as $job)
                        <div class="premium-card p-8 flex flex-col md:flex-row items-center justify-between gap-8 group hover:shadow-2xl transition-all duration-500 border-l-[6px] border-congo-blue">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-congo-blue border border-gray-100 flex-shrink-0">
                                        <i class="fas fa-industry text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black text-gray-900 group-hover:text-congo-blue transition-colors">{{ $job->title }}</h4>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $job->user->name ?? 'ENTREPRISE' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-6 mt-6">
                                    <span class="flex items-center gap-2 text-xs font-bold text-gray-500"><i class="fas fa-map-marker-alt text-congo-blue"></i> {{ $job->location }}</span>
                                    <span class="flex items-center gap-2 text-xs font-bold text-gray-500"><i class="fas fa-wallet text-congo-blue"></i> {{ $job->salary ?? 'N/A' }} / mois</span>
                                    <span class="flex items-center gap-2 text-xs font-bold text-gray-500"><i class="fas fa-clock text-congo-blue"></i> Freelance</span>
                                </div>
                                <p class="mt-6 text-sm text-gray-500 font-medium leading-relaxed max-w-2xl">{{ Str::limit($job->description, 150) }}</p>
                            </div>
                            <div class="w-full md:w-auto flex flex-col gap-3">
                                <button @click="selectedJob = { id: {{ $job->id }}, title: '{{ addslashes($job->title) }}' }; showApplyModal = true" class="btn-primary w-full md:px-10 py-4 rounded-xl font-black text-xs">POSTULER MAINTENANT</button>
                                <button class="w-full py-4 bg-gray-50 text-gray-400 rounded-xl font-bold text-xs hover:bg-congo-gold/10 hover:text-congo-gold transition-all flex items-center justify-center gap-2">
                                    <i class="far fa-bookmark"></i> SAUVEGARDER
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="premium-card p-20 text-center">
                            <p class="text-gray-400 italic">Aucune offre d'emploi disponible pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 4. MES CANDIDATURES -->
            <div x-show="activeTab === 'candidatures'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-10">
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mes Candidatures</h1>
                    <p class="text-gray-500 mt-2 font-medium">Suivez l'état d'avancement de vos demandes d'emploi.</p>
                </div>

                <div class="premium-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">OFFRE</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">DATE D'ENVOI</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">LOCALISATION</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">STATUT</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($myApplications ?? [] as $app)
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-congo-blue shadow-sm border border-gray-100">
                                                    <i class="fas fa-briefcase text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-black text-gray-900 text-sm">{{ $app->jobOffer->title }}</p>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase">{{ $app->jobOffer->user->name ?? 'Entreprise' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-sm font-bold text-gray-500">{{ $app->created_at->format('d M Y') }}</td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-900">{{ $app->jobOffer->location }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @php
                                                $statusClasses = match($app->status) {
                                                    'accepted' => 'bg-green-100 text-green-600',
                                                    'rejected' => 'bg-congo-red/10 text-congo-red',
                                                    default => 'bg-congo-gold/10 text-congo-gold',
                                                };
                                                $statusLabel = match($app->status) {
                                                    'accepted' => 'Approuvé',
                                                    'rejected' => 'Rejeté',
                                                    default => 'En attente',
                                                };
                                            @endphp
                                            <span class="status-badge {{ $statusClasses }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            @if($app->status === 'accepted')
                                                <button class="px-4 py-2 rounded-lg bg-green-500 text-white font-black text-[9px] uppercase tracking-widest shadow-lg shadow-green-500/20">Message</button>
                                            @else
                                                <button class="w-10 h-10 rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-congo-red transition-all shadow-sm">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">Vous n'avez pas encore postulé à des offres.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. MON PROFIL -->
            <div x-show="activeTab === 'profile'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-10">
                    <h1 class="text-3xl font-black text-gray-900">Mon Profil professionnel</h1>
                    <p class="text-gray-500 mt-2 font-medium">Gérez votre identité numérique sur ServiceRDC.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="premium-card p-10 text-center">
                            <div class="relative w-40 h-40 mx-auto mb-8">
                                <div class="w-full h-full rounded-[2.5rem] bg-congo-blue flex items-center justify-center text-white text-6xl font-black shadow-2xl ring-8 ring-white">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <button class="absolute bottom-2 right-2 w-12 h-12 bg-congo-gold text-white rounded-2xl flex items-center justify-center border-4 border-white shadow-xl hover:scale-110 transition-all">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-2">ID: #SRDC-00{{ auth()->id() }}</p>
                            
                            <div class="mt-10 pt-10 border-t border-gray-100 space-y-4">
                                <div class="p-4 bg-gray-50 rounded-2xl flex items-center justify-between">
                                    <span class="text-xs font-black text-gray-400 uppercase">CV Téléchargé</span>
                                    <i class="fas fa-check-circle text-green-500"></i>
                                </div>
                                <button class="w-full py-4 bg-congo-blue/5 text-congo-blue rounded-xl font-black text-xs hover:bg-congo-blue hover:text-white transition-all">
                                    <i class="fas fa-download mr-2"></i> VOIR MON CV
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="premium-card p-10">
                            <h4 class="text-lg font-black text-gray-900 mb-8 border-l-4 border-congo-blue pl-4">Informations personnelles</h4>
                            <form class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                                    <input type="text" value="{{ auth()->user()->name }}" class="w-full px-6 py-4 bg-congo-bg border-transparent rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-congo-blue/10 font-bold text-gray-900 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Addresse Email</label>
                                    <input type="email" value="{{ auth()->user()->email }}" class="w-full px-6 py-4 bg-congo-bg border-transparent rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-congo-blue/10 font-bold text-gray-700 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                                    <input type="tel" value="+243 821 000 000" class="w-full px-6 py-4 bg-congo-bg border-transparent rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-congo-blue/10 font-bold text-gray-900 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ville</label>
                                    <select class="w-full px-6 py-4 bg-congo-bg border-transparent rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-congo-blue/10 font-bold text-gray-900 transition-all appearance-none">
                                        <option>Kinshasa</option>
                                        <option>Lubumbashi</option>
                                        <option>Goma</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 pt-4">
                                    <button type="button" class="btn-primary px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest">Enregistrer les modifications</button>
                                </div>
                            </form>
                        </div>

                        <div class="premium-card p-10">
                            <h4 class="text-lg font-black text-gray-900 mb-8 border-l-4 border-congo-gold pl-4">Compétences spécialisées</h4>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="skill in ['Laravel', 'UI/UX Design', 'Gestion de Projet', 'Électricité Industrielle']">
                                    <span class="px-5 py-3 bg-congo-bg rounded-xl text-xs font-bold text-gray-700 flex items-center gap-3">
                                        <span x-text="skill"></span>
                                        <button class="text-congo-red hover:scale-125 transition-all"><i class="fas fa-times"></i></button>
                                    </span>
                                </template>
                                <button class="px-5 py-3 border-2 border-dashed border-gray-200 rounded-xl text-[10px] font-black text-gray-400 hover:border-congo-blue hover:text-congo-blue transition-all uppercase tracking-widest">+ Ajouter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="h-20 flex items-center justify-center border-t border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.5em]">&copy; 2024 SERVICERDC • LA FORCE DU NUMÉRIQUE</p>
        </footer>
    </div>

    <!-- Apply Modal -->
    <div x-show="showApplyModal" x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="showApplyModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showApplyModal = false"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div x-show="showApplyModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-10"
             class="bg-white rounded-[2.5rem] w-full max-w-xl p-10 relative z-10 shadow-2xl overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-congo-blue"></div>
            
            <h3 class="text-2xl font-black text-gray-900 mb-2">Postuler à l'offre</h3>
            <p class="text-sm font-bold text-congo-blue uppercase tracking-widest mb-8" x-text="selectedJob?.title"></p>
            
            <form @submit.prevent="showApplyModal = false; alert('Candidature envoyée avec succès !')" class="space-y-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lettre de motivation (Express)</label>
                    <textarea rows="4" class="w-full px-6 py-4 bg-congo-bg border-transparent rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-congo-blue/10 font-bold text-gray-900 transition-all" placeholder="Dites en quelques mots pourquoi vous êtes le candidat idéal..."></textarea>
                </div>

                <div class="p-6 border-2 border-dashed border-congo-blue/30 rounded-2xl bg-congo-blue/5 text-center group cursor-pointer hover:bg-congo-blue/10 transition-all">
                    <i class="fas fa-file-pdf text-3xl text-congo-blue mb-2"></i>
                    <p class="text-sm font-black text-gray-900">Mon_CV_Actualise.pdf</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1">Cliquez pour modifier</p>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-5 bg-congo-blue text-white rounded-2xl font-black text-xs shadow-2xl shadow-congo-blue/30 hover:scale-[1.02] transition-all">
                        CONFIRMER LA CANDIDATURE
                    </button>
                    <button type="button" @click="showApplyModal = false" class="px-8 py-5 bg-gray-50 text-gray-400 rounded-2xl font-black text-xs hover:bg-congo-red hover:text-white transition-all">
                        ANNULER
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>