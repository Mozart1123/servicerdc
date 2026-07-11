@extends($layout)

@section('header_title', 'Détails de la Mission')

@section($contentSection)
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

    <!-- Timeline / Timestamps -->
    <div class="bg-white rounded-[3rem] p-8 border border-slate-100 shadow-sm">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">Chronologie de la Mission</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->start_date ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->start_date ? 'bg-amber-100 text-amber-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-play"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->start_date ? 'text-amber-500' : 'text-slate-400' }}">Démarrée</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->start_date ? $mission->start_date->format('d/m/Y H:i') : 'Pas encore' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->status == 'completed' ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->status == 'completed' ? 'bg-emerald-100 text-emerald-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->status == 'completed' ? 'text-emerald-500' : 'text-slate-400' }}">Terminée</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->end_date ? $mission->end_date->format('d/m/Y H:i') : 'Pas encore' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl {{ $mission->rating ? 'bg-blue-50 border border-blue-100' : 'bg-slate-50 border border-slate-100' }}">
                <div class="w-12 h-12 rounded-xl {{ $mission->rating ? 'bg-blue-100 text-blue-500' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $mission->rating ? 'text-blue-500' : 'text-slate-400' }}">Avis Client</p>
                    <p class="text-sm font-bold text-slate-900">{{ $mission->rating ? $mission->rating . '/5' : 'En attente' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions (Status Update) -->
    @if($mission->status != 'completed' && $mission->status != 'cancelled')
    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl">
        <h3 class="text-lg font-heading font-black uppercase mb-6 flex items-center gap-2"><i class="fas fa-bolt text-amber-400"></i> Actions Rapides</h3>
        
        @if(Auth::id() == $mission->artisan_id)
        {{-- Artisan actions: start or complete --}}
        <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
            @csrf
            @method('PUT')
            
            @if($mission->status == 'pending')
            <input type="hidden" name="status" value="in_progress">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 justify-center">
                <i class="fas fa-play"></i> Démarrer la Mission
            </button>
            @elseif($mission->status == 'in_progress')
            <input type="hidden" name="status" value="completed">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 justify-center">
                <i class="fas fa-flag-checkered"></i> Marquer Terminée
            </button>
            @endif

            <button type="button" onclick="document.getElementById('cancel-form').classList.toggle('hidden')" class="w-full md:w-auto px-10 py-4 bg-red-500/20 hover:bg-red-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                Annuler
            </button>
        </form>
        <form id="cancel-form" action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="hidden mt-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="w-full px-10 py-4 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all" onsubmit="return confirm('Confirmer l\'annulation ?')">
                <i class="fas fa-ban"></i> Confirmer l'annulation
            </button>
        </form>
        @else
        {{-- Client: only cancel if not started --}}
        <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-red-500/20 hover:bg-red-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all" onsubmit="return confirm('Annuler cette mission ?')">
                <i class="fas fa-ban"></i> Annuler la Mission
            </button>
        </form>
        @endif
    </div>
    @endif

    <!-- Feedback/Evaluation -->
    @if($mission->status == 'completed')
    <div class="bg-emerald-50 rounded-[3rem] p-10 border border-emerald-100 text-center">
        <h3 class="text-xl font-heading font-black text-slate-900 uppercase mb-2">Évaluation</h3>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">La mission est terminée !</p>
        
        @if($review)
            <!-- Review existe -->
            @if($review->status === 'approved')
                <!-- Avis Approuvé -->
                <div class="bg-white p-8 rounded-3xl inline-block shadow-sm max-w-2xl">
                    <div class="text-3xl text-amber-400 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="opacity-30 fas fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase mb-3">✓ Avis approuvé et publié</p>
                    @if($review->feedback)
                        <p class="text-slate-600 italic">"{{ $review->feedback }}"</p>
                    @endif
                </div>
            @elseif($review->status === 'rejected')
                <!-- Avis Rejeté -->
                <div class="bg-red-50 p-8 rounded-3xl inline-block border border-red-100 max-w-2xl">
                    <p class="text-red-600 font-black text-sm uppercase tracking-widest mb-3">⚠ Avis rejeté</p>
                    @if($review->rejection_reason)
                        <p class="text-red-700 text-sm mb-6"><strong>Raison :</strong> {{ $review->rejection_reason }}</p>
                    @endif
                    <p class="text-slate-600 text-xs mb-6">Vous pouvez soumettre un nouvel avis</p>
                    <button type="button" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all" 
                            onclick="document.getElementById('review-form-{{ $mission->id }}').classList.toggle('hidden')">
                        <i class="fas fa-redo mr-2"></i> Resoummettre un avis
                    </button>
                    
                    <!-- Form Resoumission -->
                    <form id="review-form-{{ $mission->id }}" action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="hidden mt-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        
                        <div>
                            <select name="rating" required class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none">
                                <option value="">Nouvelle note /5</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Très bien</option>
                                <option value="3">⭐⭐⭐ Correct</option>
                                <option value="2">⭐⭐ Décevant</option>
                                <option value="1">⭐ Mauvais</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="feedback" rows="3" placeholder="Votre nouveau commentaire..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-red-500 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-lg hover:bg-red-600 transition-all">
                            Soumettre le nouvel avis
                        </button>
                    </form>
                </div>
            @else
                <!-- En attente d'approbation -->
                <div class="bg-white p-8 rounded-3xl inline-block shadow-sm max-w-2xl border border-amber-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full text-amber-500 text-2xl mb-4 animate-pulse">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <p class="text-amber-600 font-black text-sm uppercase tracking-widest mb-2">En attente de modération</p>
                    <div class="text-3xl text-amber-400 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="opacity-30 fas fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    @if($review->feedback)
                        <p class="text-slate-600 italic text-sm">"{{ $review->feedback }}"</p>
                    @endif
                    <p class="text-slate-400 text-xs mt-4">Votre avis est en cours de vérification par notre équipe d'administration.</p>
                </div>
            @endif
        @else
            <!-- Aucun avis soumis -->
            @if(Auth::id() == $mission->client_id)
                <!-- Formulaire d'évaluation pour le client -->
                <form action="{{ route('user.missions.update-status', $mission->id) }}" method="POST" class="max-w-xl mx-auto space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-widest">Quelle note donnez-vous à cet artisan ?</label>
                        <select name="rating" required class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Sélectionnez une note...</option>
                            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                            <option value="4">⭐⭐⭐⭐ Très bien</option>
                            <option value="3">⭐⭐⭐ Correct</option>
                            <option value="2">⭐⭐ Décevant</option>
                            <option value="1">⭐ Mauvais</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-widest">Votre commentaire (optionnel)</label>
                        <textarea name="feedback" rows="4" placeholder="Partagez votre expérience avec cet artisan..." class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-lg hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-star"></i> Soumettre l'évaluation
                    </button>
                </form>
            @else
                <p class="text-slate-500 font-bold bg-white px-8 py-6 rounded-3xl inline-block shadow-sm">En attente de l'évaluation du client</p>
            @endif
        @endif
    </div>
    @endif

</div>
@endsection
