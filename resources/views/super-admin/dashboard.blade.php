@extends('layouts.super-admin')

@section('header_title', 'Master Console Alpha-1')

@section('content')
<div class="space-y-12 pb-24" x-data="{ powerLevel: 100 }">
    <!-- Master Command Center Unit -->
    <div class="relative group">
        <div class="absolute -inset-1 bg-gradient-to-r from-rdc-blue via-rdc-yellow to-rdc-red rounded-[4rem] blur-2xl opacity-10 group-hover:opacity-20 transition duration-1000"></div>
        <div class="relative bg-slate-900/50 backdrop-blur-3xl rounded-[3.5rem] p-12 border border-white/5 overflow-hidden shadow-[0_40px_100px_rgba(0,0,0,0.6)]">
            
            <div class="relative z-10 flex flex-col xl:flex-row items-center justify-between gap-12">
                <div class="flex items-center gap-10">
                    <div class="relative">
                        <div class="w-28 h-28 rounded-[3rem] bg-gradient-to-br from-rdc-blue to-rdc-blue-dark flex items-center justify-center text-5xl text-white shadow-[0_0_50px_rgba(0,127,255,0.4)] transform hover:rotate-12 transition-transform duration-700">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-full border-[6px] border-slate-900 animate-pulse"></div>
                    </div>
                    <div class="text-center md:text-left">
                        <h2 class="text-5xl font-black text-white tracking-tighter uppercase italic leading-none mb-4">Master <span class="text-rdc-blue">Control</span> Unit</h2>
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-6">
                            <span class="text-[10px] font-mono font-black text-rdc-blue border border-rdc-blue/30 px-5 py-1.5 rounded-full uppercase tracking-[0.3em] bg-rdc-blue/5 italic">Authority: RELATIVE_NULL_0</span>
                            <span class="text-[10px] font-mono font-black text-emerald-500 flex items-center gap-3 uppercase tracking-tighter italic">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span> Global Link Established
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-8">
                    <!-- HUD Gauge -->
                    <div class="bg-black/40 p-6 rounded-[2.5rem] border border-white/5 backdrop-blur-sm text-center min-w-[180px]">
                        <p class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-3 italic">System Integrity</p>
                        <div class="flex items-end justify-center gap-2">
                            <span class="text-4xl font-black text-white italic tabular-nums">98.9</span>
                            <span class="text-xs font-mono font-bold text-emerald-400 mb-1">%</span>
                        </div>
                        <div class="mt-4 w-full bg-white/5 h-1 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full w-[98%] rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                        </div>
                    </div>

                    <!-- Hud Mode -->
                    <div class="bg-black/40 p-2 rounded-[2.5rem] border border-white/5 flex gap-2">
                         <button class="w-16 h-16 rounded-2xl bg-white/5 flex flex-col items-center justify-center text-slate-500 hover:bg-rdc-blue hover:text-white transition-all group">
                             <i class="fas fa-ghost text-lg mb-1"></i>
                             <span class="text-[7px] font-black uppercase italic">Stealth</span>
                         </button>
                         <button class="w-16 h-16 rounded-2xl bg-white/5 flex flex-col items-center justify-center text-slate-500 hover:bg-rdc-red hover:text-white transition-all group">
                             <i class="fas fa-skull text-lg mb-1"></i>
                             <span class="text-[7px] font-black uppercase italic">Purge</span>
                         </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Vitality Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Population Node -->
        <div class="bg-slate-900 border border-white/5 p-8 rounded-[3rem] shadow-2xl group hover:border-rdc-blue/30 transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 rounded-2xl bg-rdc-blue/10 flex items-center justify-center text-rdc-blue group-hover:scale-110 transition-transform">
                    <i class="fas fa-users-viewfinder text-2xl"></i>
                </div>
                <span class="text-[9px] font-mono text-emerald-500 font-bold uppercase tracking-widest">+42.1k / WEEK</span>
            </div>
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-2 italic">Entity Population</p>
            <h3 class="text-4xl font-black text-white italic tabular-nums">{{ number_format($stats['total_users']) }}</h3>
            <div class="mt-8 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-rdc-blue w-[85%]"></div>
            </div>
        </div>

        <!-- Wealth Node -->
        <div class="bg-slate-900 border border-white/5 p-8 rounded-[3rem] shadow-2xl group hover:border-rdc-yellow/30 transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 rounded-2xl bg-rdc-yellow/10 flex items-center justify-center text-rdc-yellow group-hover:scale-110 transition-transform">
                    <i class="fas fa-vault text-2xl"></i>
                </div>
                <div class="flex -space-x-2">
                    <div class="w-5 h-5 rounded-full bg-rdc-yellow border-2 border-slate-900"></div>
                    <div class="w-5 h-5 rounded-full bg-rdc-blue border-2 border-slate-900"></div>
                </div>
            </div>
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-2 italic">Global Wealth Center</p>
            <h3 class="text-4xl font-black text-white italic tabular-nums">{{ number_format($stats['monthly_revenue'], 2) }} <span class="text-rdc-yellow text-xl">$</span></h3>
            <p class="text-[9px] font-mono text-emerald-500 font-bold mt-8 uppercase italic">Flow Integrity: Validated</p>
        </div>

        <!-- Infrastructure Node -->
        <div class="bg-slate-900 border border-white/5 p-8 rounded-[3rem] shadow-2xl group hover:border-emerald-500/30 transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-satellite-dish text-2xl"></i>
                </div>
                <span class="text-[9px] font-mono text-slate-500 italic">DC-KIN-01</span>
            </div>
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-2 italic">Infra Load</p>
            <h3 class="text-4xl font-black text-white italic tabular-nums">24.2 <span class="text-emerald-500 text-xl">TB</span></h3>
            <div class="mt-8 flex gap-1">
                @for($i=0; $i<10; $i++)
                    <div class="flex-1 h-3 rounded-sm {{ $i < 4 ? 'bg-emerald-500' : 'bg-white/5' }}"></div>
                @endfor
            </div>
        </div>

        <!-- Security Node -->
        <div class="bg-slate-900 border border-white/5 p-8 rounded-[3rem] shadow-2xl group hover:border-rdc-red/30 transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 rounded-2xl bg-rdc-red/10 flex items-center justify-center text-rdc-red group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-halved text-2xl"></i>
                </div>
                <span class="px-3 py-1 bg-rdc-red text-white text-[8px] font-black rounded-lg uppercase italic tracking-widest">Active Protect</span>
            </div>
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-2 italic">Thread Detection</p>
            <h3 class="text-4xl font-black text-white italic tabular-nums">0 <span class="text-rdc-red text-xl">CRIT</span></h3>
            <p class="text-[9px] font-mono text-slate-400 font-bold mt-8 uppercase italic">Uptime: 142 Days Continuous</p>
        </div>
    </div>

    <!-- Master Control Grid -->
    <div class="grid grid-cols-1 2xl:grid-cols-3 gap-12">
        <!-- Live Log Console (2/3) -->
        <div class="2xl:col-span-2 space-y-10">
            <div class="bg-black/60 rounded-[3.5rem] border border-white/5 shadow-[0_40px_80px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col h-[700px] relative">
                <!-- Optical CRT Effect -->
                <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden opacity-10">
                    <div class="w-full h-[2px] bg-white/20 animate-scan"></div>
                </div>

                <div class="px-12 py-8 bg-white/5 border-b border-white/5 flex items-center justify-between relative z-10 backdrop-blur-3xl">
                    <div class="flex items-center gap-8">
                         <div class="flex gap-2">
                             <div class="w-3.5 h-3.5 rounded-full bg-[#FF5F56] shadow-[0_0_15px_rgba(255,95,86,0.3)]"></div>
                             <div class="w-3.5 h-3.5 rounded-full bg-[#FFBD2E]"></div>
                             <div class="w-3.5 h-3.5 rounded-full bg-[#27C93F]"></div>
                         </div>
                         <h4 class="text-[11px] font-mono font-black text-slate-400 uppercase tracking-[0.4em] flex items-center gap-4 italic italic">
                             <i class="fas fa-terminal text-rdc-blue"></i> Universal_Audit_Streamer
                         </h4>
                    </div>
                    <div class="flex items-center gap-4 text-[9px] font-mono text-slate-500 uppercase italic">
                         <span class="text-emerald-500 font-black">● LIVE_FEED</span>
                         <span class="opacity-30">|</span>
                         <span>Buffer: 4.2 Pb</span>
                    </div>
                </div>

                <div class="flex-1 p-12 font-mono text-sm overflow-y-auto space-y-6 relative z-10 custom-scrollbar">
                    <div class="flex gap-8 group">
                        <span class="text-white/20 font-bold shrink-0 tabular-nums">14:24:01</span>
                        <div class="flex-1 leading-relaxed">
                            <span class="text-rdc-blue font-black uppercase tracking-widest">[INFRA]</span> 
                             <span class="text-white">Nouvelle instance lancée sur <strong class="underline decoration-rdc-blue/30 underline-offset-4">Region-West-02</strong>. Temps de réponse : 12ms.</span>
                        </div>
                    </div>

                    <div class="flex gap-8 group">
                        <span class="text-white/20 font-bold shrink-0 tabular-nums">14:24:45</span>
                        <div class="flex-1 leading-relaxed">
                            <span class="text-rdc-yellow font-black uppercase tracking-widest">[WEALTH]</span> 
                             <span class="text-white">Audit financier terminé. Toutes les transactions du bloc <strong class="text-emerald-400">#48192</strong> sont validées et conformes au Protocole HQ.</span>
                        </div>
                    </div>

                    <div class="flex gap-8 group">
                        <span class="text-white/30 font-bold shrink-0 tabular-nums">14:25:12</span>
                        <div class="flex-1 leading-relaxed p-4 bg-white/5 rounded-2xl border border-white/5 group-hover:border-rdc-blue/20 transition-all">
                            <span class="text-emerald-500 font-black uppercase tracking-widest">[SECURE]</span> 
                             <span class="text-slate-300">Tentative de BruteForce détectée sur Entity_ID: <span class="bg-red-500/20 px-2 py-0.5 rounded text-red-400">ADMIN_CENTER</span>. Source IP bannie de l'Univers.</span>
                        </div>
                    </div>

                    <div class="flex gap-8 group">
                        <span class="text-white/20 font-bold shrink-0 tabular-nums">14:26:00</span>
                        <div class="flex-1 leading-relaxed">
                            <span class="text-purple-500 font-black uppercase tracking-widest">[DB]</span> 
                             <span class="text-white">Indexation globale des services terminée. <strong class="text-purple-400">42,000+</strong> entités cataloguées.</span>
                        </div>
                    </div>

                    <div class="pt-10 flex items-center gap-4 text-emerald-500 font-black">
                         <i class="fas fa-chevron-right text-xs"></i>
                         <span class="animate-pulse">Waiting for Master Commands...</span>
                    </div>
                </div>

                <!-- HUD Footer -->
                <div class="px-12 py-6 bg-white/2 p-2 border-t border-white/5 flex items-center justify-between text-[9px] font-mono text-slate-600 uppercase tracking-widest relative z-10 italic">
                    <div class="flex gap-10">
                         <span>CPU: 14.8%</span>
                         <span>RAM: 64.2 GB</span>
                         <span>SWAP: 0B</span>
                    </div>
                    <span class="text-rdc-blue font-black underline decoration-slate-800">Uplink: HQ-MASTER-STATION</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Right Control HU (1/3) -->
        <div class="space-y-12">
            <!-- AI Oracle Module -->
            <div class="bg-gradient-to-br from-slate-900 to-black rounded-[4rem] p-12 shadow-2xl border border-white/5 relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rdc-blue/5 rounded-full blur-[100px] group-hover:bg-rdc-blue/10 transition-all duration-1000"></div>
                
                <div class="flex items-center gap-6 mb-12 relative z-10">
                    <div class="w-20 h-20 rounded-[2rem] bg-rdc-blue text-white flex items-center justify-center text-4xl shadow-[0_15px_30px_rgba(0,127,255,0.4)] group-hover:rotate-12 transition-transform duration-500">
                        <i class="fas fa-brain-circuit"></i>
                    </div>
                    <div>
                        <h4 class="text-2xl font-black text-white leading-none uppercase italic tracking-tighter">AI Oracle</h4>
                        <p class="text-[9px] font-mono font-bold text-rdc-blue uppercase tracking-[0.4em] mt-2 opacity-80 italic">Neural Level 0</p>
                    </div>
                </div>

                <div class="space-y-8 relative z-10">
                     <div class="p-8 rounded-[2.5rem] bg-white/2 border border-white/5 hover:border-rdc-blue/20 transition-all cursor-pointer group/item">
                         <div class="flex items-center justify-between mb-4 text-[10px] font-black uppercase tracking-widest">
                             <span class="text-rdc-blue">Strategic Insight</span>
                             <span class="text-emerald-500">98% SENSITIVITY</span>
                         </div>
                         <p class="text-[12px] text-slate-300 leading-relaxed italic">Anomalie détectée dans le flux de conversion Mobile. Optimisation CSS de la page Welcome suggérée pour Kinshasa.</p>
                         <button class="mt-6 text-[9px] font-black text-rdc-blue uppercase tracking-widest border-b-2 border-rdc-blue/10 hover:border-rdc-blue transition-all italic pb-1">Exécuter le Auto-Fix</button>
                     </div>

                     <div class="p-8 rounded-[2.5rem] bg-white/2 border border-white/5 hover:border-rdc-yellow/20 transition-all cursor-pointer group/item">
                         <div class="flex items-center justify-between mb-4 text-[10px] font-black uppercase tracking-widest">
                             <span class="text-rdc-yellow">Risk Assessment</span>
                             <span class="text-white">NOMINAL</span>
                         </div>
                         <p class="text-[12px] text-slate-300 leading-relaxed italic">Volume de transactions en hausse exponentielle. Le module de facturation sera mis à jour vers v4 à 02:00.</p>
                         <button class="mt-6 text-[9px] font-black text-rdc-yellow uppercase tracking-widest border-b-2 border-rdc-yellow/10 hover:border-rdc-yellow transition-all italic pb-1">Revoir Plan de Déploiement</button>
                     </div>
                </div>

                <div class="mt-12 flex items-center justify-center gap-6 opacity-30 group-hover:opacity-100 transition-opacity">
                     <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0s"></div>
                     <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0.2s"></div>
                     <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>

            <!-- Global Target Surveillance -->
            <div class="bg-white rounded-[3.5rem] p-12 shadow-2xl relative overflow-hidden group">
                 <div class="flex items-center justify-between mb-10">
                      <h4 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Entités <span class="text-rdc-blue">Prioritaires</span></h4>
                      <i class="fas fa-crosshairs text-slate-300 group-hover:text-rdc-red transition-colors"></i>
                 </div>
                 
                 <div class="space-y-8">
                      @foreach($recent_users->take(4) as $user)
                      <div class="flex items-center justify-between group/user">
                           <div class="flex items-center gap-5">
                                <div class="relative">
                                     <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 font-black uppercase text-lg border border-slate-100 group-hover/user:border-rdc-blue transition-colors">
                                          {{ substr($user->name, 0, 1) }}
                                     </div>
                                     <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-4 border-white rounded-full"></div>
                                </div>
                                <div>
                                     <p class="text-sm font-black text-slate-900 uppercase italic tracking-tighter group-hover/user:text-rdc-blue transition-colors">{{ $user->name }}</p>
                                     <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest">{{ $user->role }}</p>
                                </div>
                           </div>
                           <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-eye text-xs"></i>
                           </button>
                      </div>
                      @endforeach
                 </div>

                 <button class="w-full mt-10 py-5 border-2 border-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all italic">Lancer Scan de Population</button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes scan {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(35000%); }
    }
    .animate-scan { animation: scan 10s linear infinite; }
</style>
@endsection
