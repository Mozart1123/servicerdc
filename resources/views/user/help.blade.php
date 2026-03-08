@extends('layouts.user')

@section('header_title', 'Centre d\'aide & Demandes')

@section('content')
<div class="max-w-4xl mx-auto space-y-12 pb-20">
    <!-- Header -->
    <div class="text-center space-y-4">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-50 text-rdc-blue shadow-inner text-3xl mb-4">
            <i class="fas fa-question-circle"></i>
        </div>
        <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Comment pouvons-nous vous aider ?</h2>
        <p class="text-slate-500 font-medium max-w-lg mx-auto leading-relaxed">
            Utilisez ce formulaire pour demander l'ajout d'un nouveau service ou pour toute autre question. Notre équipe vous répondra directement.
        </p>
    </div>

    <!-- Request Form Card -->
    <div class="bg-white rounded-[3.5rem] p-10 md:p-16 shadow-xl border border-slate-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-rdc-blue/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <form id="helpRequestForm" class="space-y-8 relative z-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Subject / Service Name -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Sujet / Service Souhaité</label>
                    <input type="text" name="requested_service_name" required
                           placeholder="Ex: Service de livraison de nuit"
                           class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-rdc-blue/30 focus:bg-white transition-all outline-none font-bold text-slate-900">
                </div>

                <!-- Category -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Catégorie (Optionnel)</label>
                    <select name="category_needed"
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-rdc-blue/30 focus:bg-white transition-all outline-none font-bold text-slate-900 appearance-none">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Ménage">Ménage & Nettoyage</option>
                        <option value="Réparation">Réparation & Travaux</option>
                        <option value="Transport">Transport & Logistique</option>
                        <option value="Education">Éducation & Formation</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Votre Message / Précisions</label>
                <textarea name="description" rows="5" required
                          placeholder="Décrivez votre besoin ou posez votre question ici..."
                          class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:border-rdc-blue/30 focus:bg-white transition-all outline-none font-bold text-slate-900 resize-none"></textarea>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-4">
                <div class="flex items-center gap-3 text-slate-400">
                    <i class="fas fa-shield-halved text-emerald-500"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Envoi Sécurisé vers l'Admin</span>
                </div>
                <button type="submit" id="submitBtn"
                        class="w-full md:w-auto px-12 py-5 bg-rdc-blue text-white font-black rounded-3xl text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <span>Envoyer ma Demande</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- My Requests Brief (Optionnel) -->
    @if(isset($latestRequests) && count($latestRequests) > 0)
    <div class="space-y-6">
        <h3 class="text-xl font-heading font-black text-slate-900 uppercase px-4 flex items-center gap-3">
            <i class="fas fa-history text-slate-300"></i>
            Suivi de mes demandes
        </h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach($latestRequests as $req)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center justify-between group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-rdc-blue group-hover:text-white transition-all">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">{{ $req->requested_service_name }}</h4>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ $req->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div>
                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest 
                        {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                        {{ $req->status === 'pending' ? 'En attente' : 'Répondu' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
document.getElementById('helpRequestForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const formData = new FormData(e.target);
    
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i> <span>ENVOI EN COURS...</span>`;

    try {
        const response = await fetch("{{ route('user.service-requests.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            btn.classList.remove('bg-rdc-blue');
            btn.classList.add('bg-emerald-500');
            btn.innerHTML = `<i class="fas fa-check"></i> <span>MESSAGE ENVOYÉ !</span>`;
            
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'envoi');
        }
    } catch (error) {
        btn.classList.remove('bg-rdc-blue');
        btn.classList.add('bg-red-500');
        btn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <span>ÉCHEC</span>`;
        btn.disabled = false;
        alert(error.message);
    }
});
</script>
@endsection
