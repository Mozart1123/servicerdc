<!DOCTYPE html>
<html lang="fr" x-data="{ showApplyModal: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $jobOffer->title }} - ServiceRDC</title>

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
        .premium-card { background: #FFFFFF; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 0, 0, 0.03); }
        .btn-primary { background-color: #007FFF; color: white; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #0066CC; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 127, 255, 0.2); }
    </style>
</head>

<body class="font-sans antialiased pb-20">
    <!-- Navigation -->
    <nav class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-20 sticky top-0 z-50">
        <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 text-gray-500 hover:text-congo-blue font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux offres</span>
        </a>
        <div class="flex items-center space-x-3">
             <div class="w-8 h-8 bg-congo-blue rounded-lg flex items-center justify-center text-white text-xs">
                <i class="fas fa-briefcase"></i>
            </div>
            <span class="font-black text-gray-900">Service<span class="text-congo-blue">RDC</span></span>
        </div>
    </nav>

    <main class="mt-12 px-6 lg:px-20 max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <div class="premium-card p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-congo-blue text-4xl font-black">
                            {{ substr($jobOffer->title, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tight leading-tight">{{ $jobOffer->title }}</h1>
                            <p class="text-sm font-black text-congo-blue uppercase tracking-widest mt-2">{{ $jobOffer->company_name ?? 'Entreprise' }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-8 text-sm font-bold text-gray-500 mb-12 border-y border-gray-50 py-8">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-congo-blue"></i>
                            <span>{{ $jobOffer->location }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-wallet text-congo-blue"></i>
                            <span>{{ $jobOffer->salary_range ?? '800 - 1200' }} $</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock text-congo-blue"></i>
                            <span>{{ $jobOffer->contract_type ?? 'Plein Temps' }}</span>
                        </div>
                    </div>

                    <div class="prose max-w-none text-gray-600 space-y-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 mb-4">Description du poste</h3>
                            <p class="leading-relaxed font-medium">{{ $jobOffer->description }}</p>
                        </div>
                        @if($jobOffer->requirements)
                        <div>
                            <h3 class="text-xl font-black text-gray-900 mb-4">Profil recherché</h3>
                            <p class="leading-relaxed font-medium">{{ $jobOffer->requirements }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar CTA -->
            <div class="lg:col-span-1">
                <div class="premium-card p-8 sticky top-32">
                    <div class="text-center mb-8">
                         <div class="w-16 h-16 bg-congo-blue/5 rounded-2xl flex items-center justify-center text-congo-blue text-2xl mx-auto mb-4">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h4 class="text-lg font-black text-gray-900">Postuler à cette offre</h4>
                        <p class="text-xs font-bold text-gray-400 mt-2">Réponse moyenne en 48h</p>
                    </div>
                    
                    <button @click="showApplyModal = true" class="w-full btn-primary py-5 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl mb-6">POSTULER MAINTENANT</button>
                    
                    <div class="space-y-4 pt-8 border-t border-gray-100">
                        <button class="w-full py-4 text-gray-500 font-bold text-xs uppercase flex items-center justify-center gap-2 hover:text-congo-blue transition-colors">
                            <i class="far fa-heart"></i> Sauvegarder
                        </button>
                        <button class="w-full py-4 text-gray-500 font-bold text-xs uppercase flex items-center justify-center gap-2 hover:text-congo-blue transition-colors">
                            <i class="fas fa-share-alt"></i> Partager l'offre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal (Copied from emploie) -->
    <div x-show="showApplyModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div @click="showApplyModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="premium-card w-full max-w-xl relative p-10">
             <div class="flex items-center justify-between mb-8">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tight">Candidature Rapide</h3>
                    <button @click="showApplyModal = false" class="text-gray-400 hover:text-congo-red">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Mini Form -->
                <div class="space-y-6">
                    <div class="p-8 border-2 border-dashed border-gray-100 rounded-2xl text-center bg-gray-50">
                        <i class="fas fa-file-pdf text-3xl text-gray-300 mb-4"></i>
                        <p class="text-sm font-black text-gray-900">Télécharger votre CV</p>
                    </div>
                    <button class="w-full btn-primary py-5 rounded-2xl font-black text-xs tracking-widest uppercase">ENVOYER</button>
                </div>
        </div>
    </div>
</body>
</html>
