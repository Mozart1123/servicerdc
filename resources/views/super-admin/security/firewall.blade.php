@extends('layouts.super-admin')

@section('header_title', 'Firewall System & Security')

@section('content')
<div class="space-y-8">
    <!-- Security Threat Map -->
    <div class="bg-slate-900 rounded-[3rem] shadow-2xl p-10 border border-white/5 relative overflow-hidden h-[450px]">
        <div class="absolute inset-0 pointer-events-none opacity-[0.05]" style="background-image: radial-gradient(#fff 1px, transparent 0); background-size: 50px 50px;"></div>
        
        <div class="relative z-10 h-full flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2rem] bg-red-500/10 flex items-center justify-center text-3xl text-red-500 border border-red-500/20 shadow-lg shadow-red-500/10">
                        <i class="fas fa-shield-virus"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-heading font-extrabold text-white tracking-tight">GLOBAL THREAT MONITOR</h2>
                        <p class="text-[10px] font-mono text-slate-500 uppercase tracking-widest mt-1">Status: DEFENSIF ACTIF - NIVEAU ALPHA-5</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-mono text-emerald-500 font-bold uppercase tracking-widest">Intégrité: 100%</span>
                    </div>
                    <button class="px-6 py-2.5 bg-red-600 text-white text-[10px] font-bold rounded-2xl shadow-xl shadow-red-600/20 hover:bg-red-700 transition-all uppercase tracking-widest animate-pulse-soft">
                        Lock All Nodes
                    </button>
                </div>
            </div>

            <!-- Fake Map/Abstract Viz -->
            <div class="flex-1 rounded-[2rem] bg-black/40 border border-white/5 relative group cursor-crosshair overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-[800px] h-[800px] border border-white/5 rounded-full absolute animate-ping-slow"></div>
                    <div class="w-[600px] h-[600px] border border-white/5 rounded-full absolute animate-ping-slow" style="animation-delay: 1s"></div>
                    <div class="w-[400px] h-[400px] border border-white/5 rounded-full absolute animate-ping-slow" style="animation-delay: 2s"></div>
                </div>
                
                <!-- Random Threat Points -->
                <div class="absolute top-1/4 left-1/3 w-2 h-2 bg-red-500 rounded-full shadow-[0_0_10px_rgba(239,68,68,1)] animate-pulse"></div>
                <div class="absolute top-1/2 left-1/2 w-4 h-4 bg-emerald-500 rounded-full shadow-[0_0_20px_rgba(16,185,129,1)]"></div>
                <div class="absolute bottom-1/4 right-1/4 w-2 h-2 bg-amber-500 rounded-full shadow-[0_0_10px_rgba(245,158,11,1)] animate-pulse" style="animation-delay: 0.5s"></div>
                
                <!-- Viz Label -->
                <div class="absolute bottom-8 left-8 p-6 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-white/10 w-80 shadow-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Menaces Neutralisées</span>
                        <span class="text-xs font-bold text-red-500">+1.2k / 24h</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">DDoS Protection</span>
                            <span class="text-emerald-500 font-bold">ACTIF</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Injection SQL Filter</span>
                            <span class="text-emerald-500 font-bold">ACTIF</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Force Brute Guard</span>
                            <span class="text-emerald-500 font-bold">ACTIF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Controls Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Firewall Rules -->
        <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 p-10 flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 mb-8 flex items-center justify-between font-heading uppercase tracking-tight">
                <span><i class="fas fa-list-check text-rdc-blue mr-3"></i> Filtres de Trafic Actifs</span>
                <button class="text-[10px] font-bold text-rdc-blue hover:underline uppercase tracking-widest">Nouveau Filtre</button>
            </h3>
            <div class="space-y-4 flex-1">
                @php
                    $rules = [
                        ['name' => 'Block China / Russia IPs', 'type' => 'Geo-Fence', 'status' => 'Actif', 'icon' => 'fa-globe'],
                        ['name' => 'Limit Auth Requests (10/min)', 'type' => 'Rate Limit', 'status' => 'Actif', 'icon' => 'fa-bolt'],
                        ['name' => 'Drop invalid SSL handshakes', 'type' => 'Protocol', 'status' => 'Actif', 'icon' => 'fa-lock'],
                        ['name' => 'Scrub binary file uploads', 'type' => 'Content', 'status' => 'Actif', 'icon' => 'fa-file-shield'],
                    ];
                @endphp
                @foreach($rules as $rule)
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 flex items-center justify-between group hover:border-rdc-blue/30 transition-all">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-slate-400 group-hover:text-rdc-blue transition-colors shadow-sm">
                            <i class="fas {{ $rule['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $rule['name'] }}</p>
                            <p class="text-[10px] text-slate-400 font-mono uppercase mt-1">{{ $rule['type'] }} | RULE-ID: {{ rand(1000, 9999) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase">ON</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Security Logs (Condensed) -->
        <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 p-10 flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 mb-8 flex items-center justify-between font-heading uppercase tracking-tight">
                <span><i class="fas fa-shield-halved text-red-500 mr-3"></i> Journal d'Audit Temps Réel</span>
                <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-full animate-pulse-soft">LIVE STREAM</span>
            </h3>
            <div class="bg-slate-900 rounded-3xl p-8 flex-1 font-mono text-[10px] space-y-4 overflow-hidden relative">
                <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-slate-900 to-transparent pointer-events-none"></div>
                
                <div class="flex gap-4">
                    <span class="text-slate-600">16:50:01</span>
                    <span class="text-emerald-500">[SUCCÈS]</span>
                    <span class="text-slate-300">Admin-Auth-Token validé pour Session: SRDC-112.</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-slate-600">16:50:12</span>
                    <span class="text-red-500">[BLOQUÉ]</span>
                    <span class="text-slate-300">Requête suspecte (Path Traversal) sur /storage/../../config.</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-slate-600">16:50:25</span>
                    <span class="text-amber-500">[ALERTE]</span>
                    <span class="text-slate-300">Multiples échecs de login sur Compte: manager@test.com.</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-slate-600">16:50:45</span>
                    <span class="text-rdc-blue">[SYNC]</span>
                    <span class="text-slate-300">Rotation des clés Master-JWT automatique effectuée.</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-slate-600">16:51:02</span>
                    <span class="text-emerald-500">[INFO]</span>
                    <span class="text-slate-300">Sync Master-DB-Cluster-Prime réussie.</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-slate-600">16:51:15</span>
                    <span class="text-slate-400">[SYSTÈME]</span>
                    <span class="text-slate-300">Vérification de l'intégrité des fichiers... OK.</span>
                </div>
                <div class="text-emerald-400 animate-pulse mt-4 cursor-text">>> _</div>
            </div>
            <button class="mt-6 w-full py-4 border-2 border-slate-100 text-slate-400 hover:border-rdc-blue hover:text-rdc-blue transition-all rounded-2xl text-[10px] font-bold uppercase tracking-widest">
                Exporter le journal d'audit (.PDF)
            </button>
        </div>
    </div>
</div>
@endsection
