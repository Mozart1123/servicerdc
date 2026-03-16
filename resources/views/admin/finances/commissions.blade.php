@extends('layouts.admin')

@section('title', 'Gestion Commissions')
@section('header_title', 'Revenus Plateforme')
@section('page_title', 'Régie Directe')
@section('page_subtitle', 'Consultez et ajustez les taux de commission prélevés sur les transactions de mise en relation.')

@section('content')
<div class="space-y-8 pb-20" x-data="{ 
    commissionRate: {{ $commissionRate ?? 15 }},
    showModal: false,
    newRate: {{ $commissionRate ?? 15 }},
    loading: false,
    toast: { show: false, message: '', type: 'success' },
    showToast(msg, type = 'success') {
        this.toast = { show: true, message: msg, type: type };
        setTimeout(() => { this.toast.show = false; }, 3000);
    },
    async updateRate() {
        if (this.newRate < 0 || this.newRate > 100) {
            this.showToast('Le taux doit être entre 0 et 100.', 'error');
            return;
        }
        this.loading = true;
        try {
            const response = await fetch('{{ route('admin.finances.commissions.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ rate: this.newRate })
            });
            const data = await response.json();
            if (data.success) {
                this.commissionRate = data.new_rate;
                this.showModal = false;
                this.showToast(data.message);
            } else {
                this.showToast(data.message || 'Une erreur est survenue.', 'error');
            }
        } catch (e) {
            this.showToast('Erreur lors de la communication avec le serveur.', 'error');
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] px-6 py-3 rounded-2xl shadow-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 border"
         :class="toast.type === 'success' ? 'bg-emerald-500 text-white border-emerald-400' : 'bg-rose-500 text-white border-rose-400'"
         style="display: none;">
        <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
        <span x-text="toast.message"></span>
    </div>

    <!-- Modal Adjustment -->
    <div x-show="showModal" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 sm:p-10 shadow-3xl border border-slate-100 transform"
             @click.away="!loading && (showModal = false)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-sm ring-1 ring-slate-100">
                    <i class="fas fa-sliders"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Ajuster Taux Global</h3>
            </div>

            <p class="text-[11px] text-slate-400 font-medium leading-relaxed mb-8">
                Ce taux sera appliqué instantanément sur toutes les futures transactions de la plateforme.
            </p>

            <div class="relative mb-10">
                <input type="number" 
                       x-model="newRate" 
                       min="0" max="100"
                       class="w-full bg-slate-50 border-none rounded-2xl py-6 px-8 text-3xl font-heading font-black text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"
                       :disabled="loading">
                <span class="absolute right-8 top-1/2 -translate-y-1/2 text-2xl font-black text-slate-300">%</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button @click="showModal = false" 
                        :disabled="loading"
                        class="py-4 bg-slate-50 text-slate-400 font-black rounded-xl text-[9px] uppercase tracking-widest hover:bg-slate-100 transition-all">
                    Annuler
                </button>
                <button @click="updateRate()" 
                        :disabled="loading"
                        class="py-4 bg-slate-900 text-white font-black rounded-xl text-[9px] uppercase tracking-widest shadow-2xl shadow-slate-200 hover:bg-rdc-blue disabled:opacity-50 flex items-center justify-center gap-2 transition-all">
                    <template x-if="loading">
                        <i class="fas fa-circle-notch animate-spin"></i>
                    </template>
                    <span x-text="loading ? '⏳ Maj...' : 'Confirmer'">Confirmer</span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Interactive Rate Card -->
        <div class="bg-white p-8 sm:p-12 rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-20 -top-20 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-colors"></div>
            
            <div class="flex items-center gap-3 mb-8 sm:mb-10">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm shadow-sm ring-1 ring-emerald-100">
                    <i class="fas fa-percent"></i>
                </div>
                <h4 class="text-[10px] sm:text-xs font-black text-slate-900 uppercase tracking-widest">Taux de Commission Direct</h4>
            </div>

            <div class="flex items-center gap-6 mb-10 sm:mb-12">
                <span class="text-5xl sm:text-7xl font-heading font-black text-slate-900 tracking-tighter group-hover:scale-105 transition-transform duration-500 origin-left" x-text="commissionRate">15</span>
                <span class="text-xl sm:text-2xl text-slate-300 -ml-4 sm:-ml-5 relative z-10">%</span>
                <div class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                    <i class="fas fa-arrow-trend-up text-xs"></i> +2%
                </div>
            </div>

            <p class="text-[11px] sm:text-xs text-slate-400 font-medium leading-relaxed mb-10 max-w-xs">Le prélèvement standard de <span x-text="commissionRate">15</span>% est appliqué sur toutes les finalisations de prestations directes via la plateforme.</p>
            
            <button @click="showModal = true; newRate = commissionRate" class="w-full py-4 sm:py-5 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200 hover:bg-rdc-blue hover:-translate-y-1 transition-all duration-300">
                Ajuster le barème global
            </button>
        </div>

        <!-- Projection Analytics Card -->
        <div class="bg-slate-900 p-8 sm:p-12 rounded-[2.5rem] sm:rounded-[4rem] text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-900 via-slate-900/90 to-slate-800 opacity-90"></div>
            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-rdc-blue/10 rounded-full blur-[80px]"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8 sm:mb-12">
                    <h4 class="text-[9px] sm:text-[10px] font-black text-white/40 uppercase tracking-widest">Projection de revenus</h4>
                    <span class="text-[8px] font-black bg-white/5 px-2 py-1 rounded-md text-white/60 tracking-tighter">PREMIUM DATA HUD v1.02</span>
                </div>

                <div class="flex items-end gap-1.5 h-32 sm:h-44 mb-10 sm:mb-14 overflow-hidden mask-fade-edges">
                    @foreach([20, 35, 25, 55, 40, 75, 50, 85, 95, 80, 90, 115] as $index => $h)
                        <div class="flex-1 bg-white/5 rounded-full hover:bg-rdc-blue transition-all duration-300 hover:scale-y-110 {{ $index < 4 ? 'opacity-20' : ($index < 8 ? 'opacity-50' : 'opacity-100') }}" 
                             style="height: {{ ($h/120)*100 }}%"></div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between gap-6 border-t border-white/5 pt-8 sm:pt-10">
                    <div class="min-w-0">
                        <h5 class="text-2xl sm:text-3xl font-heading font-black text-white truncate leading-none mb-1">12,480.00 $</h5>
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest truncate">Revenus estimés (Mars)</p>
                    </div>
                    <div class="text-right min-w-0">
                        <h5 class="text-2xl sm:text-3xl font-heading font-black text-emerald-400 truncate leading-none mb-1">+18.4%</h5>
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest truncate">Vs Mois Précédent</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
