<!DOCTYPE html>
<html lang="fr" x-data="{ 
    activeTab: 'dashboard', 
    mobileMenuOpen: false,
    showPostModal: false
}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ServiceRDC - Espace Recruteur Haut de Gamme">
    <title>Recruteur - ServiceRDC</title>

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
                        'rdc-emploi': '#14B8A6', // High-end Teal for Recruitment
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
        .active-link { background-color: rgba(20, 184, 166, 0.1); color: #14B8A6; border-left: 4px solid #14B8A6; }
        .sidebar-item { transition: all 0.2s ease; border-left: 4px solid transparent; }
        .sidebar-item:hover:not(.active-link) { background-color: rgba(20, 184, 166, 0.02); border-left: 4px solid rgba(20, 184, 166, 0.3); }
        .btn-emploi { background-color: #14B8A6; color: white; transition: all 0.3s ease; }
        .btn-emploi:hover { background-color: #0D9488; transform: translateY(-1px); shadow: 0 4px 12px rgba(20, 184, 166, 0.2); }
        .status-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.25rem 0.75rem; border-radius: 9999px; }
        .flag-line { height: 4px; background: linear-gradient(90deg, #007FFF 0%, #007FFF 33%, #F7D000 33%, #F7D000 66%, #CE1021 66%, #CE1021 100%); }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="h-1 fixed top-0 left-0 right-0 z-[60] bg-gradient-to-r from-congo-blue via-congo-gold to-congo-red"></div>

    <!-- Sidebar -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-72 bg-white shadow-xl z-50 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        
        <div class="h-24 flex items-center px-8 border-b border-gray-50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-rdc-emploi rounded-xl flex items-center justify-center text-white shadow-lg shadow-rdc-emploi/20">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Service<span class="text-rdc-emploi">RDC</span></h1>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Espace Recruteur</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            <div class="px-4 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Menu Recrutement
            </div>

            <!-- 1. Dashboard -->
            <button @click="activeTab = 'dashboard'; mobileMenuOpen = false" 
                    :class="activeTab === 'dashboard' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-th-large w-5 text-center"></i>
                <span>Dashboard</span>
            </button>

            <!-- 2. Publier offres -->
            <button @click="activeTab = 'publier'; mobileMenuOpen = false" 
                    :class="activeTab === 'publier' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-plus-circle w-5 text-center"></i>
                <span>Publier offres</span>
            </button>

            <!-- 3. Candidatures -->
            <button @click="activeTab = 'candidatures'; mobileMenuOpen = false" 
                    :class="activeTab === 'candidatures' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm flex justify-between">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>Candidatures</span>
                </div>
                <span class="bg-congo-red text-white text-[10px] px-2 py-0.5 rounded-full">12</span>
            </button>

            <!-- 4. Discussions -->
            <button @click="activeTab = 'discussions'; mobileMenuOpen = false" 
                    :class="activeTab === 'discussions' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm flex justify-between">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-comments w-5 text-center"></i>
                    <span>Discussions</span>
                </div>
                <span class="bg-rdc-emploi text-white text-[10px] px-2 py-0.5 rounded-full">5</span>
            </button>

            <!-- 5. Historique -->
            <button @click="activeTab = 'historique'; mobileMenuOpen = false" 
                    :class="activeTab === 'historique' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-history w-5 text-center"></i>
                <span>Historique</span>
            </button>

            <!-- 6. Settings -->
            <button @click="activeTab = 'settings'; mobileMenuOpen = false" 
                    :class="activeTab === 'settings' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-cog w-5 text-center"></i>
                <span>Settings</span>
            </button>

            <!-- 7. Mon profile -->
            <button @click="activeTab = 'profile'; mobileMenuOpen = false" 
                    :class="activeTab === 'profile' ? 'active-link' : 'text-gray-500'" 
                    class="sidebar-item w-full flex items-center space-x-4 px-4 py-3.5 rounded-r-xl font-bold text-sm">
                <i class="fas fa-building w-5 text-center"></i>
                <span>Mon profil entreprise</span>
            </button>

            <div class="px-4 pt-8 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Changer d'Espace
            </div>

            <div class="px-4 space-y-2">
                <a href="{{ route('user.switch-type', 'client') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-congo-blue/10 group transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user text-congo-blue"></i>
                        <span class="text-[10px] font-black text-gray-700">ESPACE CLIENT</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                </a>
                <a href="{{ route('user.switch-type', 'artisan') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-congo-gold/10 group transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tools text-congo-gold"></i>
                        <span class="text-[10px] font-black text-gray-700">ESPACE ARTISAN</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                </a>
            </div>
        </nav>

        <!-- 8. Déconnexion -->
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
                <div class="flex flex-col">
                    <h2 class="text-lg font-black text-gray-900 tracking-tight" x-text="activeTab.charAt(0).toUpperCase() + activeTab.slice(1)">Dashboard</h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Espace Recrutement Professionnel</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-rdc-emploi/5 text-rdc-emploi rounded-full border border-rdc-emploi/10">
                    <div class="w-1.5 h-1.5 bg-rdc-emploi rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest">Filière Active</span>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Administrateur RH</p>
                </div>
                <div class="w-10 h-10 bg-rdc-emploi text-white rounded-xl flex items-center justify-center font-black text-sm shadow-lg shadow-rdc-emploi/20">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-8 lg:p-12 max-w-7xl mx-auto w-full">
            
            <!-- 1. DASHBOARD CONTENT -->
            <div x-show="activeTab === 'dashboard'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="premium-card p-6 border-b-4 border-rdc-emploi">
                        <div class="w-12 h-12 bg-rdc-emploi/10 rounded-xl flex items-center justify-center text-rdc-emploi mb-4">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900">8</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Offres actives</p>
                    </div>
                    <div class="premium-card p-6 border-b-4 border-congo-gold">
                        <div class="w-12 h-12 bg-congo-gold/10 rounded-xl flex items-center justify-center text-congo-gold mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900">24</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Candidatures</p>
                    </div>
                    <div class="premium-card p-6 border-b-4 border-green-500">
                        <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center text-green-500 mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900">12</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Embauches</p>
                    </div>
                    <div class="premium-card p-6 border-b-4 border-congo-blue">
                        <div class="w-12 h-12 bg-congo-blue/10 rounded-xl flex items-center justify-center text-congo-blue mb-4">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900">1.2k</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Vues Offres</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="premium-card p-8">
                        <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-wider">Offres Récentes</h3>
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-xl border-l-4 border-rdc-emploi flex items-center justify-between group cursor-pointer hover:bg-rdc-emploi/5 transition-all">
                                <div>
                                    <h4 class="text-sm font-black text-gray-900">Électricien résidentiel</h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Publiée le 15/03/2024 • 8 candidats</p>
                                </div>
                                <span class="status-badge bg-rdc-emploi/10 text-rdc-emploi">Active</span>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl border-l-4 border-congo-gold flex items-center justify-between group cursor-pointer hover:bg-congo-gold/5 transition-all">
                                <div>
                                    <h4 class="text-sm font-black text-gray-900">Chef de chantier</h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Publiée le 10/03/2024 • 2 candidats</p>
                                </div>
                                <span class="status-badge bg-congo-gold/10 text-congo-gold">Urgent</span>
                            </div>
                        </div>
                    </div>

                    <div class="premium-card p-8 bg-rdc-emploi text-white overflow-hidden relative">
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-2">Besoin de talents ?</h3>
                            <p class="text-sm text-white/70 mb-8 font-medium">Boostez vos annonces pour toucher 10x plus de candidats qualifiés.</p>
                            <button class="px-8 py-4 bg-white text-rdc-emploi rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl">METTRE EN AVANT</button>
                        </div>
                        <i class="fas fa-rocket absolute -bottom-10 -right-10 text-[10rem] text-white/5 transform -rotate-12"></i>
                    </div>
                </div>
            </div>

            <!-- 2. PUBLIER CONTENT -->
            <div x-show="activeTab === 'publier'" x-cloak>
                <div class="premium-card p-10 max-w-4xl">
                    <h3 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-wider">Publier une Nouvelle Offre</h3>
                    
                    <form class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Titre du poste</label>
                                <input type="text" placeholder="Ex: Plombier Sanitaire" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-rdc-emploi transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Type de contrat</label>
                                <select class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-rdc-emploi transition-all appearance-none">
                                    <option>CDI</option>
                                    <option>CDD</option>
                                    <option>Mission</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description détaillée</label>
                            <textarea rows="6" placeholder="Missions, responsabilités, environnement..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-rdc-emploi transition-all"></textarea>
                        </div>

                        <div class="flex flex-col md:flex-row gap-8">
                             <div class="flex-1 space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Localisation</label>
                                <input type="text" placeholder="Kinshasa, Gombe" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-rdc-emploi transition-all">
                            </div>
                            <div class="flex-1 space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Salaire ($)</label>
                                <input type="text" placeholder="800 - 1500" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-rdc-emploi transition-all">
                            </div>
                        </div>

                        <div class="flex items-center gap-4 py-4 border-y border-gray-50">
                            <input type="checkbox" id="urgent_cb" class="w-5 h-5 rounded text-rdc-emploi focus:ring-rdc-emploi">
                            <label for="urgent_cb" class="text-sm font-black text-gray-900 uppercase tracking-widest">Marquer comme URGENT (+50% visibilité)</label>
                        </div>

                        <button type="submit" class="btn-emploi px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl">PUBLIER L'OFFRE MAINTENANT</button>
                    </form>
                </div>
            </div>

            <!-- 3. CANDIDATURES CONTENT -->
            <div x-show="activeTab === 'candidatures'" x-cloak>
                <div class="mb-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-wider">Candidatures Reçues</h3>
                    <div class="flex gap-4">
                         <select class="px-6 py-3 bg-white border border-gray-100 rounded-xl font-bold text-xs outline-none">
                            <option>Toutes les offres</option>
                            <option>Électricien</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="premium-card p-8 flex flex-col md:flex-row items-center justify-between gap-8 group hover:border-rdc-emploi">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="w-20 h-20 bg-rdc-emploi/5 rounded-[2rem] flex items-center justify-center text-rdc-emploi text-3xl font-black">JK</div>
                            <div>
                                <h4 class="text-xl font-black text-gray-900 leading-tight">Jean-Pierre Kabasele</h4>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Postule pour : Électricien résidentiel</p>
                                <div class="flex gap-4 mt-4">
                                    <div class="flex items-center gap-2 text-[10px] font-black text-gray-500 uppercase">
                                        <i class="fas fa-map-marker-alt text-rdc-emploi"></i>
                                        <span>Kinshasa</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] font-black text-gray-500 uppercase">
                                        <i class="fas fa-history text-rdc-emploi"></i>
                                        <span>5 ans exp.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center md:items-end gap-3">
                            <span class="status-badge bg-blue-50 text-blue-600">Nouvelle</span>
                            <div class="flex gap-2">
                                <button class="p-3 bg-gray-50 rounded-xl text-gray-400 hover:text-rdc-emploi transition-all"><i class="fas fa-eye"></i></button>
                                <button class="btn-emploi px-6 py-3 rounded-xl font-black text-[10px] uppercase">Contacter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DISCUSSIONS (Simplifié pour l'intérêt du premium) -->
            <div x-show="activeTab === 'discussions'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-[70vh]">
                     <div class="premium-card overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h4 class="text-sm font-black uppercase tracking-widest">Conversations</h4>
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-6 bg-rdc-emploi/5 border-l-4 border-rdc-emploi flex items-center gap-4 cursor-pointer">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-rdc-emploi font-black">JK</div>
                                <div class="flex-1 overflow-hidden">
                                     <div class="flex justify-between items-center mb-1">
                                         <h5 class="text-xs font-black text-gray-900 truncate">Jean-Pierre K.</h5>
                                         <span class="text-[9px] text-gray-400 uppercase">14:30</span>
                                     </div>
                                     <p class="text-xs text-gray-500 truncate">Merci pour l'entretien, je suis...</p>
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="lg:col-span-2 premium-card flex flex-col overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                             <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-rdc-emploi/10 rounded-xl flex items-center justify-center text-rdc-emploi font-black">JK</div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-900">Jean-Pierre Kabasele</h4>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase uppercase">Candidat Électricien</p>
                                </div>
                             </div>
                             <button class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:text-congo-red transition-all"><i class="fas fa-phone-alt"></i></button>
                        </div>
                        <div class="flex-1 bg-gray-50/30 p-8 overflow-y-auto space-y-6">
                             <div class="flex justify-start">
                                <div class="max-w-[80%] bg-white p-4 rounded-2xl rounded-tl-none shadow-sm text-sm font-bold text-gray-600">
                                    Bonjour, je suis disponible pour l'entretien demain à 14h.
                                </div>
                             </div>
                             <div class="flex justify-end">
                                <div class="max-w-[80%] bg-rdc-emploi p-4 rounded-2xl rounded-tr-none shadow-md text-sm font-bold text-white">
                                    C'est noté. À demain !
                                </div>
                             </div>
                        </div>
                        <div class="p-6 border-t border-gray-50">
                             <div class="flex gap-4">
                                <input type="text" placeholder="Écrivez votre message..." class="flex-1 px-6 py-4 bg-gray-50 rounded-2xl font-bold text-sm outline-none focus:bg-white transition-all">
                                <button class="w-14 h-14 bg-rdc-emploi text-white rounded-2xl flex items-center justify-center shadow-lg"><i class="fas fa-paper-plane"></i></button>
                             </div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- 5. HISTORIQUE, SETTINGS, PROFILE (Placeholders logic) -->
            <div x-show="['historique', 'settings', 'profile'].includes(activeTab)" x-cloak>
                 <div class="premium-card p-20 text-center flex flex-col items-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-300 text-4xl mb-6">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-widest">Section en Optimisation</h3>
                    <p class="text-gray-400 mt-4 max-w-md font-medium">Nous mettons à jour l'espace <span x-text="activeTab" class="text-rdc-emploi font-black"></span> pour une expérience de recrutement encore plus puissante.</p>
                 </div>
            </div>

        </main>
    </div>
</body>
</html>