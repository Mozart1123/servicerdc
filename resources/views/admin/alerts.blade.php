@extends('layouts.admin')

@section('title', 'Alertes Système')
@section('header_title', 'Sécurité & Intégrité')
@section('page_title', 'Gestion des Incidents')
@section('page_subtitle', 'Identifiez et résolvez les anomalies détectées par ServiceRDC Active Guard.')

@section('content')
<div class="space-y-8 pb-20" x-data="alertManager()">
    
    <!-- Alert HUD -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-red-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-red-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-red/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-rdc-red uppercase tracking-widest">Critique</span>
                <i class="fas fa-radiation text-rdc-red {{ $stats['critical'] > 0 ? 'animate-pulse' : '' }} text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">{{ str_pad((string)$stats['critical'], 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Incidents</p>
        </div>
        
        <div class="bg-amber-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-amber-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-amber-600 uppercase tracking-widest">Avertissements</span>
                <i class="fas fa-triangle-exclamation text-amber-500 text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">{{ str_pad((string)$stats['warning'], 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Anomalies</p>
        </div>

        <div class="bg-emerald-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-emerald-100 shadow-sm relative overflow-hidden group sm:col-span-2 lg:col-span-1">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-[8px] sm:text-[10px] font-black text-emerald-600 uppercase tracking-widest">Résolus</span>
                <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
            </div>
            <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">{{ str_pad((string)$stats['resolved'], 3, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-[9px] sm:text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Archives</p>
        </div>
    </div>

    <!-- Alert List Container -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <!-- Table Header -->
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 space-y-6 bg-slate-50/30">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Journal Alertes</h3>
                <button @click="markAllRead()" class="w-full sm:w-auto px-6 py-3 bg-red-500/10 text-red-500 text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-red hover:text-white active:scale-95 transition-all">Tout marquer Résolu</button>
            </div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar -mx-1 px-1">
                <button @click="filter = 'ALL'" :class="filter === 'ALL' ? 'bg-slate-900 text-white' : 'bg-white text-slate-400 border border-slate-100'" class="px-4 py-2 text-[8px] sm:text-[9px] font-black uppercase rounded-lg tracking-widest shrink-0 transition-all">Tous</button>
                <button @click="filter = 'critical'" :class="filter === 'critical' ? 'bg-red-600 text-white' : 'bg-white text-slate-400 border border-slate-100'" class="px-4 py-2 text-[8px] sm:text-[9px] font-black uppercase rounded-lg border shrink-0 transition-all">Critique</button>
                <button @click="filter = 'warning'" :class="filter === 'warning' ? 'bg-amber-500 text-white' : 'bg-white text-slate-400 border border-slate-100'" class="px-4 py-2 text-[8px] sm:text-[9px] font-black uppercase rounded-lg border shrink-0 transition-all">Avertissement</button>
            </div>
        </div>

        <!-- Alerts Content -->
        <div class="divide-y divide-slate-50">
            @forelse($alerts as $alert)
            <div x-show="shouldShow('{{ $alert->level }}', {{ $alert->is_resolved ? 'true' : 'false' }})"
                 class="px-6 sm:px-10 py-6 sm:py-8 flex flex-col sm:flex-row items-start gap-4 sm:gap-8 group transition-colors"
                 :class="{{ $alert->is_resolved ? 'true' : 'false' }} ? 'opacity-50 grayscale bg-slate-50/20' : 'hover:bg-blue-50/30'">
                
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform"
                     class="{{ $alert->level === 'critical' ? 'bg-red-100 text-red-600' : ($alert->level === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-rdc-blue') }}">
                    <i class="fas {{ $alert->level === 'critical' ? 'fa-shield-virus' : ($alert->level === 'warning' ? 'fa-microchip' : 'fa-info-circle') }} text-xl sm:text-2xl"></i>
                </div>
                
                <div class="flex-1 w-full">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-white text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-full {{ $alert->level === 'critical' ? 'bg-red-600' : ($alert->level === 'warning' ? 'bg-amber-500' : 'bg-blue-600') }}">
                                {{ $alert->level }}
                            </span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono truncate">{{ $alert->code }}</span>
                        </div>
                        <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest italic font-mono">{{ $alert->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-base sm:text-lg font-black text-slate-900 mb-2 uppercase tracking-tight">{{ $alert->title }}</h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">{{ $alert->description }}</p>
                    
                    @if(!$alert->is_resolved)
                    <div class="flex flex-wrap gap-2">
                        <button @click="resolveAlert({{ $alert->id }})" class="w-full sm:w-auto px-6 py-3 bg-emerald-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all shadow-lg shadow-emerald-500/20">
                            Marquer Résolu
                        </button>
                        <button class="flex-1 sm:flex-none px-4 py-3 bg-white border border-slate-200 text-slate-500 text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg active:scale-95 transition-all">Détails</button>
                    </div>
                    @else
                    <div class="flex items-center gap-2 text-emerald-600">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Résolu par {{ $alert->resolver->name ?? 'Système' }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-32 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                    <i class="fas fa-check-double"></i>
                </div>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Ciel Dégagé !</h4>
                <p class="text-slate-400 max-w-xs mx-auto mt-2 font-medium">Aucun incident de sécurité ou de performance n'a été signalé pour le moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function alertManager() {
        return {
            filter: 'ALL',
            shouldShow(level, isResolved) {
                if (this.filter === 'ALL') return true;
                return level === this.filter;
            },
            resolveAlert(id) {
                fetch(`/admin/alerts/${id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => window.location.reload());
            },
            markAllRead() {
                fetch('/admin/alerts/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => window.location.reload());
            }
        }
    }
</script>
@endpush
