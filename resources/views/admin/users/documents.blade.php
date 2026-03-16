@extends('layouts.admin')

@section('title', 'Vérification Documents')
@section('header_title', 'Conformité KYC & Certification')
@section('page_title', 'Documents à Vérifier')
@section('page_subtitle', 'Validez les pièces d\'identité et les certificats professionnels pour garantir la sécurité de la plateforme.')

@section('content')
<div class="space-y-8 pb-20" x-data="kycManager()">
    <!-- Verification Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white p-5 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
            <div>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 sm:mb-2">En Attente</p>
                <h3 class="text-xl sm:text-3xl font-black text-slate-900" x-text="stats.pending">--</h3>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-amber-50 text-amber-500 rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-2xl group-hover:scale-110 transition-transform shrink-0">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
        </div>
        <div class="bg-white p-5 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
            <div>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 sm:mb-2 text-nowrap">Validés (30j)</p>
                <h3 class="text-xl sm:text-3xl font-black text-slate-900" x-text="stats.verified_30d">--</h3>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-2xl group-hover:scale-110 transition-transform shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
        <div class="bg-slate-900 p-5 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl flex items-center justify-between group text-white">
            <div>
                <p class="text-[8px] sm:text-[10px] font-black text-white/40 uppercase tracking-widest mb-1 sm:mb-2">Taux Rejet</p>
                <h3 class="text-xl sm:text-3xl font-black"><span x-text="stats.rejected_rate">--</span>%</h3>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white/10 rounded-lg sm:rounded-2xl flex items-center justify-center text-lg sm:text-2xl group-hover:scale-110 transition-transform shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center transition-all">
            <div class="w-12 h-12 border-4 border-slate-100 border-t-rdc-blue rounded-full animate-spin"></div>
        </div>

        <div class="px-5 sm:px-10 py-5 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Flux de Vérification</h3>
            <div class="flex items-center gap-3">
                <select x-model="type" @change="fetchDocuments()" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-white border border-slate-100 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest shadow-sm outline-none">
                    <option value="">Tous les types</option>
                    <option value="identity">Pièce d'Identité</option>
                    <option value="diploma">Diplôme / Certificat</option>
                    <option value="business_reg">Registre de Commerce</option>
                </select>
            </div>
        </div>

        <!-- Scaled Responsive Table -->
        <div class="relative min-h-[400px] overflow-x-hidden">
            <table class="w-full text-left border-collapse table-fixed lg:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest w-[40%] sm:w-auto">Utilisateur</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest w-[15%] sm:w-auto">Type</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest hidden min-[480px]:table-cell">Envoi</th>
                        <th class="px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-[15%] sm:w-auto">Statut</th>
                        <th class="pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-[30%] sm:w-auto">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="doc in documents" :key="doc.id">
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(doc.user.name)}&background=random&color=fff`" 
                                         class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl shadow-sm border border-slate-100 shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate" x-text="doc.user.name"></p>
                                        <p class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate" x-text="`#USR-${doc.user.id}`"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6">
                                <span class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest truncate block" x-text="doc.type"></span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 hidden min-[480px]:table-cell">
                                <span class="text-[9px] sm:text-xs font-bold text-slate-500 italic" x-text="timeAgo(doc.created_at)"></span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                <span :class="{
                                    'bg-amber-500': doc.status === 'pending',
                                    'bg-emerald-500': doc.status === 'verified',
                                    'bg-red-500': doc.status === 'rejected'
                                }" class="inline-block w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full"></span>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <button @click="openViewer(doc)" class="px-2 sm:px-6 py-2 sm:py-2.5 bg-slate-900 text-white text-[7px] sm:text-[9px] font-black uppercase tracking-tighter sm:tracking-widest rounded-lg sm:rounded-xl hover:bg-blue-600 active:scale-95 transition-all shadow-lg shadow-blue-500/10">Examiner</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <template x-if="pagination.last_page > 1">
            <div class="px-10 py-8 bg-slate-50/30 border-t border-slate-50 flex items-center justify-between">
                <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue disabled:opacity-30 transition-all cursor-pointer">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Page <span x-text="pagination.current_page"></span> sur <span x-text="pagination.last_page"></span></span>
                <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue disabled:opacity-30 transition-all cursor-pointer">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Document Viewer Modal -->
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @keydown.escape.window="modalOpen = false">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" @click="modalOpen = false"></div>

        <!-- Modal Panel -->
        <div class="relative min-h-screen md:flex md:items-center md:justify-center p-4">
            <div class="bg-white w-full max-w-6xl rounded-[2.5rem] shadow-2xl overflow-hidden relative flex flex-col md:flex-row h-[85vh]">
                <!-- Close Button -->
                <button @click="modalOpen = false" class="absolute top-6 right-6 z-20 w-12 h-12 bg-white/10 hover:bg-white text-white hover:text-slate-900 rounded-full flex items-center justify-center backdrop-blur-md transition-all shadow-lg border border-white/20">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <!-- Image Viewer -->
                <div class="flex-1 bg-slate-900 relative flex items-center justify-center p-8 overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                    <img :src="`/${activeDoc.file_path}`" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-2xl border border-slate-700/50" alt="Preview">
                </div>

                <!-- Sidebar Info -->
                <div class="w-full md:w-[400px] bg-white border-l border-slate-100 flex flex-col z-10">
                    <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-4 mb-6" x-if="activeDoc.user">
                            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeDoc.user.name)}&background=random&color=fff`" class="w-16 h-16 rounded-2xl bg-white object-cover border-4 border-white shadow-xl">
                            <div>
                                <h3 class="text-xl font-black text-slate-900" x-text="activeDoc.user.name"></h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1" x-text="`#USR-${activeDoc.user.id}`"></p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Type de Document</p>
                                <p class="text-sm font-black text-slate-800 uppercase" x-text="activeDoc.type"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 flex-1 overflow-y-auto">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Points de Contrôle</h4>
                        <ul class="space-y-4">
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 font-medium">Validité Visuelle</span>
                                <span class="text-slate-400 font-bold bg-slate-50 px-2 py-1 rounded-lg">Manuel</span>
                            </li>
                        </ul>
                    </div>

                    <div class="p-8 border-t border-slate-100 bg-slate-50" x-show="activeDoc.status === 'pending'">
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="reject()" class="py-4 rounded-2xl bg-white border-2 border-slate-200 text-slate-900 text-xs font-black uppercase tracking-widest hover:border-red-500 hover:text-red-500 hover:bg-red-50 transition-all flex flex-col items-center gap-2 shadow-sm group">
                                <i class="fas fa-times text-xl group-hover:scale-110 transition-transform"></i>
                                Rejeter
                            </button>
                            <button @click="verify()" class="py-4 rounded-2xl bg-rdc-blue text-white text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 flex flex-col items-center gap-2 transform hover:-translate-y-1 group">
                                <i class="fas fa-check text-xl group-hover:scale-110 transition-transform"></i>
                                Valider
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function kycManager() {
    return {
        documents: [],
        stats: { pending: 0, verified_30d: 0, rejected_rate: 0 },
        loading: false,
        type: '',
        page: 1,
        pagination: {},
        modalOpen: false,
        activeDoc: {},

        init() {
            this.fetchDocuments();
        },

        fetchDocuments() {
            this.loading = true;
            fetch(`/admin/users-mgmt/documents?page=${this.page}&type=${this.type}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(res => {
                this.documents = res.documents.data;
                this.pagination = {
                    current_page: res.documents.current_page,
                    last_page: res.documents.last_page,
                    total: res.documents.total
                };
                this.stats = res.stats;
                this.loading = false;
            });
        },

        openViewer(doc) {
            this.activeDoc = doc;
            this.modalOpen = true;
        },

        verify() {
            if (!confirm('Valider ce document ?')) return;
            fetch(`/admin/users-mgmt/documents/${this.activeDoc.id}/verify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    this.modalOpen = false;
                    this.fetchDocuments();
                }
            });
        },

        reject() {
            const reason = prompt('Veuillez indiquer la raison du rejet :');
            if (!reason) return;

            fetch(`/admin/users-mgmt/documents/${this.activeDoc.id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    this.modalOpen = false;
                    this.fetchDocuments();
                }
            });
        },

        changePage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.page = p;
            this.fetchDocuments();
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
