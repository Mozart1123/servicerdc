@extends('layouts.user')

@section('header_title', 'Mes Réalisations')

@section('content')
<div class="space-y-12 pb-20">

    <!-- Header -->
    <div class="relative">
        <div class="absolute inset-0 bg-blue-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-3xl shadow-inner">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase">Mes Réalisations</h2>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Montrez vos travaux passés à vos futurs clients</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-black text-slate-900">{{ $realisations->count() }}<span class="text-slate-300">/{{ $maxRealisations }}</span></p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Photos publiées</p>
            </div>
        </div>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Formulaire d'ajout -->
    @if($realisations->count() < $maxRealisations)
        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm p-8">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5">Ajouter une réalisation</h3>
            <form action="{{ route('user.realisations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ preview: null, filename: '' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Photo <span class="text-red-500">*</span></label>
                        <div class="relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:bg-slate-50 transition-colors group">
                            <input type="file" name="image" required accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   @change="const f = $event.target.files[0]; filename = f ? f.name : ''; preview = f ? URL.createObjectURL(f) : null;">
                            <template x-if="!preview">
                                <div>
                                    <i class="fas fa-cloud-upload-alt text-rdc-blue text-2xl mb-2"></i>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG, WEBP (Max 5 Mo)</p>
                                </div>
                            </template>
                            <template x-if="preview">
                                <div class="flex items-center gap-3 justify-center">
                                    <img :src="preview" class="w-16 h-16 rounded-xl object-cover">
                                    <span class="text-xs font-bold text-slate-600" x-text="filename"></span>
                                </div>
                            </template>
                        </div>
                        @error('image')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Légende (Optionnelle)</label>
                        <input type="text" name="caption" placeholder="Ex: Rénovation cuisine à Gombe" value="{{ old('caption') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        @error('caption')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                </div>
                <button type="submit" class="px-8 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:scale-105 transition-all">
                    <i class="fas fa-plus mr-1"></i> Ajouter la photo
                </button>
            </form>
        </div>
    @else
        <div class="px-5 py-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500"></i>
            Vous avez atteint la limite de {{ $maxRealisations }} réalisations. Supprimez-en une pour en ajouter une nouvelle.
        </div>
    @endif

    <!-- Grille des réalisations -->
    <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5 px-2">Vos photos publiées</h3>

        @if($realisations->isEmpty())
            <div class="bg-slate-50 p-12 rounded-[3.5rem] text-center border border-slate-100 border-dashed flex flex-col items-center">
                <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center text-4xl text-slate-300 shadow-sm mb-6">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Aucune réalisation pour l'instant</h3>
                <p class="text-sm font-medium text-slate-500 mt-2 max-w-lg">Ajoutez vos premières photos ci-dessus — elles apparaîtront dans l'onglet "Réalisations" de votre profil public, visible par tous les clients.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                @foreach($realisations as $realisation)
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden group">
                        <div class="aspect-square bg-slate-100 relative">
                            <img src="{{ $realisation->image_url }}" alt="{{ $realisation->caption ?? 'Réalisation' }}" class="w-full h-full object-cover">
                            <form action="{{ route('user.realisations.destroy', $realisation->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette réalisation ?');"
                                  class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-white/90 text-red-500 rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                        @if($realisation->caption)
                            <p class="px-3 py-2 text-[11px] font-bold text-slate-600 truncate">{{ $realisation->caption }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
