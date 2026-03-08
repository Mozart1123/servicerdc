@props(['route', 'icon', 'label', 'badge' => null, 'badgeColor' => 'bg-rdc-blue'])

@php
    $isActive = $route !== '#' && request()->url() == route($route, [], false) || (str_contains($route, '.') && request()->routeIs(explode('.', $route)[0] . '.*'));
    // Since some routes are # for placeholders, we check if current URL matches if route is not #
    $href = $route === '#' ? '#' : (Route::has($route) ? route($route) : '#');
    $isActive = ($href !== '#' && request()->url() == $href) || ($route !== '#' && request()->routeIs($route));
@endphp

<li>
    <a href="{{ $href }}" 
       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all group {{ $isActive ? 'bg-blue-50 text-rdc-blue shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $isActive ? 'bg-rdc-blue text-white shadow-md shadow-blue-500/20' : 'bg-white border border-slate-200 text-slate-400 group-hover:border-rdc-blue/30 group-hover:text-rdc-blue' }}">
            <i class="{{ $icon }} text-xs"></i>
        </div>
        <span class="flex-1 truncate {{ $isActive ? 'font-bold' : '' }}">{{ $label }}</span>
        
        @if($badge)
            <span class="ml-auto px-1.5 py-0.5 {{ $badgeColor }} text-white text-[10px] font-bold rounded shadow-sm">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>
