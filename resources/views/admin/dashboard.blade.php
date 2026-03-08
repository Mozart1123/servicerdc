@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Vue d\'ensemble')
@section('page_title', 'Tableau de Bord')
@section('page_subtitle', 'Analysez les performances et gérez l\'activité de la plateforme.')

@section('content')
<div class="space-y-8">
    
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 bg-blue-50 text-rdc-blue rounded-xl">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <i class="fas fa-arrow-up"></i> {{ $stats['user_growth'] }}%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Utilisateurs Totaux</p>
                <h3 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ number_format($stats['total_users']) }}</h3>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                    <i class="fas fa-screwdriver-wrench text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                    Actifs
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Services Publiés</p>
                <h3 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ number_format($stats['active_services']) }}</h3>
            </div>
        </div>

        <!-- Jobs Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 bg-purple-50 text-purple-500 rounded-xl">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">
                    {{ $stats['total_applications'] }} Candidatures
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Offres d'Emploi</p>
                <h3 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ number_format($stats['total_jobs']) }}</h3>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="flex items-start justify-between mb-4 relative z-10">
                <div class="p-3 bg-emerald-50 text-emerald-500 rounded-xl">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <i class="fas fa-arrow-up"></i> {{ $stats['revenue_growth'] }}%
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500">Revenu Mensuel</p>
                <h3 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ number_format($stats['monthly_revenue'], 2) }} $</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column (2/3) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Chart Section -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-heading font-bold text-slate-900">Aperçu de l'Activité</h3>
                        <p class="text-sm text-slate-500">Trafic et inscriptions sur les 30 derniers jours</p>
                    </div>
                    <select class="text-sm border-slate-200 rounded-lg focus:ring-rdc-blue focus:border-rdc-blue">
                        <option>30 derniers jours</option>
                        <option>Cette année</option>
                        <option>Semaine dernière</option>
                    </select>
                </div>
                <div class="h-80 w-full">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-heading font-bold text-slate-900">Derniers Inscrits</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-rdc-blue hover:text-rdc-blue-dark">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Utilisateur</th>
                                <th class="px-6 py-4">Rôle</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" class="w-8 h-8 rounded-full" alt="">
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Actif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-slate-400 hover:text-rdc-blue transition-colors">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">Aucun utilisateur récent</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column (1/3) -->
        <div class="space-y-8">
            
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="text-lg font-heading font-bold text-slate-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.jobs.create') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-rdc-blue hover:shadow-md transition-all group text-center">
                        <div class="w-10 h-10 mx-auto bg-blue-100 text-rdc-blue rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Poster une offre</span>
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-amber-500 hover:shadow-md transition-all group text-center">
                        <div class="w-10 h-10 mx-auto bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Valider Services</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-emerald-500 hover:shadow-md transition-all group text-center">
                        <div class="w-10 h-10 mx-auto bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Rapports</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-slate-400 hover:shadow-md transition-all group text-center">
                        <div class="w-10 h-10 mx-auto bg-slate-200 text-slate-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Paramètres</span>
                    </a>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading font-bold text-slate-900">Offres Récentes</h3>
                    <a href="{{ route('admin.jobs.index') }}" class="text-xs font-bold text-slate-400 hover:text-rdc-blue uppercase tracking-wider">Tout voir</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentJobs as $job)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer border border-transparent hover:border-slate-100">
                        <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                            @if($job->logo_url)
                                <img src="{{ $job->logo_url }}" class="w-full h-full object-contain" alt="">
                            @else
                                <i class="fas fa-briefcase text-slate-300"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $job->title }}</h4>
                            <p class="text-xs text-slate-500 truncate">{{ $job->company_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400">{{ $job->created_at->diffForHumans(null, true) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-sm">Aucune offre récente</div>
                    @endforelse
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-indigo-900 p-6 rounded-2xl text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <h3 class="text-lg font-heading font-bold mb-4 relative z-10">État du Système</h3>
                
                <div class="space-y-4 relative z-10">
                    <div>
                        <div class="flex justify-between text-xs font-bold text-indigo-200 mb-1">
                            <span>CPU Usage</span>
                            <span>24%</span>
                        </div>
                        <div class="w-full bg-indigo-800 rounded-full h-1.5">
                            <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 24%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-bold text-indigo-200 mb-1">
                            <span>Memory</span>
                            <span>42%</span>
                        </div>
                        <div class="w-full bg-indigo-800 rounded-full h-1.5">
                            <div class="bg-blue-400 h-1.5 rounded-full" style="width: 42%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-bold text-indigo-200 mb-1">
                            <span>Storage</span>
                            <span>68%</span>
                        </div>
                        <div class="w-full bg-indigo-800 rounded-full h-1.5">
                            <div class="bg-amber-400 h-1.5 rounded-full" style="width: 68%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between pt-4 border-t border-indigo-800">
                    <span class="flex items-center gap-2 text-xs font-bold text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Opérationnel
                    </span>
                    <a href="{{ route('admin.tools.maintenance') }}" class="text-xs text-indigo-300 hover:text-white transition-colors">Maintenance ></a>
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
