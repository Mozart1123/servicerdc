@extends('layouts.admin')

@section('title', 'Gestion Commissions')
@section('header_title', 'Revenus Plateforme')
@section('page_title', 'Régie Directe')
@section('page_subtitle', 'Consultez et ajustez les taux de commission prélevés sur les transactions de mise en relation.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">Taux Actuel Par Défaut</h4>
            <div class="flex items-center gap-6 mb-10">
                <span class="text-6xl font-black text-slate-900 tracking-tighter">15<span class="text-xl text-slate-300 ml-2">%</span></span>
                <div class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest">+2% vs 2025</div>
            </div>
            <p class="text-[11px] text-slate-400 font-medium leading-relaxed mb-10">Ce taux est appliqué sur l'ensemble des transactions sauf exceptions définies pour les partenaires premium.</p>
            <button class="w-full py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200">Ajuster le Taux</button>
        </div>

        <div class="bg-indigo-900 p-10 rounded-[4rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            <h4 class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-8">Projection Mensuelle</h4>
            <div class="flex items-end gap-1 h-32 mb-8">
                @foreach([20, 40, 30, 60, 45, 80, 55, 90, 100, 85, 95, 120] as $h)
                    <div class="flex-1 bg-white/10 rounded-full hover:bg-rdc-blue transition-colors" style="height: {{ ($h/120)*100 }}%"></div>
                @endforeach
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-2xl font-black">12.4k $</h5>
                    <p class="text-[9px] font-black text-white/40 uppercase">Prévisions Com. (Fév)</p>
                </div>
                <div class="text-right">
                    <h5 class="text-2xl font-black text-emerald-400">+18%</h5>
                    <p class="text-[9px] font-black text-white/40 uppercase">Croissance Mensuelle</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
