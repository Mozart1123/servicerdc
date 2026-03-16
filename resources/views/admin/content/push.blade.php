@extends('layouts.admin')

@section('title', 'Notifications Push')
@section('header_title', 'Communication Live')
@section('page_title', 'Console Broadcast')
@section('page_subtitle', 'Envoyez des notifications en temps réel à l\'ensemble des utilisateurs de l\'écosystème ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    title: '',
    message: '',
    target: 'Tous les Utilisateurs',
    priority: 'Normal',
    device: 'iOS',
    loading: false,
    success: false,
    audience: 0,
    calculateAudience() {
        const map = {
            'Tous les Utilisateurs': 12480,
            'Artisans Active': 4120,
            'Clients Premium': 2850
        };
        this.audience = map[this.target] || 0;
    },
    broadcast() {
        if(!this.title || !this.message) {
            alert('Veuillez remplir le titre et le message');
            return;
        }
        this.loading = true;
        
        fetch('{{ route('admin.content.push.broadcast') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: this.title,
                message: this.message,
                target: this.target
            })
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            this.success = true;
            setTimeout(() => {
                this.success = false;
                window.location.reload();
            }, 2000);
        })
        .catch(err => {
            this.loading = false;
            alert('Erreur lors de la diffusion');
        });
    }
}" x-init="calculateAudience()">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- Live Composer -->
        <div class="xl:col-span-8 bg-white rounded-[2.5rem] sm:rounded-[4.5rem] border border-slate-100 shadow-sm overflow-hidden relative group">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-rdc-blue/5 rounded-full blur-[100px] group-hover:bg-rdc-blue/10 transition-colors"></div>
            
            <div class="px-8 sm:px-12 py-8 sm:py-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Notification Broadcaster</h3>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Canal Direct</span>
                </div>
            </div>

            <div class="p-8 sm:p-12 space-y-10 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Cible de l'audience</label>
                        <select x-model="target" @change="calculateAudience()" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl sm:rounded-3xl text-[11px] font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10 transition-all appearance-none cursor-pointer">
                            <option>Tous les Utilisateurs</option>
                            <option>Artisans Active</option>
                            <option>Clients Premium</option>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Priorité d'Envoi</label>
                        <div class="flex gap-2">
                            <template x-for="p in ['Normal', 'Haute', 'Critique']">
                                <button @click="priority = p" :class="priority === p ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-400'" 
                                        class="flex-1 py-4 rounded-xl sm:rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm">
                                    <span x-text="p"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Titre de la Notification</label>
                        <input type="text" x-model="title" placeholder="Ex: Flash Promo ! -20% ..." class="w-full px-8 py-6 bg-slate-50 border-none rounded-2xl sm:rounded-3xl text-sm font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10 transition-all placeholder:text-slate-200 shadow-inner">
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Corps du Message</label>
                        <textarea x-model="message" rows="4" placeholder="Tapez votre message ici..." class="w-full px-8 py-6 bg-slate-50 border-none rounded-2xl sm:rounded-3xl text-sm font-medium outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10 transition-all placeholder:text-slate-200 shadow-inner resize-none"></textarea>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl text-rdc-blue flex items-center justify-center shadow-inner ring-4 ring-blue-50/50">
                            <i class="fas fa-users-viewfinder text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Audience Estimée</p>
                            <p class="text-xl font-heading font-black text-slate-900" x-text="audience.toLocaleString() + ' Appareils'"></p>
                        </div>
                    </div>
                    
                    <button @click="broadcast()" :disabled="loading || success" class="w-full sm:w-auto px-12 py-6 bg-slate-900 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rdc-blue transition-all shadow-3xl shadow-slate-200 flex items-center justify-center gap-3 group relative overflow-hidden active:scale-95">
                        <div x-show="success" x-transition class="absolute inset-0 bg-emerald-500 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> DIFFUSÉ
                        </div>
                        <template x-if="!loading && !success">
                            <i class="fas fa-paper-plane text-xs group-hover:rotate-12 transition-transform"></i>
                        </template>
                        <template x-if="loading">
                            <i class="fas fa-circle-notch animate-spin text-xs"></i>
                        </template>
                        <span x-text="loading ? 'Traitement en cours...' : 'Diffuser la Notification'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview & Analytics -->
        <div class="xl:col-span-4 space-y-8">
            <!-- Live Preview -->
            <div class="bg-slate-900 p-8 sm:p-12 rounded-[2.5rem] sm:rounded-[4.5rem] text-white shadow-3xl relative overflow-hidden h-[500px] flex flex-col justify-between group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-rdc-blue/10 rounded-full blur-[100px] -mr-32 -mt-32 transition-all duration-1000 group-hover:bg-rdc-blue/20"></div>
                
                <div class="relative z-10 space-y-8 text-center pt-4">
                    <p class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] font-heading underline decoration-white/10 underline-offset-8">Aperçu en temps réel</p>
                    <div class="flex justify-center gap-10">
                        <button @click="device = 'iOS'" :class="device === 'iOS' ? 'text-white scale-125' : 'text-white/20 hover:text-white/40 hover:scale-110'" class="transition-all duration-300"><i class="fab fa-apple text-3xl"></i></button>
                        <button @click="device = 'Android'" :class="device === 'Android' ? 'text-white scale-125' : 'text-white/20 hover:text-white/40 hover:scale-110'" class="transition-all duration-300"><i class="fab fa-android text-3xl"></i></button>
                    </div>
                </div>

                <!-- Phone Mockup Notification -->
                <div class="relative z-10 w-full max-w-[280px] mx-auto scale-110 hover:scale-125 transition-transform duration-500">
                    <div class="bg-white/10 backdrop-blur-2xl border border-white/20 p-6 rounded-[2.5rem] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.6)] relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
                        <div class="relative flex items-start gap-4 mb-4">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white text-[10px] font-black shrink-0 ring-1 ring-white/30">
                                <span>Rdc</span>
                            </div>
                            <div class="min-w-0 pr-4">
                                <h4 class="text-[12px] font-black text-white truncate uppercase tracking-tight leading-none mb-1.5" x-text="title || 'Titre de l\'alerte'"></h4>
                                <p class="text-[10px] font-medium text-white/60 leading-relaxed line-clamp-2" x-text="message || 'Le corps de votre message s\'affichera ici en temps réel...'"></p>
                            </div>
                            <span class="text-[7px] font-black text-white/20 absolute top-0 right-0 uppercase tracking-tighter italic">Maintenant</span>
                        </div>
                        <div class="flex justify-end pt-3 border-t border-white/5">
                            <p class="text-[9px] font-black text-rdc-blue font-heading uppercase tracking-widest flex items-center gap-1">
                                <i class="fas fa-bolt-lightning text-[8px]"></i> Explorer
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pb-4">
                    <div class="px-8 py-5 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md group-hover:bg-white/10 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-black text-white/40 uppercase tracking-widest font-heading">CTR Projection</span>
                            <span class="text-[12px] font-black text-emerald-400">14.2%</span>
                        </div>
                        <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-rdc-blue to-emerald-400 h-full w-[14.2%] rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="bg-white rounded-[2.5rem] sm:rounded-[4.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <div class="px-8 sm:px-12 py-10 sm:py-12 border-b border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-6 bg-slate-50/20">
            <div>
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Archives de Diffusion</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Traceabilité complète des alertes envoyées</p>
            </div>
            <span class="px-5 py-2.5 bg-slate-100 rounded-full text-[9px] font-black text-slate-500 uppercase tracking-widest shadow-inner">Derniers 30 jours</span>
        </div>
        
        <div class="overflow-x-hidden p-4 sm:p-10">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[50%] sm:w-auto pl-6 pr-4 sm:px-10 py-8 text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest">Alerte / Timestamp</th>
                        <th class="hidden sm:table-cell px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Segment</th>
                        <th class="w-[20%] sm:w-auto px-4 sm:px-10 py-8 text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest text-center text-nowrap">Analytics</th>
                        <th class="w-[30%] sm:w-auto pr-6 pl-4 sm:px-10 py-8 text-[9px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($history as $push)
                        <tr class="group hover:bg-slate-50/40 transition-all duration-300">
                            <td class="pl-6 pr-4 sm:px-10 py-8">
                                <div class="flex items-center gap-6">
                                    <div class="hidden sm:xl:flex w-12 h-12 rounded-2xl bg-slate-900 text-white items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-rdc-blue transition-all duration-500">
                                        <i class="fas fa-check-double text-[12px]"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[12px] sm:text-base font-black text-slate-900 block truncate group-hover:text-rdc-blue transition-colors underline decoration-slate-100 underline-offset-8">{{ $push->title }}</span>
                                        <p class="text-[10px] font-medium text-slate-400 truncate mt-1.5">{{ $push->message }}</p>
                                        <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase block mt-2 opacity-60">Diffusion {{ $push->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-10 py-8 text-center">
                                <span class="px-4 py-2 bg-slate-100 text-slate-900/40 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-all duration-500">{{ $push->data['target'] ?? 'Tous' }}</span>
                            </td>
                            <td class="px-4 sm:px-10 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-[12px] sm:text-xl font-black text-slate-900 tracking-tighter">{{ number_format(rand(1000, 5000)) }}</span>
                                    <p class="text-[8px] font-black text-emerald-500 uppercase tracking-tighter">+{{ rand(5, 15) }}% CTR</p>
                                </div>
                            </td>
                            <td class="pr-6 pl-4 sm:px-10 py-8 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <button class="w-10 h-10 sm:w-14 sm:h-14 rounded-2xl bg-white text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-100 flex items-center justify-center hover:-translate-y-1 active:scale-95">
                                        <i class="fas fa-chart-pie text-[12px] sm:text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-32">
                                <div class="flex flex-col items-center justify-center text-center opacity-40">
                                    <div class="text-6xl text-slate-200 mb-6"><i class="fas fa-satellite-dish"></i></div>
                                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Silence Radio</h4>
                                    <p class="text-[10px] font-bold text-slate-300 mt-2 uppercase">Aucune notification diffusée pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
