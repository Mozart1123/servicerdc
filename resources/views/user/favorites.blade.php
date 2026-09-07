@extends($layout)

@section('header_title', 'Mes favoris')
@section('header_subtitle', 'Les artisans que vous avez enregistrés.')

@section($contentSection)
<div class="space-y-6">

    {{-- Favorites List --}}
    <div class="space-y-3" id="favs-list">
        @forelse($favoriteArtisans as $favorite)
            @php $artisan = $favorite->artisan; @endphp
            <div class="fav-item bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-300 hover:shadow-sm transition-all flex flex-col sm:flex-row sm:items-center gap-4"
                 data-favorite-id="{{ $favorite->id }}">
                {{-- Photo --}}
                <img src="{{ $artisan->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($artisan->name).'&background=16a3b0&color=fff' }}"
                     class="w-11 h-11 rounded-xl object-cover shrink-0" alt="{{ $artisan->name }}">

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="font-semibold text-slate-900 text-sm">{{ $artisan->name }}</h4>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-[#16a3b0]/10 text-[#16a3b0] border border-[#16a3b0]/20">
                            Artisan
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        <span class="flex items-center gap-1"><i class="fas fa-hammer text-slate-400"></i>{{ $artisan->profession ?? 'Artisan' }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-slate-400"></i>{{ $artisan->city ?? 'RDC' }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-calendar text-slate-400"></i>Ajouté le {{ $favorite->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0 ml-14 sm:ml-0">
                    <button type="button"
                            data-remove-favorite
                            data-url="{{ route('user.artisans.favorite.toggle', $artisan->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-heart-crack"></i> Retirer
                    </button>
                    <a href="{{ route('public.artisans.show', $artisan->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1">Aucun favori pour l'instant</h3>
                <p class="text-sm text-slate-500">Depuis le profil public d'un artisan, cliquez sur le cœur pour l'ajouter ici.</p>
            </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    csrf = csrf ? csrf.content : '';

    document.querySelectorAll('[data-remove-favorite]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.disabled = true;

            fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.favorited) {
                    var item = btn.closest('.fav-item');
                    if (item) item.remove();
                }
            })
            .catch(function () {
                // Échec silencieux — l'artisan reste dans la liste.
            })
            .finally(function () {
                btn.disabled = false;
            });
        });
    });
});
</script>
@endpush
@endsection
