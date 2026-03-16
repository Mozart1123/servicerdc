@extends('layouts.admin')

@section('title', 'Comptes Suspendus')
@section('header_title', 'Signalements & Bannissements')
@section('page_title', 'Surveillance Utilisateurs')
@section('page_subtitle', 'Gérez les comptes ayant enfreint les règles ou signalés par la communauté.')

@section('content')
<div class="space-y-8 pb-20" x-data="flaggedManager()">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center transition-all">
            <div class="w-12 h-12 border-4 border-slate-100 border-t-rdc-blue rounded-full animate-spin"></div>
        </div>

        <!-- Scaled Responsive Table -->
        <div class="relative min-h-[400px] overflow-x-hidden">
            <table class="w-full text-left border-collapse table-fixed lg:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest w-[45%] sm:w-auto">Utilisateur</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-[25%] sm:w-auto">Infraction</th>
                        <th class="pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-[30%] sm:w-auto">Actions HQ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="user in users" :key="user.id">
                        <tr class="group hover:bg-red-50/20 transition-all duration-300">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                    <div class="relative shrink-0">
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center grayscale group-hover:grayscale-0 transition-all shadow-sm overflow-hidden">
                                            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=EF4444&color=fff&size=128`" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 sm:w-5 h-3 sm:h-5 bg-rdc-red text-[6px] sm:text-[8px] flex items-center justify-center text-white rounded-full border border-white font-black animate-pulse">!</div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate" x-text="user.name"></p>
                                        <p class="text-[8px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-tight truncate mt-px" x-text="user.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                <span class="px-1.5 sm:px-3 py-1 sm:py-1.5 bg-red-100 text-rdc-red text-[7px] sm:text-[9px] font-black uppercase rounded sm:rounded-xl tracking-tighter sm:tracking-widest border border-red-200 inline-block">Suspension</span>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="flex items-center justify-end gap-1.5 sm:gap-3">
                                    <button @click="reactivate(user)" class="px-2 sm:px-6 py-2 sm:py-2.5 bg-emerald-500 text-white text-[7px] sm:text-[9px] font-black uppercase tracking-tighter sm:tracking-widest rounded-lg sm:rounded-xl hover:bg-emerald-600 active:scale-95 transition-all">Réactiver</button>
                                    <button @click="confirmDelete(user.id)" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg sm:rounded-xl bg-white border border-slate-100 text-rdc-red active:scale-95 transition-all">
                                        <i class="fas fa-trash-alt text-[10px] sm:text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <template x-if="users.length === 0 && !loading">
            <div class="flex-1 flex flex-col items-center justify-center text-center p-12 min-h-[450px]">
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight text-center">Communauté Saine</h4>
                <p class="text-slate-400 max-w-xs mx-auto mt-2 font-medium text-center">Aucun utilisateur n'est actuellement suspendu. Votre plateforme est en règle.</p>
            </div>
        </template>
        
        <!-- Pagination -->
        <template x-if="pagination.last_page > 1">
            <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between gap-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <span class="text-slate-900" x-text="users.length"></span> suspendu(s) sur cette page
                </p>
                <div class="flex items-center gap-2">
                    <button @click="changePage(pagination.current_page - 1)" 
                            :disabled="pagination.current_page === 1"
                            class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue disabled:opacity-30 transition-all">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </button>
                    <button @click="changePage(pagination.current_page + 1)" 
                            :disabled="pagination.current_page === pagination.last_page"
                            class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue disabled:opacity-30 transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function flaggedManager() {
    return {
        users: [],
        loading: false,
        page: 1,
        pagination: {},

        init() {
            this.fetchUsers();
        },

        fetchUsers() {
            this.loading = true;
            fetch(`/admin/users-mgmt/flagged?page=${this.page}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.users = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total
                };
                this.loading = false;
            });
        },

        reactivate(user) {
            fetch(`/admin/users/${user.id}/toggle-status-api`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    this.users = this.users.filter(u => u.id !== user.id);
                    window.dispatchEvent(new CustomEvent('users-updated'));
                    if (this.users.length === 0 && this.page > 1) {
                        this.page--;
                        this.fetchUsers();
                    }
                }
            });
        },

        confirmDelete(id) {
            if (confirm('Supprimer définitivement ce compte ? Cette action est irréversible.')) {
                fetch(`/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    this.fetchUsers();
                    window.dispatchEvent(new CustomEvent('users-updated'));
                });
            }
        },

        changePage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.page = p;
            this.fetchUsers();
        }
    }
}
</script>
@endsection
