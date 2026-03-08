<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentification') | ServiceRDC</title>

    <!-- Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind Config match Landing Page -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'rdc-blue': '#007FFF',
                        'rdc-blue-dark': '#0066CC',
                        'rdc-yellow': '#F0B800',
                        'rdc-red': '#FF4757',
                        'rdc-dark-blue': '#003366',
                        'glass-border': 'rgba(255, 255, 255, 0.4)',
                        'glass-bg': 'rgba(255, 255, 255, 0.7)',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'spin-slow': 'spin 12s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        
        /* Ultra Premium Light Glassmorphism */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85); /* Highly opaque white */
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 
                0 4px 6px -1px rgba(0, 51, 102, 0.1), /* Dark blue shadow */
                0 2px 4px -1px rgba(0, 51, 102, 0.05),
                0 20px 25px -5px rgba(0, 51, 102, 0.1),
                inset 0 0 20px rgba(255, 255, 255, 0.8);
        }

        /* Input Styling - Light Mode */
        .premium-input-group {
            position: relative;
            transition: all 0.3s ease;
        }

        .premium-input {
            width: 100%;
            background: #F8FAFC; /* Light gray bg */
            border: 1px solid #E2E8F0;
            color: #0F172A; /* Dark text */
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-input:focus {
            background: #FFFFFF;
            border-color: #007FFF;
            box-shadow: 0 0 0 4px rgba(0, 127, 255, 0.1);
            transform: translateY(-1px);
        }

        /* Floating Label Logic */
        .premium-input:placeholder-shown + .premium-label {
            opacity: 0;
            transform: translateY(10px);
        }

        .premium-label {
            position: absolute;
            left: 3rem;
            top: -0.6rem;
            background: #FFFFFF; /* Match input focus bg */
            padding: 0 0.5rem;
            font-size: 0.75rem;
            color: #007FFF;
            font-weight: 600;
            transition: all 0.3s ease;
            pointer-events: none;
            border-radius: 4px;
        }

        /* Animated Objects */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px); 
            z-index: 0;
            opacity: 0.8; /* Higher opacity for vibrancy */
        }

        .orb-blue {
            background: #007FFF;
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            animation: float 8s ease-in-out infinite;
        }

        .orb-yellow {
            background: #F0B800;
            width: 300px;
            height: 300px;
            bottom: 20%;
            right: -50px;
            animation: float-delayed 10s ease-in-out infinite;
        }
        
        .orb-red {
            background: #FF4757;
            width: 250px;
            height: 250px;
            bottom: -50px;
            left: 20%;
            animation: float 12s ease-in-out infinite reverse;
        }
    </style>
</head>
<body class="min-h-screen bg-white text-slate-800 overflow-x-hidden antialiased selection:bg-rdc-yellow selection:text-rdc-dark-blue">

    <!-- Vibrant Gradient Background -->
    <div class="fixed inset-0 bg-gradient-to-br from-blue-50 via-white to-blue-50 -z-20"></div>

    <!-- Ambient Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="orb orb-blue opacity-20"></div>
        <div class="orb orb-yellow opacity-20"></div>
        <div class="orb orb-red opacity-10"></div>
        
        <!-- Grid Overlay (Blue lines) -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwgMTI3LCAyNTUsIDAuMSkiLz48L3N2Zz4=')] opacity-50"></div>
    </div>

    <!-- Main Container -->
    <div class="relative min-h-screen flex flex-col lg:flex-row">
        
        <!-- Left Panel: Branding & Visuals (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-5/12 relative flex-col justify-between p-12 z-10">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3 w-fit group" data-aos="fade-down">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rdc-blue to-rdc-dark-blue flex items-center justify-center shadow-lg shadow-rdc-blue/20 group-hover:scale-110 transition-transform duration-300 border border-white">
                     <i class="fas fa-hands-helping text-white text-xl"></i>
                </div>
                <div>
                     <h1 class="text-2xl font-bold tracking-tight text-slate-900">Service<span class="text-rdc-blue">RDC</span></h1>
                     <p class="text-[10px] text-gray-500 font-bold tracking-[0.2em] uppercase">Plateforme Nationale</p>
                </div>
            </a>

            <!-- Central Visual -->
            <div class="relative my-auto py-12">
                <!-- Decorative Bar -->
                <div class="absolute -left-12 top-1/2 -translate-y-1/2 w-2 h-40 bg-gradient-to-b from-rdc-blue via-rdc-yellow to-rdc-red rounded-r-full shadow-lg"></div>
                
                <h2 class="text-5xl font-bold leading-tight mb-6 text-slate-900" data-aos="fade-right" data-aos-delay="100">
                    L'excellence <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-rdc-blue to-rdc-dark-blue animate-pulse-slow">Congolaise</span>
                    <br> à votre service.
                </h2>
                
                <p class="text-lg text-slate-600 max-w-sm leading-relaxed mb-8" data-aos="fade-right" data-aos-delay="200">
                    Connectez-vous à la première plateforme qui valorise le talent et l'expertise locale en RDC.
                </p>

                <!-- Stats Visual -->
                <div class="flex items-center gap-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white/80 backdrop-blur-md border border-white/50 shadow-xl shadow-blue-900/5 px-5 py-3 rounded-2xl flex items-center gap-3 hover:scale-105 transition-transform cursor-default">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-rdc-blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900">10k+</div>
                            <div class="text-xs text-slate-500">Utilisateurs</div>
                        </div>
                    </div>
                    
                    <div class="bg-white/80 backdrop-blur-md border border-white/50 shadow-xl shadow-yellow-900/5 px-5 py-3 rounded-2xl flex items-center gap-3 hover:scale-105 transition-transform cursor-default">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-rdc-yellow">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900">500+</div>
                            <div class="text-xs text-slate-500">Offres du jour</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-xs text-slate-500 font-medium flex gap-6" data-aos="fade-up" data-aos-delay="400">
                <span>&copy; {{ date('Y') }} ServiceRDC</span>
                <a href="#" class="hover:text-rdc-blue transition-colors">Confidentialité</a>
                <a href="#" class="hover:text-rdc-blue transition-colors">Conditions</a>
            </div>
        </div>

        <!-- Right Panel: Auth Form -->
        <div class="w-full lg:w-7/12 flex flex-col justify-center items-center p-6 lg:p-12 relative z-10">
            <!-- Mobile Header -->
            <div class="w-full max-w-md lg:hidden mb-8 flex justify-between items-center" data-aos="fade-down">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rdc-blue to-rdc-dark-blue flex items-center justify-center shadow-lg">
                        <i class="fas fa-hands-helping text-white"></i>
                    </div>
                    <span class="font-bold text-xl text-slate-900">Service<span class="text-rdc-blue">RDC</span></span>
                </a>
            </div>

            <!-- Form Container -->
            <div class="w-full max-w-md" data-aos="zoom-in-up" data-aos-duration="600">
                @yield('content')
            </div>
        </div>

    </div>

    @stack('scripts')
    
    <script>
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    </script>
</body>
</html>
