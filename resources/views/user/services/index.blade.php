<!DOCTYPE html>
<html lang="fr" x-data="{ 
    searchQuery: '',
    categoryFilter: 'Toutes les catégories',
    cityFilter: 'Toutes les villes'
}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ServiceRDC - Annuaire National des Artisans et Services">
    <title>Trouver un Artisan - ServiceRDC</title>

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
        .hero-gradient { background: linear-gradient(135deg, #F7D000 0%, #D4B100 100%); }
        .premium-card { background: #FFFFFF; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 0, 0, 0.03); transition: all 0.3s ease; }
        .premium-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #007FFF; color: white; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #0066CC; transform: translateY(-1px); }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Navbar -->
    <nav class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-20 sticky top-0 z-50">
        <a href="/" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-congo-gold rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-tools"></i>
            </div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Service<span class="text-congo-blue">RDC</span></h1>
        </a>
        <div class="hidden lg:flex items-center gap-10 text-sm font-black text-gray-400 uppercase tracking-widest">
            <a href="{{ route('services.index') }}" class="text-congo-gold border-b-2 border-congo-gold pb-1">Artisans</a>
            <a href="{{ route('jobs.index') }}" class="hover:text-congo-gold transition-colors">Emplois</a>
            @auth
                <a href="{{ route('user.dashboard') }}" class="px-6 py-2 bg-congo-blue/10 text-congo-blue rounded-xl">Mon Espace</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-congo-gold transition-colors">Connexion</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-gradient py-24 px-6 lg:px-20 text-gray-900 relative overflow-hidden">
        <div class="max-w-4xl relative z-10">
            <h2 class="text-5xl lg:text-7xl font-black mb-6 tracking-tighter leading-tight">VÉROLEZ <br><span class="text-white underline shadow-sm">LES MEILLEURS EXPERTS.</span></h2>
            <p class="text-xl text-gray-800 font-medium mb-12 max-w-2xl leading-relaxed">Le premier annuaire national certifié pour tous vos besoins techniques et domestiques en RDC.</p>
            
            <!-- Floating Search Bar -->
            <div class="bg-white p-4 rounded-[2rem] shadow-2xl flex flex-col lg:flex-row gap-4 items-center border-4 border-white/20">
                <div class="flex-1 flex items-center gap-4 px-6 w-full divide-x divide-gray-100">
                    <div class="flex-1 flex items-center gap-4">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Qui cherchez-vous ? (Électricien, Plombier...)" class="w-full py-4 text-gray-900 font-bold outline-none placeholder-gray-400">
                    </div>
                </div>
                <div class="flex-1 flex items-center gap-4 px-6 w-full lg:border-l lg:border-gray-100">
                    <i class="fas fa-map-marker-alt text-congo-gold"></i>
                    <select x-model="cityFilter" class="w-full py-4 text-gray-900 font-bold outline-none appearance-none bg-transparent">
                        <option>Toutes les villes</option>
                        <option>Kinshasa</option>
                        <option>Goma</option>
                        <option>Lubumbashi</option>
                    </select>
                </div>
                <button class="w-full lg:w-auto px-12 py-5 bg-gray-900 text-congo-gold rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl">TROUVER MAINTENANT</button>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/20 rounded-full blur-3xl"></div>
    </header>

    <main class="py-20 px-6 lg:px-20 max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-16">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-80 space-y-12">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Catégories Populaires</h3>
                    <div class="space-y-4">
                        @foreach($categories ?? ['Électricité', 'Plomberie', 'Couture', 'Maçonnerie', 'Informatique'] as $cat)
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <input type="checkbox" class="w-6 h-6 rounded-lg text-congo-gold focus:ring-congo-gold border-gray-200">
                            <span class="text-sm font-bold text-gray-600 group-hover:text-congo-gold transition-colors">{{ is_string($cat) ? $cat : $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="premium-card p-8 bg-congo-blue text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <h4 class="text-xl font-black mb-4">Vous êtes Artisan ?</h4>
                        <p class="text-sm text-white/70 mb-8 font-medium">Rejoignez la plateforme et boostez votre visibilité nationale dès aujourd'hui.</p>
                        <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-congo-blue rounded-xl font-black text-xs uppercase tracking-widest shadow-lg transform group-hover:scale-105 transition-all">S'INSCRIRE</a>
                    </div>
                    <i class="fas fa-tools absolute -bottom-12 -right-12 text-[10rem] text-white/5 transform -rotate-12 group-hover:rotate-0 transition-all duration-700"></i>
                </div>
            </aside>

            <!-- Results Grid -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-12">
                     <p class="text-sm font-black text-gray-400 uppercase tracking-widest">{{ count($services ?? $recentServices ?? []) }} Experts certifiés</p>
                     <div class="flex items-center gap-6">
                         <div class="flex items-center gap-2 text-xs font-black text-congo-gold uppercase tracking-widest">
                             <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                             <span>{{ rand(50, 200) }} Disponibles</span>
                         </div>
                     </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($services ?? $recentServices ?? [] as $service)
                    <div class="premium-card p-10 flex flex-col justify-between hover:border-congo-gold group">
                        <div>
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-congo-gold text-2xl font-black border border-gray-100 group-hover:border-congo-gold/30 transition-colors">
                                    {{ substr($service->name, 0, 1) }}
                                </div>
                                <div class="flex items-center gap-1 text-congo-gold text-sm font-black">
                                    <i class="fas fa-star"></i>
                                    <span class="text-gray-900">4.{{ rand(5,9) }}</span>
                                </div>
                            </div>
                            <h4 class="text-2xl font-black text-gray-900 leading-tight mb-2">{{ $service->name }}</h4>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Par {{ $service->user->name ?? 'Expert Indépendant' }}</p>
                            <p class="text-gray-500 font-medium text-sm leading-relaxed line-clamp-3 mb-8">{{ $service->description }}</p>
                        </div>
                        <div class="pt-8 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">A partir de</span>
                                <span class="text-xl font-black text-gray-900">{{ $service->price ?? rand(20, 100) }} $</span>
                            </div>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-gray-50 text-gray-900 rounded-xl font-black text-[10px] uppercase tracking-widest group-hover:bg-congo-gold group-hover:text-white transition-all shadow-sm">RÉSERVER</a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-24 premium-card text-center opacity-50 border-2 border-dashed border-gray-300 bg-transparent">
                        <i class="fas fa-user-slash text-6xl text-gray-300 mb-6"></i>
                        <p class="text-xl font-black text-gray-900">Désolé, aucun expert trouvé.</p>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Réessayez avec d'autres filtres ou une autre ville.</p>
                    </div>
                    @endforelse
                </div>
                
                @if(isset($services) && method_exists($services, 'links'))
                <div class="pt-12">
                    {{ $services->links() }}
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-24 px-6 lg:px-20 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-16">
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-3xl font-black mb-8 tracking-tighter">Service<span class="text-congo-blue">RDC</span></h3>
                <p class="text-gray-400 leading-relaxed font-medium max-w-sm">La force du travail local, propulsée vers le futur. Trouvez l'expertise dont vous avez besoin, partout en RDC.</p>
            </div>
            <div>
                 <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-10 text-congo-gold">Artisans</h4>
                 <ul class="space-y-4 text-sm font-black text-gray-400 uppercase tracking-widest">
                    <li><a href="#" class="hover:text-congo-gold transition-colors">Devenir Partenaire</a></li>
                    <li><a href="#" class="hover:text-congo-gold transition-colors">Nos Certifications</a></li>
                    <li><a href="#" class="hover:text-congo-gold transition-colors">Support Technique</a></li>
                 </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-white/5 mt-24 pt-10 flex flex-col md:flex-row justify-between text-[10px] font-black text-white/30 uppercase tracking-widest">
            <p>&copy; 2026 ServiceRDC. Tous droits réservés.</p>
            <p>🇨🇩 République Démocratique du Congo</p>
        </div>
    </footer>
</body>
</html>
