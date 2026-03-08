@extends('layouts.super-admin')

@section('header_title', 'Carte Omnisciente Temps Réel')

@section('content')
<div class="space-y-8">
    <!-- Divine Map Header -->
    <div class="bg-slate-900 rounded-[3rem] p-10 border border-white/5 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 0); background-size: 40px 40px;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-[2.5rem] bg-rdc-blue/10 flex items-center justify-center text-4xl text-rdc-blue border border-rdc-blue/20">
                    <i class="fas fa-satellite-dish"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-heading font-extrabold text-white tracking-tight">SURVEILLANCE GLOBALE</h2>
                    <p class="text-[10px] font-mono text-slate-500 uppercase tracking-widest mt-1">Uplink: ACTIF | Précision: < 1m | Latence: 8ms</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-4 bg-white/5 border border-white/10 rounded-2xl">
                    <span class="text-[9px] font-extrabold text-slate-500 uppercase block mb-1">Cibles Actives</span>
                    <span class="text-2xl font-heading font-extrabold text-white">1,482</span>
                </div>
                <div class="px-6 py-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                    <span class="text-[9px] font-extrabold text-emerald-500 uppercase block mb-1">Status Réseau</span>
                    <span class="text-lg font-heading font-extrabold text-emerald-400">OPTIMAL</span>
                </div>
            </div>
        </div>
    </div>

    <!-- The Map (Simulation) -->
    <div class="bg-white rounded-[3.5rem] p-4 shadow-sm border border-slate-100 h-[700px] relative overflow-hidden group">
        <div class="absolute inset-4 rounded-[3rem] bg-slate-900 overflow-hidden">
            <!-- Simulated Satellite Map Background -->
            <div class="absolute inset-0 opacity-40 bg-[url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop')] bg-cover bg-center grayscale scale-110"></div>
            
            <!-- Grid Lines -->
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 50px 50px;"></div>
            
            <!-- Scanning Line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-rdc-blue/40 blur-sm animate-bounce-slow" style="top: 20%"></div>

            <!-- Target Pins (Kinshasa Example) -->
            <div class="absolute top-1/2 left-1/3 group/pin cursor-pointer">
                <div class="w-4 h-4 bg-rdc-blue rounded-full border-4 border-white shadow-xl animate-ping opacity-75"></div>
                <div class="w-4 h-4 bg-rdc-blue rounded-full border-4 border-white shadow-xl absolute inset-0"></div>
                
                <!-- Hover Info Card -->
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-48 p-4 bg-slate-900/90 backdrop-blur-md rounded-2xl border border-white/10 opacity-0 group-hover/pin:opacity-100 transition-all scale-90 group-hover/pin:scale-100">
                    <p class="text-[10px] font-extrabold text-amber-500 uppercase">Utilisateur Détecté</p>
                    <p class="text-xs font-bold text-white mt-1">Jonathan Musasa</p>
                    <div class="mt-2 h-px bg-white/10"></div>
                    <p class="text-[9px] text-slate-400 mt-2 font-mono">ID: USER-8829<br>POS: -4.3224, 15.3070</p>
                </div>
            </div>

            <div class="absolute top-1/3 left-1/2 group/pin cursor-pointer">
                <div class="w-4 h-4 bg-red-500 rounded-full border-4 border-white shadow-xl animate-ping opacity-75"></div>
                <div class="w-4 h-4 bg-red-500 rounded-full border-4 border-white shadow-xl absolute inset-0"></div>
            </div>
            
            <div class="absolute bottom-1/4 right-1/4 group/pin cursor-pointer">
                <div class="w-4 h-4 bg-emerald-500 rounded-full border-4 border-white shadow-xl animate-ping opacity-75"></div>
                <div class="w-4 h-4 bg-emerald-500 rounded-full border-4 border-white shadow-xl absolute inset-0"></div>
            </div>

            <!-- Map Controls (HUD) -->
            <div class="absolute inset-8 pointer-events-none flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div class="p-6 bg-slate-900/60 backdrop-blur-sm rounded-3xl border border-white/5 pointer-events-auto">
                        <p class="text-[9px] font-mono text-slate-400 uppercase">Système de visée</p>
                        <div class="mt-2 flex items-center gap-4">
                            <i class="fas fa-expand text-white text-xl"></i>
                            <div class="h-8 w-px bg-white/10"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-white">AUTO-LOCK : OK</span>
                                <span class="text-[8px] text-emerald-500">ZOOM: 25,000%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 pointer-events-auto">
                        <button class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center text-white border border-white/10 hover:bg-white/20 transition-all">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center text-white border border-white/10 hover:bg-white/20 transition-all">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-end justify-between">
                    <div class="p-6 bg-slate-900/60 backdrop-blur-sm rounded-3xl border border-white/5 pointer-events-auto w-80">
                        <h4 class="text-xs font-extrabold text-white uppercase mb-4 tracking-widest">Flux d'activité Master</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/20"></span>
                                <span class="text-[10px] text-slate-300 font-mono">User_382 a postulé à Kin-382</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-rdc-blue shadow-lg shadow-blue-500/20"></span>
                                <span class="text-[10px] text-slate-300 font-mono">Nouveau Service: Divine_Cleaner</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="text-[10px] text-red-500 font-mono uppercase font-bold">Alerte: Multi-Session USER-01</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-4 bg-amber-500 rounded-3xl divine-glow shadow-2xl pointer-events-auto cursor-pointer hover:scale-105 transition-transform">
                        <span class="text-sm font-black text-slate-900 uppercase tracking-tighter">CENTRER SUR HQ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(600px); }
    }
    .animate-bounce-slow { animation: bounce-slow 10s infinite linear; }
    
    @keyframes ping-slow {
        0% { transform: scale(1); opacity: 0.1; }
        100% { transform: scale(3); opacity: 0; }
    }
    .animate-ping-slow { animation: ping-slow 4s infinite cubic-bezier(0, 0, 0.2, 1); }
</style>
@endsection
