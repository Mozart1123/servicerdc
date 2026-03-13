@extends('layouts.admin')

@section('title', 'Alertes Système')
@section('header_title', 'Sécurité & Intégrité')
@section('page_title', 'Gestion des Incidents')
@section('page_subtitle', 'Identifiez et résolvez les anomalies détectées par ServiceRDC Active Guard.')

@section('content')
<div class="space-y-8 pb-20">
    
    <!-- Alert HUD -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-red-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-red-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-red/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-rdc-red uppercase tracking-widest">Critique</span>
                <i class="fas fa-radiation text-rdc-red animate-pulse text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">03</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Incidents</p>
        </div>
        
        <div class="bg-amber-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-amber-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-amber-600 uppercase tracking-widest">Avertissements</span>
                <i class="fas fa-triangle-exclamation text-amber-500 text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">12</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Anomalies</p>
        </div>

        <div class="bg-emerald-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-emerald-100 shadow-sm relative overflow-hidden group sm:col-span-2 lg:col-span-1">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-emerald-600 uppercase tracking-widest">Résolus</span>
                <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">142</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">24 Heures</p>
        </div>
    </div>

    <!-- Alert List Container -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <!-- Table Header -->
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 space-y-6 bg-slate-50/30">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Journal Alertes</h3>
                <button class="w-full sm:w-auto px-6 py-3 bg-red-500/10 text-red-500 text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-red hover:text-white active:scale-95 transition-all">Marquer Lu</button>
            </div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar -mx-1 px-1">
                <button class="px-4 py-2 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black uppercase rounded-lg tracking-widest shrink-0">Tous</button>
                <button class="px-4 py-2 bg-white text-slate-400 text-[8px] sm:text-[9px] font-black uppercase rounded-lg border border-slate-100 shrink-0">Sécurité</button>
                <button class="px-4 py-2 bg-white text-slate-400 text-[8px] sm:text-[9px] font-black uppercase rounded-lg border border-slate-100 shrink-0">Performance</button>
            </div>
        </div>

        <!-- Alerts Content -->
        <div class="divide-y divide-slate-50">
            <!-- Alert Item: Critical -->
            <div class="px-6 sm:px-10 py-6 sm:py-8 flex flex-col sm:flex-row items-start gap-4 sm:gap-8 group hover:bg-red-50/30 transition-colors">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 text-rdc-red rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-virus text-xl sm:text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-rdc-red text-white text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-full">Critique</span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono truncate">SEC-042</span>
                        </div>
                        <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest italic font-mono">-14m</span>
                    </div>
                    <h4 class="text-base sm:text-lg font-black text-slate-900 mb-2 uppercase tracking-tight">Injection SQL Bloquée</h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">Payload malveillante sur <code class="bg-slate-100 px-1.5 py-0.5 rounded text-rdc-blue font-bold">/api/v1/search</code>. IP <span class="bg-red-50 text-red-700 px-1 rounded font-bold">197.242.12.84</span> bannie.</p>
                    
                    <div class="flex flex-wrap gap-2">
                        <button class="flex-1 sm:flex-none px-4 py-3 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all">Ban Définitif</button>
                        <button class="flex-1 sm:flex-none px-4 py-3 bg-white border border-slate-200 text-slate-500 text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all">Log</button>
                        <button class="w-full sm:w-auto px-4 py-3 bg-white border border-slate-200 text-emerald-500 text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all">Résolu</button>
                    </div>
                </div>
            </div>

            <!-- Alert Item: Warning -->
            <div class="px-6 sm:px-10 py-6 sm:py-8 flex flex-col sm:flex-row items-start gap-4 sm:gap-8 group hover:bg-amber-50/30 transition-colors">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-100 text-amber-500 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-microchip text-xl sm:text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-full">Avertissement</span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono truncate">PERF-009</span>
                        </div>
                        <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest italic font-mono">-1h</span>
                    </div>
                    <h4 class="text-base sm:text-lg font-black text-slate-900 mb-2 uppercase tracking-tight">Pic CPU Inhabituel</h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6"><span class="font-bold text-slate-700">SRV-03</span> à 85% d'utilisation. Autoscale déclenché.</p>
                    
                    <div class="flex flex-wrap gap-2">
                        <button class="flex-1 sm:flex-none px-4 py-3 bg-amber-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-amber-500/20 active:scale-95 transition-all">Optimiser</button>
                        <button class="flex-1 sm:flex-none px-4 py-3 bg-white border border-slate-200 text-slate-500 text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all">Stats</button>
                    </div>
                </div>
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
