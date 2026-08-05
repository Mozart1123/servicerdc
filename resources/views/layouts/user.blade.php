<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tableau de bord') | ProConnect</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">

    <!-- Vite compiled CSS + JS (Tailwind v4) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">



    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            borderRadius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .nav-item-active {
            background: linear-gradient(90deg, rgba(41, 182, 209, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            border-left: 3px solid #29B6D1;
            color: #29B6D1;
        }

        .nav-item {
            border-left: 3px solid transparent;
            color: #64748B;
        }

        .nav-item:hover {
            color: #0F172A;
            background: rgba(248, 250, 252, 0.8);
        }
    </style>

    @stack('styles')
</head>

<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
        class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden glass-backdrop"></div>

    <!-- Sidebar -->
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 w-80 glass-sidebar z-40 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none bg-white">

        <!-- [BRANDING] Section -->
        <div class="px-8 py-10 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm flex items-center justify-center p-1.5 bg-white border border-slate-100">
                    <img src="/assets/img/logo.png?v=1.2" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none uppercase">Pro<span class="text-rdc-blue">Connect</span></h2>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Dashboard Professionnel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-6 py-8 space-y-8 custom-scrollbar">
            @php
                $userType = auth()->user()->user_type ?? 'client';
            @endphp

            @if($userType === 'client')
                <!-- [CLIENT MENU] -->
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.dashboard') ? 'bg-rdc-blue text-white shadow-xl shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-border-all text-lg {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.services.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.services.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-wand-magic-sparkles text-lg {{ request()->routeIs('user.services.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Services
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.jobs.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.jobs.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-briefcase text-lg {{ request()->routeIs('user.jobs.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Emplois
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.applications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.applications.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-history text-lg {{ request()->routeIs('user.applications.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Candidatures
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.service-requests.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.service-requests.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-concierge-bell text-lg {{ request()->routeIs('user.service-requests.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Demandes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.messages.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-inbox text-lg {{ request()->routeIs('user.messages.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Messages
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.missions.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.missions.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-handshake text-lg {{ request()->routeIs('user.missions.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Missions
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.notifications.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-bell text-lg {{ request()->routeIs('user.notifications.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Notifications
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.subscription.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.subscription.index') ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-crown text-lg {{ request()->routeIs('user.subscription.index') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-500' }}"></i>
                            Abonnement
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.identity-verification.show') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.identity-verification.show') ? 'bg-[#e6f7f8] text-[#0f7a86] shadow-md shadow-[#29B6D1]/10' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-shield-check text-lg {{ request()->routeIs('user.identity-verification.show') ? 'text-[#0f7a86]' : 'text-slate-400 group-hover:text-[#29B6D1]' }}"></i>
                            Vérification Identité
                        </a>
                    </li>
                </ul>

            @elseif($userType === 'artisan')
                <!-- [ARTISAN MENU] -->
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.dashboard') ? 'bg-rdc-blue text-white shadow-xl shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-border-all text-lg {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.services.my') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.services.my') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-box-open text-lg {{ request()->routeIs('user.services.my') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Services
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.artisan.service-requests.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.artisan.service-requests.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-inbox text-lg {{ request()->routeIs('user.artisan.service-requests.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Demandes reçues
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.missions.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.missions.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-handshake text-lg {{ request()->routeIs('user.missions.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Missions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.artisan.reviews.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.artisan.reviews.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-star-half-alt text-lg {{ request()->routeIs('user.artisan.reviews.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Avis
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.messages.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-inbox text-lg {{ request()->routeIs('user.messages.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Messages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.notifications.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-bell text-lg {{ request()->routeIs('user.notifications.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Notifications
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.subscription.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.subscription.*') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-crown text-lg {{ request()->routeIs('user.subscription.*') ? 'text-white' : 'text-slate-400 group-hover:text-amber-500' }}"></i>
                            Abonnement Pro
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.artisan.level') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.artisan.level') ? 'bg-[#e6f7f8] text-[#0f7a86] shadow-md shadow-[#29B6D1]/10' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-shield-check text-lg {{ request()->routeIs('user.artisan.level') ? 'text-[#0f7a86]' : 'text-slate-400 group-hover:text-[#29B6D1]' }}"></i>
                            Vérification & Niveau
                        </a>
                    </li>
                </ul>

            @elseif($userType === 'recruiter' || $userType === 'job_seeker')
                <!-- [RECRUTEUR MENU] -->
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.dashboard') ? 'bg-rdc-blue text-white shadow-xl shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-border-all text-lg {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.jobs.my-offers') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.jobs.my-offers') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-briefcase text-lg {{ request()->routeIs('user.jobs.my-offers') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mes Offres d'Emploi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.jobs.create') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.jobs.create') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-plus-circle text-lg {{ request()->routeIs('user.jobs.create') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Publier une Offre
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.applications.received') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.applications.received') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-users text-lg {{ request()->routeIs('user.applications.received') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Candidatures Reçues
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.messages.*') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-inbox text-lg {{ request()->routeIs('user.messages.*') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Messages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.notifications.index') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-bell text-lg {{ request()->routeIs('user.notifications.index') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Notifications
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.subscription.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.subscription.*') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-crown text-lg {{ request()->routeIs('user.subscription.*') ? 'text-white' : 'text-slate-400 group-hover:text-amber-500' }}"></i>
                            Abonnement Pro
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.identity-verification.show') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.identity-verification.show') ? 'bg-[#e6f7f8] text-[#0f7a86] shadow-md shadow-[#29B6D1]/10' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-shield-check text-lg {{ request()->routeIs('user.identity-verification.show') ? 'text-[#0f7a86]' : 'text-slate-400 group-hover:text-[#29B6D1]' }}"></i>
                            Vérification Identité
                        </a>
                    </li>
                </ul>
            @endif

            <!-- [PARAMÈTRES] -->
            <!-- [PROFIL] -->
            <div>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('user.profile') ? 'bg-rdc-blue text-white shadow-lg shadow-[#29B6D1]/20' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fas fa-user-cog text-lg {{ request()->routeIs('user.profile') ? 'text-white' : 'text-slate-400 group-hover:text-rdc-blue' }}"></i>
                            Mon Profil
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- [BAS] - Footer Actions -->
        <div class="p-6 border-t border-slate-100 mt-auto">
            <div class="space-y-2">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-rdc-blue transition-all group">
                    <i class="fas fa-house text-lg text-slate-300 group-hover:text-rdc-blue"></i>
                    Retour accueil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl text-sm font-bold text-slate-500 hover:bg-red-50 hover:text-rdc-red transition-all group">
                        <i class="fas fa-power-off text-lg text-slate-300 group-hover:text-rdc-red"></i>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-80 flex flex-col min-h-screen transition-all duration-300">

        <!-- Top Navbar -->
        <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-rdc-blue p-2 -ml-2">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Page Title -->
            <h1 class="text-xl font-heading font-bold text-slate-800 hidden sm:block">
                @yield('header_title', 'Tableau de bord')
            </h1>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="relative hidden md:block group">
                    <input type="text" placeholder="Rechercher..."
                        class="w-64 pl-10 pr-4 py-2 bg-slate-100 border-none rounded-full text-sm focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white transition-all">
                    <i
                        class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-rdc-blue transition-colors"></i>
                </div>

                <!-- Notifications -->
                <a href="{{ route('user.notifications.index') }}"
                    class="relative p-2 text-slate-400 hover:text-rdc-blue transition-colors rounded-full hover:bg-blue-50">
                    <i class="far fa-bell text-xl"></i>
                    @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rdc-red text-[8px] font-black text-white flex items-center justify-center rounded-full border-2 border-white animate-pulse">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- User Profile Header Integration -->
                <a href="{{ route('user.profile') }}" class="flex items-center gap-3 pl-3 border-l border-slate-200 hover:opacity-90 transition-opacity">
                    <div class="relative flex-shrink-0 group">
                        <img src="{{ auth()->user()->photo_url }}"
                             class="w-10 h-10 rounded-xl border border-slate-200 shadow-sm object-cover" alt="Profile">
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-black text-slate-900 leading-tight">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                        <div class="flex items-center gap-1 mt-0.5">
                            @if(auth()->user()->isVerified())
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 bg-[#e6f7f8] text-[#0f7a86] text-[8px] font-black uppercase tracking-widest rounded border border-[#29B6D1]/20">
                                    <i class="fas fa-shield-check text-[8px]"></i>
                                    Vérifié
                                </span>
                            @else
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ auth()->user()->user_type_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

    </div>
    
    <!-- AOS Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>

    @stack('scripts')
</body>

</html>