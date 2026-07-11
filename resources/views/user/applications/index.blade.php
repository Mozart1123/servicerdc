@extends($layout)

@section('title', 'Mes Candidatures | ProConnect')
@section('header_title', 'Mes Candidatures')

@section($contentSection)
<div class="space-y-8 pb-10">

    {{-- Flash Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue', 'warning' => 'amber'] as $type => $color)
    @if(session($type))
    <div class="flex items-center gap-3 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-5 py-4 rounded-2xl" data-aos="fade-down">
        <i class="fas fa-circle-info text-xl shrink-0"></i>
        <p class="font-semibold">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4" data-aos="fade-up">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate', 'icon' => 'fa-list'],
            ['label' => 'En attente', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => 'fa-clock'],
            ['label' => 'Approuvées', 'value' => $stats['approved'], 'color' => 'emerald', 'icon' => 'fa-check-circle'],
            ['label' => 'Refusées', 'value' => $stats['rejected'], 'color' => 'red', 'icon' => 'fa-times-circle'],
            ['label' => 'Entretien', 'value' => $stats['interview'], 'color' => 'blue', 'icon' => 'fa-comments'],
            ['label' => 'Embauché', 'value' => $stats['hired'], 'color' => 'purple', 'icon' => 'fa-trophy'],
        ] as $stat)
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-{{ $stat['color'] }}-400">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400 text-sm"></i>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-col md:flex-row gap-4" data-aos="fade-up" data-aos-delay="100">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher une offre…" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <select name="status" class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-rdc-blue">
            <option value="">Tous les statuts</option>
            @foreach(['pending' => 'En attente', 'approved' => 'Approuvée', 'rejected' => 'Refusée', 'interview' => 'Entretien', 'hired' => 'Embauché'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('user.applications.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition text-center">Réinitialiser</a>
        @endif
    </form>

    {{-- List --}}
    <div class="space-y-4">
        @forelse($applications as $app)
        @php
            $colors = ['pending' => 'amber', 'approved' => 'emerald', 'accepted' => 'emerald', 'rejected' => 'red', 'interview' => 'blue', 'hired' => 'purple'];
            $c = $colors[$app->status] ?? 'slate';
            $jobOffer = $app->jobOffer;
        @endphp
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
            <div class="flex flex-col md:flex-row gap-4">

                {{-- Company logo --}}
                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden">
                    @if($jobOffer?->company_logo)
                        <img src="{{ Storage::url($jobOffer->company_logo) }}" alt="" class="w-full h-full object-contain p-2">
                    @else
                        <i class="fas fa-briefcase text-slate-400 text-2xl"></i>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-lg font-bold text-slate-900 truncate">{{ $jobOffer?->title ?? 'Offre supprimée' }}</h4>
                        <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                            {{ $app->status_label }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500 font-medium">
                        @if($jobOffer?->company_name)
                        <span><i class="far fa-building mr-1"></i>{{ $jobOffer->company_name }}</span>
                        @endif
                        @if($jobOffer?->location)
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $jobOffer->location }}</span>
                        @endif
                        <span><i class="far fa-calendar-alt mr-1"></i>Postulé le {{ $app->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    {{-- Discuss when approved/interview/hired --}}
                    @if(in_array($app->status, ['approved', 'accepted', 'interview', 'hired']) && $jobOffer?->employer_id)
                    <a href="{{ route('user.messages.start.user', $jobOffer->employer_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rdc-blue text-white text-sm font-bold rounded-xl hover:bg-rdc-blue-dark transition">
                        <i class="fas fa-comments"></i> Discuter
                    </a>
                    @endif

                    {{-- View offer --}}
                    @if($jobOffer)
                    <a href="{{ route('user.jobs.show', $jobOffer->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                        <i class="fas fa-eye"></i> Voir l'offre
                    </a>
                    @endif

                    {{-- Withdraw while pending --}}
                    @if($app->status === 'pending')
                    <form action="{{ route('user.applications.withdraw', $app->id) }}" method="POST"
                          onsubmit="return confirm('Retirer cette candidature ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 text-sm font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                            <i class="fas fa-times"></i> Retirer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-12 rounded-2xl border border-slate-100 text-center" data-aos="fade-up">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-4xl mx-auto mb-4">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h3 class="font-bold text-lg text-slate-800">Aucune candidature</h3>
            <p class="text-slate-500 mt-2">Vous n'avez pas encore postulé à des offres d'emploi.</p>
            <a href="{{ route('user.jobs.index') }}" class="inline-block mt-5 px-8 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">
                Parcourir les offres
            </a>
        </div>
        @endforelse

        @if($applications->hasPages())
        <div class="pt-4">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
