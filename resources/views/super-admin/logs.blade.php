@extends('layouts.super-admin')

@section('header_title', 'Audit Universel & Flux Cosmique | OMNISCIENCE')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Log Filter Nexus -->
    <div class="bg-white rounded-[3.5rem] p-4 shadow-sm border border-slate-100 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[300px] flex items-center gap-4 px-6 py-4 bg-slate-50 rounded-[2.5rem]">
            <i class="fas fa-search text-slate-400"></i>
            <input type="text" placeholder="RECHERCHE DANS LES ARCHIVES DU TEMPS..." class="w-full bg-transparent border-none focus:ring-0 text-xs font-bold text-slate-900 placeholder:text-slate-400 uppercase tracking-widest">
        </div>
        
        <div class="flex items-center gap-3">
            <select class="px-6 py-4 bg-white border border-slate-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-rdc-blue/10 selection:bg-rdc-blue selection:text-white outline-none">
                <option>TOUTES LES DIMENSIONS</option>
                <option>VATIONS FINANCIÈRES</option>
                <option>BRÈCHES DE SÉCURITÉ</option>
                <option>ACTIONS DIVINES</option>
                <option>MUTATIONS DE CODE</option>
            </select>
            
            <button class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:bg-rdc-blue transition-all flex items-center gap-3">
                <i class="fas fa-file-export"></i>
                EXPORTER L'HISTOIRE
            </button>
        </div>
    </div>

    <!-- The Great Scroll Case -->
    <div class="bg-[#0b0e14] rounded-[4rem] border border-white/10 shadow-3xl overflow-hidden flex flex-col h-[750px] relative group">
        <!-- CRT Scanner Grid -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.05] z-20" style="background-image: linear-gradient(rgba(18,16,16,0) 50%, rgba(0,0,0,0.25) 50%), linear-gradient(90deg, rgba(255,0,0,0.06), rgba(0,255,0,0.02), rgba(0,0,255,0.06)); background-size: 100% 2px, 3px 100%;"></div>
        
        <div class="px-12 py-8 bg-white/5 border-b border-white/5 flex items-center justify-between relative z-10 backdrop-blur-xl">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <h4 class="text-xs font-mono font-black text-emerald-400 uppercase tracking-[0.3em]">Flux de Conscience Universelle</h4>
                </div>
                <div class="h-4 w-px bg-white/10"></div>
                <p class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Index: ARCHIVE-MASTER-ALFA-9</p>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Intégrité</p>
                    <p class="text-[10px] font-mono text-emerald-400">100% VÉRIFIÉE</p>
                </div>
            </div>
        </div>

        <div class="flex-1 p-12 font-mono text-[12px] overflow-y-auto custom-terminal-scrollbar space-y-6 relative z-10">
            @for($i=0; $i<25; $i++)
            <div class="flex gap-10 group/log items-start">
                <div class="flex flex-col items-end shrink-0 min-w-[140px]">
                    <span class="text-slate-600 font-bold tracking-tighter">[2024.05.20]</span>
                    <span class="text-slate-700 text-[10px] font-medium">{{ rand(10,23) }}:{{ rand(10,59) }}:{{ rand(10,59) }}</span>
                </div>
                
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-4">
                        @php
                            $tags = [
                                ['label' => 'DANGER', 'class' => 'text-red-500 border-red-500/30 bg-red-500/5'],
                                ['label' => 'SECURITY', 'class' => 'text-amber-500 border-amber-500/30 bg-amber-500/5'],
                                ['label' => 'DIVINE', 'class' => 'text-purple-500 border-purple-500/30 bg-purple-500/5'],
                                ['label' => 'WEALTH', 'class' => 'text-emerald-500 border-emerald-500/30 bg-emerald-500/5'],
                                ['label' => 'CORE', 'class' => 'text-rdc-blue border-rdc-blue/30 bg-rdc-blue/5'],
                            ];
                            $tag = $tags[rand(0, 4)];
                        @endphp
                        <span class="px-3 py-1 border rounded-lg font-black text-[9px] uppercase tracking-widest {{ $tag['class'] }}">
                            {{ $tag['label'] }}
                        </span>
                        
                        <p class="text-slate-100 font-medium leading-relaxed">
                            @if($tag['label'] == 'DANGER')
                                Tentative d'intrusion sur le nexus <span class="text-red-400">DB-CLUSTER-01</span>. Origine: KIN-P-{{ rand(100,999) }}. Accès <span class="text-white">REFUSÉ</span>.
                            @elseif($tag['label'] == 'SECURITY')
                                L'Architecte <span class="text-white font-black">Suprême</span> a réinitialisé les protocoles de chiffrement RSA-4096.
                            @elseif($tag['label'] == 'DIVINE')
                                Mode <span class="text-purple-400">IMPERSONATION</span> activé sur l'utilisateur #{{ rand(1000,9999) }}. Durée estimée: 15m.
                            @elseif($tag['label'] == 'WEALTH')
                                Injection de capitaux détectée. <span class="text-emerald-400">+12,500.00$</span> ajoutés à la trésorerie centrale.
                            @else
                                Migration des schémas vers le noyau <span class="text-rdc-blue">V12.4.2</span> effectuée sans aucune latence.
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-6 text-[10px] text-slate-600 opacity-60 group-hover/log:opacity-100 transition-opacity">
                        <span class="flex items-center gap-2 italic"><i class="fas fa-hashtag text-[8px]"></i> {{ hash('crc32', rand(0,1000000)) }}</span>
                        <span class="flex items-center gap-2 italic"><i class="fas fa-microchip text-[8px]"></i> NODE:{{ rand(1,8) }}</span>
                        <button class="text-amber-500/50 hover:text-amber-500 transition-colors uppercase font-black text-[9px] tracking-widest">Détails</button>
                    </div>
                </div>
            </div>
            @endfor

            <div class="pt-10 flex items-center gap-4">
                <span class="text-emerald-500 font-black tracking-widest">MASTER@GOD-CONSOLE:~$</span>
                <span class="w-2.5 h-4 bg-emerald-500 animate-pulse"></span>
            </div>
        </div>

        <!-- Scroll HUD UI -->
        <div class="px-12 py-5 bg-white/5 border-t border-white/5 flex items-center justify-between text-[10px] font-mono text-slate-500 uppercase tracking-widest relative z-10">
            <div class="flex items-center gap-8">
                <span>Buffer: 100%</span>
                <span>Uptime: 1.2M Actions</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-emerald-500">Auto-Scroll: ON</span>
                <div class="w-px h-3 bg-white/10"></div>
                <span class="text-amber-500">Filter Level: ULTRA</span>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-terminal-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-terminal-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
    .custom-terminal-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .custom-terminal-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
</style>
@endsection
