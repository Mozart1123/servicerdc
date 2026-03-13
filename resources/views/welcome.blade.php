<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="ServiceRDC - Plateforme de services et d'emplois en République Démocratique du Congo">
    <title>ServiceRDC | Trouvez artisans et emplois en RDC</title>

    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Configuration avec couleurs RDC -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: {
                        'xs': '400px',
                    },
                    colors: {
                        // Couleurs officielles drapeau RDC
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#003366',
                        // Couleurs utilitaires
                        'rdc-success': '#10B981',
                        'rdc-warning': '#F59E0B',
                        'rdc-error': '#EF4444',
                        'rdc-info': '#3B82F6',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif'],
                        'poppins': ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        }
                    }
                }
            },
            plugins: [],
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <style>
        :root {
            --primary-blue: #007FFF;
            --primary-blue-dark: #0066CC;
            --accent-yellow: #F0B800;
            --accent-red: #FF4757;
            --dark-blue: #003366;
            --light-bg: #F8FAFC;
        }

        * {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* Dégradés et effets */
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 50%, var(--dark-blue) 100%);
        }

        .gradient-text {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-yellow), var(--accent-red));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .flag-stripe {
            height: 5px;
            background: linear-gradient(90deg,
                    var(--primary-blue) 0%,
                    var(--primary-blue) 33%,
                    var(--accent-yellow) 33%,
                    var(--accent-yellow) 66%,
                    var(--accent-red) 66%,
                    var(--accent-red) 100%);
        }

        /* Animations personnalisées */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .shimmer-effect {
            background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0) 100%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        /* Loading screen amélioré */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue-dark) 50%, var(--primary-blue) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.8s;
        }

        .loader-logo {
            width: 154px;
            height: 154px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 0 50px rgba(0, 127, 255, 0.3);
            padding: 20px;
        }

        .loader-logo::before,
        .loader-logo::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .loader-logo::before {
            width: 170px;
            height: 170px;
            border-top: 3px solid var(--accent-yellow);
            border-right: 3px solid var(--accent-red);
            animation: spin 2s linear infinite;
        }

        .loader-logo::after {
            width: 190px;
            height: 190px;
            border-top: 3px solid var(--primary-blue);
            border-bottom: 3px solid var(--accent-yellow);
            animation: spin 3s linear infinite reverse;
        }

        .loader-icon {
            font-size: 60px;
            color: white;
            animation: float 2s ease-in-out infinite;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        .loader-text {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-top: 1rem;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .loader-subtext {
            color: rgba(255, 255, 255, 0.85);
            margin-top: 0.5rem;
            font-size: 1rem;
            text-align: center;
            max-width: 300px;
        }

        .progress-bar {
            width: 300px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-top: 2rem;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg,
                    var(--primary-blue),
                    var(--accent-yellow),
                    var(--accent-red));
            border-radius: 3px;
            transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background-image: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.4),
                    transparent);
            animation: shimmer 2s infinite;
        }

        /* Contenu principal */
        #main-content {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
            animation-delay: 0.2s;
        }

        /* Styles pour les boutons */
        .btn-primary {
            background: var(--primary-blue);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-blue-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 127, 255, 0.2);
        }

        .btn-secondary {
            background: var(--accent-yellow);
            color: var(--dark-blue);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #E0A800;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 184, 0, 0.2);
        }

        /* Styles pour les cartes */
        .service-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-yellow));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        /* Badge urgent */
        .badge-urgent {
            background: linear-gradient(135deg, var(--accent-red), #FF6B8B);
            color: white;
            animation: pulse 2s infinite;
        }

        /* Section styles */
        .section-title {
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-yellow));
            border-radius: 2px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .loader-logo {
                width: 120px;
                height: 120px;
            }

            .loader-logo::before {
                width: 140px;
                height: 140px;
            }

            .loader-logo::after {
                width: 160px;
                height: 160px;
            }

            .loader-text {
                font-size: 2rem;
            }
        }

        /* Accessibility improvements */
        .focus-visible {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        /* Performance optimizations */
        .will-change-transform {
            will-change: transform;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 overflow-x-hidden">
    <!-- Écran de chargement -->
    <div id="loading-screen">
        <div class="loader-logo">
            <img src="/assets/img/logo.png?v=1.1" alt="ServiceRDC Logo" class="loader-img w-full h-full object-contain">
        </div>
        <div class="loader-text text-xl sm:text-2xl md:text-3xl">Service<span class="text-yellow-300">RDC</span></div>
        <div class="loader-subtext">La plateforme congolaise pour vos services et emplois</div>
        <div class="loader-subtext mt-2 animate-pulse">Chargement en cours...</div>
        <div class="progress-bar">
            <div class="progress-fill" id="progress-fill"></div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div id="main-content" class="min-h-screen flex flex-col overflow-x-hidden">
        <!-- Header & Navigation -->
        <header class="sticky top-0 z-50 bg-white shadow-lg will-change-transform">
            <div class="flag-stripe"></div>
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <a href="/" class="flex items-center space-x-2 sm:space-x-3 group min-w-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden bg-white flex items-center justify-center 
                                    group-hover:scale-105 transition-transform duration-300 shadow-lg shrink-0">
                            <img src="/assets/img/logo.png?v=1.1" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="truncate">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">Service<span class="text-rdc-blue">RDC</span></h1>
                            <p class="text-[10px] text-gray-600 hidden xs:block truncate">Plateforme de services & emplois</p>
                        </div>
                    </a>

                    <!-- Navigation Desktop -->
                    <nav class="hidden lg:flex space-x-8 items-center">
                        <a href="#accueil" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300 
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 
                                  after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                            Accueil
                        </a>
                        <a href="#services" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300 
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 
                                  after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                            Services
                        </a>
                        <a href="#emplois" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300 
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 
                                  after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                            Emplois
                        </a>
                        <a href="#fonctionnement" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300 
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 
                                  after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                            Fonctionnement
                        </a>
                        <a href="#temoignages" class="text-gray-700 hover:text-rdc-blue font-medium transition-colors duration-300 
                                  relative after:absolute after:bottom-0 after:left-0 after:h-0.5 
                                  after:w-0 after:bg-rdc-blue after:transition-all hover:after:w-full">
                            Témoignages
                        </a>
                    </nav>

                        <!-- Boutons CTA et Auth -->
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            <!-- Bouton Localisation -->
                            <button id="geolocation-btn" class="hidden xl:flex items-center space-x-2 px-4 py-2 bg-rdc-blue/10 
                                           text-rdc-blue rounded-lg hover:bg-rdc-blue/20 transition-all 
                                           duration-300 animate-pulse-slow border border-rdc-blue/20"
                                aria-label="Détecter ma position">
                                <i class="fas fa-location-dot"></i>
                                <span id="location-text">Me localiser</span>
                            </button>

                            <!-- État Auth -->
                            @guest
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <a href="{{ route('login') }}" class="px-3 sm:px-5 py-2 sm:py-2.5 text-rdc-blue text-sm sm:text-base font-semibold hover:text-rdc-blue-dark 
                                                          transition-colors duration-300 border border-rdc-blue/30 
                                                          rounded-lg hover:border-rdc-blue/50">
                                        Connexion
                                    </a>
                                    <a href="{{ route('register') }}" class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                                          text-white text-sm sm:text-base font-semibold rounded-lg hover:shadow-lg 
                                                          transition-all duration-300 hover:scale-105">
                                        Inscription
                                    </a>
                                </div>
                            @endguest

                        @auth
                            <div class="flex items-center space-x-4">
                                <!-- Notification Badge -->
                                <div class="relative">
                                    <button class="p-2 text-gray-600 hover:text-rdc-blue transition-colors">
                                        <i class="fas fa-bell text-xl"></i>
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-rdc-red text-white 
                                                                 text-xs rounded-full flex items-center justify-center">
                                            3
                                        </span>
                                    </button>
                                </div>

                                <!-- Dashboard Button -->
                                <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                                      text-white font-semibold rounded-lg hover:shadow-lg 
                                                      transition-all duration-300 flex items-center space-x-2">
                                    <i class="fas fa-th-large"></i>
                                    <span>Dashboard</span>
                                </a>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="p-2.5 text-gray-500 hover:text-rdc-red transition-colors 
                                                               rounded-lg border border-gray-200 hover:border-rdc-red/30"
                                        aria-label="Déconnexion">
                                        <i class="fas fa-sign-out-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        @endauth

                        <!-- Menu Mobile Toggle -->
                        <button id="mobile-menu-btn"
                            class="lg:hidden p-2 text-gray-700 hover:text-rdc-blue transition-colors"
                            aria-label="Menu mobile">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Menu Mobile -->
                <div id="mobile-menu" class="lg:hidden mt-6 py-6 border-t border-gray-200 hidden bg-white 
                            rounded-lg shadow-xl animate-fade-in-up">
                    <div class="flex flex-col space-y-4">
                        <a href="#accueil" class="px-4 py-3 text-gray-700 hover:text-rdc-blue hover:bg-rdc-blue/5 
                                  rounded-lg transition-colors duration-300 font-medium">
                            <i class="fas fa-home mr-3"></i>Accueil
                        </a>
                        <a href="#services" class="px-4 py-3 text-gray-700 hover:text-rdc-blue hover:bg-rdc-blue/5 
                                  rounded-lg transition-colors duration-300 font-medium">
                            <i class="fas fa-tools mr-3"></i>Services
                        </a>
                        <a href="#emplois" class="px-4 py-3 text-gray-700 hover:text-rdc-blue hover:bg-rdc-blue/5 
                                  rounded-lg transition-colors duration-300 font-medium">
                            <i class="fas fa-briefcase mr-3"></i>Emplois
                        </a>
                        <a href="#fonctionnement" class="px-4 py-3 text-gray-700 hover:text-rdc-blue hover:bg-rdc-blue/5 
                                  rounded-lg transition-colors duration-300 font-medium">
                            <i class="fas fa-play-circle mr-3"></i>Fonctionnement
                        </a>
                        <a href="#temoignages" class="px-4 py-3 text-gray-700 hover:text-rdc-blue hover:bg-rdc-blue/5 
                                  rounded-lg transition-colors duration-300 font-medium">
                            <i class="fas fa-comment-dots mr-3"></i>Témoignages
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="accueil" class="gradient-bg text-white overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-rdc-blue/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-rdc-yellow/20 rounded-full blur-3xl"></div>
            </div>

            <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <!-- Texte Hero -->
                    <div class="lg:w-1/2 mb-12 lg:mb-0 text-center lg:text-left" data-aos="fade-right" data-aos-duration="800">
                        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm 
                                    rounded-full mb-6 animate-fade-in-up">
                            <span class="text-[10px] sm:text-sm font-medium">🌟 Nouveau : Tableau de bord utilisateur</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                            <span class="block">Trouvez l'artisan</span>
                            <span class="block">ou l'emploi idéal</span>
                            <span class="gradient-text">en RDC</span>
                        </h1>

                        <p class="text-lg sm:text-xl md:text-2xl mb-8 sm:mb-10 text-blue-100 leading-relaxed max-w-2xl">
                            La première plateforme congolaise qui connecte directement
                            <span class="font-semibold text-yellow-200">chercheurs d'emploi</span> et
                            <span class="font-semibold text-yellow-200">clients</span> aux meilleurs
                            <span class="font-semibold text-yellow-200">prestataires de services</span>.
                        </p>

                        <!-- Barre de recherche améliorée -->
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2 sm:p-3 shadow-2xl border border-white/20 
                                    animate-fade-in-up" data-aos="fade-up" data-aos-delay="200">
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                <div class="flex-1 relative">
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" id="search-input"
                                        placeholder="Service, emploi..." class="w-full pl-12 pr-4 py-3 sm:py-4 rounded-xl border-0 
                                                  focus:ring-3 focus:ring-rdc-blue/30 text-gray-800
                                                  placeholder-gray-500 text-sm sm:text-base"
                                        aria-label="Rechercher des services ou emplois">
                                    <div id="search-suggestions" class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl 
                                                border border-gray-200 hidden overflow-hidden">
                                        <!-- Suggestions loaded by JS -->
                                    </div>
                                </div>
                                <button id="search-btn" class="px-8 py-4 bg-gradient-to-r from-rdc-yellow to-yellow-500 
                                               text-gray-900 font-bold rounded-xl hover:shadow-lg 
                                               transition-all duration-300 hover:scale-105 flex items-center 
                                               justify-center space-x-2">
                                    <i class="fas fa-search"></i>
                                    <span>Rechercher</span>
                                </button>
                            </div>

                            <!-- Tags de recherche rapide -->
                            <div class="flex flex-wrap gap-2 mt-4">
                                <span class="text-sm text-blue-100">Recherches rapides :</span>
                                <button class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-full 
                                               text-sm transition-colors duration-300 text-blue-100">
                                    Électricien
                                </button>
                                <button class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-full 
                                               text-sm transition-colors duration-300 text-blue-100">
                                    Plombier
                                </button>
                                <button class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-full 
                                               text-sm transition-colors duration-300 text-blue-100">
                                    Couturier
                                </button>
                                <button class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-full 
                                               text-sm transition-colors duration-300 text-blue-100">
                                    CDI Kinshasa
                                </button>
                            </div>
                        </div>

                        <!-- Statistiques améliorées -->
                        <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mt-8 sm:mt-12" data-aos="fade-up"
                            data-aos-delay="400">
                            <div class="text-center bg-white/5 backdrop-blur-sm rounded-2xl p-4 sm:p-6 
                                        border border-white/10 hover:border-white/20 transition-colors">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2 flex items-center justify-center">
                                    <span class="text-rdc-yellow">5,000+</span>
                                    <i class="fas fa-user-check ml-2 text-green-400 text-sm sm:text-base"></i>
                                </div>
                                <div class="text-blue-100 text-xs sm:text-sm font-medium">Artisans vérifiés</div>
                            </div>
                            <div class="text-center bg-white/5 backdrop-blur-sm rounded-2xl p-4 sm:p-6 
                                        border border-white/10 hover:border-white/20 transition-colors">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2 flex items-center justify-center">
                                    <span class="text-rdc-yellow">1,200+</span>
                                    <i class="fas fa-briefcase ml-2 text-blue-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="text-blue-100 text-xs sm:text-sm font-medium">Offres actives</div>
                            </div>
                            <div class="text-center bg-white/5 backdrop-blur-sm rounded-2xl p-4 sm:p-6 
                                        border border-white/10 hover:border-white/20 transition-colors xs:col-span-2 md:col-span-1">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-bold mb-1 sm:mb-2 flex items-center justify-center">
                                    <span class="text-rdc-yellow">25</span>
                                    <i class="fas fa-map-marker-alt ml-2 text-red-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="text-blue-100 text-xs sm:text-sm font-medium">Provinces couvertes</div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte interactive -->
                    <div class="lg:w-1/2 lg:pl-12" data-aos="fade-left" data-aos-duration="800">
                        <div class="relative">
                            <!-- Carte principale -->
                            <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border-2 border-white/20 
                                        shadow-2xl transform hover:scale-[1.02] transition-transform duration-500">
                                <!-- En-tête carte -->
                                <div class="text-center mb-8">
                                    <div class="inline-flex items-center px-4 py-2 bg-rdc-blue/30 
                                                rounded-full mb-4">
                                        <i class="fas fa-map-marker-alt text-yellow-300 mr-2"></i>
                                        <h3 class="text-2xl font-bold">Localisé à
                                            <span id="current-city" class="text-yellow-300">Kinshasa</span>
                                        </h3>
                                    </div>
                                    <div class="flex items-center justify-center text-blue-100">
                                        <i class="fas fa-crosshairs text-green-400 mr-2"></i>
                                        <span id="current-address" class="text-lg">
                                            Détection automatique activée
                                        </span>
                                    </div>
                                </div>

                                <!-- Services à proximité -->
                                <div class="bg-gradient-to-r from-rdc-blue/20 to-rdc-yellow/20 
                                            rounded-2xl p-6 mb-8 border border-white/10">
                                    <div class="flex justify-between items-center mb-6">
                                        <h4 class="text-xl font-bold text-white flex items-center">
                                            <i class="fas fa-bolt text-yellow-300 mr-3"></i>
                                            Services à proximité
                                        </h4>
                                        <span class="px-4 py-1 bg-rdc-blue text-white text-sm 
                                                     font-semibold rounded-full">
                                            <i class="fas fa-circle text-green-400 mr-1 text-xs"></i>
                                            Rayon: 10 km
                                        </span>
                                    </div>

                                    <!-- Liste services -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between p-3 bg-white/5 
                                                    rounded-xl hover:bg-white/10 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-blue-100/20 
                                                            flex items-center justify-center mr-4">
                                                    <i class="fas fa-bolt text-blue-300 text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Électriciens</div>
                                                    <div class="text-sm text-blue-100">Installation & dépannage</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-lg">15</div>
                                                <div class="text-sm text-green-300">disponibles</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-white/5 
                                                    rounded-xl hover:bg-white/10 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-yellow-100/20 
                                                            flex items-center justify-center mr-4">
                                                    <i class="fas fa-tools text-yellow-300 text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Plombiers</div>
                                                    <div class="text-sm text-blue-100">Installation & réparation</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-lg">9</div>
                                                <div class="text-sm text-green-300">disponibles</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-white/5 
                                                    rounded-xl hover:bg-white/10 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-red-100/20 
                                                            flex items-center justify-center mr-4">
                                                    <i class="fas fa-cut text-red-300 text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Couturiers</div>
                                                    <div class="text-sm text-blue-100">Sur mesure & réparation</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-lg">22</div>
                                                <div class="text-sm text-green-300">disponibles</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicateur de proximité -->
                                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-white/10">
                                        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                                            <div class="text-[10px] sm:text-sm text-blue-100 text-center sm:text-left">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Services dans votre zone
                                            </div>
                                            <div class="flex space-x-2">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA Carte -->
                                <div class="text-center">
                                    <a href="#services" class="inline-flex items-center px-8 py-4 bg-gradient-to-r 
                                              from-white to-gray-100 text-rdc-dark-blue font-bold 
                                              rounded-xl hover:shadow-2xl transition-all duration-300 
                                              hover:scale-105 group">
                                        <i class="fas fa-eye mr-3 group-hover:rotate-12 transition-transform"></i>
                                        Explorer tous les services
                                        <i class="fas fa-arrow-right ml-3 group-hover:translate-x-2 
                                                   transition-transform"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Éléments décoratifs -->
                            <div class="absolute -top-4 -right-4 w-20 h-20 bg-rdc-yellow/20 
                                        rounded-full blur-xl"></div>
                            <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-rdc-blue/20 
                                        rounded-full blur-xl"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vague décorative -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-12">
                    <path
                        d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                        opacity=".25" class="fill-current text-rdc-dark-blue"></path>
                    <path
                        d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                        opacity=".5" class="fill-current text-rdc-blue"></path>
                    <path
                        d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                        class="fill-current text-white/10"></path>
                </svg>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <!-- En-tête section -->
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="inline-block px-6 py-2 bg-rdc-blue/10 text-rdc-blue 
                                  rounded-full font-semibold mb-4">
                        <i class="fas fa-star mr-2"></i>Services populaires
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Découvrez les services
                        <span class="relative inline-block">
                            <span class="text-rdc-blue">les plus demandés</span>
                            <svg class="absolute -bottom-2 left-0 w-full" height="8">
                                <path d="M0,4 Q80,8 160,4 T320,4" fill="none" stroke="url(#gradient)"
                                    stroke-width="4" />
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#007FFF" />
                                        <stop offset="100%" stop-color="#F0B800" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Trouvez rapidement l'artisan dont vous avez besoin parmi nos catégories
                        de services les plus recherchées par les Congolais.
                    </p>
                </div>

                <!-- Catégories Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 
                            gap-6 mb-12" id="categories-container">
                    <!-- Catégories chargées par JavaScript -->
                </div>

                <!-- CTA Services -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="inline-flex flex-col sm:flex-row items-center gap-4 p-6 
                                bg-gradient-to-r from-rdc-blue/5 to-rdc-yellow/5 
                                rounded-2xl border border-rdc-blue/10">
                        <div class="text-left">
                            <h4 class="font-bold text-lg text-gray-900 mb-1">
                                <i class="fas fa-lightbulb text-rdc-yellow mr-2"></i>
                                Besoin d'un service spécifique ?
                            </h4>
                            <p class="text-gray-600 text-sm">
                                Ne trouvez-vous pas le service que vous cherchez ?
                                Décrivez votre besoin et nous trouverons la solution.
                            </p>
                        </div>
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                  text-white font-semibold rounded-lg hover:shadow-lg 
                                  transition-all duration-300 hover:scale-105 whitespace-nowrap 
                                  flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Faire une demande</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Emplois -->
        <section id="emplois" class="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-4">
                <!-- En-tête section -->
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="inline-block px-6 py-2 bg-rdc-yellow/10 text-rdc-dark-blue 
                                  rounded-full font-semibold mb-4">
                        <i class="fas fa-briefcase mr-2"></i>Opportunités d'emploi
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Trouvez votre
                        <span class="text-rdc-blue">prochain emploi</span>
                        en RDC
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Postulez facilement à des offres d'emploi dans toute la République
                        et développez votre carrière avec ServiceRDC.
                    </p>
                </div>

                <!-- Jobs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" id="jobs-container">
                    <!-- Offres d'emploi chargées par JavaScript -->
                </div>

                <!-- CTA Emplois -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r 
                              from-rdc-blue to-rdc-blue-dark text-white font-bold 
                              rounded-xl hover:shadow-2xl transition-all duration-300 
                              hover:scale-105 group">
                        <i class="fas fa-search mr-3 group-hover:rotate-12 transition-transform"></i>
                        Voir toutes les offres d'emploi
                        <i class="fas fa-arrow-right ml-3 group-hover:translate-x-2 
                                   transition-transform"></i>
                    </a>
                    <p class="text-gray-500 text-sm mt-4">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Candidature sécurisée • CV protégé • Réponse rapide
                    </p>
                </div>
            </div>
        </section>

        <!-- Section Fonctionnement -->
        <section id="fonctionnement" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <!-- En-tête section -->
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="inline-block px-6 py-2 bg-green-100 text-green-800 
                                  rounded-full font-semibold mb-4">
                        <i class="fas fa-play-circle mr-2"></i>Comment ça marche
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Commencez en
                        <span class="text-rdc-blue">3 étapes simples</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Notre processus simplifié vous permet de trouver rapidement
                        ce dont vous avez besoin, que ce soit un service ou un emploi.
                    </p>
                </div>

                <!-- Étapes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <!-- Étape 1 -->
                    <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-rdc-blue to-rdc-blue-dark 
                                        rounded-2xl flex items-center justify-center shadow-xl">
                                <div class="text-white text-3xl font-bold">1</div>
                            </div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 bg-rdc-yellow 
                                        rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Localisez-vous</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Autorisez la géolocalisation pour découvrir les services
                            et emplois les plus proches de votre position en RDC.
                        </p>
                    </div>

                    <!-- Étape 2 -->
                    <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-rdc-yellow to-yellow-500 
                                        rounded-2xl flex items-center justify-center shadow-xl">
                                <div class="text-gray-900 text-3xl font-bold">2</div>
                            </div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 bg-rdc-blue 
                                        rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-search text-white text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Recherchez</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Utilisez notre moteur de recherche intelligent pour trouver
                            exactement le service ou l'emploi correspondant à vos besoins.
                        </p>
                    </div>

                    <!-- Étape 3 -->
                    <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-green-500 to-green-600 
                                        rounded-2xl flex items-center justify-center shadow-xl">
                                <div class="text-white text-3xl font-bold">3</div>
                            </div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 bg-rdc-red 
                                        rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-handshake text-white text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Connectez-vous</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Contactez directement les artisans ou postulez aux offres
                            d'emploi qui vous intéressent, tout est sécurisé.
                        </p>
                    </div>
                </div>

                <!-- CTA Inscription -->
                <div class="text-center bg-gradient-to-r from-rdc-blue/10 to-rdc-yellow/10 
                            rounded-3xl p-12 border border-rdc-blue/20" data-aos="zoom-in">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        Prêt à commencer votre aventure ?
                    </h3>
                    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        Rejoignez la communauté ServiceRDC et accédez à des milliers
                        d'opportunités dans toute la République Démocratique du Congo.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @guest
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                                  text-white font-bold rounded-xl hover:shadow-2xl 
                                                  transition-all duration-300 hover:scale-105 
                                                  flex items-center justify-center space-x-3">
                                <i class="fas fa-user-plus"></i>
                                <span>Créer mon compte gratuit</span>
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-white border-2 border-rdc-blue 
                                                  text-rdc-blue font-bold rounded-xl hover:bg-rdc-blue/5 
                                                  transition-all duration-300 flex items-center 
                                                  justify-center space-x-3">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>J'ai déjà un compte</span>
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                                  text-white font-bold rounded-xl hover:shadow-2xl 
                                                  transition-all duration-300 hover:scale-105 
                                                  flex items-center justify-center space-x-3">
                                <i class="fas fa-rocket"></i>
                                <span>Accéder à mon espace</span>
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Témoignages -->
        <section id="temoignages" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <!-- En-tête section -->
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="inline-block px-6 py-2 bg-purple-100 text-purple-800 
                                  rounded-full font-semibold mb-4">
                        <i class="fas fa-comment-dots mr-2"></i>Témoignages
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Ils nous font
                        <span class="text-rdc-blue">confiance</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Découvrez les expériences de ceux qui utilisent déjà ServiceRDC
                        pour leurs besoins quotidiens en RDC.
                    </p>
                </div>

                <!-- Témoignages Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Témoignage 1 -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl 
                                transition-all duration-500 card-hover" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br 
                                        from-rdc-blue to-rdc-blue-dark 
                                        flex items-center justify-center mr-4">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Jean Kabasele</h4>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Client, Kinshasa</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex text-yellow-400 mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-gray-700 italic leading-relaxed">
                                "J'ai trouvé un électricien qualifié en moins de 30 minutes
                                pour réparer ma panne. ServiceRDC a sauvé ma soirée !"
                            </p>
                        </div>
                        <div class="pt-6 border-t border-gray-100">
                            <span class="inline-block px-4 py-1 bg-green-100 
                                          text-green-800 rounded-full text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i>Service terminé
                            </span>
                        </div>
                    </div>

                    <!-- Témoignage 2 -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl 
                                transition-all duration-500 card-hover" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br 
                                        from-rdc-yellow to-yellow-500 
                                        flex items-center justify-center mr-4">
                                <i class="fas fa-user-tie text-gray-900 text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Marie Kamanda</h4>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Couturière, Lubumbashi</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex text-yellow-400 mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="text-gray-700 italic leading-relaxed">
                                "Depuis que je me suis inscrite sur ServiceRDC, j'ai triplé
                                mon nombre de clients. C'est indispensable pour les artisans !"
                            </p>
                        </div>
                        <div class="pt-6 border-t border-gray-100">
                            <span class="inline-block px-4 py-1 bg-blue-100 
                                          text-blue-800 rounded-full text-sm font-medium">
                                <i class="fas fa-chart-line mr-1"></i>+300% de clients
                            </span>
                        </div>
                    </div>

                    <!-- Témoignage 3 -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl 
                                transition-all duration-500 card-hover" data-aos="fade-up" data-aos-delay="300">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br 
                                        from-rdc-red to-red-500 
                                        flex items-center justify-center mr-4">
                                <i class="fas fa-graduation-cap text-white text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Patrick Ilunga</h4>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>Chercheur d'emploi, Goma</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex text-yellow-400 mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-gray-700 italic leading-relaxed">
                                "J'ai trouvé mon premier emploi grâce à ServiceRDC.
                                L'interface est intuitive et les offres sont régulièrement mises à jour."
                            </p>
                        </div>
                        <div class="pt-6 border-t border-gray-100">
                            <span class="inline-block px-4 py-1 bg-purple-100 
                                          text-purple-800 rounded-full text-sm font-medium">
                                <i class="fas fa-briefcase mr-1"></i>Emploi trouvé
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats de satisfaction -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6" data-aos="fade-up">
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-bold text-rdc-blue mb-2">98%</div>
                        <div class="text-gray-600 font-medium">Satisfaction clients</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-bold text-rdc-yellow mb-2">4.8</div>
                        <div class="text-gray-600 font-medium">Note moyenne</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-bold text-green-600 mb-2">24h</div>
                        <div class="text-gray-600 font-medium">Réponse moyenne</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-bold text-rdc-red mb-2">500+</div>
                        <div class="text-gray-600 font-medium">Success stories</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-gray-900 to-rdc-dark-blue text-white pt-16 pb-8">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                    <!-- Logo et description -->
                    <div>
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r 
                                        from-rdc-blue to-rdc-yellow 
                                        flex items-center justify-center">
                                <i class="fas fa-hands-helping text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">Service<span class="text-yellow-300">RDC</span></h2>
                                <p class="text-sm text-gray-400">Fierté congolaise</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 leading-relaxed">
                            La plateforme de référence pour trouver des artisans qualifiés
                            et des opportunités d'emploi dans toute la République Démocratique du Congo.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-rdc-blue 
                                               flex items-center justify-center transition-colors 
                                               duration-300" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-blue-400 
                                               flex items-center justify-center transition-colors 
                                               duration-300" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-rdc-red 
                                               flex items-center justify-center transition-colors 
                                               duration-300" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-blue-700 
                                               flex items-center justify-center transition-colors 
                                               duration-300" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Liens Services -->
                    <div>
                        <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                            <i class="fas fa-tools mr-3 text-rdc-yellow"></i>
                            Services
                        </h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Trouver un artisan
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Catégories de services
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Artisans vérifiés
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Évaluations & avis
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Demande personnalisée
                                </a></li>
                        </ul>
                    </div>

                    <!-- Liens Emplois -->
                    <div>
                        <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                            <i class="fas fa-briefcase mr-3 text-rdc-yellow"></i>
                            Emplois
                        </h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Offres d'emploi
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Postuler en ligne
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    CV en ligne
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Conseils carrière
                                </a></li>
                            <li><a href="#" class="text-gray-400 hover:text-rdc-yellow 
                                                   transition-colors duration-300 flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i>
                                    Alertes emploi
                                </a></li>
                        </ul>
                    </div>

                    <!-- Contact & Newsletter -->
                    <div>
                        <h3 class="text-lg font-bold mb-6 text-white flex items-center">
                            <i class="fas fa-envelope mr-3 text-rdc-yellow"></i>
                            Restez informé
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm">
                            Inscrivez-vous à notre newsletter pour recevoir les dernières
                            offres et actualités ServiceRDC.
                        </p>
                        <form class="mb-6">
                            <div class="flex">
                                <input type="email" placeholder="Votre email" class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 
                                              rounded-l-lg focus:outline-none focus:border-rdc-yellow 
                                              text-white placeholder-gray-500">
                                <button type="submit" class="px-4 py-3 bg-rdc-yellow text-gray-900 font-semibold 
                                               rounded-r-lg hover:bg-yellow-500 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                        <div class="space-y-3">
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-phone mr-3 text-rdc-yellow"></i>
                                <span>+243 81 234 5678</span>
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-envelope mr-3 text-rdc-yellow"></i>
                                <span>contact@servicerdc.cd</span>
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-map-marker-alt mr-3 text-rdc-yellow"></i>
                                <span>Gombe, Kinshasa, RDC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="pt-8 border-t border-gray-800">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <p class="text-gray-500 text-sm">
                                &copy; 2024 ServiceRDC. Tous droits réservés.
                                <span class="text-rdc-yellow">🇨🇩 Fièrement congolais</span>
                            </p>
                        </div>
                        <div class="flex space-x-6 text-sm text-gray-500">
                            <a href="#" class="hover:text-rdc-yellow transition-colors">
                                Politique de confidentialité
                            </a>
                            <a href="#" class="hover:text-rdc-yellow transition-colors">
                                Conditions d'utilisation
                            </a>
                            <a href="#" class="hover:text-rdc-yellow transition-colors">
                                Mentions légales
                            </a>
                            <a href="#" class="hover:text-rdc-yellow transition-colors">
                                Plan du site
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Retour en haut -->
                <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-rdc-blue 
                               to-rdc-blue-dark text-white rounded-full shadow-2xl 
                               hover:shadow-rdc-blue/30 hover:scale-110 transition-all 
                               duration-300 flex items-center justify-center z-40 hidden">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Initialisation AOS
        document.addEventListener('DOMContentLoaded', function () {
            // Animation de la barre de progression
            const progressFill = document.getElementById('progress-fill');
            if (progressFill) {
                progressFill.style.width = '100%';
            }

            // Simulation chargement
            setTimeout(() => {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    loadingScreen.style.visibility = 'hidden';
                }

                const mainContent = document.getElementById('main-content');
                if (mainContent) {
                    mainContent.style.opacity = '1';
                }

                // Initialiser AOS
                setTimeout(() => {
                    if (typeof AOS !== 'undefined') {
                        AOS.init({
                            duration: 800,
                            once: true,
                            offset: 100,
                            easing: 'ease-out-cubic'
                        });
                    }

                    // Initialiser le reste de la page
                    initPage();
                }, 100);
            }, 2000);
        });

        // Initialisation de la page
        function initPage() {
            // Menu mobile
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenuBtn.innerHTML = mobileMenu.classList.contains('hidden')
                        ? '<i class="fas fa-bars text-2xl"></i>'
                        : '<i class="fas fa-times text-2xl"></i>';
                });

                // Fermer menu en cliquant sur un lien
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
                    });
                });
            }

            // Données des catégories
            const categories = [
                { name: "Électriciens", icon: "fa-bolt", color: "from-blue-500 to-blue-600", desc: "Installation & dépannage" },
                { name: "Plombiers", icon: "fa-tools", color: "from-yellow-500 to-yellow-600", desc: "Installation & réparation" },
                { name: "Couturiers", icon: "fa-cut", color: "from-red-500 to-red-600", desc: "Sur mesure & réparation" },
                { name: "Maçons", icon: "fa-hammer", color: "from-green-500 to-green-600", desc: "Construction & rénovation" },
                { name: "Cordonniers", icon: "fa-shoe-prints", color: "from-purple-500 to-purple-600", desc: "Réparation de chaussures" },
                { name: "Coiffeurs", icon: "fa-scissors", color: "from-pink-500 to-pink-600", desc: "Coiffure homme & femme" },
                { name: "Mécaniciens", icon: "fa-car", color: "from-indigo-500 to-indigo-600", desc: "Réparation automobile" },
                { name: "Menuisiers", icon: "fa-tree", color: "from-yellow-700 to-yellow-800", desc: "Menuiserie & ébénisterie" },
                { name: "Jardiniers", icon: "fa-leaf", color: "from-green-400 to-green-500", desc: "Entretien & aménagement" },
                { name: "Peintres", icon: "fa-paint-roller", color: "from-cyan-500 to-cyan-600", desc: "Peinture intérieur/extérieur" },
                { name: "Soudeurs", icon: "fa-fire", color: "from-orange-500 to-orange-600", desc: "Soudure & métallerie" },
                { name: "Informaticiens", icon: "fa-laptop-code", color: "from-blue-400 to-blue-500", desc: "Maintenance & support IT" }
            ];

            // Remplir les catégories
            const categoriesContainer = document.getElementById('categories-container');
            if (categoriesContainer) {
                categories.forEach((category, index) => {
                    const categoryElement = document.createElement('div');
                    categoryElement.className = 'group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 will-change-transform';
                    categoryElement.setAttribute('data-aos', 'fade-up');
                    categoryElement.setAttribute('data-aos-delay', (index % 6) * 100);

                    categoryElement.innerHTML = `
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br ${category.color} flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform duration-500 shadow-lg">
                                <i class="fas ${category.icon} text-white text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 text-center mb-2 group-hover:text-rdc-blue transition-colors">${category.name}</h4>
                            <p class="text-sm text-gray-600 text-center mb-4">${category.desc}</p>
                            <div class="text-center">
                                <span class="inline-flex items-center text-rdc-blue text-sm font-semibold group-hover:translate-x-1 transition-transform">
                                    Voir les artisans
                                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </span>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br ${category.color} opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                    `;

                    categoriesContainer.appendChild(categoryElement);
                });
            }

            // Données des emplois
            const jobs = [
                {
                    title: "Électricien Résidentiel",
                    company: "Kinshasa Électricité",
                    location: "Kinshasa",
                    type: "Temps plein",
                    salary: "800-1200$",
                    urgent: true,
                    tags: ["Électricité", "CDI", "Expérience"]
                },
                {
                    title: "Plombier Qualifié",
                    company: "Service Plomberie Pro",
                    location: "Lubumbashi",
                    type: "Temps plein",
                    salary: "700-1000$",
                    urgent: false,
                    tags: ["Plomberie", "CDD", "Formation"]
                },
                {
                    title: "Couturier sur Mesure",
                    company: "Mode & Style RDC",
                    location: "Kisangani",
                    type: "Indépendant",
                    salary: "À discuter",
                    urgent: false,
                    tags: ["Couture", "Freelance", "Créatif"]
                },
                {
                    title: "Maçon Bâtiment",
                    company: "Construction Congo",
                    location: "Goma",
                    type: "Contrat",
                    salary: "600-900$",
                    urgent: true,
                    tags: ["Construction", "CDD", "Force physique"]
                },
                {
                    title: "Coiffeur Salon",
                    company: "Beauté Noire",
                    location: "Mbuji-Mayi",
                    type: "Temps plein",
                    salary: "500-800$",
                    urgent: false,
                    tags: ["Coiffure", "CDI", "Relation client"]
                },
                {
                    title: "Mécanicien Automobile",
                    company: "Auto Service RDC",
                    location: "Bukavu",
                    type: "Temps plein",
                    salary: "750-1100$",
                    urgent: false,
                    tags: ["Mécanique", "CDI", "Technicien"]
                }
            ];

            // Remplir les emplois
            const jobsContainer = document.getElementById('jobs-container');
            if (jobsContainer) {
                jobs.forEach((job, index) => {
                    const jobElement = document.createElement('div');
                    jobElement.className = 'group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden relative';
                    jobElement.setAttribute('data-aos', 'fade-up');
                    jobElement.setAttribute('data-aos-delay', (index % 3) * 100);

                    const urgentBadge = job.urgent ?
                        `<div class="absolute top-4 right-4 px-4 py-1.5 bg-gradient-to-r from-rdc-red to-red-500 text-white text-xs font-bold rounded-full animate-pulse">
                            <i class="fas fa-bolt mr-1"></i>URGENT
                        </div>` : '';

                    const tagsHtml = job.tags.map(tag =>
                        `<span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">${tag}</span>`
                    ).join('');

                    jobElement.innerHTML = `
                        ${urgentBadge}
                        
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-rdc-blue transition-colors">${job.title}</h3>
                            <div class="flex items-center text-gray-600 mb-2">
                                <i class="fas fa-building text-rdc-blue mr-2"></i>
                                <span class="font-medium">${job.company}</span>
                            </div>
                            <div class="flex items-center text-gray-600 mb-4">
                                <i class="fas fa-map-marker-alt text-rdc-red mr-2"></i>
                                <span>${job.location}</span>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-4">
                                ${tagsHtml}
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-6">
                            <span class="px-4 py-2 bg-rdc-blue/10 text-rdc-blue font-semibold rounded-lg">
                                ${job.type}
                            </span>
                            <span class="text-lg font-bold text-gray-900">${job.salary}</span>
                        </div>
                        
                        <button class="w-full group/btn px-6 py-3.5 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark 
                                      text-white font-semibold rounded-xl hover:shadow-lg 
                                      transition-all duration-300 hover:scale-[1.02] 
                                      flex items-center justify-center space-x-3">
                            <i class="fas fa-paper-plane group-hover/btn:rotate-12 transition-transform"></i>
                            <span>Postuler maintenant</span>
                            <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                        </button>
                        
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rdc-blue to-rdc-yellow transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                    `;

                    jobsContainer.appendChild(jobElement);
                });
            }

            // Recherche intelligente
            const searchInput = document.getElementById('search-input');
            const searchSuggestions = document.getElementById('search-suggestions');
            const searchBtn = document.getElementById('search-btn');

            const suggestions = [
                "Électricien Kinshasa",
                "Plombier urgent",
                "Couturier sur mesure",
                "Maçon bâtiment",
                "Coiffeur salon",
                "Mécanicien automobile",
                "Menuisier ébéniste",
                "Peintre bâtiment",
                "Jardinier entretien",
                "Informaticien dépannage",
                "CDI administration",
                "Freelance marketing",
                "Contrat construction",
                "Stage informatique"
            ];

            if (searchInput && searchSuggestions) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase();
                    searchSuggestions.innerHTML = '';

                    if (query.length > 1) {
                        const filteredSuggestions = suggestions.filter(suggestion =>
                            suggestion.toLowerCase().includes(query)
                        );

                        if (filteredSuggestions.length > 0) {
                            searchSuggestions.classList.remove('hidden');
                            searchSuggestions.classList.add('animate-fade-in-up');

                            filteredSuggestions.forEach(suggestion => {
                                const suggestionElement = document.createElement('div');
                                suggestionElement.className = 'px-6 py-4 hover:bg-gray-50 cursor-pointer text-gray-700 border-b border-gray-100 last:border-b-0 transition-colors group/suggestion';
                                suggestionElement.innerHTML = `
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-search text-gray-400 mr-3 group-hover/suggestion:text-rdc-blue"></i>
                                            <span>${suggestion}</span>
                                        </div>
                                        <i class="fas fa-arrow-right text-xs text-gray-300 group-hover/suggestion:text-rdc-blue group-hover/suggestion:translate-x-1 transition-transform"></i>
                                    </div>
                                `;

                                suggestionElement.addEventListener('click', () => {
                                    searchInput.value = suggestion;
                                    searchSuggestions.classList.add('hidden');
                                    performSearch(suggestion);
                                });

                                searchSuggestions.appendChild(suggestionElement);
                            });
                        } else {
                            searchSuggestions.classList.add('hidden');
                        }
                    } else {
                        searchSuggestions.classList.add('hidden');
                    }
                });

                // Fermer suggestions en cliquant ailleurs
                document.addEventListener('click', (event) => {
                    if (!searchInput.contains(event.target) && !searchSuggestions.contains(event.target)) {
                        searchSuggestions.classList.add('hidden');
                    }
                });

                // Recherche au clic
                if (searchBtn) {
                    searchBtn.addEventListener('click', () => {
                        performSearch(searchInput.value);
                    });
                }

                // Recherche avec Enter
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        performSearch(searchInput.value);
                    }
                });
            }

            // Fonction de recherche
            function performSearch(query) {
                if (query.trim()) {
                    // Simulation de recherche
                    showNotification(`Recherche pour : "${query}"`, 'info');
                    searchSuggestions.classList.add('hidden');

                    // Animation du bouton
                    if (searchBtn) {
                        const originalHtml = searchBtn.innerHTML;
                        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
                        searchBtn.disabled = true;

                        setTimeout(() => {
                            searchBtn.innerHTML = originalHtml;
                            searchBtn.disabled = false;
                            // Redirection vers résultats de recherche
                            window.location.href = `{{ route('login') }}?search=${encodeURIComponent(query)}`;
                        }, 1000);
                    }
                }
            }

            // Géolocalisation
            const geolocationBtn = document.getElementById('geolocation-btn');
            const locationText = document.getElementById('location-text');
            const currentCity = document.getElementById('current-city');
            const currentAddress = document.getElementById('current-address');

            const rdCities = [
                { name: "Kinshasa", address: "Commune de la Gombe, Kinshasa", province: "Kinshasa" },
                { name: "Lubumbashi", address: "Avenue de la Libération, Lubumbashi", province: "Haut-Katanga" },
                { name: "Mbuji-Mayi", address: "Centre-ville, Mbuji-Mayi", province: "Kasaï-Oriental" },
                { name: "Kisangani", address: "Quartier Mangobo, Kisangani", province: "Tshopo" },
                { name: "Bukavu", address: "Avenue du Musée, Bukavu", province: "Sud-Kivu" },
                { name: "Goma", address: "Avenue des Volcans, Goma", province: "Nord-Kivu" },
                { name: "Kolwezi", address: "Quartier Lualaba, Kolwezi", province: "Lualaba" },
                { name: "Matadi", address: "Avenue Kasa-Vubu, Matadi", province: "Kongo-Central" }
            ];

            if (geolocationBtn && locationText) {
                geolocationBtn.addEventListener('click', function () {
                    const originalText = locationText.innerHTML;
                    locationText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Localisation...';
                    geolocationBtn.classList.add('opacity-75');
                    geolocationBtn.disabled = true;

                    // Simulation géolocalisation
                    setTimeout(() => {
                        const randomCity = rdCities[Math.floor(Math.random() * rdCities.length)];

                        if (currentCity) currentCity.textContent = randomCity.name;
                        if (currentAddress) currentAddress.textContent = `${randomCity.address} • ${randomCity.province}`;

                        locationText.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${randomCity.name}`;

                        showNotification(`Localisé à ${randomCity.name}, ${randomCity.province}`, 'success');

                        // Réinitialiser après 5 secondes
                        setTimeout(() => {
                            locationText.innerHTML = originalText;
                            geolocationBtn.classList.remove('opacity-75');
                            geolocationBtn.disabled = false;
                        }, 5000);
                    }, 2000);
                });
            }

            // Notifications
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-rdc-blue'
                };

                notification.className = `fixed top-6 right-6 px-6 py-4 rounded-xl shadow-2xl 
                                         text-white font-medium z-50 transform translate-x-full 
                                         transition-transform duration-500 ${colors[type] || colors.info}`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-3"></i>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);

                // Animation entrée
                setTimeout(() => notification.style.transform = 'translateX(0)', 10);

                // Animation sortie après 4 secondes
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 500);
                }, 4000);
            }

            // Scroll smooth amélioré
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    e.preventDefault();
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        // Fermer menu mobile si ouvert
                        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                            if (mobileMenuBtn) {
                                mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
                            }
                        }

                        // Animation scroll
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Bouton retour en haut
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.classList.remove('hidden');
                        backToTopBtn.classList.add('flex');
                    } else {
                        backToTopBtn.classList.add('hidden');
                        backToTopBtn.classList.remove('flex');
                    }
                });

                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Animation au scroll
            const animateOnScroll = () => {
                const elements = document.querySelectorAll('[data-aos]');
                elements.forEach(el => {
                    const position = el.getBoundingClientRect();
                    if (position.top < window.innerHeight - 100) {
                        el.classList.add('aos-animate');
                    }
                });
            };

            window.addEventListener('scroll', animateOnScroll);
            animateOnScroll(); // Initial call

            // Effets de hover avancés
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateY = (x - centerX) / 25;
                    const rotateX = (centerY - y) / 25;

                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`;
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(-8px)';
                });
            });

            // Initialisation des tooltips
            const tooltipElements = document.querySelectorAll('[title]');
            tooltipElements.forEach(el => {
                el.addEventListener('mouseenter', (e) => {
                    const tooltip = document.createElement('div');
                    tooltip.class = 'fixed z-50 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg';
                    tooltip.textContent = e.target.title;
                    tooltip.style.left = `${e.pageX + 10}px`;
                    tooltip.style.top = `${e.pageY + 10}px`;

                    // Fixed: Use classList instead of class property which doesn't work directly
                    tooltip.className = 'fixed z-50 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg pointer-events-none';

                    document.body.appendChild(tooltip);

                    e.target._tooltip = tooltip;
                });

                el.addEventListener('mouseleave', (e) => {
                    if (e.target._tooltip) {
                        e.target._tooltip.remove();
                        e.target._tooltip = null;
                    }
                });
            });
        }

        // Gestion des erreurs
        window.addEventListener('error', function (e) {
            console.error('Erreur JavaScript:', e.error);
        });

        // Optimisation des performances
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                // Chargement différé des images
                const images = document.querySelectorAll('img[data-src]');
                images.forEach(img => {
                    img.src = img.dataset.src;
                });
            });
        }
    </script>
</body>

</html>