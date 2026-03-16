@extends('layouts.admin')

@section('title', 'Logs Système')
@section('header_title', 'Maintenance & Audit')
@section('page_title', 'Journaux Techniques')
@section('page_subtitle', 'Consultez les empreintes numériques du serveur pour le débogage et la surveillance.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    clearLogs() {
        if(!confirm('Voulez-vous vraiment effacer les journaux ?')) return;
        fetch('{{ route('admin.tools.logs.clear') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}">
    <div class="bg-slate-900 rounded-[2rem] sm:rounded-[3rem] shadow-2xl overflow-hidden border border-white/5">
        <!-- Terminal Header -->
        <div class="px-4 sm:px-8 py-3.5 sm:py-4 bg-white/5 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center justify-between w-full sm:w-auto gap-4">
                <div class="flex gap-1.5 shrink-0">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                </div>
                <span class="text-[8px] sm:text-[10px] font-black text-white/30 uppercase tracking-[0.2em] font-mono truncate">system.log</span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button @click="window.location.reload()" class="flex-1 sm:flex-none py-2 px-4 bg-white/5 hover:bg-white/10 text-white/40 hover:text-white transition-colors text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg">Refresh</button>
                <button @click="clearLogs()" class="flex-1 sm:flex-none px-4 py-2 bg-red-500/10 text-red-500 text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg border border-red-500/20 active:scale-95 transition-all">Clear</button>
            </div>
        </div>

        <!-- Terminal Output -->
        <div class="p-4 sm:p-8 font-mono space-y-1 text-[9px] sm:text-[11px] text-white/70 overflow-x-auto no-scrollbar max-h-[400px] sm:max-h-[600px] custom-scrollbar">
            @if($logs)
                <pre class="whitespace-pre-wrap leading-relaxed">{{ $logs }}</pre>
            @else
                <div class="flex items-center justify-center p-20 text-white/20 uppercase font-black text-[10px] tracking-widest">
                    Aucune entrée dans le journal
                </div>
            @endif
        </div>
    </div>
    
    <!-- Logs Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
        <div class="bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-red-50 text-red-500 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-bug"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Erreurs</h4>
                    <p class="text-[8px] sm:text-xs text-slate-400 font-bold mt-0.5">Dernières 24h</p>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-slate-900">02</p>
        </div>
        
        <div class="bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-50 text-rdc-blue rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-microchip"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Disque</h4>
                    <p class="text-[8px] sm:text-xs text-slate-400 font-bold mt-0.5">Cloud Storage</p>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-slate-900">14<span class="text-[10px] sm:text-xs uppercase ml-0.5">%</span></p>
        </div>
    </div>
</div>
@endsection
