@extends('layouts.admin')

@section('title', 'Statistiques Temps Réel')
@section('header_title', 'Surveillance Live')
@section('page_title', 'Pulse du Système')
@section('page_subtitle', 'Monitoring en direct des interactions et des flux de données sur ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="statPulse()">
    
    <!-- Top Live Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Metric: Connections -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Connectés</span>
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900" x-text="activeUsers">248</h3>
                <span class="text-[10px] sm:text-xs font-bold text-emerald-500" x-text="'+' + userTrend + '%' ">+5%</span>
            </div>
            <!-- Mini Sparkline -->
            <div class="mt-4 h-10 sm:h-12 flex items-end gap-1">
                <template x-for="height in userSparkline">
                    <div class="flex-1 bg-rdc-blue/10 rounded-full transition-all duration-500" :style="'height: ' + height + '%'"></div>
                </template>
            </div>
        </div>

        <!-- Metric: API Requests -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Requêtes</span>
                <i class="fas fa-bolt text-amber-500 animate-pulse text-xs"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900" x-text="reqPerMin">1.2k</h3>
                <span class="text-[10px] sm:text-xs font-bold text-slate-400">Live</span>
            </div>
            <div class="mt-4 h-10 sm:h-12 flex items-end gap-1">
                <template x-for="height in reqSparkline">
                    <div class="flex-1 bg-amber-500/10 rounded-full transition-all duration-500" :style="'height: ' + height + '%'"></div>
                </template>
            </div>
        </div>

        <!-- Metric: Server Load -->
        <div class="bg-slate-900 p-5 sm:p-6 rounded-2xl sm:rounded-[2.5rem] shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[8px] sm:text-[10px] font-black text-white/40 uppercase tracking-widest">Charge</span>
                <i class="fas fa-microchip text-rdc-blue text-xs"></i>
            </div>
            <div class="flex items-baseline gap-2 relative z-10">
                <h3 class="text-3xl sm:text-4xl font-heading font-black text-white" x-text="cpuLoad + '%'">14%</h3>
            </div>
            <div class="mt-4 bg-white/5 h-1.5 sm:h-2 rounded-full overflow-hidden">
                <div class="h-full bg-rdc-blue transition-all duration-1000" :style="'width: ' + cpuLoad + '%'"></div>
            </div>
        </div>

        <!-- Metric: Conversion -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Sécurité</span>
                <i class="fas fa-shield-check text-emerald-500 text-xs"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl sm:text-4xl font-heading font-black text-slate-900">99%</h3>
            </div>
            <div class="mt-4 h-8 sm:h-12 flex items-center justify-center">
                 <div class="w-full h-0.5 sm:h-1 bg-emerald-500 rounded-full"></div>
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
                <div class="space-y-2">
                    <div class="flex justify-between text-[8px] sm:text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-900">Kinshasa</span>
                        <span class="text-rdc-blue">64%</span>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                        <div class="h-full bg-rdc-blue w-[64%]"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-[8px] sm:text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-900">Lubumbashi</span>
                        <span class="text-slate-400">18%</span>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                        <div class="h-full bg-slate-900 w-[18%]"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-[8px] sm:text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-900">Goma</span>
                        <span class="text-slate-400">12%</span>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                        <div class="h-full bg-slate-400 w-[12%]"></div>
                    </div>
                </div>
            </div>
            <!-- Region Indicator -->
            <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-50 text-rdc-blue flex items-center justify-center text-xs sm:text-base">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-slate-400">AF-RD-CENTRAL</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
            </div>
        </div>
    </div>

    <!-- Live Event Log -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/30">
            <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center shadow-sm text-slate-900 shrink-0">
                    <i class="fas fa-list-ul text-sm sm:text-base"></i>
                </div>
                <div class="overflow-hidden">
                    <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight truncate">Journal Live</h3>
                    <p class="text-[8px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest truncate">Interactions serveurs</p>
                </div>
            </div>
            <button class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-200 text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 active:scale-95 transition-all">Clear</button>
        </div>
        <div class="p-3 sm:p-4 overflow-y-auto max-h-[300px] sm:max-h-96 custom-scrollbar bg-slate-900 font-mono">
            <div class="space-y-2">
                <template x-for="event in events" :key="event.id">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-2 rounded hover:bg-white/5 transition-colors border-l-2" :class="event.type === 'error' ? 'border-red-500' : 'border-emerald-500'">
                        <div class="flex items-center gap-3">
                            <span class="text-white/30 text-[9px] sm:text-[10px]" x-text="event.time">04:58:34</span>
                            <span class="px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-black uppercase tracking-tighter shrink-0" 
                                  :class="event.type === 'error' ? 'bg-red-500/20 text-red-500' : 'bg-emerald-500/20 text-emerald-500'"
                                  x-text="event.label">AUTH</span>
                        </div>
                        <span class="text-white/70 text-[10px] sm:text-xs break-words" x-text="event.message">Message...</span>
                    </div>
                </template>
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
            userSparkline: [20, 35, 50, 40, 60, 45, 80, 55, 70, 90],
            reqSparkline: [40, 45, 42, 48, 50, 45, 52, 55, 50, 48],
            events: [
                { id: 1, time: '04:58:01', label: 'AUTH', type: 'info', message: 'Connexion réussie: user_982' },
                { id: 2, time: '04:58:12', label: 'JOB', type: 'info', message: 'Nouvelle candidature: #JOB-441 par artisan_22' },
                { id: 3, time: '04:58:15', label: 'SEC', type: 'error', message: 'Tentative de brute-force bloquée (IP: 197.242.xx.xx)' },
                { id: 4, time: '04:58:22', label: 'SERV', type: 'info', message: 'Service mis à jour: Electricité Gombe' }
            ],
            init() {
                // Simulate Real-time data movement
                setInterval(() => {
                    this.activeUsers += Math.floor(Math.random() * 5) - 2;
                    this.cpuLoad = Math.floor(Math.random() * 10) + 10;
                    this.reqPerMin = Math.floor(Math.random() * 100) + 1200;
                    
                    // Rotate sparklines
                    this.userSparkline.shift();
                    this.userSparkline.push(Math.floor(Math.random() * 80) + 20);
                    
                    this.reqSparkline.shift();
                    this.reqSparkline.push(Math.floor(Math.random() * 40) + 30);
                    
                    // Update Chart Data
                    liveChart.data.datasets[0].data.shift();
                    liveChart.data.datasets[0].data.push(Math.floor(Math.random() * 50) + 50);
                    liveChart.update('none');
                    
                    // Add Random Events
                    if (Math.random() > 0.7) {
                        const types = ['AUTH', 'JOB', 'SERV', 'PAY', 'SEC'];
                        const type = types[Math.floor(Math.random() * types.length)];
                        const isError = Math.random() > 0.9;
                        const now = new Date();
                        const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':' + now.getSeconds().toString().padStart(2, '0');
                        
                        this.events.unshift({
                            id: Date.now(),
                            time: time,
                            label: type,
                            type: isError ? 'error' : 'info',
                            message: this.generateMsg(type, isError)
                        });
                        
                        if (this.events.length > 20) this.events.pop();
                    }
                }, 3000);
            },
            generateMsg(type, isError) {
                if (isError) return 'Erreur critique détectée sur le module ' + type;
                const msgs = {
                    'AUTH': 'Utilisateur connecté avec succès',
                    'JOB': 'Profil consulté par un employeur',
                    'SERV': 'Nouveau service premium activé',
                    'PAY': 'Transaction confirmée via M-Pesa',
                    'SEC': 'Contrôle de sécurité terminé (Clean)'
                };
                return msgs[type];
            }
        }
    }

    // Chart.js Live Traffic
    const ctxLive = document.getElementById('liveTrafficChart');
    const liveChart = new Chart(ctxLive, {
        type: 'line',
        data: {
            labels: ['', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            datasets: [{
                label: 'Requêtes / sec',
                data: [65, 59, 80, 81, 56, 55, 40, 60, 75, 80, 70, 65, 85, 90, 80],
                borderColor: '#007FFF',
                borderWidth: 4,
                pointRadius: 0,
                tension: 0.4,
                fill: true,
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(0, 127, 255, 0)');
                    gradient.addColorStop(1, 'rgba(0, 127, 255, 0.1)');
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
</script>
@endpush
