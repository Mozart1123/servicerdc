@extends('layouts.user')

@section('header_title', 'Détails de la Mission')

@section('content')
<div class="space-y-10 pb-20 max-w-5xl mx-auto">
    
    <!-- Top Nav -->
    <a href="{{ route('user.missions.index') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors">
        <i class="fas fa-arrow-left"></i> Retour
    </a>

    <!-- Status Header -->
    <div class="bg-white rounded-[3.5rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 to-transparent -z-0"></div>
        
        <div class="relative z-10 flex-1 text-center md:text-left">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Mission #{{ str_pad($mission->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm font-bold text-slate-500 mt-2 uppercase tracking-widest">{{ $mission->service->title ?? 'Service Supprimé' }}</p>
        </div>
        
        <div class="relative z-10 text-center md:text-right">
            @if($mission->status == 'pending')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full text-slate-400 text-3xl mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">En attente</div>
            @elseif($mission->status == 'in_progress')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-100 rounded-full text-amber-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-spinner animate-spin"></i>
                </div>
                <div class="text-[10px] font-black text-amber-500 uppercase tracking-widest">En cours</div>
            @elseif($mission->status == 'completed')
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full text-emerald-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-check"></i>
                </div>
                <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Terminée</div>
            @else
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full text-red-500 text-3xl mb-2 shadow-inner">
                    <i class="fas fa-times"></i>
                </div>
                <div class="text-[10px] font-black text-red-500 uppercase tracking-widest">Annulée</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Actors Info -->
        <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm flex flex-col items-center text-center">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2 w-full">Profil Artisan</div>
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-2xl text-slate-400 mb-4">
                <i class="fas fa-hard-hat"></i>
            </div>
            <h4 class="font-heading font-black text-slate-900 text-lg">{{ $mission->artisan->name ?? 'Indisponible' }}</h4>
            <p class="text-xs font-bold text-slate-500 mt-1"><i class="fas fa-phone mr-1 opacity-50"></i> {{ $mission->artisan->phone ?? 'Non renseigné' }}</p>
        </div>

        <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm flex flex-col items-center text-center">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2 w-full">Profil Client</div>
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-2xl text-slate-400 mb-4">
                <i class="fas fa-user"></i>
            </div>
            <h4 class="font-heading font-black text-slate-900 text-lg">{{ $mission->client->name ?? 'Indisponible' }}</h4>
            <p class="text-xs font-bold text-slate-500 mt-1"><i class="fas fa-phone mr-1 opacity-50"></i> {{ $mission->client->phone ?? 'Non renseigné' }}</p>
        </div>
    </div>

    <!-- Actions (Status Update) -->
    @if($mission->status != 'completed' && $mission->status != 'cancelled')
    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl">
        <h3 class="text-lg font-heading font-black uppercase mb-6 flex items-center gap-2"><i class="fas fa-bolt text-amber-400"></i> Actions Rapides</h3>
        
        <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
            @csrf
            @method('PUT')
            
            <div class="flex-1 w-full">
                <select name="status" class="w-full px-6 py-4 bg-white/10 text-white border border-white/20 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-white/10 transition-all outline-none appearance-none">
                    <option value="pending" class="text-slate-900" {{ $mission->status == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="in_progress" class="text-slate-900" {{ $mission->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" class="text-slate-900" {{ $mission->status == 'completed' ? 'selected' : '' }}>Marquer comme Terminée</option>
                    <option value="cancelled" class="text-slate-900" {{ $mission->status == 'cancelled' ? 'selected' : '' }}>Annuler la mission</option>
                </select>
            </div>
            
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-rdc-blue hover:bg-blue-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                Mettre à jour
            </button>
        </form>
    </div>
    @endif

    <!-- Feedback/Evaluation -->
    @if($mission->status == 'completed')
    <div class="bg-emerald-50 rounded-[3rem] p-10 border border-emerald-100 text-center">
        <h3 class="text-xl font-heading font-black text-slate-900 uppercase mb-2">Évaluation</h3>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">La mission est terminée !</p>
        
        @if($mission->rating)
            <div class="text-3xl text-amber-400 mb-4">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $mission->rating)
                        <i class="fas fa-star"></i>
                    @else
                        <i class="opacity-30 fas fa-star"></i>
                    @endif
                @endfor
            </div>
            @if($mission->feedback)
                <p class="text-slate-600 bg-white p-6 rounded-3xl italic max-w-2xl mx-auto shadow-sm">"{{ $mission->feedback }}"</p>
            @endif
        @else
            @if(Auth::id() == $mission->client_id)
                <!-- Formulaire d'évaluation pour le client -->
                <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="max-w-xl mx-auto space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    
                    <div>
                        <select name="rating" required class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none">
                            <option value="">Note /5</option>
                            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                            <option value="4">⭐⭐⭐⭐ Très bien</option>
                            <option value="3">⭐⭐⭐ Correct</option>
                            <option value="2">⭐⭐ Décevant</option>
                            <option value="1">⭐ Mauvais</option>
                        </select>
                    </div>
                    <div>
                        <textarea name="feedback" rows="3" placeholder="Laissez un commentaire sur le travail effectué..." class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-lg hover:bg-emerald-600 transition-all">
                        Soumettre l'évaluation
                    </button>
                </form>
            @else
                <p class="text-slate-500 font-bold bg-white px-6 py-4 rounded-3xl inline-block shadow-sm">En attente de l'évaluation du client</p>
            @endif
        @endif
    </div>
    @endif

</div>
@endsection
