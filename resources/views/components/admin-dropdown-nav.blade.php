@props(['icon', 'label', 'activePrefixes' => []])

@php
    $isActive = false;
    foreach ($activePrefixes as $prefix) {
        if (request()->routeIs($prefix . '.*')) {
            $isActive = true;
            break;
        }
    }
@endphp

<li x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
    <!-- Dropdown Header -->
    <button @click="open = !open" 
            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all group {{ $isActive ? 'bg-blue-50 text-rdc-blue shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        
        <!-- Icon Box -->
        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $isActive ? 'bg-rdc-blue text-white shadow-md shadow-blue-500/20' : 'bg-white border border-slate-200 text-slate-400 group-hover:border-rdc-blue/30 group-hover:text-rdc-blue' }}">
            <i class="{{ $icon }} text-xs"></i>
        </div>
        
        <!-- Label -->
        <span class="flex-1 text-left truncate {{ $isActive ? 'font-bold' : '' }}">{{ $label }}</span>
        
        <!-- Chevron -->
        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    
    <!-- Dropdown Content (Children) -->
    <ul x-show="open" 
        x-collapse 
        class="mt-1 space-y-1 pl-11 relative before:absolute before:inset-y-0 before:left-8 before:w-px before:bg-slate-200" 
        x-cloak>
        {{ $slot }}
    </ul>
</li>