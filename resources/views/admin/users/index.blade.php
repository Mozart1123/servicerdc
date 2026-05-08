@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')
@section('header_title', 'Utilisateurs')
@section('page_title', 'Gestion de la Communauté')
@section('page_subtitle', 'Gérez les accès, les rôles et la modération des utilisateurs de la plateforme.')

@section('content')
<div class="space-y-8 pb-20" x-data="userManager()">
    <!-- Stats Grid (Summary) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-wrap">Total Utilisateurs</span>
                <i class="fas fa-users text-rdc-blue"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900" x-text="stats.total">--</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Actifs</span>
                <i class="fas fa-user-check text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900" x-text="stats.active">--</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Suspendus</span>
                <i class="fas fa-user-slash text-rdc-red"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900" x-text="stats.suspended">--</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vérifiés KYC</span>
                <i class="fas fa-id-card text-amber-500"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900" x-text="stats.verified">--</h3>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 sm:p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-4 flex-1 min-w-[300px]">
            <div class="relative flex-1 group">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                <input type="text" x-model.debounce.500ms="search" placeholder="Nom, email, téléphone..." 
                       class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white transition-all outline-none">
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rôle:</span>
                <select x-model="role" class="bg-transparent border-none text-xs font-black text-slate-900 focus:ring-0 cursor-pointer uppercase tracking-tight">
                    <option value="">Tous</option>
                    <option value="user">Utilisateurs</option>
                    <option value="admin">Admins</option>
                    <option value="super_admin">Super Admins</option>
                </select>
            </div>

            <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut:</span>
                <select x-model="status" class="bg-transparent border-none text-xs font-black text-slate-900 focus:ring-0 cursor-pointer uppercase tracking-tight">
                    <option value="">Tous</option>
                    <option value="active">Actif</option>
                    <option value="suspended">Suspendu</option>
                    <option value="pending">En attente</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center transition-all">
            <div class="w-12 h-12 border-4 border-slate-100 border-t-rdc-blue rounded-full animate-spin"></div>
        </div>

        <!-- Scaled Responsive Table (Desktop as Source of Truth) -->
        <div class="relative min-h-[400px] overflow-x-hidden">
            <table class="w-full text-left border-collapse table-fixed lg:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest w-[40%] sm:w-auto">Utilisateur</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest w-[15%] sm:w-auto">Rôle</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest hidden min-[480px]:table-cell">Inscription</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-[20%] sm:w-auto">Statut</th>
                        <th class="pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-[25%] sm:w-auto">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-blue-50/20 transition-colors group">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                    <div class="relative shrink-0">
                                        <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=${user.role === 'admin' || user.role === 'super_admin' ? '007FFF' : 'F1F5F9'}&color=${user.role === 'admin' || user.role === 'super_admin' ? 'fff' : '64748B'}`" 
                                             class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl shadow-sm border border-slate-100" alt="">
                                        <template x-if="user.status === 'active'">
                                            <span class="absolute -bottom-0.5 -right-0.5 w-2 sm:w-4 h-2 sm:h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] sm:text-sm font-black text-slate-900 group-hover:text-rdc-blue transition-colors truncate" x-text="user.name"></p>
                                        <p class="text-[8px] sm:text-[11px] text-slate-400 font-bold truncate mt-px sm:mt-0.5" x-text="user.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6">
                                <span :class="{
                                    'bg-slate-900 text-white': user.role === 'super_admin',
                                    'bg-blue-50 text-rdc-blue border border-blue-100': user.role === 'admin',
                                    'bg-slate-100 text-slate-600': user.role === 'user'
                                }" class="px-1.5 sm:px-3 py-1 sm:py-1.5 text-[7px] sm:text-[9px] font-black rounded-lg sm:rounded-xl uppercase tracking-widest inline-block text-center min-w-[35px] sm:min-w-0" 
                                x-text="user.role === 'super_admin' ? 'S-Admin' : (user.role === 'admin' ? 'Admin' : 'Util')"></span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 hidden min-[480px]:table-cell">
                                <div class="flex flex-col gap-0.5 sm:gap-1">
                                    <p class="text-[10px] sm:text-[11px] font-black text-slate-600" x-text="formatDate(user.created_at)"></p>
                                    <p class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-tighter" x-text="timeAgo(user.created_at)"></p>
                                </div>
                            </td>
                            <td class="px-1 sm:px-8 py-4 sm:py-6 text-center">
                                <button @click="toggleStatus(user)" 
                                        :class="user.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-rdc-red'"
                                        class="px-2 sm:px-4 py-1.5 sm:py-2 text-[8px] sm:text-[10px] font-black uppercase rounded-lg sm:rounded-xl tracking-tighter sm:tracking-widest hover:scale-105 active:scale-95 transition-all w-full sm:w-auto">
                                    <span x-text="user.status === 'active' ? 'Actif' : 'Susp'"></span>
                                </button>
                            </td>
                            <td class="pr-4 pl-1 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="flex items-center justify-end gap-1 sm:gap-2">
                                    <!-- Always show Edit Option -->
                                    <a :href="`/admin/users/${user.id}/edit`" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg sm:rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-rdc-blue transition-all" title="Modifier">
                                        <i class="fas fa-edit text-[10px] sm:text-sm"></i>
                                    </a>
                                    
                                    <!-- Standardized Deletion Logic: 
                                         Show disabled button for Super Admins and Self to maintain UI consistency. -->
                                    <template x-if="user.role !== 'super_admin' && user.id != {{ auth()->id() }}">
                                        <button @click="confirmDelete(user.id)" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg sm:rounded-xl bg-slate-50 text-rdc-red hover:bg-red-50 transition-all" title="Supprimer">
                                            <i class="fas fa-trash-alt text-[10px] sm:text-sm"></i>
                                        </button>
                                    </template>
                                    <template x-if="user.role === 'super_admin' || user.id == {{ auth()->id() }}">
                                        <button disabled class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg sm:rounded-xl bg-slate-50 text-slate-300 opacity-30 cursor-not-allowed" title="Action protégée">
                                            <i class="fas fa-trash-alt text-[10px] sm:text-sm"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="users.length === 0 && !loading" class="py-32 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 text-4xl mb-6">
                <i class="fas fa-users-slash animate-bounce-short"></i>
            </div>
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Aucun résultat</h4>
            <p class="text-slate-400 max-w-xs mx-auto mt-2 font-medium">Réessayez avec d'autres filtres ou une autre recherche.</p>
        </div>
        
        <!-- Premium Pagination -->
        <div class="px-10 py-8 bg-slate-50/30 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-6" x-show="pagination.last_page > 1">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Affichage <span class="text-slate-900" x-text="pagination.from"></span> - <span class="text-slate-900" x-text="pagination.to"></span> sur <span class="text-slate-900" x-text="pagination.total"></span>
            </p>
            <div class="flex items-center gap-2">
                <button @click="changePage(pagination.current_page - 1)" 
                        :disabled="pagination.current_page === 1"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue hover:shadow-lg disabled:opacity-30 disabled:hover:shadow-none transition-all">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="p in pagination.links_simple" :key="p">
                        <button @click="changePage(p)" 
                                :class="p === pagination.current_page ? 'bg-rdc-blue text-white shadow-lg shadow-blue-500/20' : 'bg-white text-slate-400 hover:bg-slate-50'"
                                class="w-12 h-12 flex items-center justify-center rounded-2xl text-[10px] font-black transition-all" x-text="p"></button>
                    </template>
                </div>
                <button @click="changePage(pagination.current_page + 1)" 
                        :disabled="pagination.current_page === pagination.last_page"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue hover:shadow-lg disabled:opacity-30 disabled:hover:shadow-none transition-all">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function userManager() {
    return {
        users: [],
        stats: { total: 0, active: 0, suspended: 0, verified: 0 },
        loading: false,
        search: '',
        role: '',
        status: '',
        page: 1,
        pagination: {},

        init() {
            this.fetchUsers();
            this.$watch('search', () => { this.page = 1; this.fetchUsers(); });
            this.$watch('role', () => { this.page = 1; this.fetchUsers(); });
            this.$watch('status', () => { this.page = 1; this.fetchUsers(); });
        },

        fetchUsers() {
            this.loading = true;
            let url = `/admin/users/api?page=${this.page}&search=${this.search}&role=${this.role}&status=${this.status}`;
            
            fetch(url)
                .then(r => r.json())
                .then(res => {
                    const data = res.users;
                    this.users = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from || 0,
                        to: data.to || 0,
                        total: data.total,
                        links_simple: this.getPaginationLinks(data.current_page, data.last_page)
                    };
                    this.stats = res.stats;
                    this.loading = false;
                });
        },

        getPaginationLinks(current, last) {
            let links = [];
            for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                links.push(i);
            }
            return links;
        },

        changePage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.page = p;
            this.fetchUsers();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        toggleStatus(user) {
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
                    user.status = res.status;
                } else {
                    alert(res.error || 'Erreur lors du changement de statut');
                }
            });
        },

        confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?')) {
                fetch(`/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => this.fetchUsers());
            }
        },

        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
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
