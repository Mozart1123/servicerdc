@extends('layouts.admin')

@section('title', 'Alertes Système')
@section('header_title', 'Sécurité & Intégrité')
@section('page_title', 'Gestion des Incidents')
@section('page_subtitle', 'Identifiez et résolvez les anomalies détectées par ServiceRDC Active Guard.')

@section('content')
<div class="space-y-8 pb-20">
    
    <!-- Alert HUD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-red-50 p-8 rounded-[2.5rem] border border-red-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-red/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[10px] font-black text-rdc-red uppercase tracking-widest">Critique</span>
                <i class="fas fa-radiation text-rdc-red animate-pulse"></i>
            </div>
            <h3 class="text-4xl font-heading font-black text-slate-900">03</h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Incidents non résolus</p>
        </div>
        
        <div class="bg-amber-50 p-8 rounded-[2.5rem] border border-amber-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Avertissements</span>
                <i class="fas fa-triangle-exclamation text-amber-500"></i>
            </div>
            <h3 class="text-4xl font-heading font-black text-slate-900">12</h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Anomalies mineures</p>
        </div>

        <div class="bg-emerald-50 p-8 rounded-[2.5rem] border border-emerald-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Résolus</span>
                <i class="fas fa-circle-check text-emerald-500"></i>
            </div>
            <h3 class="text-4xl font-heading font-black text-slate-900">142</h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Dernières 24 heures</p>
        </div>
    </div>

    <!-- Alert List Container -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
        <!-- Table Header -->
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <div class="flex items-center gap-6">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Journal des Alertes</h3>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-slate-900 text-white text-[9px] font-black uppercase rounded-xl tracking-widest">Tous</button>
                    <button class="px-4 py-2 bg-white text-slate-400 text-[9px] font-black uppercase rounded-xl border border-slate-100 hover:text-rdc-blue transition-all tracking-widest">Sécurité</button>
                    <button class="px-4 py-2 bg-white text-slate-400 text-[9px] font-black uppercase rounded-xl border border-slate-100 hover:text-rdc-blue transition-all tracking-widest">Performance</button>
                </div>
            </div>
            <button class="px-6 py-2.5 bg-rdc-red/10 text-rdc-red text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-red hover:text-white transition-all">Tout Marquer comme Lu</button>
        </div>

        <!-- Alerts Content -->
        <div class="divide-y divide-slate-50">
            <!-- Alert Item: Critical -->
            <div class="px-10 py-8 flex items-start gap-8 group hover:bg-red-50/30 transition-colors">
                <div class="w-14 h-14 bg-red-100 text-rdc-red rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-virus text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-rdc-red text-white text-[8px] font-black uppercase tracking-widest rounded-full">Critique</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">ID: SEC-2024-042</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic group-hover:text-rdc-red transition-colors">Il y a 14 minutes</span>
                    </div>
                    <h4 class="text-lg font-black text-slate-900 mb-2">Tentative d'Injection SQL Bloquée</h4>
                    <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Le firewall a détecté une payload malveillante sur la route <code class="bg-slate-100 px-2 py-0.5 rounded text-rdc-blue font-bold px-1">/api/v1/services/search</code>. L'adresse IP <span class="text-slate-900 font-bold underline decoration-rdc-red/30">197.242.12.84</span> a été bannie pour 24h.</p>
                    
                    <div class="mt-6 flex items-center gap-3">
                        <button class="px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-blue transition-all">Ban Définitif</button>
                        <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50">Détails Log</button>
                        <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:text-emerald-500">Marquer Résolu</button>
                    </div>
                </div>
            </div>

            <!-- Alert Item: Warning -->
            <div class="px-10 py-8 flex items-start gap-8 group hover:bg-amber-50/30 transition-colors">
                <div class="w-14 h-14 bg-amber-100 text-amber-500 rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-microchip text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-amber-500 text-white text-[8px] font-black uppercase tracking-widest rounded-full">Avertissement</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">ID: PERF-009</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Il y a 1 heure</span>
                    </div>
                    <h4 class="text-lg font-black text-slate-900 mb-2">Pic de Charge CPU Inhabituel</h4>
                    <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">L'instance <span class="font-bold text-slate-700">SRV-KIN-03</span> a atteint 85% d'utilisation pendant plus de 5 minutes. Autoscale en cours de déclenchement.</p>
                    
                    <div class="mt-6 flex items-center gap-3">
                        <button class="px-5 py-2.5 bg-amber-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-amber-500/20">Optimiser Cache</button>
                        <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50">Stats Temps Réel</button>
                    </div>
                </div>
            </div>

            <!-- Alert Item: Info -->
            <div class="px-10 py-8 flex items-start gap-8 group hover:bg-blue-50/30 transition-colors">
                <div class="w-14 h-14 bg-blue-100 text-rdc-blue rounded-2xl flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fas fa-arrows-rotate text-2xl group-hover:rotate-180 transition-transform duration-700"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-rdc-blue text-white text-[8px] font-black uppercase tracking-widest rounded-full">Information</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">ID: SYS-045</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Il y a 3 heures</span>
                    </div>
                    <h4 class="text-lg font-black text-slate-900 mb-2">Sauvegarde Hebdomadaire Terminée</h4>
                    <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">L'instantané complet du système (4.2 GB) a été uploadé avec succès sur le bucket S3 redondant.</p>
                    
                    <div class="mt-6 flex items-center gap-3">
                        <button class="px-5 py-2.5 bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-xl cursor-default">Archive Sécurisée</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State (Hidden) -->
        <div class="hidden py-32 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                <i class="fas fa-check-double"></i>
            </div>
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Ciel Dégagé !</h4>
            <p class="text-slate-400 max-w-xs mx-auto mt-2 font-medium">Aucun incident de sécurité ou de performance n'a été signalé pour le moment.</p>
        </div>
    </div>
</div>
@endsection
