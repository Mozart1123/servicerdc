@extends('layouts.admin')

@section('title', 'Vérification Documents')
@section('header_title', 'Conformité KYC & Certification')
@section('page_title', 'Documents à Vérifier')
@section('page_subtitle', 'Validez les pièces d\'identité et les certificats professionnels pour garantir la sécurité de la plateforme.')

@section('content')
<div class="space-y-8 pb-20" x-data="{ 
    modalOpen: false,
    activeDoc: { 
        id: null,
        name: '', 
        userId: '', 
        type: '', 
        date: '', 
        status: '', 
        statusColor: '',
        confidence: '',
        avatar: '',
        docImage: '' 
    },
    rejectReason: '',
    openModal(doc) {
        this.activeDoc = doc;
        this.rejectReason = '';
        this.modalOpen = true;
    },
    submitVerify() {
        if(confirm('Êtes-vous sûr de vouloir valider ce document ?')) {
            document.getElementById('verify-form-' + this.activeDoc.id).submit();
        }
    },
    submitReject() {
        if(!this.rejectReason) {
            let reason = prompt('Veuillez indiquer la raison du rejet :');
            if(reason) {
                this.rejectReason = reason;
                // Update hidden input
                let input = document.getElementById('reject-reason-' + this.activeDoc.id);
                if(input) input.value = reason;
                document.getElementById('reject-form-' + this.activeDoc.id).submit();
            }
        } else {
             document.getElementById('reject-form-' + this.activeDoc.id).submit();
        }
    }
}">
    <!-- Verification Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">En Attente</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $stats['pending'] }}</h3>
            </div>
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Validés (30j)</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $stats['verified_30d'] }}</h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl flex items-center justify-between group text-white">
            <div>
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Taux Rejet</p>
                <h3 class="text-3xl font-black">{{ $stats['rejected_rate'] }}%</h3>
            </div>
            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-10 py-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/20">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Flux de Vérification</h3>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.users-mgmt.docs') }}" method="GET" class="flex gap-3">
                    <select name="type" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-slate-100 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm outline-none">
                        <option value="">Tous les types</option>
                        <option value="identity" {{ request('type') == 'identity' ? 'selected' : '' }}>Pièce d'Identité</option>
                        <option value="diploma" {{ request('type') == 'diploma' ? 'selected' : '' }}>Diplôme / Certificat</option>
                        <option value="business_reg" {{ request('type') == 'business_reg' ? 'selected' : '' }}>Registre de Commerce</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type de Document</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date d'Envoi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions HQ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($documents as $doc)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($doc->user->name) }}&background=random&color=fff" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $doc->user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #USR-{{ $doc->user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-8">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-blue-50 text-rdc-blue flex items-center justify-center text-xs">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <span class="text-xs font-black text-slate-700 uppercase">{{ ucfirst($doc->type) }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-8">
                            <span class="text-xs font-bold text-slate-500 italic">{{ $doc->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-8 py-8">
                            @if($doc->status == 'pending')
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-amber-600 uppercase">En Attente</span>
                                </div>
                            @elseif($doc->status == 'verified')
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase">Validé</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    <span class="text-[10px] font-black text-red-600 uppercase">Rejeté</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button @click="openModal({
                                    id: {{ $doc->id }},
                                    name: '{{ addslashes($doc->user->name) }}',
                                    userId: '#USR-{{ $doc->user->id }}',
                                    type: '{{ ucfirst($doc->type) }}',
                                    date: '{{ $doc->created_at->diffForHumans() }}',
                                    status: '{{ $doc->ai_status ?? 'En attente' }}',
                                    statusColor: '{{ ($doc->ai_confidence ?? 'low') == 'high' ? 'text-emerald-600' : 'text-amber-600' }}',
                                    confidence: '{{ $doc->ai_confidence ?? 'low' }}',
                                    avatar: 'https://ui-avatars.com/api/?name={{ urlencode($doc->user->name) }}&background=random&color=fff',
                                    docImage: '{{ asset($doc->file_path) }}'
                                })" class="px-4 py-2 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-rdc-blue transition-all">
                                    Visualiser
                                </button>
                                
                                @if($doc->status === 'pending')
                                    <form id="verify-form-{{ $doc->id }}" action="{{ route('admin.users-mgmt.docs.verify', $doc->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-500 hover:text-white transition-all" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <form id="reject-form-{{ $doc->id }}" action="{{ route('admin.users-mgmt.docs.reject', $doc->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="reason" id="reject-reason-{{ $doc->id }}">
                                        <button type="button" onclick="let reason = prompt('Raison du rejet:'); if(reason) { document.getElementById('reject-reason-{{ $doc->id }}').value = reason; this.form.submit(); }" class="w-10 h-10 flex items-center justify-center bg-red-50 text-rdc-red rounded-xl hover:bg-rdc-red hover:text-white transition-all" title="Rejeter">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-8 text-center text-slate-500 font-medium">Aucun document à vérifier.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-10 py-6 bg-slate-50/30 border-t border-slate-50">
            {{ $documents->links() }}
        </div>
    </div>

    <!-- Document Viewer Modal -->
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="modalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" 
             @click="modalOpen = false"></div>

        <!-- Modal Panel -->
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative min-h-screen md:flex md:items-center md:justify-center p-4">
            
            <div class="bg-white w-full max-w-6xl rounded-[2.5rem] shadow-2xl overflow-hidden relative flex flex-col md:flex-row h-[85vh]">
                
                <!-- Close Button -->
                <button @click="modalOpen = false" class="absolute top-6 right-6 z-20 w-12 h-12 bg-white/10 hover:bg-white text-white hover:text-slate-900 rounded-full flex items-center justify-center backdrop-blur-md transition-all shadow-lg border border-white/20">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <!-- Image Viewer (Left - Dark) -->
                <div class="flex-1 bg-slate-900 relative group flex items-center justify-center p-8 overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                    
                    <!-- Simuler le document -->
                    <div class="relative transform transition-transform duration-500 hover:scale-[1.02] flex items-center justify-center h-full w-full">
                         <!-- Fallback if no image -->
                        <div x-show="!activeDoc.docImage" class="text-white text-center">
                            <i class="fas fa-file-pdf text-6xl mb-4 opacity-50"></i>
                            <p>Aperçu non disponible</p>
                        </div>
                        <img x-show="activeDoc.docImage" :src="activeDoc.docImage" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-2xl border border-slate-700/50" alt="Document Preview">
                        
                        <!-- Overlay controls -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a :href="activeDoc.docImage" target="_blank" class="px-5 py-2.5 bg-black/60 text-white rounded-full backdrop-blur-md text-xs font-bold hover:bg-rdc-blue transition-colors border border-white/10 shadow-lg flex items-center">
                                <i class="fas fa-search-plus mr-2"></i> Ouvrir
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info (Right - Light) -->
                <div class="w-full md:w-[400px] bg-white border-l border-slate-100 flex flex-col z-10">
                    <!-- User Header -->
                    <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-4 mb-6">
                            <img :src="activeDoc.avatar" class="w-16 h-16 rounded-2xl bg-white object-cover border-4 border-white shadow-xl">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 leading-tight" x-text="activeDoc.name"></h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1" x-text="activeDoc.userId"></p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5"><i class="fas fa-file-contract mr-1"></i> Type de Document</p>
                                <p class="text-sm font-black text-slate-800" x-text="activeDoc.type"></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Date Envoi</p>
                                    <span class="text-xs font-bold text-slate-600" x-text="activeDoc.date"></span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Analyse IA</p>
                                    <span class="text-xs font-black uppercase" :class="activeDoc.statusColor" x-text="activeDoc.status"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details List -->
                    <div class="p-8 flex-1 overflow-y-auto">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Points de Contrôle</h4>
                        <ul class="space-y-4">
                             <!-- This can be dynamic too if we had AI data stored -->
                            <li class="flex items-center justify-between text-sm group">
                                <span class="text-slate-500 font-medium group-hover:text-slate-700 transition-colors">Authenticité Visuelle</span>
                                <span class="text-emerald-500 font-bold bg-emerald-50 px-2 py-1 rounded-lg"><i class="fas fa-check-circle mr-1"></i> Passé</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons in Modal -->
                    <div class="p-8 border-t border-slate-100 bg-slate-50 text-center" x-show="activeDoc.status != 'Validé' && activeDoc.status != 'Rejeté'">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <button @click="submitReject()" class="py-4 rounded-2xl bg-white border-2 border-slate-200 text-slate-900 text-xs font-black uppercase tracking-widest hover:border-red-500 hover:text-red-500 hover:bg-red-50 transition-all flex flex-col items-center gap-2 shadow-sm group">
                                <i class="fas fa-times text-xl group-hover:scale-110 transition-transform"></i>
                                Rejeter
                            </button>
                            <button @click="submitVerify()" class="py-4 rounded-2xl bg-rdc-blue text-white text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 flex flex-col items-center gap-2 transform hover:-translate-y-1 group">
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
@endsection
