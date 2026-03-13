@props(['route', 'label', 'badge' => null, 'badgeColor' => 'bg-rdc-blue'])

@php
    $href = $route === '#' ? '#' : (Route::has($route) ? route($route) : '#');
    $isActive = ($href !== '#' && request()->url() == $href) || ($route !== '#' && request()->routeIs($route));
@endphp

<li>
    <a href="{{ $href }}" 
       class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all group relative {{ $isActive ? 'text-rdc-blue bg-blue-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
        
        <!-- Indicator Dot on Active -->
        <div class="absolute -left-[19px] top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full transition-all {{ $isActive ? 'bg-rdc-blue ring-4 ring-white' : 'bg-slate-300 group-hover:bg-slate-400' }}"></div>

        <span class="flex-1 truncate">{{ $label }}</span>
        
        @if($badge)
            <span class="ml-auto px-1.5 py-0.5 {{ $badgeColor }} text-white text-[9px] font-bold rounded shadow-sm leading-none flex items-center justify-center">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>