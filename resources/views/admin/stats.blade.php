@extends('layouts.admin')

@section('title', 'Statistiques Temps Réel')
@section('header_title', 'Surveillance Live')
@section('page_title', 'Pulse du Système')
@section('page_subtitle', 'Monitoring en direct des interactions et des flux de données sur ProConnect.')

@section('content')
<div class="space-y-8 pb-20" x-data="statPulse()" x-init="init()">
    
    <!-- Top Alert Banner System -->
    <template x-for="alert in activeAlerts" :key="alert.id">
        <div class="mb-4 p-4 rounded-2xl flex items-center justify-between border animate-pulse-soft"
             :class="alert.type === 'critical' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-amber-50 border-amber-200 text-amber-800'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     :class="alert.type === 'critical' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600'">
                    <i class="fas" :class="alert.icon"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black uppercase tracking-tight" x-text="alert.title">ALERTE</h4>
                    <p class="text-xs opacity-80" x-text="alert.message">Message...</p>
                </div>
            </div>
            <button @click="acknowledgeAlert(alert.id)" class="px-4 py-2 bg-white border rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">Acquitter</button>
        </div>
    </template>

    <!-- Banner for Server Load -->
    <div x-show="cpuLoad >= 80" x-transition 
         class="mb-4 p-4 bg-red-600 text-white rounded-2xl flex items-center justify-between shadow-lg shadow-red-500/20">
        <div class="flex items-center gap-4">
            <i class="fas fa-warning text-xl animate-bounce"></i>
            <div>
                <p class="font-black uppercase tracking-widest text-xs">Alerte Critique</p>
                <p class="text-sm font-bold">La charge serveur est critique : <span x-text="cpuLoad"></span>%</p>
            </div>
        </div>
        <button @click="cpuLoad = 14" class="text-xs font-bold border border-white/30 px-3 py-1.5 rounded-lg hover:bg-white/10">Simuler retour normal</button>
    </div>

    <!-- Top Live Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

        <!-- Card: Connectés -->
        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-users text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    <i class="fas fa-arrow-up text-[6px] sm:text-[8px]"></i>
                    <span x-text="userTrend + '%'">5%</span>
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Connectés</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate" x-text="activeUsers">248</h3>
            </div>
            <!-- Mini Sparkline -->
            <div class="mt-3 h-8 flex items-end gap-0.5">
                <template x-for="height in userSparkline">
                    <div class="flex-1 bg-blue-100 rounded-sm transition-all duration-500" :style="'height: ' + height + '%'"></div>
                </template>
            </div>
        </div>

        <!-- Card: Requêtes -->
        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-bolt text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    Live
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Requêtes / min</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate" x-text="reqPerMin.toLocaleString()">1240</h3>
            </div>
            <div class="mt-3 h-8 flex items-end gap-0.5">
                <template x-for="height in reqSparkline">
                    <div class="flex-1 bg-amber-100 rounded-sm transition-all duration-500" :style="'height: ' + height + '%'"></div>
                </template>
            </div>
        </div>

        <!-- Card: Charge Serveur -->
        <div class="p-4 sm:p-6 rounded-xl sm:rounded-2xl border shadow-sm hover:shadow-md transition-all relative overflow-hidden"
             :class="cpuLoad >= 80 ? 'bg-red-600 border-red-500' : 'bg-slate-900 border-slate-800'">
            <div class="absolute right-0 top-0 w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-white/10 to-transparent rounded-bl-full -mr-4 -mt-4"></div>
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl"
                     :class="cpuLoad >= 80 ? 'bg-white/20 text-white' : 'bg-white/10 text-rdc-blue'">
                    <i class="fas fa-microchip text-sm sm:text-xl" :class="cpuLoad >= 80 ? 'animate-spin' : ''"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full"
                      :class="cpuLoad >= 80 ? 'bg-white/20 text-white' : 'bg-white/10 text-white/60'">
                    Serveur
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium truncate" :class="cpuLoad >= 80 ? 'text-white/80' : 'text-slate-400'">Charge CPU</p>
                <h3 class="text-lg sm:text-2xl font-black text-white mt-1 truncate" x-text="cpuLoad + '%'">14%</h3>
                <div x-show="cpuLoad >= 80" class="mt-1 text-[9px] font-black text-white/80 uppercase tracking-widest flex items-center gap-1 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-[8px]"></i> Surcharge
                </div>
            </div>
            <div class="mt-3 bg-white/10 h-1.5 rounded-full overflow-hidden">
                <div class="h-full transition-all duration-1000 rounded-full"
                     :class="cpuLoad >= 80 ? 'bg-white' : 'bg-rdc-blue'"
                     :style="'width: ' + cpuLoad + '%'"></div>
            </div>
        </div>

        <!-- Card: Sécurité -->
        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-shield-halved text-sm sm:text-xl"></i>
                </div>
                <span class="flex items-center gap-1 text-[8px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">
                    <i class="fas fa-check text-[6px] sm:text-[8px]"></i> OK
                </span>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Sécurité</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">99%</h3>
            </div>
            <div class="mt-3 bg-emerald-50 h-1.5 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: 99%"></div>
            </div>
        </div>

    </div>


    <!-- Charts Hub -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Live Traffic Chart -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Trafic Live</h3>
                    <p class="text-[8px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest">Requêtes par seconde</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-2.5 py-1 bg-slate-100 text-[8px] sm:text-[10px] font-black uppercase rounded-lg">Realtime</span>
                </div>
            </div>
            <div class="h-60 sm:h-80 w-full">
                <canvas id="liveTrafficChart"></canvas>
            </div>
        </div>

        <!-- Geographic Pulse -->
        <div class="bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[3rem] border border-slate-100 shadow-sm flex flex-col">
            <div class="mb-6 sm:mb-8">
                <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Géographie</h3>
                <p class="text-[8px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest">Origine par Province</p>
            </div>
            <div class="flex-1 space-y-5 sm:space-y-6">
                @foreach(array_slice($sortedProvinces, 0, 3) as $prov)
                <div class="space-y-2">
                    <div class="flex justify-between text-[8px] sm:text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-900">{{ $prov['name'] }}</span>
                        <span class="{{ $loop->first ? 'text-rdc-blue' : 'text-slate-400' }}">{{ $prov['percentage'] }}% ({{ $prov['userCount'] }})</span>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                        <div class="h-full {{ $loop->first ? 'bg-rdc-blue' : ($loop->index == 1 ? 'bg-slate-900' : 'bg-slate-400') }}" style="width: {{ $prov['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Region Indicator -->
            <div @click="showGeographyModal = true" class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-slate-50 flex items-center justify-between cursor-pointer group/geo">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-50 text-rdc-blue flex items-center justify-center text-xs sm:text-base group-hover/geo:scale-110 transition-transform">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover/geo:text-rdc-blue transition-colors">AF-RD-CENTRAL</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs group-hover/geo:translate-x-1 transition-transform"></i>
            </div>
        </div>
    </div>

    <!-- Geography Modal -->
    <div x-show="showGeographyModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showGeographyModal = false" 
             class="bg-white w-full max-w-6xl max-h-[90vh] rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">🗺️ Géographie — 26 Provinces de la RDC</h2>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Répertoire territorial et activités en direct</p>
                </div>
                <button @click="showGeographyModal = false" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-all shadow-sm">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="province in provinces" :key="province.id">
                        <div class="p-6 rounded-3xl border border-slate-100 bg-white hover:border-rdc-blue/30 hover:shadow-xl hover:shadow-blue-500/5 transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-black text-slate-900" x-text="province.name">Province</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="flex h-2 w-2 relative">
                                            <span x-show="province.active" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2" :class="province.active ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                        </span>
                                        <span class="text-[9px] font-black uppercase tracking-widest" :class="province.active ? 'text-emerald-500' : 'text-slate-400'" x-text="province.active ? '● Actif' : '○ Inactif'"></span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-rdc-blue transition-colors">
                                    <i class="fas fa-map-marker-alt text-sm"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2.5 py-1 bg-slate-50 rounded-lg text-[10px] font-bold text-slate-600 border border-slate-100">
                                        <span x-text="province.userCount"></span> Utilisateur(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/30 flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-slate-400">
                <span>Données consolidées par province</span>
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 
                    <span x-text="provinces.filter(p => p.active).length"></span> provinces actives
                </span>
            </div>
        </div>
    </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function statPulse() {
        return {
            activeUsers: 248,
            userTrend: 5,
            reqPerMin: 1240,
            cpuLoad: 14,
            currentFilter: 'ALL',
            searchQuery: '',
            authFailures: 0,
            consecutivePayFailures: 0,
            lastAuthFailureTime: 0,
            activeAlerts: [],
            showGeographyModal: false,
            userSparkline: [20, 35, 50, 40, 60, 45, 80, 55, 70, 90],
            reqSparkline: [40, 45, 42, 48, 50, 45, 52, 55, 50, 48],
            provinces: @json($provinceData),
            events: [
                { id: 1, time: '17:20:01', label: 'AUTH', type: 'info', message: 'Connexion réussie: superadmin@proconnect.com' },
                { id: 2, time: '17:20:12', label: 'JOB', type: 'info', message: 'Nouvelle offre publiée: Développeur PHP à Gombe' },
                { id: 3, time: '17:20:15', label: 'SEC', type: 'error', message: 'Tentative d\'injection SQL bloquée (Source: IP 105.x.x.x)' },
                { id: 4, time: '17:20:22', label: 'SERV', type: 'info', message: 'Service validé: Plomberie Express' }
            ],

            init() {
                // Fetch initial logs
                this.fetchLogs();
                
                // Refresh logs every 3 seconds
                setInterval(() => {
                    this.fetchLogs();
                    
                    // 1. Simuler fluctuation stats basic
                    this.activeUsers += Math.floor(Math.random() * 5) - 2;
                    
                    if (this.cpuLoad >= 80) {
                        // Keep it high until user clicks "Simuler retour normal"
                        this.cpuLoad = Math.max(80, Math.min(100, this.cpuLoad + (Math.floor(Math.random() * 5) - 2)));
                    } else if (Math.random() > 0.95) {
                        this.cpuLoad = Math.floor(Math.random() * 15) + 80; 
                    } else {
                        this.cpuLoad = Math.floor(Math.random() * 10) + 10;
                    }
                    
                    this.reqPerMin = Math.floor(Math.random() * 100) + 1200;
                    
                    // Rotate sparklines
                    this.userSparkline.shift();
                    this.userSparkline.push(Math.floor(Math.random() * 80) + 20);
                    this.reqSparkline.shift();
                    this.reqSparkline.push(Math.floor(Math.random() * 40) + 30);
                    
                    if (window.liveChart) {
                        window.liveChart.data.datasets[0].data.shift();
                        window.liveChart.data.datasets[0].data.push(Math.floor(Math.random() * 50) + 50);
                        window.liveChart.update('none');
                    }


                }, 3000);
            },

            fetchLogs() {
                let url = `/admin/api/logs?type=${this.currentFilter}`;
                if (this.searchQuery) url += `&search=${this.searchQuery}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.events = data;
                        
                        // Refine alert counters based on real logs
                        // Count errors in the last set of logs
                        this.authFailures = data.filter(e => e.label === 'AUTH' && e.type === 'error').length;
                        this.consecutivePayFailures = 0;
                        for (let e of data) {
                            if (e.label === 'PAY') {
                                if (e.type === 'error') this.consecutivePayFailures++;
                                else break; // Stop counting consecutive on first success
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching logs:', err));
            },

            filteredEvents() {
                // Now handled by fetchLogs server-side, but keep as fallback or for local refine
                return this.events;
            },

            checkThresholds() {
                // Payment issues
                if (this.consecutivePayFailures >= 3 && !this.hasAlert('pay-fail')) {
                    this.triggerAlert('warning', 'fa-credit-card', '⚠️ PROBLÈME PAIEMENT DÉTECTÉ', '3 transactions consécutives ont échoué.', 'pay-fail');
                }
            },

            triggerAlert(type, icon, title, message, ref) {
                this.activeAlerts.unshift({ id: Date.now(), ref: ref, type, icon, title, message });
                this.updateBellCounter(1);
            },

            hasAlert(ref) {
                return this.activeAlerts.some(a => a.ref === ref);
            },

            acknowledgeAlert(id) {
                this.activeAlerts = this.activeAlerts.filter(a => a.id !== id);
                this.updateBellCounter(-1);
            },

            updateBellCounter(val) {
                const bell = document.querySelector('.fa-bell + span');
                if (bell) {
                    let current = parseInt(bell.innerText) || 0;
                    bell.innerText = Math.max(0, current + val);
                    bell.classList.add('animate-bounce');
                    setTimeout(() => bell.classList.remove('animate-bounce'), 1000);
                }
            }
        }
    }

    // Chart.js Live Traffic
    window.onload = function() {
        const ctxLive = document.getElementById('liveTrafficChart');
        if (ctxLive) {
            window.liveChart = new Chart(ctxLive, {
                type: 'line',
                data: {
                    labels: ['', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                    datasets: [{
                        label: 'Requêtes / sec',
                        data: [65, 59, 80, 81, 56, 55, 40, 60, 75, 80, 70, 65, 85, 90, 80],
                        borderColor: '#29B6D1',
                        borderWidth: 4,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(0, 210, 255, 0)');
                            gradient.addColorStop(1, 'rgba(0, 210, 255, 0.1)');
                            return gradient;
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false },
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#F1F5F9' },
                            ticks: { font: { size: 10, weight: 'bold' }, color: '#94A3B8' }
                        }
                    }
                }
            });
        }
    };
</script>
@endpush
