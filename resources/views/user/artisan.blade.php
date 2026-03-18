<!DOCTYPE html>
<html lang="fr" x-data="{ 
    activeTab: 'dashboard', 
    mobileMenuOpen: false,
    showServiceModal: false,
    selectedService: null
}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ServiceRDC - Espace Artisan Premium">
    <title>Espace Artisan - ServiceRDC</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'congo-blue': '#007FFF',
                        'congo-gold': '#F7D000',
                        'congo-red': '#CE1021',
                        'congo-bg': '#F0F4F5',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'display': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #F0F4F5; color: #1A202C; }
        .premium-card { background: #FFFFFF; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 0, 0, 0.03); transition: all 0.3s ease; }
        .active-link { background-color: rgba(0, 127, 255, 0.1); color: #007FFF; border-left: 4px solid #007FFF; }
        .sidebar-item { transition: all 0.2s ease; border-left: 4px solid transparent; }
        .sidebar-item:hover:not(.active-link) { background-color: rgba(0, 0, 0, 0.02); border-left: 4px solid rgba(0, 127, 255, 0.3); }
        .btn-primary { background-color: #007FFF; color: white; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #0066CC; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 127, 255, 0.2); }
        .status-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.25rem 0.75rem; border-radius: 9999px; }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="h-1 fixed top-0 left-0 right-0 z-[60] bg-gradient-to-r from-congo-blue via-congo-gold to-congo-red"></div>
    <!-- Sidebar -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-72 bg-white shadow-xl z-50 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        
        <div class="h-24 flex items-center px-8 border-b border-gray-50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-congo-gold rounded-xl flex items-center justify-center text-white shadow-lg shadow-congo-gold/20">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Service<span class="text-congo-blue">RDC</span></h1>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Espace Artisan</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-8 px-4 space-y-2 overflow-y-auto">
            <div class="px-4 mb-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Navigation</p>
            </div>

            <button @click="activeTab = 'dashboard'; mobileMenuOpen = false" :class="activeTab === 'dashboard' ? 'active-link' : 'text-gray-500'" class="sidebar-item w-full flex items-center space-x-4 px-4 py-4 rounded-r-xl font-bold text-sm">
                <i class="fas fa-th-large w-5"></i>
                <span>Vue d'ensemble</span>
            </button>

            <button @click="activeTab = 'services'; mobileMenuOpen = false" :class="activeTab === 'services' ? 'active-link' : 'text-gray-500'" class="sidebar-item w-full flex items-center space-x-4 px-4 py-4 rounded-r-xl font-bold text-sm">
                <i class="fas fa-layer-group w-5"></i>
                <span>Mes Services</span>
            </button>

            <button @click="activeTab = 'demandes'; mobileMenuOpen = false" :class="activeTab === 'demandes' ? 'active-link' : 'text-gray-500'" class="sidebar-item w-full flex items-center space-x-4 px-4 py-4 rounded-r-xl font-bold text-sm">
                <i class="fas fa-inbox w-5"></i>
                <span>Demandes Clients</span>
            </button>

            <button @click="activeTab = 'historique'; mobileMenuOpen = false" :class="activeTab === 'historique' ? 'active-link' : 'text-gray-500'" class="sidebar-item w-full flex items-center space-x-4 px-4 py-4 rounded-r-xl font-bold text-sm">
                <i class="fas fa-history w-5"></i>
                <span>Historique</span>
            </button>

            <button @click="activeTab = 'profile'; mobileMenuOpen = false" :class="activeTab === 'profile' ? 'active-link' : 'text-gray-500'" class="sidebar-item w-full flex items-center space-x-4 px-4 py-4 rounded-r-xl font-bold text-sm">
                <i class="fas fa-user-circle w-5"></i>
                <span>Mon Profil Public</span>
            </button>

            <div class="px-4 pt-10 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Changer d'Espace
            </div>

            <div class="px-4 space-y-3">
                <a href="{{ route('user.switch-type', 'client') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-congo-blue/10 group transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user text-congo-blue"></i>
                        <span class="text-xs font-black text-gray-700">ESPACE CLIENT</span>
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

    <div class="lg:ml-72 min-h-screen flex flex-col">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-50 flex items-center justify-between px-8 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = true" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-400 bg-gray-50 rounded-xl">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Actif Maintenant</span>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-gray-900">{{ auth()->user()->name }}</p>
                    <div class="flex items-center justify-end gap-1 text-congo-gold text-[10px]">
                        <i class="fas fa-star"></i>
                        <span class="font-bold text-gray-900">4.9 / 5.0</span>
                    </div>
                </div>
                <div class="w-10 h-10 bg-congo-gold rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-congo-gold/20">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-8 lg:p-12 max-w-7xl mx-auto w-full">
            
            <!-- VUE D'ENSEMBLE -->
            <div x-show="activeTab === 'dashboard'" x-cloak>
                <div class="mb-12">
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight">Espace <span class="text-congo-gold">Expert</span></h1>
                    <p class="text-gray-500 mt-2 text-lg">Votre activité en un coup d'œil. 3 nouvelles demandes en attente.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                    <div class="premium-card p-6 flex flex-col justify-between">
                        <div class="w-12 h-12 bg-congo-blue/5 rounded-xl flex items-center justify-center text-congo-blue mb-4">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-900">450$</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Revenus (Mars)</p>
                        </div>
                    </div>
                    <div class="premium-card p-6 flex flex-col justify-between">
                        <div class="w-12 h-12 bg-congo-gold/5 rounded-xl flex items-center justify-center text-congo-gold mb-4">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-900">12</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Missions Finies</p>
                        </div>
                    </div>
                    <div class="premium-card p-6 flex flex-col justify-between">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500 mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-900">156</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Vues Profil</p>
                        </div>
                    </div>
                    <div class="premium-card p-6 flex flex-col justify-between">
                        <div class="w-12 h-12 bg-congo-red/5 rounded-xl flex items-center justify-center text-congo-red mb-4">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-900">4.9</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Note Moyenne</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Notifications Récentes -->
                    <div class="premium-card p-8">
                        <h3 class="text-xl font-black text-gray-900 mb-8">Alertes de mission</h3>
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-xl border-l-4 border-congo-red flex items-center justify-between group cursor-pointer hover:bg-white transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-congo-red shadow-sm">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">Urgence : Panne de Courant</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Gombe • 15:45</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:text-congo-blue"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Activité de gains -->
                    <div class="premium-card p-8 bg-congo-blue text-white overflow-hidden relative">
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-2 whitespace-nowrap">Boostez votre visibilité</h3>
                            <p class="text-sm text-white/70 mb-8">Passez en compte 'Elite' pour apparaître en premier dans les recherches.</p>
                            <button class="px-8 py-4 bg-white text-congo-blue rounded-xl font-black text-xs uppercase shadow-xl">PASSER À L'ÉLITE</button>
                        </div>
                        <i class="fas fa-rocket absolute -bottom-10 -right-10 text-[10rem] text-white/5 transform -rotate-12"></i>
                    </div>
                </div>
            </div>

            <!-- MES SERVICES -->
            <div x-show="activeTab === 'services'" x-cloak>
                <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-6">
                    <div>
                        <h1 class="text-4xl font-black text-gray-900">Mes Services</h1>
                        <p class="text-gray-500 mt-2 text-lg">Gérez vos prestations et ajustez vos tarifs.</p>
                    </div>
                    <button @click="showServiceModal = true" class="btn-primary px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-xl flex items-center gap-3">
                        <i class="fas fa-plus"></i> AJOUTER UN SERVICE
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($recentServices ?? [] as $service)
                    <div class="premium-card p-8 group hover:shadow-2xl transition-all border-b-8 border-congo-gold/20">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 bg-congo-gold/5 rounded-2xl flex items-center justify-center text-congo-gold text-xl font-black border border-congo-gold/10">
                                {{ substr($service->name, 0, 1) }}
                            </div>
                            <span class="status-badge bg-green-50 text-green-600">ACTIF</span>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 mb-2 truncate group-hover:text-congo-blue transition-colors">{{ $service->name }}</h4>
                        <p class="text-gray-500 text-sm mb-6 line-clamp-2">{{ $service->description }}</p>
                        <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase">Tarif de base</p>
                                <p class="text-xl font-black text-gray-900">45$</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:text-congo-blue transition-all"><i class="fas fa-edit"></i></button>
                                <button class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:text-congo-red transition-all"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- DEMANDES CLIENTS -->
            <div x-show="activeTab === 'demandes'" x-cloak>
                <div class="mb-12">
                    <h1 class="text-4xl font-black text-gray-900">Demandes Clients</h1>
                    <p class="text-gray-500 mt-2 text-lg">Nouveaux projets et demandes de devis à valider.</p>
                </div>

                <div class="space-y-6">
                    @forelse($recentJobs->take(3) as $demand)
                    <div class="premium-card p-10 flex flex-col lg:flex-row items-center justify-between gap-10 hover:shadow-2xl border-l-[12px] border-congo-gold">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-congo-blue shadow-inner font-black text-2xl">
                                    {{ substr($demand->user->name ?? 'C', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900">{{ $demand->user->name ?? 'Client Particulier' }}</h4>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $demand->title }}</p>
                                </div>
                            </div>
                            <p class="text-gray-500 font-medium leading-relaxed max-w-2xl italic">"J'ai besoin de vos services d'urgence pour la maintenance de mon installation. Merci de me contacter."</p>
                        </div>
                        <div class="flex flex-col gap-4 w-full lg:w-auto">
                            <button class="btn-primary px-12 py-5 rounded-2xl font-black text-xs">ACCEPTER LA MISSION</button>
                            <button class="px-12 py-5 bg-gray-50 text-gray-400 rounded-2xl font-black text-xs hover:bg-congo-red/10 hover:text-congo-red transition-all">DÉCLINER</button>
                        </div>
                    </div>
                    @empty
                    <div class="premium-card p-20 text-center text-gray-400 italic font-medium">Aucune nouvelle demande client.</div>
                    @endforelse
                </div>
            </div>
            
            <!-- HISTORIQUE -->
            <div x-show="activeTab === 'historique'" x-cloak>
                <div class="mb-12">
                    <h1 class="text-4xl font-black text-gray-900">Historique</h1>
                    <p class="text-gray-500 mt-2 text-lg">Vos missions passées et avis clients.</p>
                </div>

                <div class="premium-card overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Service</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Client</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Date</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($myApplications->take(5) as $app)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6 text-center font-black text-gray-900 text-sm">{{ $app->jobOffer->title }}</td>
                                <td class="px-8 py-6 text-center text-sm font-bold text-gray-500">M. Kabange</td>
                                <td class="px-8 py-6 text-center text-[10px] font-bold text-gray-400 uppercase">12 MARS 2024</td>
                                <td class="px-8 py-6 text-right font-black text-congo-gold">⭐ 5.0</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PROFIL PUBLIC -->
            <div x-show="activeTab === 'profile'" x-cloak>
                <div class="mb-12">
                    <h1 class="text-4xl font-black text-gray-900">Mon Profil Public</h1>
                    <p class="text-gray-500 mt-2 text-lg">Gérez votre visibilité et votre biographie d'artisan.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-1">
                        <div class="premium-card p-10 text-center bg-white">
                            <div class="w-40 h-40 mx-auto bg-congo-gold rounded-[30%] shadow-2xl flex items-center justify-center text-white text-6xl font-black mb-8 border-8 border-congo-gold/10">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2">{{ auth()->user()->name }}</h3>
                            <p class="text-[10px] font-black text-congo-blue uppercase tracking-widest mb-8">Artisan Certifié ServiceRDC</p>
                            
                            <div class="flex justify-center gap-4 py-8 border-t border-gray-50">
                                <div class="text-center">
                                    <p class="text-xl font-black text-gray-900">128</p>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">Interventions</p>
                                </div>
                                <div class="w-px h-8 bg-gray-100 my-auto"></div>
                                <div class="text-center">
                                    <p class="text-xl font-black text-gray-900">4.9</p>
                                    <p class="text-[9px] font-black text-gray-400 uppercase">Note Client</p>
                                </div>
                            </div>
                            <button class="w-full py-4 bg-congo-blue text-white rounded-2xl font-black text-[10px] uppercase shadow-xl">PARTAGER MON PROFIL</button>
                        </div>
                    </div>

                    <div class="lg:col-span-2 premium-card p-12">
                        <form class="space-y-10">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Biographie Professionnelle</label>
                                <textarea rows="6" class="w-full px-8 py-6 bg-congo-bg border-transparent rounded-[2rem] font-medium text-gray-900 outline-none focus:bg-white focus:ring-4 focus:ring-congo-blue/10 transition-all" placeholder="Décrivez votre expérience et vos domaines de prédilection..."></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ville d'intervention</label>
                                    <input type="text" value="Kinshasa" class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-2xl font-black text-gray-900 outline-none focus:bg-white focus:ring-4 focus:ring-congo-blue/10 transition-all">
                                </div>
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Années d'expérience</label>
                                    <input type="number" value="8" class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-2xl font-black text-gray-900 outline-none focus:bg-white focus:ring-4 focus:ring-congo-blue/10 transition-all">
                                </div>
                            </div>
                            <button class="btn-primary px-16 py-5 rounded-2xl font-black text-xs uppercase shadow-xl tracking-widest">ENREGISTRER LES MODIFICATIONS</button>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Add Service Modal -->
    <div x-show="showServiceModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="showServiceModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-md"></div>
        <div class="bg-white rounded-[4rem] w-full max-w-2xl p-16 relative z-10 shadow-3xl border-t-[12px] border-congo-gold overflow-y-auto max-h-[90vh]">
            <h3 class="text-4xl font-black text-gray-900 mb-2">Nouveau Service</h3>
            <p class="text-gray-500 font-medium mb-12">Ajoutez une prestation à votre catalogue public.</p>
            
            <form @submit.prevent="showServiceModal = false" class="space-y-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Nom de la prestation</label>
                    <input type="text" required class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-3xl font-bold outline-none focus:bg-white focus:ring-4 focus:ring-congo-gold/10 transition-all" placeholder="Ex: Maintenance Électrique Industrielle">
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Prix ($)</label>
                        <input type="number" required class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-3xl font-bold outline-none focus:bg-white focus:ring-4 focus:ring-congo-gold/10 transition-all" placeholder="Tarif base">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Catégorie</label>
                        <select class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-3xl font-bold outline-none focus:bg-white focus:ring-4 focus:ring-congo-gold/10 transition-all">
                            <option>Électricité</option>
                            <option>Plomberie</option>
                            <option>Climatisation</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Description</label>
                    <textarea rows="4" required class="w-full px-8 py-5 bg-congo-bg border-transparent rounded-3xl font-bold outline-none focus:bg-white focus:ring-4 focus:ring-congo-gold/10 transition-all" placeholder="Parlez-nous de ce service..."></textarea>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 py-6 bg-congo-gold text-white rounded-[2rem] font-black text-xs shadow-2xl shadow-congo-gold/20 uppercase tracking-widest">PUBLIER MAINTENANT</button>
                    <button type="button" @click="showServiceModal = false" class="px-10 py-6 bg-gray-50 text-gray-400 rounded-[2rem] font-black text-xs uppercase tracking-widest">ANNULER</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>