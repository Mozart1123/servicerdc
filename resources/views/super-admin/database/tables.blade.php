@extends('layouts.super-admin')

@section('header_title', 'Database Master Explorer')

@section('content')
<div class="space-y-8">
    <!-- DB Health Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="fas fa-database text-8xl text-white"></i>
            </div>
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest">Taille Totale DB</p>
            <h3 class="text-3xl font-heading font-extrabold text-white mt-2">1.2 GB</h3>
            <p class="text-[9px] font-mono text-emerald-400 mt-2">STORAGE: 12% UTILISÉ</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Indexation</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900 mt-2">99.9%</h3>
            </div>
            <div class="mt-4 flex gap-1">
                @for($i=0; $i<12; $i++) <div class="h-1 flex-1 bg-emerald-500 rounded-full"></div> @endfor
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Connexions Actives</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900 mt-2">142</h3>
            </div>
            <p class="text-[9px] font-mono text-slate-500 mt-4 uppercase tracking-tighter">DRIVER: MYSQL v8.0.32</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Latence Query</p>
                <h3 class="text-3xl font-heading font-extrabold text-emerald-600 mt-2">4ms</h3>
            </div>
            <span class="text-[9px] font-mono text-emerald-500 font-bold uppercase tracking-tight">Vitesse Divine</span>
        </div>
    </div>

    <!-- Table Browser -->
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rdc-blue text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fas fa-table-list"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 font-heading tracking-tight uppercase">REGISTRE DES TABLES MÉTIER</h3>
                    <p class="text-slate-500 text-xs">Accès direct aux données bas niveau du système.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative group">
                    <input type="text" placeholder="Filtrer les tables..." class="pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl text-xs w-64 focus:ring-2 focus:ring-rdc-blue/20 transition-all font-medium">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-rdc-blue transition-colors text-xs"></i>
                </div>
                <button class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-bold rounded-xl shadow-lg hover:bg-rdc-blue transition-all uppercase tracking-widest">
                    Lancer Migration
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nom de la Table</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-center">Enregistrements</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-center">Taille</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Dernière Modification</th>
                        <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $tables = [
                            ['name' => 'users', 'count' => '12,450', 'size' => '4.2 MB', 'date' => 'Il y a 2 mins', 'color' => 'rdc-blue'],
                            ['name' => 'services', 'count' => '4,812', 'size' => '1.8 MB', 'date' => 'Il y a 15 mins', 'color' => 'amber-500'],
                            ['name' => 'jobs', 'count' => '2,340', 'size' => '842 KB', 'date' => 'Il y a 1h', 'color' => 'red-500'],
                            ['name' => 'messages', 'count' => '148,920', 'size' => '124 MB', 'date' => 'En continu', 'color' => 'indigo-500'],
                            ['name' => 'transactions', 'count' => '8,245', 'size' => '3.5 MB', 'date' => 'Il y a 5 mins', 'color' => 'emerald-500'],
                            ['name' => 'logs_security', 'count' => '1.2M', 'size' => '480 MB', 'date' => 'Il y a 1 sec', 'color' => 'rose-600'],
                            ['name' => 'failed_jobs', 'count' => '0', 'size' => '12 KB', 'date' => 'Il y a 4 jours', 'color' => 'slate-400'],
                        ];
                    @endphp
                    @foreach($tables as $table)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-database text-[10px] opacity-50"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $table['name'] }}</p>
                                    <p class="text-[9px] text-slate-400 font-mono uppercase tracking-tighter">InnoDB | utf8mb4_unicode_ci</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ $table['count'] }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg border border-slate-100">{{ $table['size'] }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ $table['color'] }} animate-pulse"></div>
                                <span class="text-xs font-bold text-slate-600">{{ $table['date'] }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button title="Explorer les données" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all">
                                    <i class="fas fa-search-plus text-xs"></i>
                                </button>
                                <button title="Analyser structure" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-500 hover:text-white transition-all">
                                    <i class="fas fa-wrench text-xs"></i>
                                </button>
                                <button title="Vider la table" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total: 48 Tables Système Détectées</p>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-xl hover:border-rdc-blue hover:text-rdc-blue transition-all uppercase tracking-widest">Backup Global SQL</button>
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-xl hover:border-rdc-blue hover:text-rdc-blue transition-all uppercase tracking-widest">Optimiser Index</button>
            </div>
        </div>
    </div>
</div>
@endsection
