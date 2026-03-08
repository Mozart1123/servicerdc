@props(['route' => '#', 'icon', 'label', 'badge' => null, 'badgeColor' => 'bg-rdc-blue', 'variant' => 'default', 'glow' => false])

@php
    $href = $route === '#' ? '#' : (Route::has($route) ? route($route) : '#');
    $isActive = ($href !== '#' && request()->url() == $href) || ($route !== '#' && request()->routeIs($route));
    
    $baseClasses = "flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all group relative overflow-hidden";
    
    $variantClasses = match($variant) {
        'danger' => $isActive 
            ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' 
            : 'text-red-400 hover:bg-red-500/10 hover:text-red-500',
        'panic' => 'bg-red-600/10 text-red-500 border border-red-600/20 hover:bg-red-600 hover:text-white animate-pulse-soft',
        'divine' => $isActive
            ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20'
            : 'text-amber-500 hover:bg-amber-500/10 hover:text-amber-500',
        'cosmic' => $isActive
            ? 'bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white shadow-xl shadow-purple-500/20'
            : 'text-purple-400 hover:bg-purple-500/10 hover:text-purple-500',
        'gold' => $isActive
            ? 'bg-gradient-to-r from-amber-400 to-yellow-600 text-white shadow-lg shadow-amber-500/30'
            : 'text-amber-500 hover:bg-amber-500/10 hover:text-amber-600',
        default => $isActive 
            ? 'bg-blue-50 text-rdc-blue shadow-sm ring-1 ring-blue-100' 
            : 'text-slate-400 hover:bg-slate-50 hover:text-slate-900'
    };

    $iconBaseClasses = "w-8 h-8 rounded-lg flex items-center justify-center transition-all relative z-10";
    $iconVariantClasses = match($variant) {
        'danger' => $isActive 
            ? 'bg-white/20 text-white' 
            : 'bg-red-500/10 border border-red-500/20 text-red-400 group-hover:bg-red-500 group-hover:text-white',
        'panic' => 'bg-red-600 text-white shadow-lg shadow-red-600/40',
        'divine' => $isActive
            ? 'bg-white/20 text-white'
            : 'bg-amber-500/10 border border-amber-500/20 text-amber-500 group-hover:bg-amber-500 group-hover:text-white',
        'cosmic' => $isActive
            ? 'bg-white/20 text-white'
            : 'bg-purple-500/10 border border-purple-500/20 text-purple-400 group-hover:bg-purple-600 group-hover:text-white',
        'gold' => $isActive
            ? 'bg-white/20 text-white'
            : 'bg-amber-500/10 border border-amber-500/20 text-amber-500 group-hover:bg-amber-500 group-hover:text-white',
        default => $isActive 
            ? 'bg-rdc-blue text-white shadow-md shadow-blue-500/20' 
            : 'bg-white border border-slate-200 text-slate-400 group-hover:border-rdc-blue/30 group-hover:text-rdc-blue'
    };
@endphp

<li>
    <a href="{{ $href }}" class="{{ $baseClasses }} {{ $variantClasses }}">
        @if($glow && $isActive)
            <div class="absolute inset-0 bg-white/10 blur-xl"></div>
        @endif
        
        <div class="{{ $iconBaseClasses }} {{ $iconVariantClasses }}">
            <i class="{{ $icon }} text-xs"></i>
        </div>
        
        <span class="flex-1 truncate {{ $isActive ? 'font-bold' : '' }} relative z-10">{{ $label }}</span>
        
        @if($badge)
            <span class="ml-auto px-1.5 py-0.5 {{ $badgeColor }} text-white text-[9px] font-bold rounded shadow-sm relative z-10 uppercase tracking-tighter">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>
