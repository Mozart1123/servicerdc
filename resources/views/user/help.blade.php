@extends($layout)

@section('header_title', 'Aide & Demandes')
@section('header_subtitle', 'Posez une question ou demandez l\'ajout d\'un service.')

@section($contentSection)
<div class="space-y-10">

    <section>
        <h2 class="text-lg font-bold text-slate-900 mb-6">Envoyer une demande</h2>

        <form id="helpRequestForm" class="space-y-6 max-w-3xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Sujet / Service souhaité <span class="text-red-500">*</span></label>
                    <input type="text" name="requested_service_name" required
                           placeholder="Ex: Service de livraison de nuit"
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Catégorie (optionnel)</label>
                    <select name="category_needed"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm appearance-none">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Ménage">Ménage & Nettoyage</option>
                        <option value="Réparation">Réparation & Travaux</option>
                        <option value="Transport">Transport & Logistique</option>
                        <option value="Education">Éducation & Formation</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Votre message / précisions <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          placeholder="Décrivez votre besoin ou posez votre question ici..."
                          class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <p class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-slate-400"></i>
                    Votre demande sera transmise directement à notre équipe.
                </p>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors flex items-center justify-center gap-2">
                    <span>Envoyer ma demande</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </section>

    @if(isset($latestRequests) && count($latestRequests) > 0)
    <hr class="border-slate-100">

    <section>
        <h2 class="text-lg font-bold text-slate-900 mb-6">Mes demandes récentes</h2>
        <div class="space-y-3 max-w-3xl">
            @foreach($latestRequests as $req)
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                        <i class="fas fa-comment-dots text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-900 text-sm">{{ $req->requested_service_name }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $req->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full self-start sm:self-auto
                    {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                    {{ $req->status === 'pending' ? 'En attente' : 'Répondu' }}
                </span>
            </div>
            @endforeach
        </div>
    </section>
    @endif

</div>

<script>
document.getElementById('helpRequestForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const formData = new FormData(e.target);
    
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i> <span>Envoi en cours...</span>`;

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
            btn.classList.remove('bg-[#16a3b0]', 'hover:bg-[#138b96]');
            btn.classList.add('bg-emerald-500');
            btn.innerHTML = `<i class="fas fa-check"></i> <span>Demande envoyée !</span>`;
            
            setTimeout(() => { location.reload(); }, 2000);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'envoi');
        }
    } catch (error) {
        btn.classList.remove('bg-[#16a3b0]', 'hover:bg-[#138b96]');
        btn.classList.add('bg-red-500');
        btn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <span>Échec</span>`;
        btn.disabled = false;
        alert(error.message);
    }
});
</script>
@endsection
