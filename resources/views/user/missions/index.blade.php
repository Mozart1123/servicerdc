@extends($layout)

@section('header_title', 'Mes Missions')

@section($contentSection)
<div class="space-y-12 pb-20">
    <!-- Header -->
    <div class="relative">
        <div class="absolute inset-0 bg-emerald-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-500 text-3xl shadow-inner">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase">Mes Missions</h2>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Suivez l'état de vos prestations de service</p>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="flex gap-2">
                <a href="{{ route('user.missions.index') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl">Toutes</a>
                <a href="{{ route('user.missions.index', ['status' => 'in_progress']) }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition">En cours</a>
                <a href="{{ route('user.missions.index', ['status' => 'completed']) }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition">Terminées</a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['in_progress'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Missions en cours</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-spinner"></i></div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['completed'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Missions terminées</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['cancelled'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Missions annulées</p>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>

    <!-- Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- Missions on which I am the Artisan -->
        @if(Auth::user()->isArtisan() || (isset($artisanMissions) && count($artisanMissions) > 0))
        <div class="space-y-6">
            <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Moi en tant qu'Artisan</h3>
            
            @forelse($artisanMissions as $mission)
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="font-black text-slate-900">{{ $mission->service->title ?? 'Service' }}</h4>
                        <p class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-widest"><i class="fas fa-user text-rdc-blue mr-1"></i> Client: {{ $mission->client->name ?? 'Anonyme' }}</p>
                    </div>
                    @if($mission->status == 'completed')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Terminée</span>
                    @elseif($mission->status == 'in_progress')
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full">En cours</span>
                    @else
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-full">{{ $mission->status }}</span>
                    @endif
                </div>
                
                <div class="flex justify-end mt-4">
                    <a href="{{ route('user.missions.show', $mission->id) }}" class="px-4 py-2 bg-rdc-blue text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition">Voir Détails</a>
                </div>
            </div>
            @empty
            <div class="bg-slate-50 p-8 rounded-[2.5rem] text-center border border-slate-100 border-dashed">
                <p class="text-sm font-bold text-slate-400">Aucune mission en tant qu'artisan</p>
            </div>
            @endforelse
        </div>
        @endif

        <!-- Missions on which I am the Client -->
        <div class="space-y-6 @if(!Auth::user()->isArtisan()) lg:col-span-2 @endif">
            <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Moi en tant que Client</h3>
            
            @forelse($clientMissions as $mission)
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="font-black text-slate-900">{{ $mission->service->title ?? 'Service' }}</h4>
                        <p class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-widest"><i class="fas fa-hammer text-amber-500 mr-1"></i> Artisan: {{ $mission->artisan->name ?? 'Non assigné' }}</p>
                        @if($mission->start_date || $mission->end_date)
                        <div class="flex gap-4 mt-2">
                            @if($mission->start_date)
                            <span class="text-[9px] font-bold text-slate-400"><i class="fas fa-play text-amber-400 mr-1"></i>Démarrée: {{ $mission->start_date->format('d/m/Y H:i') }}</span>
                            @endif
                            @if($mission->end_date)
                            <span class="text-[9px] font-bold text-slate-400"><i class="fas fa-flag-checkered text-emerald-400 mr-1"></i>Terminée: {{ $mission->end_date->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @if($mission->status == 'completed')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Terminée</span>
                    @elseif($mission->status == 'in_progress')
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full">En cours</span>
                    @else
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-full">{{ $mission->status }}</span>
                    @endif
                </div>

                @if($mission->status == 'completed' && $mission->rating)
                <div class="bg-slate-50 rounded-2xl p-4 mb-4 border border-slate-100">
                    <div class="flex items-center gap-1 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $mission->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                        <span class="text-xs font-bold text-slate-600 ml-1">{{ $mission->rating }}/5</span>
                    </div>
                    @if($mission->feedback)
                    <p class="text-xs text-slate-500 italic">"{{ \Str::limit($mission->feedback, 100) }}"</p>
                    @endif
                </div>
                @endif
                
                <div class="flex justify-end mt-4 gap-3">
                    @if($mission->status == 'completed' && !$mission->rating)
                        <a href="{{ route('user.missions.show', $mission->id) }}" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition flex items-center gap-1">
                            <i class="fas fa-star"></i> Laisser un avis
                        </a>
                    @endif
                    <a href="{{ route('user.missions.show', $mission->id) }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition">Suivre ma commande</a>
                </div>
            </div>
            @empty
            <div class="bg-slate-50 p-8 rounded-[2.5rem] text-center border border-slate-100 border-dashed">
                <p class="text-sm font-bold text-slate-400">Aucune commande de service en tant que client</p>
                <a href="{{ route('user.services.index') }}" class="inline-block mt-4 px-6 py-2 bg-rdc-blue text-white rounded-xl text-[10px] font-black uppercase tracking-widest">Trouver un Artisan</a>
            </div>
            @endforelse
        </div>
        
    </div>
</div>
@endsection
