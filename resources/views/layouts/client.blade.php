@extends('layouts.public')

@section('content')
<div class="bg-[#f7f9fa] min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Flash Messages (Global) --}}
        @if(session('success'))
            <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-medium text-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 font-medium text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- SIDEBAR (becomes full width on mobile) --}}
            <div class="w-full lg:w-72 shrink-0">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden lg:sticky lg:top-24">
                    
                    {{-- User Info --}}
                    <div class="p-6 border-b border-slate-50 flex items-center gap-4">
                        <img src="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=16a3b0&background=e0f2f1' }}" class="w-14 h-14 rounded-full border-2 border-[#16a3b0] object-cover">
                        <div>
                            <p class="font-bold text-slate-800 text-lg leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#16a3b0] font-medium mt-0.5">Espace Client</p>
                        </div>
                    </div>
                    
                    {{-- Navigation Accordion --}}
                    <div class="p-4 space-y-6 lg:space-y-8" id="sidebar-nav-menu">
                        
                        {{-- Groupe 1 : Mon compte --}}
                        <div>
                            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Mon compte</p>
                            <div class="space-y-2 lg:space-y-1">
                                
                                @php $isProfile = request()->routeIs('user.profile*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.profile') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isProfile ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-user w-5 text-center {{ $isProfile ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Profil
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isProfile ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                                @php $isSecurity = request()->routeIs('user.security*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.security') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isSecurity ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-shield-alt w-5 text-center {{ $isSecurity ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Sécurité
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isSecurity ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                                @php $isReport = request()->routeIs('user.report*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.report') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isReport ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-cog w-5 text-center {{ $isReport ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Paramètres & Aide
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isReport ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                            </div>
                        </div>

                        {{-- Groupe 2 : Activité --}}
                        <div>
                            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Activité</p>
                            <div class="space-y-2 lg:space-y-1">
                                
                                @php 
                                    $isRequests = request()->routeIs('user.service-requests.*');
                                    $reqCount = \App\Models\ServiceRequest::where('user_id', auth()->id())->whereIn('status', ['pending', 'accepted'])->count(); 
                                @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.service-requests.index') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isRequests ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-clipboard-list w-5 text-center {{ $isRequests ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Mes demandes
                                            @if($reqCount > 0)
                                                <span class="bg-[#16a3b0]/10 text-[#16a3b0] text-[10px] font-bold px-2 py-0.5 rounded-full ml-1">{{ $reqCount }}</span>
                                            @endif
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isRequests ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>
                                
                                @php $isMessages = request()->routeIs('user.messages.*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.messages.index') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isMessages ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-envelope w-5 text-center {{ $isMessages ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Messages
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isMessages ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                                @php $isJobs = request()->routeIs('user.jobs.*') || request()->routeIs('user.applications.*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.jobs.index') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isJobs ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-briefcase w-5 text-center {{ $isJobs ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Emplois & Candidatures
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isJobs ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                                @php $isFavorites = request()->routeIs('user.favorites*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.favorites') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isFavorites ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-heart w-5 text-center {{ $isFavorites ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Favoris
                                        </div>
                                        <i class="fas fa-chevron-down lg:hidden transition-transform duration-300 {{ $isFavorites ? 'rotate-180' : '' }}"></i>
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                    
                    {{-- Logout --}}
                    <div class="p-4 border-t border-slate-50">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            {{-- DESKTOP DESTINATION CONTAINER --}}
            <div id="desktop-content-container" class="hidden lg:block flex-1 min-w-0"></div>

        </div>
    </div>
</div>

{{-- THE ACTUAL CONTENT TO BE CLONED/MOVED --}}
{{-- This wrapper is moved by JS to either the desktop container OR the active mobile accordion slot --}}
<div id="client-content-wrapper" class="hidden" style="display: none;">
    {{-- Header Title area, outside the card --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">@yield('header_title', 'Mon Espace')</h1>
            @hasSection('header_subtitle')
                <p class="mt-1 text-sm text-slate-500">@yield('header_subtitle')</p>
            @endif
        </div>
    </div>

    {{-- Main content card --}}
    <div id="client-main-card" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 lg:p-8">
        @yield('client_content')
    </div>
</div>

<script>
    (function() {
        const BREAKPOINT = 1024; // lg breakpoint in Tailwind
        
        function handleResponsiveLayout() {
            const wrapper = document.getElementById('client-content-wrapper');
            const desktopContainer = document.getElementById('desktop-content-container');
            const activeNav = document.querySelector('.is-active-nav');
            
            if (!wrapper) return;

            if (window.innerWidth < BREAKPOINT) {
                // MOBILE MODE: Move content under the active nav item
                if (activeNav) {
                    const navItemWrapper = activeNav.closest('.nav-item-wrapper');
                    if (wrapper.parentNode !== navItemWrapper) {
                        navItemWrapper.appendChild(wrapper);
                        
                        // Style adjustments for mobile accordion body
                        wrapper.style.display = 'block';
                        wrapper.classList.remove('hidden');
                        wrapper.classList.add('mt-3', 'mb-4', 'ml-2', 'pl-3', 'border-l-2', 'border-[#16a3b0]/20');
                    }
                } else {
                    // Fallback if no active nav (e.g. 404 or unmapped route)
                    if (desktopContainer && wrapper.parentNode !== desktopContainer) {
                        desktopContainer.appendChild(wrapper);
                        wrapper.style.display = 'block';
                    }
                }
            } else {
                // DESKTOP MODE: Move content back to the right column
                if (desktopContainer && wrapper.parentNode !== desktopContainer) {
                    desktopContainer.appendChild(wrapper);
                    
                    // Reset styles
                    wrapper.style.display = 'block';
                    wrapper.classList.remove('hidden', 'mt-3', 'mb-4', 'ml-2', 'pl-3', 'border-l-2', 'border-[#16a3b0]/20');
                }
            }
        }

        // Run immediately to arrange DOM before initial paint
        handleResponsiveLayout();
        
        // Listen to window resize
        window.addEventListener('resize', handleResponsiveLayout);

        // Mobile specific interactions
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Smooth scroll to active section on mobile load
            if (window.innerWidth < BREAKPOINT) {
                const activeNav = document.querySelector('.is-active-nav');
                if (activeNav) {
                    setTimeout(() => {
                        // Offset for sticky header
                        const y = activeNav.getBoundingClientRect().top + window.scrollY - 80;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }, 100);
                }
            }

            // 2. Accordion visual feedback on click
            document.querySelectorAll('.is-nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth < BREAKPOINT) {
                        // If clicking the currently open section, we can toggle it to hide/show
                        if (this.classList.contains('is-active-nav')) {
                            e.preventDefault(); // Prevent full page reload just to see the same page
                            const wrapper = document.getElementById('client-content-wrapper');
                            const chevron = this.querySelector('.fa-chevron-down');
                            
                            if (wrapper.style.display === 'none') {
                                wrapper.style.display = 'block';
                                if (chevron) chevron.classList.add('rotate-180');
                            } else {
                                wrapper.style.display = 'none';
                                if (chevron) chevron.classList.remove('rotate-180');
                            }
                            return;
                        }

                        // If clicking a new section, rotate the chevron immediately for visual feedback
                        // then let the browser navigate normally
                        const currentActive = document.querySelector('.is-active-nav');
                        if (currentActive) {
                            const oldChevron = currentActive.querySelector('.fa-chevron-down');
                            if (oldChevron) oldChevron.classList.remove('rotate-180');
                        }
                        
                        const newChevron = this.querySelector('.fa-chevron-down');
                        if (newChevron) newChevron.classList.add('rotate-180');
                    }
                });
            });
        });
    })();
</script>
@endsection
