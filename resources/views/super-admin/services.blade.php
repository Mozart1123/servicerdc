@extends('layouts.super-admin')

@section('header_title', 'Écosystème des Services | MANIPULATION UNIVERSELLE')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Divine Command Banner -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-[3.5rem] p-12 text-white shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="flex items-center gap-10">
                <div class="w-24 h-24 rounded-[2.5rem] bg-white text-amber-600 flex items-center justify-center text-4xl shadow-2xl divine-glow">
                    <i class="fas fa-microchip"></i>
                </div>
                <div>
                    <h2 class="text-4xl font-heading font-black tracking-tighter uppercase mb-2">Centre de Contrôle des Services</h2>
                    <p class="text-white/80 font-medium tracking-wide uppercase text-[11px] decoration-white/30 underline underline-offset-8">Gérez, Promouvez ou Effacez n'importe quelle offre sur le marché.</p>
                </div>
            </div>
            
            <button class="px-10 py-5 bg-white text-amber-600 font-black rounded-2xl text-[11px] uppercase tracking-[0.2em] shadow-xl hover:scale-105 transition-all">
                + CRÉATION EX-NIHILO
            </button>
        </div>
    </div>

    <!-- Analytics Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 group hover:border-amber-500/20 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Volume de Services</p>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-heading font-black text-slate-900 leading-none">1,248</h3>
                <span class="text-emerald-500 text-[10px] font-black mb-1">+12%</span>
            </div>
            <div class="mt-6 flex gap-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full bg-amber-500 w-[60%]"></div>
                <div class="h-full bg-rdc-blue w-[25%]"></div>
                <div class="h-full bg-slate-300 w-[15%]"></div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 group hover:border-emerald-500/20 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Approbation Divine</p>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-heading font-black text-slate-900 leading-none">88.3%</h3>
                <span class="text-slate-400 text-[10px] font-black mb-1">GLOBAL</span>
            </div>
            <p class="text-[9px] text-emerald-500 font-bold mt-4 uppercase">146 EN ATTENTE DE JUGEMENT</p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 group hover:border-rdc-blue/20 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Valeur du Marché</p>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-heading font-black text-slate-900 leading-none">42.5K$</h3>
                <span class="text-rdc-blue text-[10px] font-black mb-1">TOTAL</span>
            </div>
            <p class="text-[9px] text-slate-400 font-bold mt-4 uppercase font-mono tracking-tighter">MOYENNE: 142.50$ / SERVICE</p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 group hover:border-red-500/20 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Intégrité Contenu</p>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-heading font-black text-slate-900 leading-none">99%</h3>
                <span class="text-red-500 text-[10px] font-black mb-1">-1%</span>
            </div>
            <p class="text-[9px] text-red-500 font-bold mt-4 uppercase tracking-tighter">4 SIGNALEMENTS À TRAITER</p>
        </div>
    </div>

    <!-- Master Registry -->
    <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 bg-slate-50/20 flex flex-wrap items-center justify-between gap-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 font-heading tracking-tight uppercase">REGISTRE UNIVERSEL DES SERVICES</h3>
                    <p class="text-slate-400 text-[10px] font-mono uppercase tracking-[0.2em] mt-1">Source: DATABASE-SERVICES-MASTER</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative group">
                    <input type="text" placeholder="SCANNAGE PAR MOT-CLÉ..." class="pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs w-80 font-black focus:ring-4 focus:ring-amber-500/10 transition-all outline-none uppercase tracking-widest">
                    <i class="fas fa-radar absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-500 transition-colors animate-pulse"></i>
                </div>
                <div class="flex gap-2">
                    <button class="p-4 bg-slate-900 text-white rounded-2xl hover:bg-amber-500 transition-all"><i class="fas fa-filter text-xs"></i></button>
                    <button class="p-4 bg-slate-900 text-white rounded-2xl hover:bg-amber-500 transition-all"><i class="fas fa-download text-xs"></i></button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Service Identité</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Sujet Créateur</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Flux Financiers</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status de Réalité</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Contrôle Divin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $list = [
                            ['title' => 'Gardiennage Elite Gombe', 'cat' => 'SÉCURITÉ', 'user' => 'Michel Kabongo', 'price' => '450$', 'status' => 'MASTERPIECE', 'color' => 'amber'],
                            ['title' => 'Plomberie Nanotechnologique', 'cat' => 'DEPANNAGE', 'user' => 'Kevin Diallo', 'price' => '85$', 'status' => 'VERIFIED', 'color' => 'emerald'],
                            ['title' => 'Conciergerie de Luxe', 'cat' => 'SERVICES', 'user' => 'Sarah Lopez', 'price' => '1,200$', 'status' => 'LEGENDARY', 'color' => 'purple'],
                            ['title' => 'Destruction de Débris (Mass)', 'cat' => 'NETTOYAGE', 'user' => 'Alain Mwemba', 'price' => '45$', 'status' => 'PENDING', 'color' => 'slate'],
                        ];
                    @endphp
                    @foreach($list as $s)
                    <tr class="hover:bg-slate-100/50 transition-all group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-3xl bg-slate-900 p-[2px] shadow-lg group-hover:scale-110 transition-transform">
                                    <div class="w-full h-full rounded-[1.3rem] bg-white flex items-center justify-center text-slate-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                        <i class="fas fa-atom text-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors uppercase">{{ $s['title'] }}</p>
                                    <p class="text-[9px] font-mono text-slate-400 mt-1 tracking-widest">{{ $s['cat'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($s['user']) }}&background=0F172A&color=FFF" class="w-10 h-10 rounded-2xl shadow-xl hover:rotate-12 transition-transform" alt="">
                                <div>
                                    <p class="text-[11px] font-black text-slate-800 uppercase">{{ $s['user'] }}</p>
                                    <p class="text-[9px] font-mono text-slate-400">UID:{{ rand(1000,9999) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="space-y-1">
                                <p class="text-sm font-black text-slate-900">{{ $s['price'] }} <span class="text-[9px] text-slate-400 font-mono">Net</span></p>
                                <div class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black rounded-lg w-fit uppercase">Frais: 1.5%</div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-{{ $s['color'] }}-500 animate-pulse-soft shadow-[0_0_10px_rgba(var(--{{ $s['color'] }}-500-rgb),0.5)]"></div>
                                <span class="text-[9px] font-black text-{{ $s['color'] }}-600 uppercase tracking-[0.2em]">{{ $s['status'] }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                <button class="w-10 h-10 rounded-2xl bg-white shadow-xl hover:bg-slate-900 hover:text-white transition-all border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-wand-magic-sparkles text-xs"></i>
                                </button>
                                <button class="w-10 h-10 rounded-2xl bg-white shadow-xl hover:bg-amber-500 hover:text-white transition-all border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-layer-group text-xs"></i>
                                </button>
                                <button class="w-10 h-10 rounded-2xl bg-red-500 text-white shadow-xl hover:bg-red-600 transition-all flex items-center justify-center">
                                    <i class="fas fa-meteor text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-10 bg-slate-900 border-t border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-10">
                <div class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Page: 01/48</div>
                <div class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Total: 1,248 Entités</div>
            </div>
            <div class="flex gap-4">
                <button class="px-10 py-4 bg-white/5 border border-white/10 text-slate-400 font-black rounded-2xl text-[9px] uppercase tracking-widest hover:bg-white/10">Dimension Précédente</button>
                <button class="px-10 py-4 bg-white/10 border border-white/20 text-white font-black rounded-2xl text-[9px] uppercase tracking-widest hover:bg-amber-500">Dimension Suivante</button>
            </div>
        </div>
    </div>
</div>
@endsection
