@extends('layouts.admin')

@section('title', 'Modération des Avis')
@section('header_title', 'Modération des Avis Clients')
@section('page_subtitle', 'Approuvez ou rejetez les avis en attente de modération')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total Avis</div>
            <div class="text-4xl font-heading font-black text-slate-900">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 shadow-sm">
            <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3">En Attente</div>
            <div class="text-4xl font-heading font-black text-amber-600">{{ $stats['pending'] }}</div>
        </div>

        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 shadow-sm">
            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3">Approuvés</div>
            <div class="text-4xl font-heading font-black text-emerald-600">{{ $stats['approved'] }}</div>
        </div>

        <div class="bg-red-50 rounded-2xl p-6 border border-red-100 shadow-sm">
            <div class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-3">Rejetés</div>
            <div class="text-4xl font-heading font-black text-red-600">{{ $stats['rejected'] }}</div>
        </div>

        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 shadow-sm">
            <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3">Note Moyenne</div>
            <div class="text-4xl font-heading font-black text-blue-600">{{ number_format($stats['avg_rating'], 1) }}/5</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Filtres</h3>
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" placeholder="Chercher par client ou texte..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="status" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all">Tous les statuts</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejetés</option>
            </select>

            <select name="rating" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes les notes</option>
                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5/5</option>
                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4/5</option>
                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>⭐⭐⭐ 3/5</option>
                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>⭐⭐ 2/5</option>
                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>⭐ 1/5</option>
            </select>

            <button type="submit" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">
                <i class="fas fa-search mr-2"></i> Filtrer
            </button>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Mission</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Client</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Artisan</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Note</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Avis</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Statut</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reviews as $review)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900">
                        <a href="{{ route('admin.missions.show', $review->mission_id) }}" class="text-blue-500 hover:text-blue-600">
                            #{{ str_pad($review->mission_id, 5, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">{{ $review->client->name }}</td>
                    <td class="px-6 py-4 font-bold text-slate-900">{{ $review->artisan->name }}</td>
                    <td class="px-6 py-4 font-bold">
                        <span class="text-amber-500">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs truncate text-slate-600 italic">{{ $review->feedback }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($review->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[9px] font-bold uppercase">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @elseif($review->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[9px] font-bold uppercase">
                                <i class="fas fa-check"></i> Approuvé
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-[9px] font-bold uppercase">
                                <i class="fas fa-ban"></i> Rejeté
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($review->status === 'pending')
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.moderation.reviews.approve', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-lg text-[9px] uppercase transition-all" title="Approuver">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button type="button" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white font-black rounded-lg text-[9px] uppercase transition-all" 
                                    onclick="document.getElementById('reject-modal-{{ $review->id }}').classList.remove('hidden')" title="Rejeter">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                </tr>

                <!-- Reject Modal -->
                @if($review->status === 'pending')
                <div id="reject-modal-{{ $review->id }}" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-2xl p-8 max-w-md w-full">
                        <h3 class="text-lg font-black text-slate-900 mb-4">Rejeter l'avis</h3>
                        <form action="{{ route('admin.moderation.reviews.reject', $review->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">Raison du rejet</label>
                                <textarea name="rejection_reason" required 
                                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-red-500" 
                                          rows="3" placeholder="Expliquez pourquoi cet avis est rejeté..."></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-900 font-black rounded-xl text-xs uppercase transition-all"
                                        onclick="document.getElementById('reject-modal-{{ $review->id }}').classList.add('hidden')">
                                    Annuler
                                </button>
                                <button type="submit" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-black rounded-xl text-xs uppercase transition-all">
                                    Rejeter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-4 opacity-20"></i>
                            <p class="font-bold">Aucun avis trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
