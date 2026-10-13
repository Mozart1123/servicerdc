@extends($layout)

@section('title', 'Décrire mon besoin | ProConnect')
@section('header_title', 'Décrire mon besoin')
@section('header_subtitle', 'Publiez votre demande : nous la transmettons aux artisans les plus adaptés près de chez vous.')

@php
    // Revenir directement à l'étape qui contient l'erreur renvoyée par le serveur.
    $errorStep = match (true) {
        $errors->hasAny(['category_id', 'service_type_id', 'precision']) => 1,
        $errors->hasAny(['description', 'photos', 'photos.*']) || collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'photos.')) => 2,
        $errors->hasAny(['commune_id', 'quartier']) => 3,
        $errors->hasAny(['timing', 'preferred_date']) => 4,
        $errors->hasAny(['budget_type', 'budget_amount', 'budget_min', 'budget_max']) => 5,
        default => 1,
    };
    $initial = [
        'step'          => $errors->any() ? $errorStep : 1,
        'categoryId'    => (int) old('category_id', $preselectCategory) ?: null,
        'serviceTypeId' => (int) old('service_type_id') ?: null,
        'precision'     => old('precision', ''),
        'description'   => old('description', ''),
        'communeId'     => (int) old('commune_id', $defaultCommuneId) ?: null,
        'quartier'      => old('quartier', $defaultQuartier ?? ''),
        'timing'        => old('timing', ''),
        'preferredDate' => old('preferred_date', ''),
        'budgetType'    => old('budget_type', ''),
        'budgetAmount'  => old('budget_amount', ''),
        'budgetMin'     => old('budget_min', ''),
        'budgetMax'     => old('budget_max', ''),
    ];
    $categoryList = $categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'icon' => $c->icon, 'image' => $c->image_url])->values();
    $communeList = $communes->map->only('id', 'name')->values();
    $stepLabels = ['Service', 'Description', 'Lieu', 'Date', 'Budget', 'Récapitulatif'];
@endphp

