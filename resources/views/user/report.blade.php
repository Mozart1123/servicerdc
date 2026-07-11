@extends($layout)

@section('header_title', 'Paramètres & Aide')
@section('header_subtitle', 'Signaler une anomalie ou obtenir de l\'assistance.')

@section($contentSection)
<div class="space-y-10">

    <section>
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Signaler un problème</h2>
            <p class="text-sm text-slate-500">Un bug, un comportement inapproprié ou un problème technique ? Signalez-le nous.</p>
        </div>

        <form id="reportProblemForm" class="space-y-6 max-w-3xl">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Problem Type -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Type de problème <span class="text-red-500">*</span></label>
                    <select name="problem_type" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm appearance-none">
                        <option value="">Sélectionner le type</option>
                        <option value="bug">Bug technique / Erreur d'affichage</option>
                        <option value="harassment">Comportement inapproprié</option>
                        <option value="scam">Suspicion de fraude / Arnaque</option>
                        <option value="content">Contenu illégal ou choquant</option>
                        <option value="other">Autre problème</option>
                    </select>
                </div>

                <!-- Urgency -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Niveau de gravité</label>
                    <div class="flex items-center bg-slate-50 p-1 rounded-lg border border-slate-200">
                        <button type="button" onclick="setUrgency('low', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn text-slate-600" data-val="low">Faible</button>
                        <button type="button" onclick="setUrgency('medium', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn bg-white shadow-sm border border-slate-200 text-[#16a3b0]" data-val="medium">Moyen</button>
                        <button type="button" onclick="setUrgency('high', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn text-slate-600" data-val="high">Critique</button>
                        <input type="hidden" name="urgency" value="medium" id="urgencyInput">
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Objet du signalement <span class="text-red-500">*</span></label>
                <input type="text" name="subject" required
                       placeholder="Ex: Impossible de télécharger mon CV"
                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Détails de l'incident <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          placeholder="Merci de nous donner le maximum de précisions..."
                          class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                <p class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fas fa-info-circle text-slate-400"></i>
                    Votre signalement sera traité de manière confidentielle.
                </p>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors flex items-center justify-center gap-2">
                    <span>Envoyer le signalement</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </section>

    <hr class="border-slate-100">

    <section>
        <div class="flex items-start gap-4 p-5 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="text-[#16a3b0] mt-0.5">
                <i class="fas fa-shield-alt text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm mb-1">Utilisation responsable</h4>
                <p class="text-sm text-slate-600">
                    Les fausses alertes répétées peuvent entraîner une restriction de votre compte. 
                    Aidez-nous à maintenir la plateforme sûre en signalant uniquement les problèmes réels.
                </p>
            </div>
        </div>
    </section>

</div>

<script>
function setUrgency(val, el) {
    document.getElementById('urgencyInput').value = val;
    document.querySelectorAll('.urgency-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-slate-200', 'text-[#16a3b0]', 'text-red-600');
        btn.classList.add('text-slate-600');
    });
    
    el.classList.remove('text-slate-600');
    el.classList.add('bg-white', 'shadow-sm', 'border', 'border-slate-200');
    
    if (val === 'high') el.classList.add('text-red-600');
    else el.classList.add('text-[#16a3b0]');
}

document.getElementById('reportProblemForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i> <span>Envoi en cours...</span>`;

    // Simulated submission
    setTimeout(() => {
        btn.classList.remove('bg-[#16a3b0]', 'hover:bg-[#138b96]');
        btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600');
        btn.innerHTML = `<i class="fas fa-check"></i> <span>Signalement envoyé</span>`;
        
        setTimeout(() => {
            alert("Merci pour votre signalement. Nos équipes ont été prévenues.");
            window.location.reload();
        }, 1500);
    }, 2000);
});
</script>
@endsection
