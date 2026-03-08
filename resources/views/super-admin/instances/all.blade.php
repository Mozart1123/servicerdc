@extends('layouts.super-admin')

@section('header_title', 'Gestion Multi-Instance | NEXUS PLANÉTAIRE')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Cluster Overview -->
    <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-950 rounded-[3.5rem] p-12 text-white shadow-2xl relative overflow-hidden group">
        <!-- Animated World Map SVG background (Mockup) -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                <path d="M150,150 Q400,100 650,150" stroke="white" stroke-width="0.5" fill="none" class="animate-pulse" />
                <circle cx="150" cy="150" r="2" fill="#007FFF" class="animate-ping" />
                <circle cx="650" cy="150" r="2" fill="#F0B800" />
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="flex items-center gap-10">
                <div class="w-24 h-24 rounded-[2.5rem] bg-rdc-blue/10 border-2 border-rdc-blue/30 flex items-center justify-center text-4xl text-rdc-blue shadow-[0_0_50px_rgba(0,127,255,0.2)]">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div>
                    <h2 class="text-4xl font-heading font-black tracking-tighter uppercase mb-2">Cluster SRDC-PRIME</h2>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-mono text-emerald-400 uppercase font-bold">3 Instances Actives</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-bolt text-amber-500 text-[10px]"></i>
                            <span class="text-[10px] font-mono text-slate-400 uppercase">Load Balancer: OK</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="px-8 py-5 bg-white/5 backdrop-blur-md rounded-3xl border border-white/10 text-center">
                    <p class="text-[9px] font-mono text-slate-500 uppercase tracking-widest mb-1">Total Traffic</p>
                    <p class="text-2xl font-black">1.2 TB <span class="text-emerald-500 text-xs">/h</span></p>
                </div>
                <div class="px-8 py-5 bg-white/5 backdrop-blur-md rounded-3xl border border-white/10 text-center">
                    <p class="text-[9px] font-mono text-slate-500 uppercase tracking-widest mb-1">Global Health</p>
                    <p class="text-2xl font-black text-emerald-400">99.9%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Instance Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @php
            $instances = [
                ['id' => 'KIN-MAIN-01', 'region' => 'Africa (Kinshasa)', 'status' => 'MASTER', 'usage' => 12, 'type' => 'm6g.2xlarge', 'ip' => '197.242.0.1'],
                ['id' => 'EU-PARIS-02', 'region' => 'Europe (Paris)', 'status' => 'SLAVE', 'usage' => 45, 'type' => 't4g.xlarge', 'ip' => '15.2.8.4'],
                ['id' => 'US-VAULT-01', 'region' => 'North America (N. Virginia)', 'status' => 'BACKUP', 'usage' => 2, 'type' => 't4g.medium', 'ip' => '54.1.2.9'],
            ];
        @endphp
        @foreach($instances as $inst)
        <div class="bg-white rounded-[3.5rem] p-4 shadow-sm border border-slate-100 group hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
            <div class="bg-slate-50 rounded-[2.5rem] p-8 space-y-8 relative overflow-hidden">
                <!-- Status Badge -->
                <div class="absolute top-8 right-8 px-3 py-1 bg-white rounded-full border border-slate-200 text-[9px] font-black uppercase tracking-widest {{ $inst['status'] == 'MASTER' ? 'text-amber-500 ring-2 ring-amber-500/10' : 'text-slate-400' }}">
                    {{ $inst['status'] }}
                </div>

                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-xl {{ $inst['status'] == 'MASTER' ? 'text-amber-500' : 'text-slate-400' }}">
                        <i class="fas fa-server"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 font-heading uppercase tracking-tight">{{ $inst['id'] }}</h4>
                        <p class="text-[9px] text-slate-400 font-mono uppercase tracking-widest mt-1">{{ $inst['region'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="space-y-1">
                        <p class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Usage CPU</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $inst['usage'] > 80 ? 'red-500' : ($inst['usage'] > 50 ? 'amber-500' : 'emerald-500') }}" style="width: {{ $inst['usage'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-mono font-bold">{{ $inst['usage'] }}%</span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Instance Type</p>
                        <p class="text-[10px] font-mono font-bold text-slate-900">{{ $inst['type'] }}</p>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-between border-t border-slate-200">
                    <span class="text-[10px] font-mono text-slate-400">{{ $inst['ip'] }}</span>
                    <div class="flex gap-2">
                        <button class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rdc-blue transition-all" title="SSH Terminal">
                            <i class="fas fa-terminal text-[10px]"></i>
                        </button>
                        <button class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-all" title="Restart">
                            <i class="fas fa-power-off text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <button class="w-full py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-all">
                    Analyser Métriques Détallées
                </button>
            </div>
        </div>
        @endforeach

        <!-- Add Instance Card -->
        <button class="bg-slate-50 rounded-[3.5rem] border-2 border-dashed border-slate-200 p-8 flex flex-col items-center justify-center text-slate-400 hover:bg-slate-100 hover:border-slate-300 transition-all group min-h-[400px]">
            <div class="w-20 h-20 rounded-full border-2 border-dashed border-slate-200 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <p class="font-black text-[11px] uppercase tracking-widest">Invoquer une Nouvelle Instance</p>
            <p class="text-[9px] mt-2 opacity-60">Provisionnement AWS instantané</p>
        </button>
    </div>
</div>
@endsection
