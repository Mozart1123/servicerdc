@props(['notification', 'size' => 'md'])

@php
    [$bg, $text, $icon] = $notification->iconClasses();
    $sizeClasses = $size === 'lg' ? 'w-14 h-14 text-xl rounded-2xl' : 'w-12 h-12 text-lg rounded-2xl';
@endphp

<div {{ $attributes->merge(['class' => "$bg $text $sizeClasses flex items-center justify-center shrink-0 shadow-sm"]) }}>
    <i class="fas {{ $icon }}"></i>
</div>
