@extends('layouts.admin')

@section('title', 'Gestion Géo-Spatiale')
@section('header_title', 'HQ Geo-Fencing')
@section('page_title', 'Zones de Service')
@section('page_subtitle', 'Carte interactive des 26 provinces — présence d\'utilisateurs et volume d\'activité réel.')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<div class="space-y-6 sm:space-y-8 pb-20 px-1 sm:px-0" x-data="rdcGeoMap()" x-init="init()">

    <!-- Résumé -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6">
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Provinces actives</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-900"><span x-text="activeCount"></span><span class="text-slate-300">/26</span></p>
        </div>
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6">
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Utilisateurs localisés</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-900" x-text="totalUsers"></p>
        </div>
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6">
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Activité totale</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-900" x-text="totalUsage"></p>
        </div>
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6">
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Province la plus active</p>
            <p class="text-sm sm:text-lg font-black text-slate-900 truncate" x-text="topProvince ? topProvince.name : '—'"></p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px] flex flex-col lg:flex-row">
        <!-- Provinces List -->
        <div class="w-full lg:w-80 border-b lg:border-b-0 lg:border-r border-slate-50 p-6 sm:p-8 flex flex-col max-h-[700px]">
            <h4 class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 sm:mb-6 shrink-0">Les 26 Provinces (RDC)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2 overflow-y-auto pr-2 custom-scrollbar pb-4">
                <template x-for="p in sortedProvinces" :key="p.name">
                    <button
                        @click="selectProvince(p.name)"
                        class="w-full px-5 py-3.5 sm:px-6 sm:py-4 rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-left flex items-center justify-between transition-all group border"
                        :class="[
                            p.is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100 hover:bg-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100 hover:bg-slate-100',
                            selected === p.name ? 'ring-2 ring-offset-1 ring-emerald-400' : ''
                        ]">
                        <span x-text="p.name"></span>
                        <div class="flex items-center gap-2">
                            <span class="text-[7px] px-1.5 py-0.5 rounded-md" :class="p.is_active ? 'bg-emerald-100' : 'bg-slate-200 text-slate-500'" x-text="p.is_active ? p.user_count + ' util.' : 'Inactif'"></span>
                            <i class="fas fa-check-circle text-xs" x-show="p.is_active"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Map -->
        <div class="flex-1 bg-slate-50 p-6 sm:p-8 flex flex-col min-h-[400px]">
            <div class="bg-white p-4 rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 sm:mb-8">
                <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto min-w-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl flex items-center justify-center shrink-0">
                        <i class="fas fa-location-crosshairs text-sm"></i>
                    </div>
                    <template x-if="!selected">
                        <span class="text-xs sm:text-sm font-black text-slate-400 uppercase truncate">Sélectionnez une province pour voir les détails</span>
                    </template>
                    <template x-if="selected && current()">
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-black text-slate-900 uppercase truncate" x-text="selected"></p>
                            <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">
                                <span x-text="current().user_count"></span> utilisateur(s) ·
                                <span x-text="current().usage_count"></span> activité(s) enregistrée(s)
                            </p>
                        </div>
                    </template>
                </div>
                <a :href="selected ? ('{{ route('admin.users.index') }}?province=' + encodeURIComponent(selected)) : '#'"
                   x-show="selected"
                   class="shrink-0 px-4 py-2.5 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-700 transition-all whitespace-nowrap">
                    Voir les utilisateurs
                </a>
            </div>

            <!-- Real map -->
            <div class="flex-1 rounded-[1.5rem] sm:rounded-[2.5rem] overflow-hidden border-2 sm:border-4 border-white shadow-inner" style="min-height: 380px;">
                <div id="rdc-map" style="height: 100%; min-height: 380px; width: 100%;"></div>
            </div>

            <!-- Breakdown for selected province -->
            <template x-if="selected && current()">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 sm:mt-8">
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Clients</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().users_by_type.client"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Artisans</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().users_by_type.artisan"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Chercheurs d'emploi</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().users_by_type.job_seeker"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Recruteurs</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().users_by_type.recruiter"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Missions</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().missions_count"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Demandes de service</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().service_requests_count"></p>
                    </div>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-slate-100 p-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Offres d'emploi</p>
                        <p class="text-lg font-black text-slate-900" x-text="current().job_offers_count"></p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl sm:rounded-2xl border border-emerald-100 p-4">
                        <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1">Total activité</p>
                        <p class="text-lg font-black text-emerald-700" x-text="current().usage_count"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function rdcGeoMap() {
    return {
        provinces: @json($provinceStats),
        selected: null,
        map: null,
        geoLayer: null,

        get sortedProvinces() {
            return [...this.provinces].sort((a, b) => b.usage_count - a.usage_count || a.name.localeCompare(b.name));
        },
        get activeCount() {
            return this.provinces.filter(p => p.is_active).length;
        },
        get totalUsers() {
            return this.provinces.reduce((sum, p) => sum + p.user_count, 0);
        },
        get totalUsage() {
            return this.provinces.reduce((sum, p) => sum + p.usage_count, 0);
        },
        get topProvince() {
            return this.sortedProvinces.find(p => p.usage_count > 0) || null;
        },
        get maxUsage() {
            return Math.max(1, ...this.provinces.map(p => p.usage_count));
        },

        current() {
            return this.provinces.find(p => p.name === this.selected) || null;
        },

        init() {
            this.map = L.map('rdc-map', {
                zoomControl: true,
                attributionControl: true,
                scrollWheelZoom: false,
            }).setView([-4.0, 23.5], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 10,
                minZoom: 4,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(this.map);

            fetch('{{ asset('data/rdc-provinces.geojson') }}')
                .then(res => res.json())
                .then(geojson => {
                    this.geoLayer = L.geoJSON(geojson, {
                        style: (feature) => this.styleFor(feature.properties.province),
                        onEachFeature: (feature, layer) => {
                            const name = feature.properties.province;
                            layer.bindTooltip(name, { sticky: true, direction: 'center', className: 'rdc-province-tooltip' });
                            layer.on('click', () => this.selectProvince(name));
                            layer.on('mouseover', () => { if (this.selected !== name) layer.setStyle({ weight: 2, color: '#0f172a' }); });
                            layer.on('mouseout', () => { if (this.selected !== name) layer.setStyle(this.styleFor(name)); });
                        },
                    }).addTo(this.map);

                    this.map.fitBounds(this.geoLayer.getBounds(), { padding: [12, 12] });
                });
        },

        colorFor(name) {
            const p = this.provinces.find(x => x.name === name);
            if (!p || !p.is_active) return '#e2e8f0'; // slate-200 : aucun utilisateur
            const intensity = p.usage_count / this.maxUsage;
            if (intensity > 0.66) return '#059669'; // emerald-600
            if (intensity > 0.33) return '#34d399'; // emerald-400
            if (intensity > 0)    return '#a7f3d0'; // emerald-200
            return '#d1fae5'; // emerald-100 : présence d'utilisateurs mais 0 activité mesurée
        },

        styleFor(name) {
            const isSelected = this.selected === name;
            return {
                fillColor: this.colorFor(name),
                fillOpacity: isSelected ? 0.95 : 0.8,
                color: isSelected ? '#0f172a' : '#ffffff',
                weight: isSelected ? 3 : 1,
            };
        },

        selectProvince(name) {
            this.selected = this.selected === name ? null : name;
            if (this.geoLayer) {
                this.geoLayer.eachLayer(l => l.setStyle(this.styleFor(l.feature.properties.province)));
            }
        },
    };
}
</script>
<style>
    .rdc-province-tooltip {
        font-weight: 800;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush
@endsection
