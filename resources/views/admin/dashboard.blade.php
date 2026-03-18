@extends('layouts.admin')

@section('title', 'Admin HQ | Command Center')
@section('header_title', 'Centre d\'Opérations HQ')

@section('content')
<div class="space-y-10 animate-slide-in-right">
    
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Users Card -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-users text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    <i class="fas fa-arrow-up text-[6px] sm:text-[8px]"></i> {{ $stats['user_growth'] }}%
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate uppercase tracking-widest italic">Utilisateurs</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate tabular-nums">{{ number_format($stats['total_users']) }}</h3>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-screwdriver-wrench text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full uppercase italic">
                    Artisans
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate uppercase tracking-widest italic">Services</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate tabular-nums">{{ number_format($stats['active_services']) }}</h3>
            </div>
        </div>

        <!-- Jobs Card -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-briefcase text-sm sm:text-xl"></i>
                </div>
                <span class="hidden sm:flex items-center gap-1 text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full uppercase italic">
                    {{ $stats['total_applications'] }} Ops
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate uppercase tracking-widest italic">Offres Job</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate tabular-nums">{{ number_format($stats['total_jobs']) }}</h3>
            </div>
        </div>

        <!-- Finance Card -->
        <div class="bg-slate-900 p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-800 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-bl-full -mr-4 -mt-4"></div>
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-white/10 text-emerald-400 rounded-lg sm:rounded-xl">
                    <i class="fas fa-wallet text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full uppercase italic">+{{ $stats['revenue_growth'] }}%</span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-400 truncate uppercase tracking-widest italic">Flux Mensuel ($)</p>
                <h3 class="text-lg sm:text-2xl font-black text-white mt-1 truncate tabular-nums">{{ number_format($stats['monthly_revenue'], 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Operational Command Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Performance Unit (2/3) -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight italic">Unité de <span class="text-rdc-blue">Monitoring</span></h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Analyse du trafic HQ</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-slate-50 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-blue hover:text-white transition-all italic">Semaine</button>
                        <button class="px-4 py-2 bg-rdc-blue text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/20 italic">Mois</button>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight italic">Récentes <span class="text-rdc-red">Activations</span></h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Derniers dossiers utilisateurs</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-rdc-blue hover:text-rdc-blue-dark uppercase tracking-widest italic border-b-2 border-rdc-blue/10 hover:border-rdc-blue transition-all">Visualiser tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest italic">
                            <tr>
                                <th class="px-8 py-4">Utilisateur</th>
                                <th class="px-8 py-4">Grade</th>
                                <th class="px-8 py-4">Statut Flux</th>
                                <th class="px-8 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors group border-b border-slate-50 last:border-0">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="relative shrink-0">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007FFF&color=fff" class="w-10 h-10 rounded-xl shadow-sm border border-white" alt="">
                                            <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 tracking-tight italic uppercase">{{ $user->name }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase italic">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-500' }}">
                                        @if($user->role === 'admin') <i class="fas fa-crown mr-1.5"></i> @endif {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-[11px] font-bold text-slate-900 uppercase italic">Opérationnel</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all inline-flex items-center justify-center group-hover:shadow-lg group-hover:shadow-blue-500/20">
                                        <i class="fas fa-bolt-lightning text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="opacity-30 grayscale">
                                        <i class="fas fa-satellite text-5xl mb-4 text-rdc-blue"></i>
                                        <p class="text-xs font-black uppercase tracking-widest italic">Aucun flux détecté</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Control Panel (1/3) -->
        <div class="space-y-8">
            <!-- Security Hub -->
            <div class="bg-rdc-dark-blue p-8 rounded-3xl text-white relative overflow-hidden group shadow-2xl">
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-rdc-blue/10 rounded-full blur-3xl group-hover:bg-rdc-blue/20 transition-all duration-1000"></div>
                <div class="relative z-10 flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black uppercase tracking-tight italic">État <span class="text-rdc-blue">Infrastructure</span></h3>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase text-emerald-500 italic">LIVE</span>
                    </div>
                </div>
                
                <div class="space-y-6 relative z-10">
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase text-slate-400 mb-2 italic">
                            <span>Service Load</span>
                            <span class="text-white">24%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1">
                            <div class="bg-rdc-blue h-1 rounded-full shadow-[0_0_10px_rgba(0,127,255,0.5)]" style="width: 24%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase text-slate-400 mb-2 italic">
                            <span>Security Integrity</span>
                            <span class="text-white">99.8%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1">
                            <div class="bg-emerald-500 h-1 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]" style="width: 99.8%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3 relative z-10">
                    <a href="{{ route('admin.tools.maintenance') }}" class="px-4 py-3 rounded-2xl bg-white/5 border border-white/5 hover:border-rdc-blue/30 transition-all text-[9px] font-black uppercase text-center text-slate-400 hover:text-white italic">Maint.</a>
                    <a href="{{ route('admin.tools.logs') }}" class="px-4 py-3 rounded-2xl bg-white/5 border border-white/5 hover:border-rdc-blue/30 transition-all text-[9px] font-black uppercase text-center text-slate-400 hover:text-white italic">Logs</a>
                </div>
            </div>

            <!-- Quick Deploy Actions -->
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-6 italic">Actions <span class="text-rdc-blue">Prioritaires</span></h4>
                <div class="space-y-3">
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-rdc-blue hover:text-white transition-all group border border-transparent shadow-sm hover:shadow-xl hover:shadow-blue-500/20">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rdc-blue group-hover:scale-110 transition-transform shadow-sm">
                                <i class="fas fa-users-gear"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest italic">Gérer Membres</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-all group-hover:translate-x-1"></i>
                    </a>

                    <a href="{{ route('admin.services.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-rdc-yellow hover:text-white transition-all group border border-transparent shadow-sm hover:shadow-xl hover:shadow-yellow-500/20">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rdc-yellow group-hover:scale-110 transition-transform shadow-sm">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest italic">Valider Services</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-all group-hover:translate-x-1"></i>
                    </a>

                    <a href="{{ route('admin.jobs.create') }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-rdc-red hover:text-white transition-all group border border-transparent shadow-sm hover:shadow-xl hover:shadow-red-500/20">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rdc-red group-hover:scale-110 transition-transform shadow-sm">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest italic">Nouvelle Offre</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-all group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 127, 255, 0.2)');
        gradient.addColorStop(1, 'rgba(0, 127, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['POINT A', 'POINT B', 'POINT C', 'POINT D', 'POINT E', 'POINT F', 'POINT G'],
                datasets: [{
                    label: 'Fréquentation Flux',
                    data: [120, 190, 300, 250, 280, 420, 380],
                    borderColor: '#007FFF',
                    backgroundColor: gradient,
                    borderWidth: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#007FFF',
                    pointBorderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '900', size: 9 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.03)', borderDash: [5, 5] },
                        ticks: { font: { weight: '900', size: 9 }, color: '#94a3b8' }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
