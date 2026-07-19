@props([
    'job',
    'showRoute' => 'public.jobs.show',
])

@php
    $logoUrl = $job->company_logo ? Storage::url($job->company_logo) : null;
    $initial = strtoupper(substr($job->company_name ?? 'C', 0, 1));
@endphp

<a href="{{ route($showRoute, $job->id) }}"
   class="flex flex-col bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group overflow-hidden">

    {{-- Card Body --}}
    <div class="p-4 flex-1 flex flex-col gap-2">
        {{-- Logo + Company --}}
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $job->company_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-[#16a3b0]/20 to-[#16a3b0]/5 flex items-center justify-center">
                        <span class="text-[#16a3b0] font-black text-lg">{{ $initial }}</span>
                    </div>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 truncate">{{ $job->company_name }}</p>
                @if($job->location)
                    <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                        <i class="fas fa-map-marker-alt text-[8px]"></i>
                        <span class="truncate">{{ $job->location }}</span>
                    </p>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <h3 class="font-semibold text-slate-900 text-base leading-snug group-hover:text-[#16a3b0] transition-colors line-clamp-2 break-words">
            {{ $job->title }}
        </h3>

        {{-- Badges --}}
        <div class="flex flex-wrap gap-2">
            <span class="px-2.5 py-1 text-[10px] font-black bg-[#16a3b0]/10 text-[#16a3b0] rounded-full uppercase tracking-wide">
                {{ $job->contract_type }}
            </span>
            @if($job->category)
                <span class="px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-500 rounded-full uppercase tracking-wide">
                    {{ $job->category }}
                </span>
            @endif
            @if($job->salary_range)
                <span class="px-2.5 py-1 text-[10px] font-black bg-green-50 text-green-700 rounded-full">
                    <i class="fas fa-dollar-sign text-[9px] mr-0.5"></i>{{ $job->salary_range }}
                </span>
            @endif
        </div>
    </div>

    {{-- Card Footer --}}
    <div class="px-4 pt-2 pb-3 border-t border-slate-50 flex items-center justify-between bg-slate-50/50">
        <span class="text-[10px] text-slate-400 font-medium">
            <i class="far fa-clock mr-1"></i>{{ $job->created_at->diffForHumans() }}
        </span>
        <span class="inline-flex items-center gap-1 text-[10px] font-black text-[#16a3b0] group-hover:gap-2 transition-all">
            Voir <i class="fas fa-arrow-right text-[9px]"></i>
        </span>
    </div>
</a>
