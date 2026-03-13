@extends('layouts.admin')

@section('title', 'Gestion Commissions')
@section('header_title', 'Revenus Plateforme')
@section('page_title', 'Régie Directe')
@section('page_subtitle', 'Consultez et ajustez les taux de commission prélevés sur les transactions de mise en relation.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl text-nowrap"></div>
            <h4 class="text-[10px] sm:text-xs font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8">Taux Actuel</h4>
            <div class="flex items-center gap-4 sm:gap-6 mb-8 sm:mb-10">
                <span class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tighter">15<span class="text-lg sm:text-xl text-slate-300 ml-1 sm:ml-2">%</span></span>
                <div class="px-3 sm:px-4 py-1.5 sm:py-2 bg-emerald-50 text-emerald-600 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest">+2%</div>
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium leading-relaxed mb-8 sm:mb-10">Ce taux est appliqué par défaut sur l'ensemble des transactions.</p>
            <button class="w-full py-3.5 sm:py-4 bg-slate-900 text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200">Ajuster</button>
        </div>

        <div class="bg-indigo-900 p-6 sm:p-10 rounded-[2rem] sm:rounded-[4rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            <h4 class="text-[9px] sm:text-[10px] font-black text-white/40 uppercase tracking-widest mb-6 sm:mb-8">Projection</h4>
            <div class="flex items-end gap-1 h-24 sm:h-32 mb-6 sm:mb-8">
                @foreach([20, 40, 30, 60, 45, 80, 55, 90, 100, 85, 95, 120] as $index => $h)
                    <div class="flex-1 bg-white/10 rounded-full hover:bg-rdc-blue transition-colors {{ $index > 6 ? 'hidden sm:block' : '' }}" style="height: {{ ($h/120)*100 }}%"></div>
                @endforeach
            </div>
            <div class="flex items-center justify-between gap-4">
                <div class="overflow-hidden">
                    <h5 class="text-xl sm:text-2xl font-black truncate">12.4k $</h5>
                    <p class="text-[8px] sm:text-[9px] font-black text-white/40 uppercase truncate">Prévisions (Fév)</p>
                </div>
                <div class="text-right overflow-hidden">
                    <h5 class="text-xl sm:text-2xl font-black text-emerald-400 truncate">+18%</h5>
                    <p class="text-[8px] sm:text-[9px] font-black text-white/40 uppercase truncate">Croissance</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
