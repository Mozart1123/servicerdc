@extends('layouts.admin')

@section('title', 'Centre de Support')
@section('header_title', 'Tickets & Assistance')
@section('page_title', 'Support Utilisateurs')
@section('page_subtitle', 'Gérez les demandes d\'assistance et les tickets techniques de la communauté ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    showModal: false,
    selectedTicket: null,
    reply: '',
    loading: false,
    openTicket(ticket) {
        this.selectedTicket = ticket;
        this.reply = '';
        this.showModal = true;
    },
    sendReply() {
        if(!this.reply) return;
        this.loading = true;
        fetch(`/admin/support-hq/tickets/${this.selectedTicket.id}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reply: this.reply })
        })
        .then(() => window.location.reload());
    },
    closeTicket(id) {
        if(confirm('Fermer ce ticket ?')) {
            fetch(`/admin/support-hq/tickets/${id}/close`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    }
}">
    <!-- Support Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-3 sm:gap-4 group transition-all hover:border-amber-100">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-ticket"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En attente</p>
                <h4 class="text-lg sm:text-xl font-black text-slate-900">{{ number_format($tickets->where('status', 'open')->count()) }}</h4>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-3 sm:gap-4 group transition-all hover:border-blue-100">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">En cours</p>
                <h4 class="text-lg sm:text-xl font-black text-slate-900">{{ number_format($tickets->where('status', 'pending')->count()) }}</h4>
            </div>
        </div>
        <div class="bg-emerald-500 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-lg shadow-emerald-500/10 flex items-center gap-3 sm:gap-4 text-white group hover:scale-[1.02] transition-transform">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:rotate-12 transition-transform">
                <i class="fas fa-check"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-white/60 uppercase tracking-widest truncate">Résolus</p>
                <h4 class="text-lg sm:text-xl font-black">{{ number_format($tickets->where('status', 'closed')->count()) }}</h4>
            </div>
        </div>
        <div class="bg-slate-900 p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2rem] shadow-xl flex items-center gap-3 sm:gap-4 text-white group hover:scale-[1.02] transition-transform">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center text-lg sm:text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-clock"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[7px] sm:text-[9px] font-black text-white/40 uppercase tracking-widest truncate">Moyen</p>
                <h4 class="text-lg sm:text-xl font-black">4h 12m</h4>
            </div>
        </div>
    </div>

    <!-- Tickets Feed -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
            <div class="flex items-center gap-4">
                <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Maintenance Support</h3>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded animate-pulse">Live Feed</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative group w-full sm:w-64">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" placeholder="Rechercher un ticket..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-100 rounded-xl text-xs font-bold focus:ring-4 focus:ring-rdc-blue/5 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($tickets as $ticket)
                <div @click="openTicket({{ $ticket }})" class="px-6 sm:px-10 py-6 sm:py-8 flex flex-col sm:flex-row items-start gap-4 sm:gap-8 group hover:bg-slate-50/50 transition-colors cursor-pointer">
                    <div class="hidden sm:block w-14 h-14 rounded-2xl bg-white border border-slate-100 overflow-hidden shrink-0 shadow-sm">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name) }}&background=random" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex items-start justify-between mb-2 gap-2">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <h4 class="text-xs sm:text-sm font-black text-slate-900 leading-tight">{{ $ticket->subject }}</h4>
                                @if($ticket->priority == 'urgent')
                                    <span class="inline-block w-fit px-2 py-0.5 bg-rose-50 text-rose-600 text-[7px] sm:text-[8px] font-black uppercase tracking-widest rounded-md">Urgent</span>
                                @endif
                            </div>
                            <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest italic shrink-0">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-slate-400 font-medium line-clamp-2 sm:line-clamp-1">{{ $ticket->message }}</p>
                        <div class="mt-3 sm:mt-4 flex items-center justify-between sm:justify-start gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] sm:text-[10px] font-black text-slate-900 uppercase">{{ $ticket->user->name }}</span>
                            </div>
                            <span class="hidden sm:inline w-1 h-1 bg-slate-200 rounded-full"></span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div class="hidden sm:flex flex-col items-end gap-2">
                        <span class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest
                            {{ $ticket->status == 'open' ? 'bg-blue-50 text-rdc-blue' : ($ticket->status == 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400') }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-32 text-center opacity-40">
                    <i class="fas fa-ticket-alt text-6xl text-slate-200 mb-6"></i>
                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucun ticket</h4>
                    <p class="text-[10px] font-bold text-slate-300 mt-2">Le support est actuellement disponible</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Reply Modal -->
    <template x-if="showModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div @click.away="showModal = false" class="bg-white w-full max-w-xl rounded-[3rem] shadow-3xl overflow-hidden p-10">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight" x-text="selectedTicket.subject"></h3>
                        <p class="text-xs font-bold text-slate-400 uppercase mt-1">Ticket #TKT-<span x-text="selectedTicket.id"></span></p>
                    </div>
                    <button @click="showModal = false" class="text-slate-300 hover:text-slate-900"><i class="fas fa-times"></i></button>
                </div>

                <div class="p-6 bg-slate-50 rounded-3xl mb-8">
                    <p class="text-xs text-slate-600 leading-relaxed" x-text="selectedTicket.message"></p>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Votre Réponse</label>
                    <textarea x-model="reply" rows="4" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-medium outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10" placeholder="Écrire une réponse..."></textarea>
                </div>

                <div class="pt-8 flex gap-4">
                    <button @click="closeTicket(selectedTicket.id)" class="px-8 py-5 bg-rose-50 text-rose-500 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">Fermer le Ticket</button>
                    <button @click="sendReply()" :disabled="loading" class="flex-1 py-5 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rdc-blue transition-all disabled:opacity-50">
                        <span x-text="loading ? 'Envoi...' : 'Envoyer la Réponse'">Envoyer la Réponse</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
