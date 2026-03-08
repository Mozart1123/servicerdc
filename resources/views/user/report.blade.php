@extends('layouts.user')

@section('header_title', 'Signaler un Problème')

@section('content')
<div class="max-w-4xl mx-auto space-y-12 pb-20">
    <!-- Header -->
    <div class="text-center space-y-4">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-red-50 text-rdc-red shadow-inner text-3xl mb-4 animate-pulse">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Signaler une Anomalie</h2>
        <p class="text-slate-500 font-medium max-w-lg mx-auto leading-relaxed">
            Un bug, un comportement inapproprié ou un problème technique ? Signalez-le nous pour que nous puissions intervenir rapidement.
        </p>
    </div>

    <!-- Report Form Card -->
    <div class="bg-white rounded-[3.5rem] p-10 md:p-16 shadow-xl border border-red-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <form id="reportProblemForm" class="space-y-8 relative z-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Problem Type -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Type de Problème</label>
                    <select name="problem_type" required
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-500/30 focus:bg-white transition-all outline-none font-bold text-slate-900 appearance-none">
                        <option value="">Sélectionner le type</option>
                        <option value="bug">Bug Technique / Erreur Affichage</option>
                        <option value="harassment">Comportement Inapproprié</option>
                        <option value="scam">Suspicion de Fraude / Arnaque</option>
                        <option value="content">Contenu Illégal ou Choquant</option>
                        <option value="other">Autre Problème</option>
                    </select>
                </div>

                <!-- Urgency -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Niveau de Gravité</label>
                    <div class="flex items-center bg-slate-50 p-1.5 rounded-2xl border border-transparent">
                        <button type="button" onclick="setUrgency('low', this)" class="flex-1 py-3 px-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all urgency-btn" data-val="low">Faible</button>
                        <button type="button" onclick="setUrgency('medium', this)" class="flex-1 py-3 px-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all urgency-btn bg-white shadow-sm ring-1 ring-slate-100" data-val="medium">Moyen</button>
                        <button type="button" onclick="setUrgency('high', this)" class="flex-1 py-3 px-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all urgency-btn" data-val="high">Critique</button>
                        <input type="hidden" name="urgency" value="medium" id="urgencyInput">
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Objet du Signalement</label>
                <input type="text" name="subject" required
                       placeholder="Ex: Impossible de télécharger mon CV"
                       class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-500/30 focus:bg-white transition-all outline-none font-bold text-slate-900">
            </div>

            <!-- Description -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Détails de l'Incident</label>
                <textarea name="description" rows="6" required
                          placeholder="Merci de nous donner le maximum de précisions pour que nous puissions reproduire le problème..."
                          class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[2rem] focus:border-red-500/30 focus:bg-white transition-all outline-none font-bold text-slate-900 resize-none"></textarea>
            </div>

            <!-- Footer Action -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-4 border-t border-slate-50">
                <div class="flex items-center gap-3 text-red-500/70">
                    <i class="fas fa-user-shield"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Signalement Anonyme ou Identifié</span>
                </div>
                <button type="submit" id="submitBtn"
                        class="w-full md:w-auto px-12 py-5 bg-slate-900 text-white font-black rounded-3xl text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-red-600 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <span>Lancer l'Alerte</span>
                    <i class="fas fa-radiation"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Security Note -->
    <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-100 flex items-start gap-6">
        <div class="text-rdc-blue text-xl mt-1">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="space-y-2">
            <h4 class="font-bold text-slate-900 uppercase text-xs tracking-wider">Note de Sécurité</h4>
            <p class="text-xs text-slate-500 leading-relaxed font-medium">
                Toutes les fausses alertes répétées pourront entraîner une suspension temporaire de votre compte. 
                Utilisez ce service avec responsabilité pour nous aider à maintenir <strong>ServiceRDC</strong> sûr pour tous.
            </p>
        </div>
    </div>
</div>

<script>
function setUrgency(val, el) {
    document.getElementById('urgencyInput').value = val;
    document.querySelectorAll('.urgency-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'shadow-sm', 'ring-1', 'ring-slate-100', 'text-red-600', 'text-amber-600', 'text-emerald-600');
    });
    el.classList.add('bg-white', 'shadow-sm', 'ring-1', 'ring-slate-100');
    if(val === 'high') el.classList.add('text-red-600');
    if(val === 'medium') el.classList.add('text-amber-600');
    if(val === 'low') el.classList.add('text-emerald-600');
}

document.getElementById('reportProblemForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i> <span>ANALYSIS EN COURS...</span>`;

    // Simulated submission
    setTimeout(() => {
        btn.classList.remove('bg-slate-900');
        btn.classList.add('bg-emerald-500');
        btn.innerHTML = `<i class="fas fa-check"></i> <span>ALERTE TRANSMISE</span>`;
        
        setTimeout(() => {
            alert("Merci pour votre signalement. Nos administrateurs ont été prévenus.");
            window.location.href = "{{ route('user.dashboard') }}";
        }, 1500);
    }, 2000);
});
</script>
@endsection