@section($contentSection)
<div class="max-w-3xl mx-auto pb-10"
     x-data="needForm(@js($initial), @js($categoryList), @js($serviceTypes), @js($communeList), {{ $maxPhotos }})">

    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
            <i class="fas fa-circle-exclamation text-xl shrink-0"></i>
            <p class="font-semibold text-sm">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm">
            <p class="font-bold mb-1">Merci de corriger les points suivants :</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
            @if(old('description'))
                <p class="mt-2 text-xs text-red-600">Pour votre sécurité, les photos ne sont pas conservées : ajoutez-les à nouveau.</p>
            @endif
        </div>
    @endif

    {{-- Progression --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 mb-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                Étape <span x-text="step"></span> / {{ count($stepLabels) }}
            </p>
            <p class="text-sm font-bold text-slate-900" x-text="stepLabels[step - 1]"></p>
        </div>
        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-rdc-blue rounded-full transition-all duration-300" :style="`width: ${step / {{ count($stepLabels) }} * 100}%`"></div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.needs.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-7"
          @submit="submitting = true" x-ref="form">
        @csrf

        {{-- Valeurs envoyées (les étapes masquées restent dans le formulaire) --}}
        <input type="hidden" name="category_id" :value="categoryId ?? ''">
        <input type="hidden" name="service_type_id" :value="serviceTypeId ?? ''">
        <input type="hidden" name="timing" :value="timing">
        <input type="hidden" name="budget_type" :value="budgetType">

        {{-- 1. Service --}}
        <section x-show="step === 1" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-1">De quel service avez-vous besoin ?</h2>
            <p class="text-sm text-slate-500 mb-5">Choisissez une catégorie.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <template x-for="cat in categories" :key="cat.id">
                    <button type="button" @click="selectCategory(cat.id)"
                            class="flex flex-col items-center gap-2 p-3 rounded-2xl border-2 text-center transition-all"
                            :class="categoryId === cat.id ? 'border-rdc-blue bg-rdc-blue/5' : 'border-slate-100 hover:border-slate-300'">
                        <span class="w-14 h-14 rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center text-rdc-blue text-xl">
                            <template x-if="cat.image"><img :src="cat.image" alt="" class="w-full h-full object-cover"></template>
                            <template x-if="!cat.image"><i class="fas" :class="cat.icon && cat.icon.startsWith('fa-') ? cat.icon : 'fa-toolbox'"></i></template>
                        </span>
                        <span class="text-xs font-bold text-slate-700 leading-tight" x-text="cat.name"></span>
                    </button>
                </template>
            </div>

            <div x-show="categoryId" x-cloak class="mt-6 space-y-3">
                <template x-if="typesFor(categoryId).length">
                    <div>
                        <p class="text-sm font-semibold text-slate-700 mb-2">Précisez si vous le souhaitez <span class="text-slate-400 font-normal">(facultatif)</span></p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="type in typesFor(categoryId)" :key="type.id">
                                <button type="button" @click="serviceTypeId = serviceTypeId === type.id ? null : type.id"
                                        class="px-3 h-9 rounded-xl border text-xs font-bold transition-all"
                                        :class="serviceTypeId === type.id ? 'bg-rdc-blue border-rdc-blue text-white' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                        x-text="type.title"></button>
                            </template>
                        </div>
                    </div>
                </template>
                <div>
                    <label for="precision" class="block text-sm font-semibold text-slate-700 mb-1">Intitulé de votre besoin <span class="text-slate-400 font-normal">(facultatif)</span></label>
                    <input type="text" id="precision" name="precision" maxlength="120" x-model="precision"
                           placeholder="Ex : Peinture intérieure d'un salon"
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
                </div>
            </div>

            @if($categories->isEmpty())
                <p class="text-sm text-slate-500">Aucune catégorie n'est disponible pour le moment.</p>
            @endif
        </section>

        {{-- 2. Description + photos --}}
        <section x-show="step === 2" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Décrivez votre besoin</h2>
            <p class="text-sm text-slate-500 mb-5">Plus vous êtes précis, plus les propositions seront justes.</p>

            <textarea name="description" rows="6" maxlength="2000" x-model="description"
                      placeholder="Ex : Je souhaite repeindre un salon de 20 m², murs et plafond, couleur blanc cassé. La peinture est déjà achetée."
                      class="w-full px-4 py-3 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm resize-none"></textarea>
            <p class="text-xs text-slate-400 text-right mt-1"><span x-text="description.length"></span> / 2000</p>

            <div class="mt-4">
                <p class="text-sm font-semibold text-slate-700 mb-2">Photos <span class="text-slate-400 font-normal">(facultatif, jusqu'à {{ $maxPhotos }})</span></p>
                <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden" x-ref="photoInput" @change="addPhotos($event)">
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                    <template x-for="(photo, index) in photoPreviews" :key="photo.url">
                        <div class="relative aspect-square rounded-xl overflow-hidden border border-slate-200">
                            <img :src="photo.url" alt="" class="w-full h-full object-cover">
                            <button type="button" @click="removePhoto(index)" aria-label="Retirer la photo"
                                    class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/60 text-white text-xs flex items-center justify-center">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    <button type="button" x-show="photoPreviews.length < maxPhotos" @click="$refs.photoInput.click()"
                            class="aspect-square rounded-xl border-2 border-dashed border-slate-300 text-slate-400 hover:border-rdc-blue hover:text-rdc-blue flex flex-col items-center justify-center gap-1 transition-colors">
                        <i class="fas fa-camera text-lg"></i>
                        <span class="text-[10px] font-bold">Ajouter</span>
                    </button>
                </div>
                <p class="text-xs text-red-500 mt-2" x-show="photoError" x-text="photoError"></p>
            </div>
        </section>

        {{-- 3. Lieu --}}
        <section x-show="step === 3" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Où le service doit-il être réalisé ?</h2>
            <p class="text-sm text-slate-500 mb-5">Nous contactons en priorité les artisans de votre commune.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="commune_id" class="block text-sm font-semibold text-slate-700 mb-1">Commune</label>
                    <select id="commune_id" name="commune_id" x-model.number="communeId"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
                        <option value="">— Choisir —</option>
                        <template x-for="commune in communes" :key="commune.id">
                            <option :value="commune.id" x-text="commune.name" :selected="communeId === commune.id"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="quartier" class="block text-sm font-semibold text-slate-700 mb-1">Quartier <span class="text-slate-400 font-normal">(facultatif)</span></label>
                    <input type="text" id="quartier" name="quartier" maxlength="120" x-model="quartier" placeholder="Ex : Matonge"
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
                </div>
            </div>
        </section>

        {{-- 4. Date --}}
        <section x-show="step === 4" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Pour quand ?</h2>
            <p class="text-sm text-slate-500 mb-5">Une demande urgente est envoyée aux artisans disponibles tout de suite.</p>

            <div class="space-y-3">
                @foreach(\App\Models\ServiceRequest::TIMINGS as $value => $label)
                    <button type="button" @click="timing = '{{ $value }}'"
                            class="w-full flex items-center gap-3 p-4 rounded-2xl border-2 text-left transition-all"
                            :class="timing === '{{ $value }}' ? 'border-rdc-blue bg-rdc-blue/5' : 'border-slate-100 hover:border-slate-300'">
                        <i class="fas {{ ['urgent' => 'fa-bolt text-amber-500', 'this_week' => 'fa-calendar-week text-rdc-blue', 'date' => 'fa-calendar-day text-emerald-500'][$value] }} text-lg w-6 text-center"></i>
                        <span class="font-bold text-sm text-slate-800">{{ $label }}</span>
                    </button>
                @endforeach
            </div>
            <div x-show="timing === 'date'" x-cloak class="mt-4">
                <label for="preferred_date" class="block text-sm font-semibold text-slate-700 mb-1">Date souhaitée</label>
                <input type="date" id="preferred_date" name="preferred_date" x-model="preferredDate"
                       min="{{ now()->toDateString() }}" max="{{ now()->addMonths(6)->toDateString() }}"
                       class="w-full sm:w-64 px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
            </div>
        </section>

        {{-- 5. Budget --}}
        <section x-show="step === 5" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Quel est votre budget ?</h2>
            <p class="text-sm text-slate-500 mb-5">En dollars. Pas d'idée du prix ? Aucun souci, les artisans vous feront une proposition.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach(['amount' => ['Un montant', 'fa-dollar-sign'], 'range' => ['Une fourchette', 'fa-arrows-left-right'], 'none' => ["Je n'ai pas d'idée", 'fa-circle-question']] as $value => [$label, $icon])
                    <button type="button" @click="budgetType = '{{ $value }}'"
                            class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all"
                            :class="budgetType === '{{ $value }}' ? 'border-rdc-blue bg-rdc-blue/5' : 'border-slate-100 hover:border-slate-300'">
                        <i class="fas {{ $icon }} text-rdc-blue text-lg"></i>
                        <span class="font-bold text-xs text-slate-800">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            <div x-show="budgetType === 'amount'" x-cloak class="mt-4">
                <label for="budget_amount" class="block text-sm font-semibold text-slate-700 mb-1">Montant ($)</label>
                <input type="number" id="budget_amount" name="budget_amount" min="1" max="1000000" step="1" x-model="budgetAmount"
                       class="w-full sm:w-64 px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
            </div>
            <div x-show="budgetType === 'range'" x-cloak class="mt-4 flex items-end gap-3">
                <div>
                    <label for="budget_min" class="block text-sm font-semibold text-slate-700 mb-1">De ($)</label>
                    <input type="number" id="budget_min" name="budget_min" min="0" max="1000000" step="1" x-model="budgetMin"
                           class="w-32 px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
                </div>
                <div>
                    <label for="budget_max" class="block text-sm font-semibold text-slate-700 mb-1">À ($)</label>
                    <input type="number" id="budget_max" name="budget_max" min="1" max="1000000" step="1" x-model="budgetMax"
                           class="w-32 px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-rdc-blue focus:border-rdc-blue outline-none text-sm">
                </div>
            </div>
        </section>

        {{-- 6. Récapitulatif --}}
        <section x-show="step === 6" x-cloak>
            <h2 class="text-lg font-bold text-slate-900 mb-5">Vérifiez votre demande</h2>
            <dl class="divide-y divide-slate-100 text-sm">
                <template x-for="row in summary()" :key="row.label">
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-slate-400 font-semibold shrink-0" x-text="row.label"></dt>
                        <dd class="text-slate-800 font-semibold text-right break-words min-w-0" x-text="row.value"></dd>
                    </div>
                </template>
            </dl>
            <div x-show="photoPreviews.length" class="flex gap-2 mt-3 overflow-x-auto">
                <template x-for="photo in photoPreviews" :key="photo.url">
                    <img :src="photo.url" alt="" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shrink-0">
                </template>
            </div>
            <p class="mt-5 text-xs text-slate-500 bg-slate-50 rounded-xl p-3">
                <i class="fas fa-shield-halved text-rdc-blue mr-1"></i>
                Votre numéro n'est jamais communiqué : vous échangez avec les artisans via la messagerie ProConnect.
            </p>
        </section>

        <p class="text-sm text-red-600 font-semibold mt-5" x-show="stepError" x-text="stepError"></p>

        {{-- Navigation --}}
        <div class="flex items-center justify-between gap-3 mt-7 pt-5 border-t border-slate-100">
            <button type="button" x-show="step > 1" @click="back()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </button>
            <span x-show="step === 1"></span>
            <button type="button" x-show="step < 6" @click="next()"
                    class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md">
                Continuer <i class="fas fa-arrow-right ml-1"></i>
            </button>
            <button type="submit" x-show="step === 6" :disabled="submitting"
                    class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-sm font-bold shadow-md disabled:opacity-60">
                <span x-show="!submitting"><i class="fas fa-paper-plane mr-1"></i> Publier ma demande</span>
                <span x-show="submitting" x-cloak><i class="fas fa-spinner fa-spin mr-1"></i> Publication…</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function needForm(initial, categories, serviceTypes, communes, maxPhotos) {
        return {
            ...initial,
            categories, serviceTypes, communes, maxPhotos,
            stepLabels: @js($stepLabels),
            photoFiles: [], photoPreviews: [], photoError: '', stepError: '', submitting: false,
            timingLabels: @js(\App\Models\ServiceRequest::TIMINGS),

            typesFor(categoryId) { return this.serviceTypes[categoryId] || []; },
            selectCategory(id) {
                if (this.categoryId !== id) this.serviceTypeId = null;
                this.categoryId = id;
            },
            validateStep() {
                const amount = v => v !== '' && v !== null && !isNaN(Number(v));
                switch (this.step) {
                    case 1: return this.categoryId ? '' : 'Choisissez une catégorie.';
                    case 2: return this.description.trim().length >= 15 ? '' : 'Décrivez votre besoin en au moins 15 caractères.';
                    case 3: return this.communeId ? '' : 'Choisissez votre commune.';
                    case 4:
                        if (!this.timing) return 'Choisissez quand vous avez besoin du service.';
                        return this.timing === 'date' && !this.preferredDate ? 'Choisissez la date souhaitée.' : '';
                    case 5:
                        if (!this.budgetType) return 'Indiquez votre budget ou choisissez « Je n\'ai pas d\'idée ».';
                        if (this.budgetType === 'amount' && !(amount(this.budgetAmount) && Number(this.budgetAmount) >= 1)) return 'Indiquez un montant valide.';
                        if (this.budgetType === 'range') {
                            if (!amount(this.budgetMin) || !amount(this.budgetMax)) return 'Indiquez les deux montants de la fourchette.';
                            if (Number(this.budgetMax) <= Number(this.budgetMin)) return 'Le maximum doit être supérieur au minimum.';
                        }
                        return '';
                }
                return '';
            },
            next() {
                this.stepError = this.validateStep();
                if (this.stepError) return;
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            back() { this.stepError = ''; this.step--; },

            addPhotos(event) {
                this.photoError = '';
                for (const file of event.target.files) {
                    if (this.photoFiles.length >= this.maxPhotos) { this.photoError = `Maximum ${this.maxPhotos} photos.`; break; }
                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) { this.photoError = 'Formats acceptés : JPG, PNG, WEBP.'; continue; }
                    if (file.size > 5 * 1024 * 1024) { this.photoError = 'Chaque photo doit faire moins de 5 Mo.'; continue; }
                    this.photoFiles.push(file);
                    this.photoPreviews.push({ url: URL.createObjectURL(file) });
                }
                this.syncPhotoInput();
            },
            removePhoto(index) {
                URL.revokeObjectURL(this.photoPreviews[index].url);
                this.photoFiles.splice(index, 1);
                this.photoPreviews.splice(index, 1);
                this.syncPhotoInput();
            },
            // Le champ fichier envoie exactement les photos affichées.
            syncPhotoInput() {
                const dt = new DataTransfer();
                this.photoFiles.forEach(f => dt.items.add(f));
                this.$refs.photoInput.files = dt.files;
            },

            summary() {
                const cat = this.categories.find(c => c.id === this.categoryId);
                const type = this.typesFor(this.categoryId).find(t => t.id === this.serviceTypeId);
                const commune = this.communes.find(c => c.id === this.communeId);
                let when = this.timingLabels[this.timing] || '';
                if (this.timing === 'date' && this.preferredDate) when = 'Le ' + new Date(this.preferredDate + 'T00:00').toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
                const budget = { amount: `$${this.budgetAmount}`, range: `$${this.budgetMin} – $${this.budgetMax}`, none: "Pas d'idée du prix" }[this.budgetType] || '';
                return [
                    { label: 'Service', value: [cat?.name, type?.title].filter(Boolean).join(' · ') },
                    { label: 'Intitulé', value: this.precision.trim() || '—' },
                    { label: 'Description', value: this.description.trim() },
                    { label: 'Lieu', value: [this.quartier.trim(), commune?.name].filter(Boolean).join(', ') },
                    { label: 'Date', value: when },
                    { label: 'Budget', value: budget },
                    { label: 'Photos', value: this.photoFiles.length ? `${this.photoFiles.length} photo(s)` : 'Aucune' },
                ];
            },
        };
    }
</script>
@endpush
