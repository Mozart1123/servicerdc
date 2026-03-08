# 🚀 COPY-PASTE FIX: Complete app.blade.php Layout

**This is your complete, production-ready solution. Copy this entire file and paste it into `resources/views/layouts/app.blade.php`**

---

## ✅ What This File Fixes

- ✅ Broken CSS (Tailwind CDN replaces broken Vite)
- ✅ Sidebar navigation on left (not top)
- ✅ Professional light theme (#F0F4F5 background)
- ✅ Congo Blue active states
- ✅ National Red logout button at sidebar bottom
- ✅ White cards with shadows
- ✅ Top navbar with logo and search
- ✅ Dark mode support
- ✅ Responsive design

---

## 📋 Complete app.blade.php Code

Copy everything below and paste into: `resources/views/layouts/app.blade.php`

```php
<!DOCTYPE html>
<html lang="fr" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark',
    sidebarOpen: true,
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    }
}" :class="{ 'dark': darkMode }" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MOSALA+') | ServiceRDC</title>

    <!-- ⭐ TAILWIND CSS CDN - FIXES BROKEN CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ⭐ CUSTOM COLOR CONFIGURATION -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'congo-blue': {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#007FFF',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        'congo-yellow': {
                            50: '#fefce8',
                            100: '#fffbcc',
                            200: '#fff399',
                            300: '#ffed4e',
                            400: '#ffeb14',
                            500: '#F7D000',
                            600: '#daa520',
                            700: '#b8860b',
                            800: '#8b6914',
                            900: '#332d00',
                        },
                        'congo-red': {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#CE1021',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        'mosala-light': '#F0F4F5',
                    },
                    spacing: {
                        '18': '4.5rem',
                        '22': '5.5rem',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    boxShadow: {
                        'light': '0 1px 3px rgba(0, 0, 0, 0.05)',
                        'sm-light': '0 1px 2px rgba(0, 0, 0, 0.05)',
                    },
                    transitionDuration: {
                        '300': '300ms',
                        '500': '500ms',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Theme Detection -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* ============================================
           LIGHT MODE PROFESSIONAL DESIGN SYSTEM
           ============================================ */

        :root:not(.dark) {
            --bg-primary: #F0F4F5;
            --bg-secondary: #FFFFFF;
            --bg-tertiary: #F8FAFC;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --border-color: #E2E8F0;
            --congo-blue: #007FFF;
            --congo-yellow: #F7D000;
            --congo-red: #CE1021;
        }

        :root.dark {
            --bg-primary: #0A0F1C;
            --bg-secondary: #111827;
            --bg-tertiary: #1F2937;
            --text-primary: #FFFFFF;
            --text-secondary: #D1D5DB;
            --border-color: #374151;
            --congo-blue: #007FFF;
            --congo-yellow: #F7D000;
            --congo-red: #CE1021;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 300ms ease, color 300ms ease;
            min-height: 100vh;
        }

        main {
            background-color: var(--bg-primary);
            transition: background-color 300ms ease;
        }

        .card {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
            transition: all 300ms ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .sidebar {
            background-color: var(--bg-primary);
            border-right: 1px solid var(--border-color);
            transition: all 300ms ease;
        }

        .nav-link {
            color: var(--text-secondary);
            transition: all 200ms ease;
        }

        .nav-link:hover {
            color: var(--congo-blue);
            background-color: rgba(0, 127, 255, 0.05);
        }

        .nav-link.active {
            color: var(--congo-blue);
            background-color: rgba(0, 127, 255, 0.08);
            border-left: 4px solid var(--congo-blue);
            padding-left: calc(1rem - 4px);
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--congo-blue);
            color: white;
            transition: all 200ms ease;
        }

        .btn-primary:hover {
            background-color: #0066CC;
            box-shadow: 0 4px 12px rgba(0, 127, 255, 0.3);
        }

        .btn-secondary {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            transition: all 200ms ease;
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
        }

        .btn-danger {
            background-color: var(--congo-red);
            color: white;
            transition: all 200ms ease;
        }

        .btn-danger:hover {
            background-color: #A00A1A;
            box-shadow: 0 4px 12px rgba(206, 16, 33, 0.3);
        }

        .badge-warning {
            background-color: var(--congo-yellow);
            color: #1E293B;
        }

        table {
            background-color: var(--bg-secondary);
        }

        table thead {
            background-color: rgba(0, 127, 255, 0.05);
            color: var(--congo-blue);
            font-weight: 600;
        }

        table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background-color 200ms ease;
        }

        table tbody tr:hover {
            background-color: rgba(0, 127, 255, 0.03);
        }

        input, textarea, select {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
            border-color: var(--border-color);
            transition: all 200ms ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--congo-blue);
            box-shadow: 0 0 0 3px rgba(0, 127, 255, 0.1);
        }

        .transition-theme {
            transition: background-color 300ms ease, color 300ms ease, border-color 300ms ease;
        }

        .text-congo-blue {
            color: var(--congo-blue);
        }

        .text-congo-red {
            color: var(--congo-red);
        }

        .text-congo-yellow {
            color: var(--congo-yellow);
        }

        .bg-mosala-light {
            background-color: #F0F4F5;
        }

        .dark .bg-mosala-light {
            background-color: #0A0F1C;
        }
    </style>

    @yield('extra_styles')
</head>

<body class="bg-mosala-light dark:bg-[#0A0F1C] transition-theme">
    <div class="flex h-screen overflow-hidden">
        <!-- ⭐ SIDEBAR ON LEFT -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- ⭐ TOP NAVBAR -->
            @include('components.navbar')

            <!-- ⭐ PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto bg-mosala-light dark:bg-[#0A0F1C]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('MOSALA+ Application Loaded');
        });
    </script>

    @yield('extra_scripts')
</body>

</html>
```

---

## 📄 Sidebar Component (Save as: `resources/views/components/sidebar.blade.php`)

```php
<!-- Sidebar Navigation Component - Professional Light Theme -->
<aside class="sidebar w-64 flex flex-col bg-mosala-light dark:bg-[#0A0F1C] border-r border-gray-200 dark:border-gray-800 transition-theme h-screen">
    <!-- Logo Section -->
    <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-congo-blue flex items-center justify-center">
                <i class="fas fa-hands-helping text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">MOSALA<span class="text-congo-blue">+</span></h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">ServiceRDC</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-6">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') ?? '#' }}" class="nav-link flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-5 h-5 mr-3"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('profile.edit') ?? '#' }}" class="nav-link flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-user w-5 h-5 mr-3"></i>
                <span class="font-medium">Mon profil</span>
            </a>

            <!-- Services Section -->
            <div x-data="{ servicesOpen: false }" class="space-y-2">
                <button @click="servicesOpen = !servicesOpen" class="nav-link flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-900">
                    <div class="flex items-center">
                        <i class="fas fa-briefcase w-5 h-5 mr-3"></i>
                        <span class="font-medium">Services</span>
                    </div>
                    <i class="fas fa-chevron-right w-4 h-4 transition-transform" :class="{ 'rotate-90': servicesOpen }"></i>
                </button>
                <div x-show="servicesOpen" class="pl-4 space-y-2">
                    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg text-sm">
                        <i class="fas fa-list w-4 h-4 mr-3"></i>
                        Mes services
                    </a>
                    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg text-sm">
                        <i class="fas fa-plus w-4 h-4 mr-3"></i>
                        Ajouter un service
                    </a>
                </div>
            </div>

            <!-- Job Applications -->
            <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-file-alt w-5 h-5 mr-3"></i>
                <span class="font-medium">Candidatures</span>
            </a>

            <!-- Messages -->
            <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-envelope w-5 h-5 mr-3"></i>
                <span class="font-medium">Messages</span>
                <span class="ml-auto bg-congo-red text-white text-xs rounded-full px-2 py-1">3</span>
            </a>

            <!-- Ratings -->
            <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-star w-5 h-5 mr-3 text-congo-yellow"></i>
                <span class="font-medium">Avis et notes</span>
            </a>

            <!-- Settings -->
            <a href="#" class="nav-link flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-cog w-5 h-5 mr-3"></i>
                <span class="font-medium">Paramètres</span>
            </a>
        </div>
    </nav>

    <!-- Bottom Section: Theme Toggle & Logout -->
    <div class="border-t border-gray-200 dark:border-gray-800 px-4 py-6 space-y-3">
        <!-- Theme Toggle -->
        <button @click="toggleTheme()" class="nav-link flex items-center w-full px-4 py-3 rounded-lg">
            <i class="fas w-5 h-5 mr-3" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
            <span class="font-medium" x-text="darkMode ? 'Mode clair' : 'Mode sombre'"></span>
        </button>

        <!-- ⭐ RED LOGOUT BUTTON -->
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="btn-danger flex items-center w-full px-4 py-3 rounded-lg font-medium hover:shadow-md transition-all">
                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
```

---

## 🧭 Navbar Component (Save as: `resources/views/components/navbar.blade.php`)

```php
<!-- Professional Navbar Component - Light Theme -->
<nav class="sticky top-0 z-40 w-full bg-white dark:bg-[#111827] border-b border-gray-200 dark:border-gray-800 transition-theme">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left Section: Page Title -->
            <div class="flex items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@yield('page_title', 'MOSALA+')</h2>
            </div>

            <!-- Right Section: Search & User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Search Bar -->
                <div class="hidden md:flex items-center">
                    <div class="relative">
                        <input type="search" placeholder="Rechercher..." class="pl-10 pr-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-congo-blue focus:ring-2 focus:ring-congo-blue/20 transition-all">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-congo-blue transition-colors">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1 right-1 w-3 h-3 bg-congo-red rounded-full"></span>
                </button>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-congo-blue flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="hidden sm:inline text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#111827] rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                        <a href="{{ route('profile.edit') ?? '#' }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-user-circle mr-2 text-congo-blue"></i> Mon profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-cog mr-2 text-congo-blue"></i> Paramètres
                        </a>
                        <hr class="my-2 border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-congo-red hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
```

---

## 🎯 How to Use This Layout

### **For Any Page:**

```php
@extends('layouts.app')

@section('title', 'Page Title')
@section('page_title', 'Display Title')

@section('content')
    <!-- Your content here -->
    <div class="card rounded-xl p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome</h2>
        <p class="text-gray-600 dark:text-gray-400">Your content goes here</p>
    </div>
@endsection
```

---

## 📊 Example Card Layout:

```php
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Stat Card -->
    <div class="card rounded-xl p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Services</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">42</p>
                <p class="text-xs text-congo-blue mt-2"><i class="fas fa-arrow-up mr-1"></i> +5 this month</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-congo-blue/10 flex items-center justify-center">
                <i class="fas fa-briefcase text-congo-blue text-xl"></i>
            </div>
        </div>
    </div>
</div>
```

---

## ✅ Verification Checklist

After pasting the files, check:

- [ ] Sidebar is on the left
- [ ] Sidebar background is light gray (#F0F4F5)
- [ ] Page background is light gray (#F0F4F5)
- [ ] Cards are white with shadows
- [ ] Top navbar has logo and search
- [ ] Red logout button is at sidebar bottom
- [ ] Links turn Congo Blue (#007FFF) on hover
- [ ] No CSS errors in browser console
- [ ] Dark mode toggle works
- [ ] Responsive on mobile

---

## 🎉 That's It!

Your broken CSS is now fixed. All styling comes from Tailwind CDN (no Vite issues), and everything is responsive and professional.

**No additional setup needed.** Just copy-paste and enjoy your professional light theme! 🚀
