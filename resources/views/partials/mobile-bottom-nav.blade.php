@auth
@php
    $user = auth()->user();
    $isArtisan = $user->isArtisan();
    $unreadMessages = $user->unread_messages_count ?? 0;

    $homeRoute = route('user.dashboard');
    $homeActive = request()->routeIs('user.dashboard');

    $requestsRoute = $isArtisan ? route('user.artisan.service-requests.index') : route('user.service-requests.index');
    $requestsActive = request()->routeIs('user.service-requests.*') || request()->routeIs('user.artisan.service-requests.*');

    $centerRoute = $isArtisan ? route('user.services.create') : route('user.services.index');
    $centerLabel = $isArtisan ? 'Ajouter' : 'Besoin';

    $messagesRoute = route('user.messages.index');
    $messagesActive = request()->routeIs('user.messages.*');

    $profileRoute = route('user.profile');
    $profileActive = request()->routeIs('user.profile*') || request()->routeIs('user.account*');
@endphp

<!-- Barre d'onglets fixe en bas (Mobile uniquement) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-t border-slate-200/80 shadow-[0_-4px_25px_rgba(0,0,0,0.06)] px-2 py-1.5"
     style="padding-bottom: max(0.375rem, env(safe-area-inset-bottom));">
    <div class="max-w-md mx-auto flex items-center justify-around relative">

        <!-- 1. Accueil -->
        <a href="{{ $homeRoute }}"
           class="flex-1 flex flex-col items-center justify-center py-1 text-center transition-colors {{ $homeActive ? 'text-rdc-blue font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fas fa-house text-lg mb-0.5 {{ $homeActive ? 'text-rdc-blue scale-110' : '' }} transition-transform"></i>
            </div>
            <span class="text-[10px] tracking-tight leading-none">Accueil</span>
        </a>

        <!-- 2. Mes demandes -->
        <a href="{{ $requestsRoute }}"
           class="flex-1 flex flex-col items-center justify-center py-1 text-center transition-colors {{ $requestsActive ? 'text-rdc-blue font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fas fa-list-check text-lg mb-0.5 {{ $requestsActive ? 'text-rdc-blue scale-110' : '' }} transition-transform"></i>
            </div>
            <span class="text-[10px] tracking-tight leading-none">{{ $isArtisan ? 'Demandes' : 'Mes demandes' }}</span>
        </a>

        <!-- 3. Bouton central "+" (Mise en avant) -->
        <div class="flex-1 flex flex-col items-center justify-center relative">
            <a href="{{ $centerRoute }}"
               title="{{ $centerLabel }}"
               class="w-12 h-12 -mt-6 rounded-full bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white flex items-center justify-center shadow-lg shadow-rdc-blue/35 border-4 border-white active:scale-95 hover:scale-105 transition-all">
                <i class="fas fa-plus text-lg"></i>
            </a>
            <span class="text-[10px] tracking-tight leading-none font-bold text-rdc-blue mt-1">{{ $centerLabel }}</span>
        </div>

        <!-- 4. Messages -->
        <a href="{{ $messagesRoute }}"
           class="flex-1 flex flex-col items-center justify-center py-1 text-center transition-colors relative {{ $messagesActive ? 'text-rdc-blue font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fas fa-comments text-lg mb-0.5 {{ $messagesActive ? 'text-rdc-blue scale-110' : '' }} transition-transform"></i>
                @if($unreadMessages > 0)
                <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center leading-none ring-2 ring-white">
                    {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                </span>
                @endif
            </div>
            <span class="text-[10px] tracking-tight leading-none">Messages</span>
        </a>

        <!-- 5. Profil -->
        <a href="{{ $profileRoute }}"
           class="flex-1 flex flex-col items-center justify-center py-1 text-center transition-colors {{ $profileActive ? 'text-rdc-blue font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fas fa-user text-lg mb-0.5 {{ $profileActive ? 'text-rdc-blue scale-110' : '' }} transition-transform"></i>
            </div>
            <span class="text-[10px] tracking-tight leading-none">Profil</span>
        </a>

    </div>
</nav>
@endauth
