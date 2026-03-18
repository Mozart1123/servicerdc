<!DOCTYPE html>
<html lang="fr" x-data="{ 
    searchQuery: '',
    cityFilter: 'Toutes les villes',
    typeFilter: 'Tous les contrats'
}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ServiceRDC - Hub d'Emplois National | Explorez toutes les opportunités">
    <title>Trouver un Emploi - ServiceRDC</title>

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
        body { background-color: #F0F4F5; color: #1A202C; scroll-behavior: smooth; }
        .hero-gradient { background: linear-gradient(135deg, #007FFF 0%, #004AAD 100%); }
        .premium-card { background: #FFFFFF; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 0, 0, 0.03); transition: all 0.3s ease; }
        .premium-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #007FFF; color: white; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #0066CC; transform: translateY(-1px); }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Navbar (Integrated with National Theme) -->
    <nav class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-20 sticky top-0 z-50">
        <a href="/" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-congo-blue rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-briefcase"></i>
            </div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Service<span class="text-congo-blue">RDC</span></h1>
        </a>
        <div class="hidden lg:flex items-center gap-10 text-sm font-black text-gray-400 uppercase tracking-widest">
            <a href="{{ route('services.index') }}" class="hover:text-congo-blue">Artisans</a>
            <a href="{{ route('jobs.index') }}" class="text-congo-blue border-b-2 border-congo-blue pb-1">Emplois</a>
            @auth
                <a href="{{ route('user.dashboard') }}" class="px-6 py-2 bg-congo-blue/10 text-congo-blue rounded-xl">Mon Espace</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-congo-blue">Connexion</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-gradient py-24 px-6 lg:px-20 text-white relative overflow-hidden">
        <div class="max-w-4xl relative z-10">
            <h2 class="text-5xl lg:text-7xl font-black mb-6 tracking-tighter leading-tight">VOTRE FUTUR <br><span class="text-congo-gold underline">DÉBUTE ICI.</span></h2>
            <p class="text-xl text-white/80 font-medium mb-12 max-w-2xl leading-relaxed">Trouvez l'offre qui vous correspond parmi des milliers d'opportunités à travers toute la République Démocratique du Congo.</p>
            
            <!-- Floating Search Bar -->
            <div class="bg-white p-4 rounded-[2rem] shadow-2xl flex flex-col lg:flex-row gap-4 items-center border-4 border-white/20">
                <div class="flex-1 flex items-center gap-4 px-6 w-full divide-x divide-gray-100">
                    <div class="flex-1 flex items-center gap-4">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Quoi ? (Métier, compétence...)" class="w-full py-4 text-gray-900 font-bold outline-none placeholder-gray-400">
                    </div>
                </div>
                <div class="flex-1 flex items-center gap-4 px-6 w-full lg:border-l lg:border-gray-100">
                    <i class="fas fa-map-marker-alt text-congo-blue"></i>
                    <select x-model="cityFilter" class="w-full py-4 text-gray-900 font-bold outline-none appearance-none">
                        <option>Toutes les villes</option>
                        <option>Kinshasa</option>
                        <option>Goma</option>
                        <option>Lubumbashi</option>
                    </select>
                </div>
                <button class="w-full lg:w-auto px-12 py-5 bg-congo-blue text-white rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl shadow-congo-blue/30">RECHERCHER</button>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-[600px] h-[600px] bg-white/5 rounded-full blur-3xl"></div>
    </header>

    <main class="py-20 px-6 lg:px-20 max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-16">
            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-80 space-y-12">
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Filtrer par Contrat</h3>
                    <div class="space-y-4">
                        @foreach($contractTypes ?? ['CDI', 'CDD', 'Stage', 'Freelance'] as $type)
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <input type="checkbox" class="w-6 h-6 rounded-lg text-congo-blue focus:ring-congo-blue border-gray-200">
                            <span class="text-sm font-bold text-gray-600 group-hover:text-congo-blue transition-colors">{{ $type }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Results -->
            <div class="flex-1 space-y-10">
                <div class="flex items-center justify-between mb-10">
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">{{ count($jobs ?? []) }} Offres Trouvées</p>
                    <select class="px-6 py-3 bg-white border border-gray-100 rounded-xl font-bold text-xs shadow-sm outline-none">
                        <option>Les plus récentes</option>
                        <option>Salaire élevé</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    @forelse($jobs ?? [] as $job)
                    <div class="premium-card p-10 flex flex-col md:flex-row items-center justify-between gap-10 hover:border-congo-blue">
                        <div class="flex-1">
                            <div class="flex items-center gap-6 mb-6">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-congo-blue text-3xl font-black">
                                    {{ substr($job->title, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 leading-tight">{{ $job->title }}</h4>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mt-1">{{ $job->user->name ?? 'Confidentiel' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-8 text-sm font-bold text-gray-500 mb-6">
                                <span class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-congo-blue"></i> {{ $job->location }}</span>
                                <span class="flex items-center gap-2"><i class="fas fa-wallet text-congo-blue"></i> {{ $job->salary ?? '---' }}$</span>
                                <span class="flex items-center gap-2"><i class="fas fa-clock text-congo-blue"></i> {{ $job->contract_type ?? 'Plein Temps' }}</span>
                            </div>
                            <p class="text-gray-500 font-medium leading-relaxed max-w-2xl">{{ Str::limit($job->description, 180) }}</p>
                        </div>
                        <div class="w-full lg:w-auto">
                            <a href="{{ route('jobs.show', $job->id) }}" class="btn-primary block text-center px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest whitespace-nowrap shadow-xl">POSTULER</a>
                        </div>
                    </div>
                    @empty
                    <div class="premium-card p-24 text-center text-gray-400 opacity-50">
                        <i class="fas fa-briefcase text-7xl mb-10"></i>
                        <p class="text-2xl font-black">Aucune offre trouvée.</p>
                        <p class="mt-4 font-bold">Réessayez avec d'autres filtres ou une autre ville.</p>
                    </div>
                    @endforelse
                </div>

                @if(isset($jobs) && method_exists($jobs, 'links'))
                <div class="pt-10">
                    {{ $jobs->links() }}
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-20 px-6 lg:px-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-16">
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-2xl font-black mb-8 tracking-tighter">Service<span class="text-congo-blue">RDC</span></h3>
                <p class="text-gray-400 leading-relaxed max-w-sm">La première plateforme nationale de mise en relation entre artisans, recruteurs et citoyens en République Démocratique du Congo.</p>
            </div>
            <div>
                <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-8">Hub Carrière</h4>
                <ul class="space-y-4 text-sm font-bold text-gray-400">
                    <li><a href="#" class="hover:text-congo-blue">Chercher un Job</a></li>
                    <li><a href="#" class="hover:text-congo-blue">Publier une Offre</a></li>
                    <li><a href="#" class="hover:text-congo-blue">Conseils Recrutement</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-8">Assistance</h4>
                <ul class="space-y-4 text-sm font-bold text-gray-400">
                    <li><a href="#" class="hover:text-congo-blue">Centre d'aide</a></li>
                    <li><a href="#" class="hover:text-congo-blue">Confidentialité</a></li>
                    <li><a href="#" class="hover:text-congo-blue">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-white/5 mt-20 pt-10 flex flex-col md:flex-row justify-between text-[10px] font-black text-white/40 uppercase tracking-widest">
            <p>&copy; 2026 ServiceRDC. Tous droits réservés.</p>
            <p>Fait en RDC avec fierté.</p>
        </div>
    </footer>
</body>
</html>
