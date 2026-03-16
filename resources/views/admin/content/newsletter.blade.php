@extends('layouts.admin')

@section('title', 'Gestion Newsletter')
@section('header_title', 'Engagement Communautaire')
@section('page_title', 'Campagnes E-mail')
@section('page_subtitle', 'Gérez vos listes de diffusion et concevez des newsletters percutantes pour vos abonnés.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    showModal: false,
    newCampaign: { subject: '', audience: 'Tous', content: '' },
    loading: false,
    sendCampaign() {
        this.loading = true;
        fetch('{{ route('admin.content.newsletter.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.newCampaign)
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            this.showModal = false;
            window.location.reload();
        });
    },
    duplicateCampaign(id) {
        fetch(`/admin/content/newsletter/${id}/duplicate`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    },
    deleteCampaign(id) {
        if(confirm('Supprimer cette campagne ?')) {
            fetch(`/admin/content/newsletter/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    }
}">
    <!-- HUD Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group border border-slate-800">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rdc-blue/10 rounded-full blur-2xl group-hover:bg-rdc-blue/20 transition-colors"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <p class="text-[8px] font-black text-white/30 uppercase tracking-[0.2em]">Total Abonnés</p>
                <h4 class="text-3xl font-heading font-black text-white">12,482</h4>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[8px] font-black text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full">+4.2%</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Taux d'Ouverture</p>
                <h4 class="text-3xl font-heading font-black text-slate-900">42.8%</h4>
                <div class="w-full bg-slate-50 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full w-[42.8%] rounded-full"></div>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Taux de Clic</p>
                <h4 class="text-3xl font-heading font-black text-rdc-blue">18.4%</h4>
                <div class="w-full bg-slate-50 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-rdc-blue h-full w-[18.4%] rounded-full"></div>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-colors"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Désabonnements</p>
                <h4 class="text-3xl font-heading font-black text-rose-500">0.4%</h4>
                <p class="text-[8px] font-bold text-slate-300 mt-1 uppercase tracking-tighter italic">Seuil de sécurité respecté</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
        <!-- Lists Management -->
        <div class="lg:col-span-4 bg-white p-8 sm:p-10 rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-8 sm:mb-12">
                <h4 class="text-[10px] sm:text-xs font-black text-slate-900 uppercase tracking-widest">Listes de Diffusion</h4>
                <button @click="alert('Module de gestion des listes en cours de déploiement')" class="w-8 h-8 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-xs hover:bg-slate-900 hover:text-white transition-all">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="space-y-4 sm:space-y-6">
                <div @click="alert('Chargement de la liste Utilisateurs...')" class="group p-4 sm:p-6 bg-slate-50/50 rounded-2xl sm:rounded-3xl border border-slate-100 flex items-center justify-between hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center shadow-inner">
                            <i class="fas fa-users text-sm sm:text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-sm font-black text-slate-900 uppercase">Utilisateurs</p>
                            <p class="text-[8px] sm:text-[10px] font-bold text-slate-400">8,248 abonnés</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-slate-200 group-hover:text-rdc-blue transition-colors"></i>
                </div>
                <div @click="alert('Chargement de la liste Artisans...')" class="group p-4 sm:p-6 bg-slate-50/50 rounded-2xl sm:rounded-3xl border border-slate-100 flex items-center justify-between hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                            <i class="fas fa-screwdriver-wrench text-sm sm:text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-sm font-black text-slate-900 uppercase">Artisans</p>
                            <p class="text-[8px] sm:text-[10px] font-bold text-slate-400">4,124 abonnés</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-slate-200 group-hover:text-emerald-500 transition-colors"></i>
                </div>
            </div>
            
            <div class="mt-10 sm:mt-12 p-6 rounded-3xl bg-slate-900 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:bg-white/10 transition-colors"></div>
                <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-2">Conseil Stratégique</p>
                <p class="text-[11px] font-medium leading-relaxed mb-4">La segmentation de vos listes augmente votre taux de clic de <span class="text-emerald-400 font-black">24%</span> en moyenne.</p>
                <a href="javascript:void(0)" @click="alert('Redirection vers le centre d\'aide...')" class="text-[9px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir les guides</a>
            </div>
        </div>

        <!-- Recent Campaigns -->
        <div class="lg:col-span-8 bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative">
            <div class="px-8 sm:px-10 py-8 sm:py-10 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Campagnes Récentes</h3>
                <button @click="showModal = true" class="px-6 py-3.5 bg-slate-900 text-white text-[9px] font-black uppercase rounded-2xl tracking-widest hover:bg-rdc-blue transition-all shadow-xl shadow-slate-200 flex items-center gap-2 group">
                    <i class="fas fa-paper-plane group-hover:rotate-12 transition-transform"></i> Nouvelle Campagne
                </button>
            </div>
            
            <div class="overflow-x-hidden p-2 sm:p-4">
                <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="w-[55%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Campagne / État</th>
                            <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Abonnés</th>
                            <th class="w-[20%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Score</th>
                            <th class="w-[25%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($campaigns as $campaign)
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="pl-4 pr-2 sm:px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="hidden sm:flex w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue items-center justify-center shrink-0">
                                            <i class="fas fa-envelope-open-text"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-[11px] sm:text-sm font-black text-slate-900 block truncate group-hover:text-rdc-blue transition-colors">{{ $campaign->subject }}</span>
                                            <span class="flex items-center gap-1.5 mt-1">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $campaign->status == 'sent' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                <span class="text-[7px] sm:text-[9px] font-black {{ $campaign->status == 'sent' ? 'text-emerald-500' : 'text-slate-400' }} uppercase">
                                                    {{ $campaign->status == 'sent' ? 'Envoyé ' . $campaign->sent_at?->diffForHumans() : 'Brouillon' }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-8 py-6 text-center">
                                    <span class="text-[10px] font-black text-slate-900 uppercase">{{ $campaign->audience }}</span>
                                </td>
                                <td class="px-2 sm:px-8 py-6 text-center">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase ring-1 ring-emerald-100">
                                        <i class="fas fa-bolt text-[8px]"></i> {{ $campaign->status == 'sent' ? '98%' : '-' }}
                                    </div>
                                </td>
                                <td class="pr-4 pl-2 sm:px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="duplicateCampaign({{ $campaign->id }})" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-purple-500 transition-all"><i class="fas fa-copy text-[10px]"></i></button>
                                        <button @click="deleteCampaign({{ $campaign->id }})" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-rdc-red transition-all"><i class="fas fa-trash text-[10px]"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-20 text-center text-slate-400 font-black uppercase text-[10px]">Aucune campagne enregistrée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal New Campaign -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="showModal = false" class="bg-white w-full max-w-xl rounded-[3rem] shadow-3xl overflow-hidden p-10">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-8">Nouvelle Campagne</h3>
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Sujet</label>
                    <input type="text" x-model="newCampaign.subject" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Audience</label>
                    <select x-model="newCampaign.audience" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10">
                        <option>Tous</option>
                        <option>Artisans</option>
                        <option>Clients</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Contenu Email (HTML possible)</label>
                    <textarea x-model="newCampaign.content" rows="6" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-medium outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10"></textarea>
                </div>
                <div class="pt-4 flex gap-4">
                    <button @click="showModal = false" class="flex-1 py-5 bg-slate-100 text-slate-400 font-black rounded-2xl text-xs uppercase tracking-widest">Brouillon</button>
                    <button @click="sendCampaign()" :disabled="loading" class="flex-1 py-5 bg-slate-900 text-white font-black rounded-2xl text-xs uppercase tracking-widest hover:bg-rdc-blue transition-all disabled:opacity-50">
                        <span x-text="loading ? 'Envoi...' : 'Diffuser la Campagne'">Diffuser la Campagne</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
