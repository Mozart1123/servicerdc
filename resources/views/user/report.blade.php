@extends($layout)

@section('header_title', 'Paramètres & Aide')
@section('header_subtitle', 'Signaler une anomalie ou obtenir de l\'assistance.')

@section($contentSection)
<div class="space-y-10">

    {{-- Espace Client affiche déjà les messages flash globalement dans son
         layout ; on ne le répète ici que pour artisan/recruteur (layouts.user
         ne l'affiche pas). --}}
    @unless ($isClient)
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    @endunless

    <section>
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Signaler un problème</h2>
            <p class="text-sm text-slate-500">Un bug, un comportement inapproprié ou un problème technique ? Signalez-le nous.</p>
        </div>

        <form id="reportProblemForm" method="POST" action="{{ route('user.report.submit') }}" class="space-y-6 max-w-3xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Problem Type -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Type de problème <span class="text-red-500">*</span></label>
                    <select name="problem_type" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none transition-all text-sm appearance-none @error('problem_type') border-red-400 @enderror">
                        <option value="">Sélectionner le type</option>
                        <option value="bug" @selected(old('problem_type') === 'bug')>Bug technique / Erreur d'affichage</option>
                        <option value="harassment" @selected(old('problem_type') === 'harassment')>Comportement inapproprié</option>
                        <option value="scam" @selected(old('problem_type') === 'scam')>Suspicion de fraude / Arnaque</option>
                        <option value="content" @selected(old('problem_type') === 'content')>Contenu illégal ou choquant</option>
                        <option value="other" @selected(old('problem_type') === 'other')>Autre problème</option>
                    </select>
                    @error('problem_type')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urgency -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Niveau de gravité</label>
                    <div class="flex items-center bg-slate-50 p-1 rounded-lg border border-slate-200">
                        <button type="button" onclick="setUrgency('low', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn text-slate-600" data-val="low">Faible</button>
                        <button type="button" onclick="setUrgency('medium', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn bg-white shadow-sm border border-slate-200 text-rdc-blue" data-val="medium">Moyen</button>
                        <button type="button" onclick="setUrgency('high', this)" class="flex-1 py-2 px-2 text-xs font-medium rounded-md transition-all urgency-btn text-slate-600" data-val="high">Critique</button>
                        <input type="hidden" name="urgency" value="{{ old('urgency', 'medium') }}" id="urgencyInput">
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Objet du signalement <span class="text-red-500">*</span></label>
                <input type="text" name="subject" required value="{{ old('subject') }}"
                       placeholder="Ex: Impossible de télécharger mon CV"
                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none transition-all text-sm @error('subject') border-red-400 @enderror">
                @error('subject')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Détails de l'incident <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required minlength="10"
                          placeholder="Merci de nous donner le maximum de précisions..."
                          class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none transition-all text-sm resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                <p class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fas fa-info-circle text-slate-400"></i>
                    Votre signalement sera traité de manière confidentielle.
                </p>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-2.5 bg-rdc-blue text-white font-medium text-sm rounded-lg hover:bg-rdc-blue-dark transition-colors flex items-center justify-center gap-2">
                    <span>Envoyer le signalement</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </section>

    <hr class="border-slate-100">

    <section>
        <div class="flex items-start gap-4 p-5 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="text-rdc-blue mt-0.5">
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
        btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-slate-200', 'text-rdc-blue', 'text-red-600');
        btn.classList.add('text-slate-600');
    });

    el.classList.remove('text-slate-600');
    el.classList.add('bg-white', 'shadow-sm', 'border', 'border-slate-200');

    if (val === 'high') el.classList.add('text-red-600');
    else el.classList.add('text-rdc-blue');
}

// Met en surbrillance le bouton de gravité correspondant à la valeur restaurée
// après une erreur de validation (old('urgency')), sans changer la logique
// de soumission : le formulaire est envoyé normalement au serveur.
document.addEventListener('DOMContentLoaded', function () {
    const current = document.getElementById('urgencyInput')?.value;
    if (!current) return;
    const btn = document.querySelector('.urgency-btn[data-val="' + current + '"]');
    if (btn) setUrgency(current, btn);
});

document.getElementById('reportProblemForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i> <span>Envoi en cours...</span>`;
});
</script>
@endsection
