@if(isset($user) && $user->isVerified())
    @php
        $isClient = $user->isClient();
    @endphp

    @if($isClient)
        {{-- Client badge: discrete inline badge, shown only in private area or specified context --}}
        @if(!empty($showClientPrivate))
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#e6f7f8] text-[#0f7a86] border border-[#29B6D1]/20">
                <i class="fas fa-shield-check text-[#0f7a86] text-[11px]"></i>
                <span>{{ $user->verified_badge_label }}</span>
            </span>
        @endif
    @else
        {{-- Artisan & Recruteur badge: visible public badge --}}
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#e6f7f8] text-[#0f7a86] border border-[#29B6D1]/30 shadow-sm" title="{{ $user->verified_badge_label }}">
            <i class="fas fa-shield-check text-[#0f7a86]"></i>
            <span>{{ $user->verified_badge_label }}</span>
        </span>
    @endif
@endif
