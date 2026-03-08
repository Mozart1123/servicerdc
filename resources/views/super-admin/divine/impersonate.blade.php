@extends('layouts.super-admin')

@section('header_title', 'Infiltration de Session Master | OMNIPRÉSENCE')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Divine Infiltration Access (HUD Style) -->
    <div class="bg-slate-900 rounded-[3.5rem] p-12 text-white shadow-2xl relative overflow-hidden group">
        <!-- Target Grid Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <!-- Rotating HUD Element -->
        <div class="absolute -right-20 -top-20 w-80 h-80 border-4 border-amber-500/10 rounded-full animate-spin-slow"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="flex-1 space-y-8">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-[2.5rem] bg-amber-500 text-slate-900 flex items-center justify-center text-4xl shadow-[0_0_50px_rgba(245,158,11,0.3)] divine-glow">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-heading font-black tracking-tighter uppercase mb-2">VISION DIVINE : INFILTRATION</h2>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-mono text-emerald-400 border border-emerald-400/20 px-3 py-1 rounded-full uppercase tracking-widest bg-emerald-400/5">Stealth: ACTIF</span>
                            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">Protocol: SHADOW-WALK-V2</span>
                        </div>
                    </div>
                </div>

                <div class="max-w-xl">
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Prenez le contrôle total de n'importe quel avatar digital. Votre présence sera masquée par le "Ghost Protocol", empêchant toute détection par les systèmes de surveillance utilisateur.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative group">
                        <div class="absolute inset-0 bg-amber-500/5 rounded-2xl blur group-focus-within:bg-amber-500/10 transition-all"></div>
                        <input type="text" placeholder="ID CIBLE OU EMAIL UNIVERSEL..." class="relative w-full bg-white/5 border border-white/10 rounded-2xl px-8 py-5 text-white font-mono text-sm tracking-widest outline-none focus:border-amber-500/40 transition-all">
                        <i class="fas fa-crosshairs absolute right-8 top-1/2 -translate-y-1/2 text-amber-500/50 group-focus-within:text-amber-500 animate-pulse"></i>
                    </div>
                    <button class="px-12 py-5 bg-amber-500 text-slate-900 font-black rounded-2xl text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:shadow-amber-500/40 transform hover:-translate-y-1 transition-all">
                        Lancer l'Infiltration
                    </button>
                </div>
            </div>

            <div class="hidden lg:block w-72 h-72 border-2 border-white/5 rounded-full relative flex items-center justify-center">
                <div class="absolute inset-4 border border-white/10 rounded-full animate-ping opacity-20"></div>
                <div class="absolute inset-10 border border-amber-500/20 rounded-full"></div>
                <i class="fas fa-satellite-dish text-5xl text-amber-500 opacity-40 animate-pulse"></i>
            </div>
        </div>
    </div>

    <!-- Active Targets & Historical Shadows -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Live Target Nexus -->
        <div class="lg:col-span-2 bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xl shadow-lg">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 font-heading tracking-tight uppercase">Nexus des Sujets Actifs</h3>
                        <p class="text-slate-500 text-[10px] uppercase tracking-widest font-mono">Précision Satellite: 0.8m</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-3">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        1,482 Cibles Potentielles
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-10 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Avatar Cible</th>
                            <th class="px-10 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Coordonnées (IP)</th>
                            <th class="px-10 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Activité Détectée</th>
                            <th class="px-10 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Intervenir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                            $sessions = [
                                ['name' => 'Michel Kabongo', 'email' => 'm.kabongo@corp.cd', 'ip' => '197.242.1.2', 'loc' => 'Kinshasa, Gombe', 'action' => 'Édition Service #382'],
                                ['name' => 'Sarah Lopez', 'email' => 'sarah.l@gmail.com', 'ip' => '105.235.12.148', 'loc' => 'Lubumbashi', 'action' => 'Paiement en cours'],
                                ['name' => 'Alain Mwemba', 'email' => 'a.mwemba@artisan.cd', 'ip' => '41.243.2.12', 'loc' => 'Goma', 'action' => 'Navigation Profile'],
                                ['name' => 'Hélène Sufa', 'email' => 'h.sufa@provider.cd', 'ip' => '197.242.145.22', 'loc' => 'Kinshasa, Limete', 'action' => 'Recherche Travail'],
                            ];
                        @endphp
                        @foreach($sessions as $session)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($session['name']) }}&background=F1F5F9&color=64748B" class="w-12 h-12 rounded-2xl border-2 border-white shadow-sm transition-all group-hover:scale-110 group-hover:shadow-xl" alt="">
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $session['name'] }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono tracking-tighter">{{ $session['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-xs font-bold text-slate-700 font-mono tracking-tight">{{ $session['ip'] }}</span>
                                <p class="text-[9px] text-slate-400 uppercase mt-1">{{ $session['loc'] }}</p>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-rdc-blue animate-pulse-soft"></div>
                                    <span class="text-[11px] font-bold text-slate-600">{{ $session['action'] }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <button class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all shadow-lg shadow-slate-200">
                                    Infiltration
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Oracle Side Panel -->
        <div class="space-y-10">
            <!-- Security Status -->
            <div class="bg-gradient-to-br from-slate-900 to-black rounded-[3.5rem] p-10 shadow-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl"></div>
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="fas fa-shield-halved text-amber-500"></i>
                    PROTOCOLES D'OMBRE
                </h4>
                <div class="space-y-6">
                    <div class="flex items-center justify-between group/row cursor-pointer">
                        <span class="text-[10px] font-bold text-slate-400 uppercase group-hover/row:text-white transition-colors">Nettoyer les Logs</span>
                        <div class="w-10 h-6 bg-emerald-500 rounded-full relative p-1 shadow-inner shadow-black/20">
                            <div class="w-4 h-4 bg-white rounded-full ml-auto shadow-sm"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between group/row cursor-pointer">
                        <span class="text-[10px] font-bold text-slate-400 uppercase group-hover/row:text-white transition-colors">VPN Inter-Dimensionnel</span>
                        <div class="w-10 h-6 bg-emerald-500 rounded-full relative p-1 shadow-inner shadow-black/20">
                            <div class="w-4 h-4 bg-white rounded-full ml-auto shadow-sm"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between group/row cursor-pointer">
                        <span class="text-[10px] font-bold text-slate-400 uppercase group-hover/row:text-white transition-colors">Détruire les Cookies Cibles</span>
                        <div class="w-10 h-6 bg-white/10 rounded-full relative p-1">
                            <div class="w-4 h-4 bg-white/20 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-10 pt-10 border-t border-white/5">
                    <p class="text-[9px] text-slate-500 italic leading-relaxed">
                        "En devenant l'autre, vous apprenez la vérité que les données ne peuvent pas exprimer."
                    </p>
                </div>
            </div>

            <!-- Historical Vault -->
            <div class="bg-white rounded-[3.5rem] p-10 shadow-sm border border-slate-100">
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">ARCHIVES DES OMBRES</h4>
                <div class="space-y-6">
                    @for($i=0; $i<3; $i++)
                    <div class="flex items-center gap-4 group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <i class="fas fa-history text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-slate-900">Session #{{ rand(1000,9999) }}</p>
                            <p class="text-[9px] text-slate-400 uppercase tracking-widest mt-0.5">client_valide@hq.cd</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-spin-slow { animation: spin 20s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
