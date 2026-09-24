@extends('layouts.admin')

@section('title', 'Tous les Avis')
@section('header_title', 'Tous les Avis Clients')
@section('page_subtitle', 'Consultez et surveillez tous les avis laissés par les clients')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-100 text-slate-500 rounded-xl sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-star"></i>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Avis</p>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 font-mono mt-1">{{ $stats['total'] }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 text-amber-500 rounded-xl sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-clock"></i>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">En Attente</p>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 font-mono mt-1">{{ $stats['pending'] }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-50 text-emerald-500 rounded-xl sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-circle-check"></i>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Approuvés</p>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 font-mono mt-1">{{ $stats['approved'] }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 text-rdc-red rounded-xl sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Rejetés</p>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 font-mono mt-1">{{ $stats['rejected'] }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group col-span-2 lg:col-span-1">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-rdc-blue rounded-xl sm:rounded-2xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-bar"></i>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Note Moyenne</p>
            <h3 class="text-lg sm:text-2xl font-black text-slate-900 font-mono mt-1">{{ number_format($stats['avg_rating'] ?? 0, 1) }}/5</h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 border border-slate-100 shadow-sm">
        <h3 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Filtres</h3>
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1 group">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                <input type="text" name="search" placeholder="Chercher par client, artisan ou texte..."
                       value="{{ $search ?? '' }}"
                       class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-xs font-black text-slate-900 outline-none focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all">
            </div>

            <select name="status" class="px-5 py-4 bg-slate-50 border-none rounded-2xl text-xs font-black text-slate-900 outline-none focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all cursor-pointer">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous les statuts</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvés</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetés</option>
            </select>

            <select name="rating" class="px-5 py-4 bg-slate-50 border-none rounded-2xl text-xs font-black text-slate-900 outline-none focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all cursor-pointer">
                <option value="">Toutes les notes</option>
                <option value="5" {{ $rating === '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5/5</option>
                <option value="4" {{ $rating === '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4/5</option>
                <option value="3" {{ $rating === '3' ? 'selected' : '' }}>⭐⭐⭐ 3/5</option>
                <option value="2" {{ $rating === '2' ? 'selected' : '' }}>⭐⭐ 2/5</option>
                <option value="1" {{ $rating === '1' ? 'selected' : '' }}>⭐ 1/5</option>
            </select>

            <button type="submit" class="px-8 py-4 bg-slate-900 hover:bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-slate-200 shrink-0">
                <i class="fas fa-search mr-2"></i> Filtrer
            </button>
            
            <a href="{{ route('admin.avis.index') }}" class="px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all shrink-0 text-center">
                Réinitialiser
            </a>

            <a href="{{ route('admin.reports.preview', ['type' => 'reviews']) }}" class="px-8 py-4 bg-slate-900 hover:bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-slate-200 shrink-0 flex items-center justify-center gap-2">
                <i class="fas fa-file-shield"></i> Rapport Modération
            </a>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Mission / Demande</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Client</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Artisan</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Note</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Avis</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Statut</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-left font-black text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reviews as $review)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900">
                        @if($review->mission_id)
                            <a href="{{ route('admin.missions.show', $review->mission_id) }}" class="text-blue-500 hover:text-blue-600">
                                Mission #{{ str_pad($review->mission_id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        @elseif($review->service_request_id)
                            <a href="{{ route('admin.service-requests.show', $review->service_request_id) }}" class="text-purple-500 hover:text-purple-600">
                                Demande #{{ str_pad($review->service_request_id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper($review->client->name[0]) }}
                            </div>
                            <span>{{ $review->client->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-50 text-rdc-blue rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper($review->artisan->name[0]) }}
                            </div>
                            <span>{{ $review->artisan->name }}</span>
                        </div>
                    </td>
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
                        <span class="ml-2 text-slate-500 font-normal">{{ $review->rating }}/5</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs truncate text-slate-600 italic">{{ $review->feedback ?? '—' }}</div>
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
                    <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                        {{ $review->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($review->mission_id)
                                <a href="{{ route('admin.missions.show', $review->mission_id) }}" 
                                   class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-black rounded-lg text-[9px] uppercase transition-all" 
                                   title="Voir la mission">
                                    <i class="fas fa-briefcase mr-1"></i> Mission
                                </a>
                            @elseif($review->service_request_id)
                                <a href="{{ route('admin.service-requests.show', $review->service_request_id) }}" 
                                   class="px-3 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 font-black rounded-lg text-[9px] uppercase transition-all" 
                                   title="Voir la demande">
                                    <i class="fas fa-file-alt mr-1"></i> Demande
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.users.show', $review->artisan_id) }}" 
                               class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-lg text-[9px] uppercase transition-all" 
                               title="Voir l'artisan">
                                <i class="fas fa-user-tie mr-1"></i> Artisan
                            </a>
                            
                            @if($review->status === 'pending')
                                <a href="{{ route('admin.moderation.reviews') }}?status=pending" 
                                   class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-black rounded-lg text-[9px] uppercase transition-all" 
                                   title="Modérer">
                                    <i class="fas fa-gavel mr-1"></i> Modérer
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-24 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-5xl mb-8 shadow-inner">
                                <i class="fas fa-star-half-stroke"></i>
                            </div>
                            <h4 class="text-base font-black text-slate-400 uppercase tracking-widest">Aucun avis trouvé</h4>
                            <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">Essayez de modifier vos filtres.</p>
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