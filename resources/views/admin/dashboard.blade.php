@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Vue d\'ensemble')
@section('page_title', 'Tableau de Bord')
@section('page_subtitle', 'Analysez les performances et gérez l\'activité de la plateforme.')

@section('content')
<div class="space-y-8">
    
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
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Utilisateurs</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($stats['total_users']) }}</h3>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-screwdriver-wrench text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    Artisans
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Services</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($stats['active_services']) }}</h3>
            </div>
        </div>

        <!-- Jobs Card -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-briefcase text-sm sm:text-xl"></i>
                </div>
                <span class="hidden sm:flex items-center gap-1 text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">
                    {{ $stats['total_applications'] }} Ops
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Offres Job</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($stats['total_jobs']) }}</h3>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-slate-900 p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-800 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-bl-full -mr-4 -mt-4"></div>
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-white/10 text-emerald-400 rounded-lg sm:rounded-xl">
                    <i class="fas fa-wallet text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-400 truncate">Revenu (CDF)</p>
                <h3 class="text-lg sm:text-2xl font-black text-white mt-1 truncate">{{ number_format($stats['monthly_revenue'], 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6 sm:space-y-8">
            
            <!-- Chart Section -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6 gap-4">
                    <div class="overflow-hidden">
                        <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight truncate">Activité</h3>
                        <p class="text-[10px] sm:text-sm text-slate-400 truncate">Évolution des flux</p>
                    </div>
                    <select class="text-[10px] sm:text-xs font-bold border-slate-200 rounded-xl bg-slate-50 focus:ring-rdc-blue">
                        <option>30j</option>
                        <option>Année</option>
                    </select>
                </div>
                <div class="h-64 sm:h-80 w-full">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
                    <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Utilisateurs HQ</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-[10px] sm:text-sm font-black text-rdc-blue uppercase tracking-widest">Voir</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-fixed sm:table-auto">
                        <thead class="bg-slate-50/50 text-[8px] sm:text-xs uppercase font-black text-slate-400 tracking-widest">
                            <tr>
                                <th class="w-3/4 sm:w-auto px-6 py-4">Nom & Rôle</th>
                                <th class="hidden sm:table-cell px-6 py-4">Statut</th>
                                <th class="hidden md:table-cell px-6 py-4">Date</th>
                                <th class="w-1/4 sm:w-auto px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="relative shrink-0">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" class="w-8 h-8 rounded-full shadow-sm" alt="">
                                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-xs sm:text-sm font-black text-slate-900 truncate">{{ $user->name }}</p>
                                            <p class="text-[9px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest truncate">{{ $user->role }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600">Actif</span>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-xs text-slate-400 font-medium">
                                    {{ $user->created_at->format('d M') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium text-sm">Vide</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-6 sm:space-y-8">
            
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-4">Actions</h3>
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <a href="{{ route('admin.jobs.create') }}" class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-rdc-blue hover:shadow-lg transition-all group text-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-sm sm:text-base"></i>
                        </div>
                        <span class="text-[9px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter">Job</span>
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-amber-500 hover:shadow-lg transition-all group text-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-check text-sm sm:text-base"></i>
                        </div>
                        <span class="text-[9px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter">Services</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-emerald-500 hover:shadow-lg transition-all group text-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-invoice text-sm sm:text-base"></i>
                        </div>
                        <span class="text-[9px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter">Reports</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-slate-400 hover:shadow-lg transition-all group text-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto bg-slate-100 text-slate-500 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-cog text-sm sm:text-base"></i>
                        </div>
                        <span class="text-[9px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter">Settings</span>
                    </a>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Jobs</h3>
                    <a href="{{ route('admin.jobs.index') }}" class="text-[9px] font-black text-slate-400 hover:text-rdc-blue uppercase tracking-widest">Tout</a>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @forelse($recentJobs as $job)
                    <div class="flex items-center gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-50 group">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 group-hover:scale-105 transition-transform">
                            @if($job->logo_url)
                                <img src="{{ $job->logo_url }}" class="w-full h-full object-contain" alt="">
                            @else
                                <i class="fas fa-briefcase text-slate-300 text-sm"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate leading-tight">{{ $job->title }}</h4>
                            <p class="text-[9px] sm:text-xs text-slate-400 truncate uppercase mt-0.5">{{ $job->company_name }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-[10px] font-medium uppercase">Aucun job</div>
                    @endforelse
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-slate-900 p-6 rounded-2xl text-white relative shadow-2xl overflow-hidden">
                <div class="absolute inset-0 bg-blue-500/5 blur-3xl rounded-full translate-x-1/2 translate-y-1/2"></div>
                <h3 class="text-sm font-black uppercase tracking-widest mb-6 relative z-10 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> HQ Status
                </h3>
                
                <div class="space-y-5 relative z-10">
                    <div class="group">
                        <div class="flex justify-between text-[8px] font-black text-white/40 uppercase tracking-widest mb-1.5 transition-colors group-hover:text-emerald-400">
                            <span>CPU</span>
                            <span>24%</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1 overflow-hidden">
                            <div class="bg-emerald-400 h-1 rounded-full group-hover:shadow-[0_0_8px_rgba(52,211,153,0.5)] transition-all" style="width: 24%"></div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="flex justify-between text-[8px] font-black text-white/40 uppercase tracking-widest mb-1.5 transition-colors group-hover:text-blue-400">
                            <span>Memory</span>
                            <span>42%</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1 overflow-hidden">
                            <div class="bg-blue-400 h-1 rounded-full group-hover:shadow-[0_0_8px_rgba(96,165,250,0.5)] transition-all" style="width: 42%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between pt-4 border-t border-white/5 relative z-10">
                    <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">ONLINE</span>
                    <a href="{{ route('admin.tools.maintenance') }}" class="text-[9px] font-black text-white/20 hover:text-white uppercase tracking-widest transition-colors">Tools ></a>
                </div>
            </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        // Gradient fill for chart
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 127, 255, 0.2)');
        gradient.addColorStop(1, 'rgba(0, 127, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1', '5', '10', '15', '20', '25', '30'],
                datasets: [{
                    label: 'Visiteurs Uniques',
                    data: [120, 190, 300, 250, 280, 420, 380],
                    borderColor: '#007FFF',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#007FFF',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            family: 'Inter',
                            size: 13
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 13
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [5, 5],
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                family: 'Inter'
                            },
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush
@endsection
