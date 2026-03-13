@extends('layouts.admin')

@section('title', 'Statistiques Temps Réel')
@section('header_title', 'Surveillance Live')
@section('page_title', 'Pulse du Système')
@section('page_subtitle', 'Monitoring en direct des interactions et des flux de données sur ServiceRDC.')

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
    <div x-show="cpuLoad > 85" x-transition 
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
        <div :class="cpuLoad > 85 ? 'bg-red-600 border-red-400' : 'bg-slate-900 border-transparent'" 
             class="p-5 sm:p-6 rounded-2xl sm:rounded-[2.5rem] shadow-xl relative overflow-hidden group border transition-colors duration-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest" :class="cpuLoad > 85 ? 'text-white/60' : 'text-white/40'">Charge</span>
                <i class="fas fa-microchip text-xs" :class="cpuLoad > 85 ? 'text-white animate-spin' : 'text-rdc-blue'"></i>
            </div>
            <div class="flex items-baseline gap-2 relative z-10">
                <h3 class="text-3xl sm:text-4xl font-heading font-black text-white" x-text="cpuLoad + '%'">14%</h3>
            </div>
            <div class="mt-4 bg-white/5 h-1.5 sm:h-2 rounded-full overflow-hidden">
                <div class="h-full bg-rdc-blue transition-all duration-1000" :style="'width: ' + cpuLoad + '%'"></div>
            </div>
            <div x-show="cpuLoad > 85" class="absolute inset-0 bg-red-500/10 animate-pulse pointer-events-none"></div>
        </div>

        <!-- Metric: Security -->
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
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Villes principales</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="city in province.cities">
                                        <span class="px-2.5 py-1 bg-slate-50 rounded-lg text-[10px] font-bold text-slate-600 border border-slate-100" x-text="city">Ville</span>
                                    </template>
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
            provinces: [
                { id: 1, name: 'Kinshasa', active: true, cities: ['Kinshasa', 'Masina', 'Ndjili', 'Kintambo'] },
                { id: 2, name: 'Kongo-Central', active: false, cities: ['Matadi', 'Boma', 'Mbanza-Ngungu', 'Lukala'] },
                { id: 3, name: 'Kwango', active: false, cities: ['Kenge', 'Popokabaka', 'Kahemba'] },
                { id: 4, name: 'Kwilu', active: false, cities: ['Bandundu', 'Kikwit', 'Idiofa', 'Gungu'] },
                { id: 5, name: 'Mai-Ndombe', active: false, cities: ['Inongo', 'Bolobo', 'Kutu'] },
                { id: 6, name: 'Kasaï', active: false, cities: ['Tshikapa', 'Ilebo', 'Luebo'] },
                { id: 7, name: 'Kasaï-Central', active: false, cities: ['Kananga', 'Dibaya', 'Dimbelenge'] },
                { id: 8, name: 'Kasaï-Oriental', active: false, cities: ['Mbuji-Mayi', 'Miabi', 'Tshilenge'] },
                { id: 9, name: 'Lomami', active: false, cities: ['Kabinda', 'Ngandajika', 'Kamiji'] },
                { id: 10, name: 'Sankuru', active: false, cities: ['Lodja', 'Lusambo', 'Katako-Kombe'] },
                { id: 11, name: 'Maniema', active: false, cities: ['Kindu', 'Kalima', 'Punia', 'Kasongo'] },
                { id: 12, name: 'Sud-Kivu', active: false, cities: ['Bukavu', 'Uvira', 'Baraka', 'Shabunda'] },
                { id: 13, name: 'Nord-Kivu', active: true, cities: ['Goma', 'Butembo', 'Beni', 'Rutshuru'] },
                { id: 14, name: 'Ituri', active: false, cities: ['Bunia', 'Aru', 'Mahagi', 'Irumu'] },
                { id: 15, name: 'Haut-Uele', active: false, cities: ['Isiro', 'Wamba', 'Dungu'] },
                { id: 16, name: 'Bas-Uele', active: false, cities: ['Buta', 'Aketi', 'Bondo'] },
                { id: 17, name: 'Tshopo', active: false, cities: ['Kisangani', 'Ubundu', 'Isangi', 'Basoko'] },
                { id: 18, name: 'Mongala', active: false, cities: ['Lisala', 'Bumba', 'Bongandanga'] },
                { id: 19, name: 'Nord-Ubangi', active: false, cities: ['Gbadolite', 'Mobayi-Mbongo', 'Businga'] },
                { id: 20, name: 'Sud-Ubangi', active: false, cities: ['Gemena', 'Zongo', 'Kungu', 'Libenge'] },
                { id: 21, name: 'Équateur', active: false, cities: ['Mbandaka', 'Bikoro', 'Ingende'] },
                { id: 22, name: 'Tshuapa', active: false, cities: ['Boende', 'Befale', 'Monkoto'] },
                { id: 23, name: 'Tanganyika', active: false, cities: ['Kalemie', 'Kongolo', 'Moba', 'Nyunzu'] },
                { id: 24, name: 'Haut-Lomami', active: false, cities: ['Kamina', 'Kabalo', 'Malemba-Nkulu'] },
                { id: 25, name: 'Lualaba', active: true, cities: ['Kolwezi', 'Likasi', 'Fungurume', 'Mutshatsha'] },
                { id: 26, name: 'Haut-Katanga', active: true, cities: ['Lubumbashi', 'Kipushi', 'Kasenga', 'Sakania'] }
            ],
            events: [
                { id: 1, time: '17:20:01', label: 'AUTH', type: 'info', message: 'Connexion réussie: superadmin@servicerdc.com' },
                { id: 2, time: '17:20:12', label: 'JOB', type: 'info', message: 'Nouvelle offre publiée: Développeur PHP à Gombe' },
                { id: 3, time: '17:20:15', label: 'SEC', type: 'error', message: 'Tentative d\'injection SQL bloquée (Source: IP 105.x.x.x)' },
                { id: 4, time: '17:20:22', label: 'SERV', type: 'info', message: 'Service validé: Plomberie Express' }
            ],

            init() {
                // Initial Chart Render is handled by global script
                
                // Simulate Real-time data movement
                setInterval(() => {
                    // 1. Simuler fluctuation stats
                    this.activeUsers += Math.floor(Math.random() * 5) - 2;
                    
                    // Aléatoirement simuler un pic CPU pour tester l'alerte
                    if (Math.random() > 0.95) {
                        this.cpuLoad = Math.floor(Math.random() * 15) + 85; 
                    } else {
                        this.cpuLoad = Math.floor(Math.random() * 10) + 10;
                    }
                    
                    this.reqPerMin = Math.floor(Math.random() * 100) + 1200;
                    
                    // Rotate sparklines
                    this.userSparkline.shift();
                    this.userSparkline.push(Math.floor(Math.random() * 80) + 20);
                    this.reqSparkline.shift();
                    this.reqSparkline.push(Math.floor(Math.random() * 40) + 30);
                    
                    // Update Chart Data
                    if (window.liveChart) {
                        window.liveChart.data.datasets[0].data.shift();
                        window.liveChart.data.datasets[0].data.push(Math.floor(Math.random() * 50) + 50);
                        window.liveChart.update('none');
                    }
                    
                    // 2. Simuler événements
                    if (Math.random() > 0.6) {
                        this.generateRandomEvent();
                    }

                    // 3. Vérifier seuils pour alertes
                    this.checkThresholds();

                    // 4. Simuler changement d'activité par province
                    if (Math.random() > 0.8) {
                        const idx = Math.floor(Math.random() * 26);
                        this.provinces[idx].active = !this.provinces[idx].active;
                    }
                }, 3000);
            },

            generateRandomEvent() {
                const types = ['AUTH', 'JOB', 'SERV', 'PAY', 'SEC'];
                const type = types[Math.floor(Math.random() * types.length)];
                
                // Simuler des échecs pour tester les alertes
                let isError = Math.random() > 0.85;
                
                // Cas spécifique pour forcer les alertes pendant la démo
                if (type === 'AUTH' && Math.random() > 0.7) isError = true;
                if (type === 'PAY' && Math.random() > 0.6) isError = true;

                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':' + now.getSeconds().toString().padStart(2, '0');
                
                const event = {
                    id: Date.now(),
                    time: time,
                    label: type,
                    type: isError ? 'error' : 'info',
                    message: this.getDynamicMsg(type, isError)
                };

                this.events.unshift(event);
                if (this.events.length > 50) this.events.pop();

                // Tracker les échecs pour les alertes
                if (isError) {
                    if (type === 'AUTH') {
                        const nowMs = Date.now();
                        if (nowMs - this.lastAuthFailureTime > 60000) this.authFailures = 0;
                        this.authFailures++;
                        this.lastAuthFailureTime = nowMs;
                    }
                    if (type === 'PAY') {
                        this.consecutivePayFailures++;
                    }
                } else {
                    if (type === 'PAY') this.consecutivePayFailures = 0;
                }
            },

            getDynamicMsg(type, isError) {
                if (isError) {
                    const errors = {
                        'AUTH': 'Échec de connexion : Mot de passe incorrect (User ID: 442)',
                        'SEC': 'XSS suspect détecté sur le formulaire de contact',
                        'PAY': 'Paiement décliné par l\'opérateur (Solde insuffisant)',
                        'JOB': 'Erreur lors de l\'upload du CV (Format non supporté)',
                        'SERV': 'Échec de validation d\'image (Fichier corrompu)'
                    };
                    return errors[type];
                }
                const msgs = {
                    'AUTH': 'Session utilisateur initialisée avec succès',
                    'JOB': 'Nouveau candidat pour: Responsable IT',
                    'SERV': 'Mise à jour tariffaire pour "Maintenance Froid"',
                    'PAY': 'Transaction 45,000 FC acceptée via Orange Money',
                    'SEC': 'Scan d\'intégrité des fichiers terminé'
                };
                return msgs[type];
            },

            filteredEvents() {
                return this.events.filter(event => {
                    const matchesFilter = this.currentFilter === 'ALL' || 
                                        (this.currentFilter === 'ERRORS' ? event.type === 'error' : event.label === this.currentFilter);
                    const matchesSearch = this.searchQuery === '' || 
                                        event.message.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                        event.label.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchesFilter && matchesSearch;
                });
            },

            checkThresholds() {
                // Brute-force detection
                if (this.authFailures >= 5 && !this.hasAlert('brute-force')) {
                    this.triggerAlert('critical', 'fa-user-shield', '🚨 ATTAQUE BRUTE-FORCE DÉTECTÉE', `Plus de 5 échecs de connexion en moins de 60s.`, 'brute-force');
                }

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
        }
    };
</script>
@endpush
