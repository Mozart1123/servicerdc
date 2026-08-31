@extends('layouts.admin')

@section('title', 'Validation Utilisateurs')
@section('header_title', 'Approbation des Comptes')
@section('page_title', 'Comptes en Attente')
@section('page_subtitle', 'Vérifiez et activez les nouveaux utilisateurs souhaitant rejoindre la plateforme.')

@section('content')
<div class="space-y-8 pb-20" x-data="pendingManager({{ json_encode($users->items()) }}, {{ json_encode(['current_page' => $users->currentPage(), 'last_page' => $users->lastPage(), 'total' => $users->total()]) }})">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center transition-all">
            <div class="w-12 h-12 border-4 border-slate-100 border-t-rdc-blue rounded-full animate-spin"></div>
        </div>

        <!-- Scaled Responsive Table Container -->
        <div class="relative min-h-[400px] overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-4 pr-2 sm:px-8 py-4 sm:py-6 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-2 sm:px-8 py-4 sm:py-6 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest hidden min-[480px]:table-cell">Date d'inscription</th>
                        <th class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action Administrative</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="user in users" :key="user.id">
                        <tr class="group hover:bg-blue-50/20 transition-all duration-300">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform overflow-hidden">
                                        <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=29B6D1&color=fff&size=128`" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-black text-slate-900 truncate" x-text="user.name"></p>
                                        <p class="text-[9px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-tight truncate mt-px" x-text="user.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 hidden min-[480px]:table-cell">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-black text-slate-500 font-mono" x-text="formatDate(user.created_at)"></span>
                                    <span class="text-[9px] text-slate-400 uppercase tracking-widest" x-text="timeAgo(user.created_at)"></span>
                                </div>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                                    <template x-if="user.artisan_level && user.artisan_level.verification_status === 'pending'">
                                        <a href="{{ route('admin.verifications.index') }}" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-amber-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg sm:rounded-xl hover:bg-amber-600 transition-all inline-flex items-center gap-1">
                                            <i class="fas fa-id-card"></i> Doc Artisan
                                        </a>
                                    </template>
                                    <template x-if="user.identity_verification && user.identity_verification.verification_status === 'pending'">
                                        <a href="{{ route('admin.verifications-general.index') }}" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-blue-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg sm:rounded-xl hover:bg-blue-600 transition-all inline-flex items-center gap-1">
                                            <i class="fas fa-id-card"></i> Doc Recruteur
                                        </a>
                                    </template>
                                    <button @click="approve(user)" :disabled="processing.has(user.id)" class="px-3 sm:px-6 py-2 sm:py-2.5 bg-emerald-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg sm:rounded-xl hover:bg-emerald-600 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">Activer Compte</button>
                                    <button @click="reject(user)" :disabled="processing.has(user.id)" class="px-3 sm:px-6 py-2 sm:py-2.5 bg-white border border-slate-200 text-rdc-red text-[8px] sm:text-[9px] font-black uppercase tracking-widest rounded-lg sm:rounded-xl hover:bg-red-50 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">Refuser</button>
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
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner animate-pulse">
                    <i class="fas fa-check-double"></i>
                </div>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Ciel Dégagé !</h4>
                <p class="text-slate-400 max-w-xs mx-auto mt-2 font-medium">Tous les nouveaux comptes ont été traités. Aucun utilisateur en attente.</p>
            </div>
        </template>
        
        <!-- Pagination -->
        <template x-if="pagination.last_page > 1">
            <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between gap-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <span class="text-slate-900" x-text="users.length"></span> en attente sur cette page
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
function pendingManager(initialUsers = [], initialPagination = {}) {
    return {
        users: initialUsers,
        loading: false,
        processing: new Set(),
        page: initialPagination.current_page || 1,
        pagination: initialPagination,

        init() {
            if (this.users.length === 0) {
                this.fetchUsers();
            }
        },

        fetchUsers() {
            this.loading = true;
            fetch(`{{ route('admin.users-mgmt.pending') }}?page=${this.page}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.users = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total
                };
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                alert('Erreur réseau : impossible de charger les comptes en attente.');
            });
        },

        approve(user) {
            if (this.processing.has(user.id)) return;
            this.processing.add(user.id);
            fetch(`{{ route('admin.users-mgmt.pending.approve-api', ':id') }}`.replace(':id', user.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                this.processing.delete(user.id);
                if (data.success) {
                    this.users = this.users.filter(u => u.id !== user.id);
                    document.dispatchEvent(new CustomEvent('users-updated'));
                } else {
                    alert(data.message || "Erreur lors de l'activation du compte.");
                }
            })
            .catch(() => {
                this.processing.delete(user.id);
                alert(`Erreur réseau : impossible d'activer le compte de ${user.name}. Réessayez.`);
            });
        },

        reject(user) {
            if (!confirm(`Refuser l'inscription de ${user.name} ?`)) return;
            if (this.processing.has(user.id)) return;
            this.processing.add(user.id);
            fetch(`{{ route('admin.users-mgmt.pending.reject-api', ':id') }}`.replace(':id', user.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                this.processing.delete(user.id);
                if (data.success) {
                    this.users = this.users.filter(u => u.id !== user.id);
                } else {
                    alert(data.message || "Erreur lors du refus de l'inscription.");
                }
            })
            .catch(() => {
                this.processing.delete(user.id);
                alert(`Erreur réseau : impossible de refuser l'inscription de ${user.name}. Réessayez.`);
            });
        },

        changePage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.page = p;
            this.fetchUsers();
        },

        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        timeAgo(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            if (diffInSeconds < 60) return "À l'instant";
            if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
            if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)} h`;
            return `Il y a ${Math.floor(diffInSeconds / 86400)} j`;
        }
    }
}
</script>
@endsection
