@extends('layouts.admin')

@section('title', 'Vérification d\'Identité')
@section('header_title', 'Vérification des Artisans')
@section('page_title', 'Identités des Artisans')
@section('page_subtitle', 'Examinez et validez les pièces d\'identité soumises par les artisans.')

@section('content')
<div class="space-y-8 pb-20">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="bg-white border p-6 rounded-[2rem] shadow-sm flex items-center justify-between group transition {{ $statusFilter === 'pending' ? 'border-rdc-blue ring-2 ring-rdc-blue/10' : 'border-slate-100' }}">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">En Attente</p>
                <p class="text-3xl font-black text-slate-900 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl group-hover:scale-110 transition">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </a>

        <a href="{{ route('admin.verifications.index', ['status' => 'approved']) }}" class="bg-white border p-6 rounded-[2rem] shadow-sm flex items-center justify-between group transition {{ $statusFilter === 'approved' ? 'border-rdc-blue ring-2 ring-rdc-blue/10' : 'border-slate-100' }}">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Approuvées</p>
                <p class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['approved'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl group-hover:scale-110 transition">
                <i class="fas fa-check-circle"></i>
            </div>
        </a>

        <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" class="bg-white border p-6 rounded-[2rem] shadow-sm flex items-center justify-between group transition {{ $statusFilter === 'rejected' ? 'border-rdc-blue ring-2 ring-rdc-blue/10' : 'border-slate-100' }}">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rejetées</p>
                <p class="text-3xl font-black text-red-600 mt-1">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl group-hover:scale-110 transition">
                <i class="fas fa-times-circle"></i>
            </div>
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Artisan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type de pièce</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut & Date</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($verifications as $v)
                        <tr class="group hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $v->user->photo_url }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-100">
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $v->user->name }}</p>
                                        <p class="text-xs text-slate-400 font-medium">{{ $v->user->email }} • Inscrit le {{ $v->user->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                @php
                                    $docTypeLabel = match($v->identity_document_type) {
                                        'national_id' => 'Carte Nationale d\'Identité',
                                        'passport' => 'Passeport',
                                        'driving_license' => 'Permis de conduire',
                                        default => 'Document'
                                    };
                                @endphp
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl uppercase tracking-wider">
                                    {{ $docTypeLabel }}
                                </span>
                            </td>

                            <td class="px-8 py-6">
                                @if($v->verification_status === 'approved')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl uppercase tracking-wider">Approuvée</span>
                                    <p class="text-[10px] text-slate-400 font-mono mt-1">Validée le {{ $v->verified_at ? $v->verified_at->format('d/m/Y H:i') : '-' }}</p>
                                @elseif($v->verification_status === 'pending')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-xl uppercase tracking-wider">En Attente</span>
                                    <p class="text-[10px] text-slate-400 font-mono mt-1">Soumise le {{ $v->updated_at->format('d/m/Y H:i') }}</p>
                                @elseif($v->verification_status === 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-xl uppercase tracking-wider">Rejetée</span>
                                    @if($v->verification_rejection_reason)
                                        <p class="text-xs text-red-600 font-medium mt-1 truncate max-w-xs" title="{{ $v->verification_rejection_reason }}">Motif : {{ $v->verification_rejection_reason }}</p>
                                    @endif
                                @endif
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3" x-data="{ showRejectModal: false }">
                                    
                                    <!-- View Document Button -->
                                    <a href="{{ route('admin.verifications.download', $v->id) }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                        <i class="fas fa-eye"></i> Document
                                    </a>

                                    @if($v->verification_status === 'pending' || $v->verification_status === 'rejected')
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('admin.verifications.approve', $v->id) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Confirmer l\'approbation de l\'identité de {{ addslashes($v->user->name) }} ?')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-sm">
                                                Approuver
                                            </button>
                                        </form>
                                    @endif

                                    @if($v->verification_status === 'pending' || $v->verification_status === 'approved')
                                        <!-- Reject Trigger -->
                                        <button @click="showRejectModal = true" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                                            Rejeter
                                        </button>

                                        <!-- Rejection Modal -->
                                        <div x-show="showRejectModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
                                            <div @click.outside="showRejectModal = false" class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl text-left">
                                                <h3 class="text-lg font-black text-slate-900 mb-2">Rejeter l'identité</h3>
                                                <p class="text-xs text-slate-500 mb-4">Précisez le motif du refus transmis à {{ $v->user->name }}.</p>
                                                
                                                <form method="POST" action="{{ route('admin.verifications.reject', $v->id) }}" class="space-y-4">
                                                    @csrf
                                                    <textarea name="reason" required rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-medium focus:ring-4 focus:ring-red-100 focus:border-red-500" placeholder="Ex: Photo floue, document expiré..."></textarea>
                                                    
                                                    <div class="flex gap-3">
                                                        <button type="button" @click="showRejectModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs uppercase tracking-wider">Annuler</button>
                                                        <button type="submit" class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-red-700 shadow-md">Confirmer le rejet</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-slate-400 text-sm font-medium">
                                Aucun document de vérification trouvé pour ce filtre.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($verifications->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $verifications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
