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
            @php
                $isAccountPage = request()->routeIs('user.account');
            @endphp
            <div class="w-full lg:w-72 shrink-0 {{ $isAccountPage ? 'block' : 'hidden lg:block' }}">
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
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
                                    </a>
                                </div>

                                @php $isSecurity = request()->routeIs('user.security*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.security') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isSecurity ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-shield-alt w-5 text-center {{ $isSecurity ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Sécurité
                                        </div>
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
                                    </a>
                                </div>

                                @php $isReport = request()->routeIs('user.report*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.report') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isReport ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-cog w-5 text-center {{ $isReport ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Paramètres & Aide
                                        </div>
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
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
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
                                    </a>
                                </div>
                                
                                @php $isMessages = request()->routeIs('user.messages.*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.messages.index') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isMessages ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-envelope w-5 text-center {{ $isMessages ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Messages
                                        </div>
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
                                    </a>
                                </div>

                                @php $isJobs = request()->routeIs('user.jobs.*') || request()->routeIs('user.applications.*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.jobs.index') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isJobs ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-briefcase w-5 text-center {{ $isJobs ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Emplois & Candidatures
                                        </div>
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
                                    </a>
                                </div>

                                @php $isFavorites = request()->routeIs('user.favorites*'); @endphp
                                <div class="nav-item-wrapper">
                                    <a href="{{ route('user.favorites') }}" class="is-nav-link flex items-center justify-between px-4 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-colors {{ $isFavorites ? 'is-active-nav bg-[#16a3b0]/10 text-[#16a3b0]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-heart w-5 text-center {{ $isFavorites ? 'text-[#16a3b0]' : 'text-slate-400' }}"></i> Favoris
                                        </div>
                                        <i class="fas fa-chevron-right lg:hidden text-slate-400 transition-transform duration-300"></i>
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

            {{-- CONTENT AREA --}}
            <div class="flex-1 min-w-0 {{ $isAccountPage ? 'hidden lg:block' : 'block' }}">
                
                {{-- Mobile Back Button --}}
                <div class="lg:hidden mb-4">
                    <a href="{{ route('user.account') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-900 font-medium text-sm transition-colors shadow-sm">
                        <i class="fas fa-arrow-left"></i> Retour au menu
                    </a>
                </div>

                {{-- Header Title area --}}
                <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">@yield('header_title', 'Mon Espace')</h1>
                        @hasSection('header_subtitle')
                            <p class="mt-1 text-sm text-slate-500">@yield('header_subtitle')</p>
                        @endif
                    </div>
                </div>

                {{-- Main content card --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 lg:p-8">
                    @yield('client_content')
                </div>
            </div>

        </div>
    </div>
</div>

@if(!auth()->user()->city)
{{-- ═══ Invite à activer la localisation — même bloc que dans
     layouts/user.blade.php (artisan/recruteur), adapté ici pour les comptes
     client qui passent par ce layout au lieu de layouts/user.blade.php.
     S'affiche une fois par session tant que la ville n'est pas renseignée. ═══ --}}
<div id="location-prompt-overlay"
     style="position:fixed;inset:0;z-index:9998;background:rgba(15,23,42,.55);backdrop-filter:blur(2px);display:none;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:24px;max-width:420px;width:100%;padding:32px;box-shadow:0 25px 60px rgba(0,0,0,.25);text-align:center;">
        <div style="width:56px;height:56px;border-radius:16px;background:rgba(41,182,209,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="fas fa-location-dot" style="color:#29B6D1;font-size:22px;"></i>
        </div>
        <h3 style="font-weight:800;font-size:18px;color:#0f172a;margin:0 0 8px;">Activer ta localisation ?</h3>
        <p style="font-size:13px;color:#64748b;margin:0 0 24px;line-height:1.5;">
            Ça nous aide à te proposer des artisans{{ config('features.recruitment_enabled') ? ' et des offres' : '' }} près de chez toi.
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button id="location-prompt-accept" style="background:linear-gradient(90deg,#29B6D1,#1E9CB5);color:#fff;font-weight:700;font-size:14px;border:none;border-radius:12px;padding:13px;cursor:pointer;">
                <i class="fas fa-location-crosshairs" style="margin-right:8px;"></i><span id="location-prompt-accept-text">Activer ma localisation</span>
            </button>
            <button id="location-prompt-dismiss" style="background:#f1f5f9;color:#475569;font-weight:700;font-size:14px;border:none;border-radius:12px;padding:13px;cursor:pointer;">
                Plus tard
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    if (sessionStorage.getItem('location_prompt_dismissed') === '1') return;

    const overlay     = document.getElementById('location-prompt-overlay');
    const acceptBtn   = document.getElementById('location-prompt-accept');
    const acceptText  = document.getElementById('location-prompt-accept-text');
    const dismissBtn  = document.getElementById('location-prompt-dismiss');
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const ROUTE_SAVE  = '{{ route("user.detect-location") }}';

    // Laisse la page se charger avant d'afficher l'invite.
    setTimeout(() => { overlay.style.display = 'flex'; }, 1200);

    function dismiss() {
        sessionStorage.setItem('location_prompt_dismissed', '1');
        overlay.style.display = 'none';
    }

    dismissBtn.addEventListener('click', dismiss);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) dismiss();
    });

    acceptBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            dismiss();
            return;
        }

        acceptText.textContent = 'Localisation...';
        acceptBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            async function (position) {
                try {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`, {
                        headers: { 'Accept-Language': 'fr' }
                    });
                    const data = await res.json();
                    const city     = data.address.city || data.address.town || data.address.village || data.address.suburb || null;
                    const province = data.address.state || data.address.province || null;

                    if (city) {
                        await fetch(ROUTE_SAVE, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ city: city, province: province })
                        });
                    }
                } catch (e) {
                    // Échec silencieux — la localisation n'est pas indispensable.
                } finally {
                    dismiss();
                }
            },
            function () {
                // Permission refusée ou erreur — on ferme sans insister,
                // l'invite réapparaîtra à la prochaine session.
                dismiss();
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
})();
</script>
@endpush
@endif
@endsection
