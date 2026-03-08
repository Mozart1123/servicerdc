@extends('layouts.admin')

@section('title', 'Logs Système')
@section('header_title', 'Maintenance & Audit')
@section('page_title', 'Journaux Techniques')
@section('page_subtitle', 'Consultez les empreintes numériques du serveur pour le débogage et la surveillance.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-slate-900 rounded-[3rem] shadow-2xl overflow-hidden border border-white/5">
        <!-- Terminal Header -->
        <div class="px-8 py-4 bg-white/5 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                </div>
                <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] ml-4 font-mono">system.log - service-rdc-main</span>
            </div>
            <div class="flex items-center gap-4">
                <button class="text-white/40 hover:text-white transition-colors text-xs font-black uppercase tracking-widest text-[9px]">Download Log</button>
                <button class="px-4 py-1.5 bg-red-500/10 text-red-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-red-500/20">Clear Console</button>
            </div>
        </div>

        <!-- Terminal Output -->
        <div class="p-8 font-mono space-y-2 text-xs overflow-y-auto max-h-[600px] custom-scrollbar">
            <div class="flex gap-4">
                <span class="text-white/20">05:12:01</span>
                <span class="text-emerald-500 font-bold">[INFO]</span>
                <span class="text-white/70">Database migration sync completed successfully.</span>
            </div>
            <div class="flex gap-4">
                <span class="text-white/20">05:12:02</span>
                <span class="text-emerald-500 font-bold">[INFO]</span>
                <span class="text-white/70">Cache cleared via admin console (User ID: 1).</span>
            </div>
            <div class="flex gap-4">
                <span class="text-white/20">05:12:05</span>
                <span class="text-amber-500 font-bold">[WARN]</span>
                <span class="text-white/70">Slow query detected on /api/v1/jobs/active (0.84s).</span>
            </div>
            <div class="flex gap-4">
                <span class="text-white/20">05:12:10</span>
                <span class="text-red-500 font-bold">[ERROR]</span>
                <span class="text-red-400">Failed to upload attachment for ticket #TKT-00421. [Storage:DiskFull]</span>
            </div>
            <div class="flex gap-4">
                <span class="text-white/20">05:12:15</span>
                <span class="text-blue-400 font-bold">[DEBUG]</span>
                <span class="text-white/50">Heartbeat: All nodes operational. (Node: KIN-MAIN-01)</span>
            </div>
        </div>
    </div>
    
    <!-- Logs Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-red-50 text-rdc-red rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-bug"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Erreurs Fatales</h4>
                    <p class="text-xs text-slate-400 font-bold mt-1">Dernières 24h</p>
                </div>
            </div>
            <p class="text-4xl font-black text-slate-900">02</p>
        </div>
        
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-blue-50 text-rdc-blue rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-microchip"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Utilisation Disque</h4>
                    <p class="text-xs text-slate-400 font-bold mt-1">S3 Cloud Storage</p>
                </div>
            </div>
            <p class="text-4xl font-black text-slate-900">14<span class="text-xs uppercase ml-1">%</span></p>
        </div>
    </div>
</div>
@endsection
