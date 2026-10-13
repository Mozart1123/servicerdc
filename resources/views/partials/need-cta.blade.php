{{-- Bouton "Décrire mon besoin" (catégories vides). Masqué pour les artisans et recruteurs.
     $categoryId (optionnel) pré-sélectionne la catégorie dans le formulaire. --}}
@if(!auth()->check() || auth()->user()->isClient())
<div class="mt-6">
    <p class="text-sm text-slate-500 mb-3">Décrivez votre besoin : nous le transmettons aux artisans disponibles près de chez vous.</p>
    <a href="{{ route('user.needs.create', array_filter(['category' => $categoryId ?? null])) }}"
       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md transition-all">
        <i class="fas fa-pen-to-square"></i> Décrire mon besoin
    </a>
</div>
@endif
