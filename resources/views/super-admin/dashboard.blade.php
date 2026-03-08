@extends('layouts.super-admin')

@section('header_title', 'Centre De Commandement Suprême | OMNIPOTENCE')

@section('content')
<div class="space-y-10 pb-20" x-data="{ 
    destructionMode: false, 
    stealthMode: false,
    divinityLevel: 100,
    init() {
        setInterval(() => {
            this.divinityLevel = 98 + Math.floor(Math.random() * 3);
        }, 5000);
    }
}">
    <!-- Global Status Banner (Holographic) -->
    <div class="relative group">
        <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 via-rdc-blue to-purple-600 rounded-[3.5rem] blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
        <div class="relative bg-slate-900 rounded-[3rem] p-10 border border-white/10 overflow-hidden shadow-2xl">
            <!-- Background HUD Elements -->
            <div class="absolute inset-0 opacity-10 pointer-events-none overflow-hidden">
                <div class="absolute top-0 right-0 w-[500px] h-[500px] border border-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute top-0 right-0 w-[700px] h-[700px] border border-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
                <div class="flex items-center gap-8">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-[2.5rem] bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-4xl text-black shadow-2xl divine-glow">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-2xl border-4 border-slate-900 flex items-center justify-center text-white text-xs animate-pulse">
                            <i class="fas fa-signal"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-4xl font-heading font-black text-white tracking-tighter uppercase mb-2">Architecte Suprême</h2>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-mono text-amber-500 border border-amber-500/30 px-3 py-1 rounded-full uppercase tracking-widest">Autorité: ABSOLUE</span>
                            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">ID: GOD-MODE-001</span>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></div>
                                <span class="text-[10px] font-mono text-emerald-400 uppercase">Synchronisé</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-6">
                    <!-- Gauge Divinity -->
                    <div class="text-center group/gauge">
                        <div class="relative w-24 h-24 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="6" fill="transparent" class="text-white/5" />
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="6" fill="transparent" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (divinityLevel / 100) * 251.2" class="text-amber-500 transition-all duration-1000 ease-out" />
                            </svg>
                            <span class="absolute text-xl font-black text-white" x-text="divinityLevel + '%'"></span>
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 tracking-widest">Pouvoir Divin</p>
                    </div>

                    <!-- HUD Mode Toggles -->
                    <div class="flex gap-4">
                        <button @click="stealthMode = !stealthMode" :class="stealthMode ? 'bg-rdc-blue border-rdc-blue shadow-[0_0_20px_rgba(0,127,255,0.4)]' : 'bg-white/5 border-white/10 text-slate-400'" class="p-4 rounded-3xl border transition-all duration-500 group">
                            <i class="fas fa-user-secret text-2xl" :class="stealthMode ? 'text-white' : 'group-hover:text-white'"></i>
                            <span class="block text-[8px] font-bold uppercase mt-2 tracking-tighter" :class="stealthMode ? 'text-white' : ''">Mode Furtif</span>
                        </button>
                        <button @click="destructionMode = !destructionMode" :class="destructionMode ? 'bg-red-500 border-red-500 shadow-[0_0_20px_rgba(239,68,68,0.4)]' : 'bg-white/5 border-white/10 text-slate-400'" class="p-4 rounded-3xl border transition-all duration-500 group">
                            <i class="fas fa-skull text-2xl" :class="destructionMode ? 'text-white' : 'group-hover:text-red-500'"></i>
                            <span class="block text-[8px] font-bold uppercase mt-2 tracking-tighter" :class="destructionMode ? 'text-white' : ''">Apocalypse</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Metric: Population -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 relative group overflow-hidden hover:border-amber-500/30 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fas fa-users-crown text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-500">+1.2k today</span>
            </div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Sujets de l'Empire</p>
            <h3 class="text-4xl font-heading font-black text-slate-900 mt-2">{{ number_format($stats['total_users']) }}</h3>
            <div class="mt-6 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 w-[75%] rounded-full"></div>
            </div>
        </div>

        <!-- Metric: Finances -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 relative group overflow-hidden hover:border-emerald-500/30 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-vault text-xl"></i>
                </div>
                <div class="w-4 h-4 bg-emerald-100 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-emerald-600 rounded-full animate-ping"></div>
                </div>
            </div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Trésorerie Universelle</p>
            <h3 class="text-4xl font-heading font-black text-slate-900 mt-2">{{ number_format($stats['monthly_revenue'], 2) }}$</h3>
            <p class="text-[10px] text-emerald-600 font-bold mt-4 uppercase">Génération Ex-Nihilo: OPTIMALE</p>
        </div>

        <!-- Metric: System Power -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 relative group overflow-hidden hover:border-rdc-blue/30 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-rdc-blue">
                    <i class="fas fa-microchip text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 font-mono">{{ $stats['system_load'] }}%</span>
            </div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Complexité de l'Univers</p>
            <h3 class="text-4xl font-heading font-black text-slate-900 mt-2">{{ $stats['active_instances'] }} NODES</h3>
            <p class="text-[10px] text-rdc-blue font-bold mt-4 uppercase">Latence Inter-Stellaire: 8ms</p>
        </div>

        <!-- Metric: Integrity -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 relative group overflow-hidden hover:border-red-500/30 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-all"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600">
                    <i class="fas fa-shield-virus text-xl"></i>
                </div>
                <div class="px-2 py-1 bg-red-500 text-white text-[8px] font-black rounded-lg uppercase tracking-widest">LOCKDOWN</div>
            </div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Intégrité Temporelle</p>
            <h3 class="text-4xl font-heading font-black text-slate-900 mt-2">100%</h3>
            <p class="text-[10px] text-slate-400 font-medium mt-4 uppercase">Paradoxes Détectés: 0</p>
        </div>
    </div>

    <!-- Main Command Core -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        <!-- Live Chronicle (Central Terminal) -->
        <div class="xl:col-span-2 space-y-8">
            <div class="bg-[#0b0e14] rounded-[3.5rem] border border-white/10 shadow-2xl overflow-hidden flex flex-col h-[650px] relative">
                <!-- Scanning CRT Line Animation -->
                <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden opacity-10">
                    <div class="w-full h-1/4 bg-gradient-to-b from-transparent via-rdc-blue to-transparent animate-scan"></div>
                </div>
                
                <div class="px-10 py-6 bg-white/5 border-b border-white/5 flex items-center justify-between relative z-10 backdrop-blur-md">
                    <div class="flex items-center gap-6">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <h4 class="text-xs font-mono font-bold text-slate-300 uppercase tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-satellite text-amber-500 animate-spin-slow"></i> 
                            Flux de Conscience Universelle
                        </h4>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Protocol</p>
                            <p class="text-[10px] font-mono text-emerald-400">ENCRYPTED_DIVINE_A_2</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 p-10 font-mono text-[12px] overflow-y-auto custom-terminal-scrollbar space-y-4 relative z-10">
                    <div class="flex gap-6 group">
                        <span class="text-slate-600 font-bold shrink-0">[10:04:12]</span>
                        <span class="text-white"><span class="text-amber-500">BOOT:</span> Séquence d'initialisation du Nexus de Commandement terminée.</span>
                    </div>
                    
                    <div class="flex gap-6 group">
                        <span class="text-slate-600 font-bold shrink-0">[10:05:01]</span>
                        <span class="text-white"><span class="text-rdc-blue">SECURE:</span> Le pare-feu divin a intercepté 4,281 tentatives d'accès non-autorisées en provenance du monde extérieur.</span>
                    </div>

                    <div class="flex gap-6 group">
                        <span class="text-slate-600 font-bold shrink-0">[10:05:22]</span>
                        <span class="text-white"><span class="text-emerald-500">WEALTH:</span> Génération automatique de dividendes pour l'utilisateur #1. +150,00$.</span>
                    </div>

                    <div class="flex gap-6 group">
                        <span class="text-slate-600 font-bold shrink-0">[10:06:15]</span>
                        <span class="text-white"><span class="text-purple-500">TIME:</span> Point de sauvegarde universel créé. Restauration possible en cas de catastrophe.</span>
                    </div>

                    <div class="flex gap-6 group" x-show="destructionMode">
                        <span class="text-red-500 font-black shrink-0 animate-pulse">[!] ALERTE [!]</span>
                        <span class="text-red-400 font-black">PROTOCOLE D'APOCALYPSE ARMÉ. EN ATTENTE DE CONFIRMATION VOCALE.</span>
                    </div>

                    <div class="flex gap-6 group" x-show="stealthMode">
                        <span class="text-rdc-blue font-black shrink-0">[STEALTH]</span>
                        <span class="text-slate-400 italic">L'Architecte a disparu de tous les flux de monitoring. Fantôme dans la machine.</span>
                    </div>

                    <div class="pt-6">
                        <div class="flex items-center gap-3">
                            <span class="text-emerald-500 font-black">root@MASTER:~$</span>
                            <span class="text-white animate-pulse">_</span>
                        </div>
                    </div>
                </div>

                <!-- Terminal Bottom Info -->
                <div class="px-10 py-4 bg-white/5 border-t border-white/5 flex items-center justify-between text-[9px] font-mono text-slate-500 uppercase tracking-widest relative z-10">
                    <span>Uplink: Satellite-6_KIN</span>
                    <span>Buffer: 4.2 Pb / 100 Pb</span>
                    <span class="text-amber-500 font-bold">Safe Mode: OFF</span>
                </div>
            </div>

            <!-- Quick Access Actions (God Tools) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button class="group p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:border-amber-500/20 hover:shadow-xl transition-all h-full text-left">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-all">
                        <i class="fas fa-bolt-lightning text-xl"></i>
                    </div>
                    <h5 class="font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Boost Universel</h5>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">Double le temps de calcul pour 1h</p>
                </button>

                <button class="group p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:border-rdc-blue/20 hover:shadow-xl transition-all h-full text-left">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-rdc-blue flex items-center justify-center mb-4 group-hover:bg-rdc-blue group-hover:text-white transition-all">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <h5 class="font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">Audit de Conscience</h5>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">Scanner tous les noeuds à 100%</p>
                </button>

                <button class="group p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:border-emerald-500/20 hover:shadow-xl transition-all h-full text-left">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        <i class="fas fa-wand-magic-sparkles text-xl"></i>
                    </div>
                    <h5 class="font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Restauration Divine</h5>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">Corriger toutes les erreurs BDD</p>
                </button>
            </div>
        </div>

        <!-- Sidebar Components (Right Control Panel) -->
        <div class="space-y-10">
            <!-- AI Strategist Card -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[3.5rem] p-10 shadow-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-amber-500/5 rounded-full blur-[100px] group-hover:bg-amber-500/10 transition-all duration-1000"></div>
                
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-16 h-16 rounded-3xl bg-amber-500 text-slate-900 flex items-center justify-center text-3xl shadow-[0_0_30px_rgba(245,158,11,0.3)] group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-brain-circuit"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-heading font-black text-white leading-tight uppercase">IA Omnisciente</h4>
                        <p class="text-[9px] font-mono text-amber-500 uppercase tracking-widest font-bold">Oracle v4.0.2</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 group-hover:border-amber-500/20 transition-all cursor-pointer">
                        <div class="flex items-center justify-between mb-3 text-[10px] font-bold uppercase tracking-widest">
                            <span class="text-amber-500">Analyse de Menace</span>
                            <span class="text-emerald-500">LOW</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">Le traffic suspect sur l'instance <strong class="text-white">KIN-01</strong> a été mitigé. Augmentation de la RAM recommandée.</p>
                        <button class="mt-4 text-[9px] font-black text-amber-500 uppercase hover:text-white transition-colors underline underline-offset-4 decoration-amber-500/40">Approuver l'augmentation</button>
                    </div>

                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 group-hover:border-rdc-blue/20 transition-all cursor-pointer">
                        <div class="flex items-center justify-between mb-3 text-[10px] font-bold uppercase tracking-widest">
                            <span class="text-rdc-blue">Opportunité</span>
                            <span class="text-white">FINANCE</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">Forte demande de services de nettoyage. On pourrait augmenter les frais de commission de <strong class="text-white">1.5%</strong>.</p>
                        <button class="mt-4 text-[9px] font-black text-rdc-blue uppercase hover:text-white transition-colors underline underline-offset-4 decoration-rdc-blue/40">Lancer l'ajustement</button>
                    </div>
                </div>

                <button class="w-full mt-10 py-5 bg-amber-500 text-slate-900 font-black rounded-2xl text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 transform hover:-translate-y-1 transition-all">
                    Synchroniser l'Oracle
                </button>
            </div>

            <!-- Subject Surveillance (Mini List) -->
            <div class="bg-white rounded-[3.5rem] p-10 shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Sujets Sous Veille</h4>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                
                <div class="space-y-6">
                    @foreach($recent_users->take(4) as $user)
                    <div class="flex items-center justify-between group cursor-pointer p-2 rounded-2xl hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=F1F5F9&color=64748B" 
                                     class="w-12 h-12 rounded-2xl shadow-sm border border-slate-100" alt="">
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $user->name }}</p>
                                <p class="text-[9px] text-slate-400 font-mono uppercase">{{ $user->role }}</p>
                            </div>
                        </div>
                        <button class="w-8 h-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-expand-alt text-[10px]"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('super-admin.divine.tracking') }}" class="block mt-10 text-center py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border border-slate-100 rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all">
                    Voir la Carte Globale
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes scan {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(400%); }
    }
    .animate-scan { animation: scan 8s linear infinite; }
    
    .custom-terminal-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-terminal-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-terminal-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-terminal-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    
    .divine-glow {
        box-shadow: 0 0 30px rgba(245, 158, 11, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.2);
    }
    
    .animate-spin-slow { animation: spin 10s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
