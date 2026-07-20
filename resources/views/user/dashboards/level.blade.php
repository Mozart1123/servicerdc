@extends('layouts.user')

@section('header_title', 'Mon niveau & Réputation')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">
    
    <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm text-center">
        <h2 class="text-3xl font-black text-slate-900 mb-2">Niveau Actuel</h2>
        
        <div class="flex flex-col items-center justify-center py-6">
            @php
                $badgeClass = match($level->level) {
                    'actif' => 'bg-slate-100 text-slate-600',
                    'verifie' => 'bg-[#29B6D1]/10 text-[#29B6D1]',
                    'elite' => 'bg-amber-100 text-amber-600',
                    default => 'bg-slate-100 text-slate-600'
                };
            @endphp
            <div class="w-32 h-32 rounded-full {{ $badgeClass }} flex items-center justify-center mb-6 shadow-inner relative">
                <i class="fas {{ $level->level_icon }} text-5xl"></i>
            </div>
            
            <h3 class="text-2xl font-black {{ str_replace('bg-', 'text-', explode(' ', $badgeClass)[0]) }} uppercase tracking-widest">{{ $level->level_label }}</h3>
            
            <div class="mt-6 bg-slate-50 border border-slate-100 rounded-3xl p-6 px-10 flex gap-10">
                <div class="text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Note moyenne</p>
                    <p class="text-2xl font-black text-slate-900">{{ number_format($level->average_rating, 1) }} <span class="text-yellow-400 text-lg">★</span></p>
                </div>
                <div class="w-px bg-slate-200"></div>
                <div class="text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Missions totales</p>
                    <p class="text-2xl font-black text-slate-900">{{ $level->total_missions }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($nextLevel)
    <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xl">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase">Prochain niveau : {{ $nextLevel }}</h3>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Progression basée sur les missions requises</p>
            </div>
        </div>

        <div class="mb-4 flex justify-between items-end">
            <span class="text-sm font-black text-slate-700">{{ round($progress) }}% complété</span>
        </div>
        
        <div class="w-full bg-slate-100 rounded-full h-4 mb-6 overflow-hidden">
            <div class="bg-[#29B6D1] h-4 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
        </div>

        @if($missingMessage)
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex gap-4 items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
            <p class="text-sm text-blue-800 font-medium leading-relaxed">{{ $missingMessage }}</p>
        </div>
        @else
        <div class="bg-green-50 border border-green-100 rounded-2xl p-5 flex gap-4 items-start">
            <i class="fas fa-check-circle text-green-500 mt-1"></i>
            <p class="text-sm text-green-800 font-medium leading-relaxed">Vous remplissez toutes les conditions. Le niveau sera mis à jour lors de la prochaine vérification quotidienne.</p>
        </div>
        @endif
    </div>
    @else
    <div class="relative bg-amber-50 border border-amber-100 p-8 rounded-[3rem] shadow-sm text-center">
        <div class="w-16 h-16 rounded-full bg-amber-200 flex items-center justify-center text-amber-600 text-3xl mx-auto mb-4">
            <i class="fas fa-trophy"></i>
        </div>
        <h3 class="text-xl font-black text-amber-900 uppercase mb-2">Vous êtes au sommet !</h3>
        <p class="text-sm text-amber-800 font-medium leading-relaxed">Félicitations, vous avez atteint le niveau maximum sur ProConnect. Maintenez vos excellentes performances pour conserver ce statut.</p>
    </div>
    @endif
</div>
@endsection
