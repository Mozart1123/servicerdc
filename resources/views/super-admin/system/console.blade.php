@extends('layouts.super-admin')

@section('header_title', 'Terminal Root Universel | KERNEL DIVIN')

@section('content')
<div class="space-y-8" x-data="{ 
    terminals: [
        { id: 1, name: 'Cluster-Primary', status: 'Online', output: [] },
        { id: 2, name: 'Security-Shield', status: 'Online', output: [] },
        { id: 3, name: 'Financial-Nexus', status: 'Ready', output: [] }
    ],
    selectedTerminal: 1
}">
    <!-- System Health Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-slate-900 rounded-[3rem] p-10 border border-white/10 shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 0); background-size: 30px 30px;"></div>
            
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-3xl text-emerald-500 divine-glow">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-heading font-black text-white uppercase">VITALITÉ SYSTÈME</h4>
                        <p class="text-[9px] font-mono text-slate-400 uppercase tracking-widest">Temps de réponse global: 12ms</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-mono text-slate-400 uppercase">Load: 0.12</div>
                    <div class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-mono text-slate-400 uppercase">Nodes: 48/48</div>
                </div>
            </div>

            <!-- CPU/RAM/Network Visualization (Mockup with CSS) -->
            <div class="grid grid-cols-3 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>CPU POWER</span>
                        <span class="text-amber-500">14%</span>
                    </div>
                    <div class="h-24 flex items-end gap-1.5">
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-amber-500/20 transition-all h-[10%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-amber-500/30 transition-all h-[25%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-amber-500/40 transition-all h-[15%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-amber-500/50 transition-all h-[40%] animate-pulse"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>MEMORY FLUX</span>
                        <span class="text-rdc-blue">2.4/16 GB</span>
                    </div>
                    <div class="h-24 flex items-end gap-1.5">
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-rdc-blue/20 transition-all h-[60%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-rdc-blue/30 transition-all h-[55%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-rdc-blue/40 transition-all h-[58%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-rdc-blue/50 transition-all h-[62%] animate-pulse"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>NET UPLINK</span>
                        <span class="text-emerald-500">4.1 Gb/s</span>
                    </div>
                    <div class="h-24 flex items-end gap-1.5">
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-emerald-500/20 transition-all h-[30%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-emerald-500/30 transition-all h-[80%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-emerald-500/40 transition-all h-[40%]"></div>
                        <div class="flex-1 bg-white/5 rounded-t-lg group-hover:bg-emerald-500/50 transition-all h-[95%] animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Switch Panel -->
        <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm flex flex-col justify-between">
            <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8">COMMANDES DE CRISE</h5>
            <div class="space-y-4">
                <button class="w-full py-4 px-6 bg-slate-50 group hover:bg-slate-900 text-slate-600 hover:text-white rounded-2xl border border-slate-100 transition-all flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-widest">Flush All Cache</span>
                    <i class="fas fa-wind group-hover:rotate-45 transition-transform text-xs"></i>
                </button>
                <button class="w-full py-4 px-6 bg-slate-50 group hover:bg-rdc-blue text-slate-600 hover:text-white rounded-2xl border border-slate-100 transition-all flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-widest">Reinforce Firewall</span>
                    <i class="fas fa-shield-halved text-xs"></i>
                </button>
                <button class="w-full py-4 px-6 bg-red-50 group hover:bg-red-500 text-red-500 hover:text-white rounded-2xl border border-red-100 transition-all flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-widest">Force Reboot System</span>
                    <i class="fas fa-power-off text-xs"></i>
                </button>
            </div>
            <div class="mt-8 pt-8 border-t border-slate-50">
                <p class="text-[9px] text-slate-400 italic">Attention: Ces actions affectent l'intégralité de l'univers SRDC immédiatement.</p>
            </div>
        </div>
    </div>

    <!-- Multi-Terminal Emulator -->
    <div class="bg-[#0b0e14] rounded-[3.5rem] border border-white/10 shadow-2xl overflow-hidden flex flex-col h-[600px] relative">
        <div class="flex border-b border-white/5">
            <template x-for="term in terminals" :key="term.id">
                <button @click="selectedTerminal = term.id" 
                        :class="selectedTerminal === term.id ? 'bg-white/5 text-white border-b-2 border-amber-500' : 'text-slate-500 hover:bg-white/[0.02]'"
                        class="px-10 py-5 text-[10px] font-mono font-bold uppercase tracking-widest transition-all outline-none">
                    <i class="fas fa-terminal mr-3 text-[8px]" :class="selectedTerminal === term.id ? 'text-amber-500' : ''"></i>
                    <span x-text="term.name"></span>
                </button>
            </template>
        </div>

        <div class="flex-1 p-10 font-mono text-[11px] overflow-y-auto custom-terminal-scrollbar space-y-3 relative">
            <template x-if="selectedTerminal === 1">
                <div class="space-y-2">
                    <div class="text-slate-500 flex gap-4"><span>[21:14:02]</span> <span class="text-emerald-500">SYSTEM:</span> Cluster-Primary node linked with KIN-Master Hub.</div>
                    <div class="text-slate-500 flex gap-4"><span>[21:14:05]</span> <span class="text-rdc-blue">VPC:</span> Secure tunnel established via AWS-Route-66.</div>
                    <div class="text-slate-500 flex gap-4"><span>[21:15:10]</span> <span class="text-amber-500">AUTH:</span> SuperAdmin vision session started. Root privileges active.</div>
                    <div class="text-white pt-4">root@UNIVERS_SRDC:~$ <span class="animate-pulse">_</span></div>
                </div>
            </template>
            <template x-if="selectedTerminal === 2">
                <div class="space-y-2">
                    <div class="text-red-500 font-bold flex gap-4"><span>[21:14:15]</span> <span>ATTACK:</span> Brute-force attempt blocked from IP 142.1... [REDACTED]</div>
                    <div class="text-slate-500 flex gap-4"><span>[21:14:16]</span> <span class="text-emerald-400">DEFENSE:</span> Quantum encryption re-cycled.</div>
                    <div class="text-white pt-4">sec@SHIELD_SRDC:~$ <span class="animate-pulse">_</span></div>
                </div>
            </template>
            <template x-if="selectedTerminal === 3">
                <div class="space-y-2">
                    <div class="text-slate-500 flex gap-4"><span>[21:14:20]</span> <span class="text-emerald-500">TRANSACTION:</span> User_3829 added credits. Verification SUCCESS.</div>
                    <div class="text-slate-500 flex gap-4"><span>[21:14:22]</span> <span class="text-amber-500">LEDGER:</span> Merkle Tree validation complete. 0 errors found.</div>
                    <div class="text-white pt-4">fin@MONEY_NEXUS:~$ <span class="animate-pulse">_</span></div>
                </div>
            </template>
        </div>
        
        <!-- CRT Overlay & Grid -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] overflow-hidden">
            <div class="h-full w-full bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"></div>
        </div>
    </div>
</div>

<style>
    .custom-terminal-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-terminal-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-terminal-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    
    @keyframes bar-grow {
        from { height: 0; }
        to { height: var(--final-height); }
    }
</style>
@endsection
