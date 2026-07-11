@extends($layout)

@section('header_title', 'Mes favoris')
@section('header_subtitle', 'Services et emplois que vous avez sauvegardés.')

@section($contentSection)
<div class="space-y-6">

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 flex-wrap">
        <button onclick="filterFavs('all', this)" class="fav-tab px-4 py-1.5 rounded-full text-sm font-medium border transition-colors bg-[#16a3b0] text-white border-[#16a3b0]">Tout</button>
        <button onclick="filterFavs('service', this)" class="fav-tab px-4 py-1.5 rounded-full text-sm font-medium border transition-colors bg-white text-slate-600 border-slate-200 hover:border-[#16a3b0] hover:text-[#16a3b0]">Services</button>
        <button onclick="filterFavs('job', this)" class="fav-tab px-4 py-1.5 rounded-full text-sm font-medium border transition-colors bg-white text-slate-600 border-slate-200 hover:border-[#16a3b0] hover:text-[#16a3b0]">Emplois</button>
    </div>

    {{-- Favorites List --}}
    <div class="space-y-3" id="favs-list">
        @php
            $favorites = [
                ['type' => 'service', 'title' => 'Plomberie Sanitaire Express', 'author' => 'Jean Plombier', 'date' => '05 Fév 2025', 'loc' => 'Gombe, Kinshasa'],
                ['type' => 'job',     'title' => 'Analyste Financier Senior',    'author' => 'Equity BCDC',  'date' => '02 Fév 2025', 'loc' => '30 Juin, Kinshasa'],
                ['type' => 'service', 'title' => 'Consultance Juridique Business','author' => 'Me Mbemba',    'date' => '28 Jan 2025', 'loc' => 'Lubumbashi'],
            ];
        @endphp

        @forelse($favorites as $fav)
        <div class="fav-item bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-300 hover:shadow-sm transition-all flex flex-col sm:flex-row sm:items-center gap-4" data-type="{{ $fav['type'] }}">
            {{-- Icon --}}
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $fav['type'] === 'job' ? 'bg-blue-50 text-blue-500' : 'bg-[#16a3b0]/10 text-[#16a3b0]' }}">
                <i class="fas {{ $fav['type'] === 'job' ? 'fa-briefcase' : 'fa-tools' }}"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h4 class="font-semibold text-slate-900 text-sm">{{ $fav['title'] }}</h4>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $fav['type'] === 'job' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-[#16a3b0]/10 text-[#16a3b0] border border-[#16a3b0]/20' }}">
                        {{ $fav['type'] === 'job' ? 'Emploi' : 'Service' }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span class="flex items-center gap-1"><i class="fas fa-building text-slate-400"></i>{{ $fav['author'] }}</span>
                    <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-slate-400"></i>{{ $fav['loc'] }}</span>
                    <span class="flex items-center gap-1"><i class="fas fa-calendar text-slate-400"></i>{{ $fav['date'] }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0 ml-14 sm:ml-0">
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    <i class="fas fa-heart-crack"></i> Retirer
                </button>
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    <i class="fas fa-eye"></i> Voir
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                <i class="fas fa-heart"></i>
            </div>
            <h3 class="font-semibold text-slate-800 mb-1">Aucun favori pour l'instant</h3>
            <p class="text-sm text-slate-500">Explorez la plateforme et enregistrez les services qui vous intéressent.</p>
        </div>
        @endforelse
    </div>

</div>

<script>
function filterFavs(type, btn) {
    document.querySelectorAll('.fav-tab').forEach(t => {
        t.classList.remove('bg-[#16a3b0]', 'text-white', 'border-[#16a3b0]');
        t.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
    });
    btn.classList.add('bg-[#16a3b0]', 'text-white', 'border-[#16a3b0]');
    btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');

    document.querySelectorAll('.fav-item').forEach(item => {
        item.style.display = (type === 'all' || item.dataset.type === type) ? '' : 'none';
    });
}
</script>
@endsection
