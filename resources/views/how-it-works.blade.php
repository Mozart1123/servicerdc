@extends('layouts.public')

@section('title', 'Comment ça marche')
@section('meta_description', 'Découvrez comment ProConnect met en relation clients, artisans, recruteurs et chercheurs d\'emploi en RDC, en 3 étapes simples.')

@section('content')
<div class="py-20">
    {{-- En-tête --}}
    <div class="text-center mb-16 px-4">
        <span class="inline-block px-6 py-2 bg-green-100 text-green-800 rounded-full font-semibold mb-4">
            <i class="fas fa-play-circle mr-2"></i>Comment ça marche
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
            Commencez en <span class="text-[#29B6D1]">3 étapes simples</span>
        </h1>
        <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
            Notre processus simplifié vous permet de trouver rapidement ce dont vous avez besoin,
            que ce soit un service, un artisan, ou un emploi.
        </p>
    </div>

    {{-- Les 3 étapes --}}
    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
        <div class="text-center">
            <div class="relative mb-8">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-[#29B6D1] to-[#1E8FA3] rounded-2xl flex items-center justify-center shadow-xl">
                    <div class="text-white text-3xl font-bold">1</div>
                </div>
                <div class="absolute -top-2 -right-2 w-10 h-10 bg-[#F0B800] rounded-full flex items-center justify-center shadow-lg mx-auto" style="left: 50%; margin-left: 22px;">
                    <i class="fas fa-map-marker-alt text-white text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-4">Localisez-vous</h3>
            <p class="text-slate-600 leading-relaxed">
                Autorisez la géolocalisation pour découvrir les services et emplois les plus proches
                de votre position en RDC.
            </p>
        </div>
        <div class="text-center">
            <div class="relative mb-8">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-[#F0B800] to-yellow-500 rounded-2xl flex items-center justify-center shadow-xl">
                    <div class="text-slate-900 text-3xl font-bold">2</div>
                </div>
                <div class="absolute -top-2 -right-2 w-10 h-10 bg-[#29B6D1] rounded-full flex items-center justify-center shadow-lg mx-auto" style="left: 50%; margin-left: 22px;">
                    <i class="fas fa-search text-white text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-4">Recherchez</h3>
            <p class="text-slate-600 leading-relaxed">
                Utilisez notre moteur de recherche intelligent pour trouver exactement le service
                ou l'emploi correspondant à vos besoins.
            </p>
        </div>
        <div class="text-center">
            <div class="relative mb-8">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-xl">
                    <div class="text-white text-3xl font-bold">3</div>
                </div>
                <div class="absolute -top-2 -right-2 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center shadow-lg mx-auto" style="left: 50%; margin-left: 22px;">
                    <i class="fas fa-handshake text-white text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-4">Connectez-vous</h3>
            <p class="text-slate-600 leading-relaxed">
                Contactez directement les artisans ou postulez aux offres d'emploi qui vous
                intéressent, tout est sécurisé.
            </p>
        </div>
    </div>

    {{-- Le détail par profil --}}
    <div class="bg-slate-50 py-20">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Selon votre profil</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    ProConnect s'adapte à ce que vous cherchez à faire sur la plateforme.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Client --}}
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-8">
                    <div class="w-12 h-12 rounded-xl bg-[#29B6D1]/10 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-[#29B6D1]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Je suis client</h3>
                    <ol class="space-y-3 text-slate-600 text-sm">
                        <li><span class="font-bold text-slate-900">1.</span> Recherchez le service dont vous avez besoin (plomberie, électricité, couture...).</li>
                        <li><span class="font-bold text-slate-900">2.</span> Choisissez un artisan vérifié et envoyez une demande.</li>
                        <li><span class="font-bold text-slate-900">3.</span> Une fois la demande acceptée, payez en espèces ou via Mobile Money.</li>
                        <li><span class="font-bold text-slate-900">4.</span> Suivez la mission jusqu'à sa complétion et laissez un avis.</li>
                    </ol>
                </div>

                {{-- Artisan --}}
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-8">
                    <div class="w-12 h-12 rounded-xl bg-[#F0B800]/10 flex items-center justify-center mb-4">
                        <i class="fas fa-screwdriver-wrench text-[#F0B800]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Je suis artisan</h3>
                    <ol class="space-y-3 text-slate-600 text-sm">
                        <li><span class="font-bold text-slate-900">1.</span> Créez votre profil et faites vérifier votre identité.</li>
                        <li><span class="font-bold text-slate-900">2.</span> Publiez vos services avec vos tarifs.</li>
                        <li><span class="font-bold text-slate-900">3.</span> Acceptez les demandes de clients près de chez vous.</li>
                        <li><span class="font-bold text-slate-900">4.</span> Réalisez la mission et recevez votre paiement.</li>
                    </ol>
                </div>

                {{-- Recruteur --}}
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-8">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Je suis recruteur</h3>
                    <ol class="space-y-3 text-slate-600 text-sm">
                        <li><span class="font-bold text-slate-900">1.</span> Publiez une offre d'emploi détaillée.</li>
                        <li><span class="font-bold text-slate-900">2.</span> Recevez les candidatures directement sur votre tableau de bord.</li>
                        <li><span class="font-bold text-slate-900">3.</span> Filtrez, passez en entretien, puis embauchez le bon candidat.</li>
                    </ol>
                </div>

                {{-- Chercheur d'emploi --}}
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-8">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-4">
                        <i class="fas fa-briefcase text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Je cherche un emploi</h3>
                    <ol class="space-y-3 text-slate-600 text-sm">
                        <li><span class="font-bold text-slate-900">1.</span> Complétez votre profil et ajoutez votre CV.</li>
                        <li><span class="font-bold text-slate-900">2.</span> Parcourez les offres disponibles dans toute la RDC.</li>
                        <li><span class="font-bold text-slate-900">3.</span> Postulez en un clic et suivez l'avancement de vos candidatures.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="max-w-4xl mx-auto px-4 mt-20 text-center">
        <div class="inline-flex flex-col items-center gap-6 p-10 bg-gradient-to-r from-[#29B6D1]/5 to-[#F0B800]/5 rounded-2xl border border-[#29B6D1]/10 w-full">
            <h3 class="text-2xl md:text-3xl font-bold text-slate-900">Prêt à commencer votre aventure ?</h3>
            <p class="text-lg text-slate-600 max-w-2xl">
                Rejoignez la communauté ProConnect et accédez à des milliers d'opportunités
                dans toute la République Démocratique du Congo.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}"
                       class="px-8 py-4 bg-gradient-to-r from-[#29B6D1] to-[#1E8FA3] text-white font-bold rounded-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-user-plus"></i>
                        <span>Créer mon compte gratuit</span>
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-8 py-4 bg-white border-2 border-[#29B6D1] text-[#29B6D1] font-bold rounded-xl hover:bg-[#29B6D1]/5 transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>J'ai déjà un compte</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="px-8 py-4 bg-gradient-to-r from-[#29B6D1] to-[#1E8FA3] text-white font-bold rounded-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-rocket"></i>
                        <span>Accéder à mon espace</span>
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
