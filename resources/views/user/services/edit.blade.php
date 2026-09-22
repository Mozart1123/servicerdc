@extends('layouts.user')

@section('header_title', 'Modifier le Service')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">
    
    <div class="relative">
        <div class="absolute inset-0 bg-amber-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Modifier: {{ $service->title }}</h2>
            <p class="text-sm font-bold text-slate-400 mt-2 uppercase tracking-widest">Mettez à jour les détails de votre offre</p>
            
            <form action="{{ route('user.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="mt-10 space-y-8"
                  x-data="serviceForm()" x-init="init()">
                @csrf
                @method('PUT')
                
                <!-- Catégorie (Métier) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Catégorie de métier <span class="text-red-500">*</span></label>
                    <select name="category_id" required x-model="categoryId" @change="loadServiceTypes()"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="">Sélectionnez un domaine d'activité</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Type de service (Sous-service) -->
                <div class="space-y-2" x-show="serviceTypes.length > 0" x-transition>
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Type de service (Optionnel)</label>
                    <select name="service_type_id" x-model="serviceTypeId" @change="onServiceTypeChange()"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="">Sélectionnez le type de service (optionnel)</option>
                        <template x-for="st in serviceTypes" :key="st.id">
                            <option :value="st.id" x-text="st.title" :selected="st.id == serviceTypeId"></option>
                        </template>
                    </select>
                    @error('service_type_id')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Titre & Statut -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre du service <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required x-model="title"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        @error('title')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Statut <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none appearance-none">
                            <option value="active" {{ (old('status', $service->status) == 'active') ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ (old('status', $service->status) == 'inactive') ? 'selected' : '' }}>Inactif (Caché)</option>
                        </select>
                        @error('status')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Localisation -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Localisation (Ville, Commune) <span class="text-red-500">*</span></label>
                    <input type="text" name="location" required value="{{ old('location', $service->location) }}"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    @error('location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Tarification -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Tarification <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label :class="pricingType === 'fixed' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                               class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                            <input type="radio" name="pricing_type" value="fixed" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                            <span class="text-xs font-bold text-slate-800 leading-tight">Prix fixe</span>
                        </label>
                        <label :class="pricingType === 'starting_from' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                               class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                            <input type="radio" name="pricing_type" value="starting_from" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                            <span class="text-xs font-bold text-slate-800 leading-tight">À partir de</span>
                        </label>
                        <label :class="pricingType === 'quote' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                               class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                            <input type="radio" name="pricing_type" value="quote" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                            <span class="text-xs font-bold text-slate-800 leading-tight">Sur devis</span>
                        </label>
                    </div>
                    @error('pricing_type')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror

                    <!-- Champ Prix -->
                    <div class="space-y-2 relative" x-show="pricingType !== 'quote'" x-transition>
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">
                            <span x-text="pricingType === 'starting_from' ? 'Prix minimum (USD)' : 'Prix (USD)'"></span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" step="0.01" min="0" placeholder="0.00" x-model="price"
                               :required="pricingType !== 'quote'"
                               :disabled="pricingType === 'quote'"
                               class="w-full pl-6 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <span class="absolute right-4 top-[38px] text-slate-400 font-bold">$</span>
                        @error('price')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Durée approximative & Lieu de prestation (côte à côte sur desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Durée approximative <span class="text-red-500">*</span></label>
                        <select name="duration" required x-model="duration"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            <option value="">Sélectionnez la durée</option>
                            <option value="30_minutes">30 minutes</option>
                            <option value="45_minutes">45 minutes</option>
                            <option value="1_hour">1 heure</option>
                            <option value="1h30">1 h 30</option>
                            <option value="2_hours">2 heures</option>
                            <option value="3_hours">3 heures</option>
                            <option value="half_day">Demi-journée</option>
                            <option value="full_day">Journée complète</option>
                            <option value="multiple_days">Plusieurs jours</option>
                            <option value="to_define">À définir avec le client</option>
                        </select>
                        @error('duration')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Lieu de prestation <span class="text-red-500">*</span></label>
                        <select name="service_location" required x-model="serviceLocation"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            <option value="">Où proposez-vous ce service ?</option>
                            <option value="home">À domicile</option>
                            <option value="provider">Chez moi</option>
                            <option value="both">Les deux</option>
                        </select>
                        <p class="text-[10px] text-slate-400 font-bold pl-4">Sélectionnez où vous proposez habituellement ce service.</p>
                        @error('service_location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Disponibilité -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Disponibilité <span class="text-red-500">*</span></label>
                        <select name="availability" required x-model="availability"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            <option value="">Sélectionnez votre disponibilité</option>
                            <option value="daily">Tous les jours</option>
                            <option value="weekdays">Du lundi au vendredi</option>
                            <option value="monday_saturday">Du lundi au samedi</option>
                            <option value="weekends">Week-ends uniquement</option>
                            <option value="appointment">Sur rendez-vous</option>
                            <option value="availability_based">Selon disponibilité</option>
                        </select>
                        @error('availability')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>

                    <!-- Préavis minimum si sur rendez-vous -->
                    <div class="space-y-2" x-show="availability === 'appointment'" x-transition>
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Préavis minimum <span class="text-red-500">*</span></label>
                        <select name="min_notice" x-model="minNotice" :required="availability === 'appointment'"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            <option value="none">Aucun</option>
                            <option value="2_hours">2 heures</option>
                            <option value="6_hours">6 heures</option>
                            <option value="12_hours">12 heures</option>
                            <option value="24_hours">24 heures</option>
                            <option value="48_hours">48 heures</option>
                            <option value="72_hours">72 heures</option>
                        </select>
                        @error('min_notice')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Description détaillée (Optionnelle)</label>
                    <textarea name="description" rows="5"
                              class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none resize-none">{{ old('description', $service->description) }}</textarea>
                    @error('description')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Image Principale -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Image Principale (Miniature)</label>
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        {{-- Aperçu : affiche le fichier sélectionné en priorité, sinon l'image actuelle --}}
                        <div class="relative w-40 h-40 rounded-2xl overflow-hidden border-2 flex-none transition-all"
                             :class="mainImagePreview ? 'border-rdc-blue shadow-md' : 'border-slate-100 shadow-sm'">
                            <img
                                :src="mainImagePreview ?? '{{ $service->service_image ? Storage::url($service->service_image) : '' }}'"
                                x-show="mainImagePreview || {{ $service->service_image ? 'true' : 'false' }}"
                                class="w-full h-full object-cover"
                            >
                            <div x-show="!mainImagePreview && {{ $service->service_image ? 'false' : 'true' }}"
                                 class="w-full h-full bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300">
                                <i class="fas fa-image text-2xl mb-2"></i>
                                <span class="text-[8px] font-black uppercase">Aucune</span>
                            </div>
                            {{-- Badge « Aperçu » lorsqu'un fichier est sélectionné --}}
                            <div x-show="mainImagePreview"
                                 class="absolute bottom-0 inset-x-0 bg-rdc-blue/80 text-white text-[8px] font-black uppercase text-center py-1">
                                Aperçu
                            </div>
                            {{-- Overlay « Actuelle » pour l'image enregistrée --}}
                            <div x-show="!mainImagePreview && {{ $service->service_image ? 'true' : 'false' }}"
                                 class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-white text-[8px] font-black uppercase">Actuelle</span>
                            </div>
                        </div>

                        <div class="flex-1 w-full relative border-2 border-dashed rounded-3xl p-8 text-center transition-colors group"
                             :class="mainImagePreview ? 'border-rdc-blue bg-rdc-blue/5' : 'border-slate-200 hover:bg-slate-50'">
                            <input
                                type="file"
                                name="service_image"
                                accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @change="
                                    const f = $event.target.files[0];
                                    if (f) {
                                        const reader = new FileReader();
                                        reader.onload = e => { mainImagePreview = e.target.result; };
                                        reader.readAsDataURL(f);
                                    } else {
                                        mainImagePreview = null;
                                    }
                                "
                            >
                            <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-lg mx-auto mb-3 group-hover:scale-110 transition-transform"
                                 :class="mainImagePreview ? 'text-rdc-blue' : 'text-rdc-blue'">
                                <i x-show="!mainImagePreview" class="fas fa-camera"></i>
                                <i x-show="mainImagePreview" class="fas fa-check"></i>
                            </div>
                            <h4 class="font-bold text-[11px] mb-1" :class="mainImagePreview ? 'text-rdc-blue' : 'text-slate-900'">
                                <span x-show="!mainImagePreview">Remplacer l'image principale</span>
                                <span x-show="mainImagePreview">Image sélectionnée — cliquer pour changer</span>
                            </h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Images existantes -->
                @php $currentGallery = $service->gallery_images ?? $service->images ?? []; @endphp
                @if(is_array($currentGallery) && count($currentGallery) > 0)
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Images actuelles</label>
                    <div class="flex gap-4 overflow-x-auto pb-4">
                        @foreach($currentGallery as $index => $img)
                            <div class="relative flex-none w-32 h-32 rounded-2xl overflow-hidden">
                                <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                                <button
                                    type="button"
                                    onclick="if(confirm('Supprimer cette image ?')) document.getElementById('deleteImgForm-{{ $index }}').submit()"
                                    class="absolute top-1.5 right-1.5 w-8 h-8 bg-red-500/90 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-110 z-10"
                                    title="Supprimer cette image"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Nouvelles Images -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Ajouter de nouvelles images (Max 5)</label>
                    <div class="relative border-2 border-dashed rounded-3xl p-10 text-center transition-colors group"
                         :class="newGalleryPreviews.length > 0 ? 'border-amber-400 bg-amber-50/50' : 'border-slate-200 hover:bg-slate-50'">
                        <input
                            type="file"
                            name="images[]"
                            multiple
                            accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            @change="
                                newGalleryPreviews = [];
                                const files = Array.from($event.target.files).slice(0, 5);
                                files.forEach(f => {
                                    const reader = new FileReader();
                                    reader.onload = e => { newGalleryPreviews.push(e.target.result); };
                                    reader.readAsDataURL(f);
                                });
                            "
                        >
                        {{-- Aperçus galerie --}}
                        <template x-if="newGalleryPreviews.length === 0">
                            <div>
                                <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-amber-500 text-2xl mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h4 class="font-bold text-slate-900 mb-1">Cliquez ou glissez vos images ici</h4>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG, GIF (Max 2MB)</p>
                            </div>
                        </template>
                        <template x-if="newGalleryPreviews.length > 0">
                            <div>
                                <div class="flex flex-wrap gap-3 justify-center mb-3 pointer-events-none">
                                    <template x-for="(src, i) in newGalleryPreviews" :key="i">
                                        <img :src="src" class="w-20 h-20 object-cover rounded-xl border-2 border-amber-300 shadow-sm">
                                    </template>
                                </div>
                                <p class="text-xs font-black text-amber-600 uppercase tracking-widest">
                                    <span x-text="newGalleryPreviews.length"></span> image(s) sélectionnée(s) — cliquer pour modifier
                                </p>
                            </div>
                        </template>
                    </div>
                    @error('images.*')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <div class="pt-6 flex gap-4 border-t border-slate-100">
                    <a href="{{ route('user.services.my') }}" class="px-8 py-5 bg-slate-100 text-slate-600 font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Annuler</a>
                    <button type="submit" class="flex-1 px-8 py-5 bg-amber-500 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 transition-all">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<!-- Forms for deleting images inline -->
@if(is_array($currentGallery))
    @foreach($currentGallery as $index => $img)
        <form id="deleteImgForm-{{ $index }}" action="{{ route('user.services.remove-image', $service->id) }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="image_index" value="{{ $index }}">
        </form>
    @endforeach
@endif

<script>
function serviceForm() {
    return {
        categoryId: '{{ old('category_id', $service->category_id) }}',
        serviceTypeId: '{{ old('service_type_id', $service->service_type_id) }}',
        title: '{{ old('title', addslashes($service->title)) }}',
        pricingType: '{{ old('pricing_type', $service->pricing_type ?? 'fixed') }}',
        price: '{{ old('price', $service->price) }}',
        duration: '{{ old('duration', $service->duration) }}',
        serviceLocation: '{{ old('service_location', $service->service_location) }}',
        availability: '{{ old('availability', $service->availability) }}',
        minNotice: '{{ old('min_notice', $service->min_notice ?? 'none') }}',
        serviceTypes: [],
        // Aperçu instantané — uniquement côté navigateur, aucune requête réseau
        mainImagePreview: null,    // data-URL de l'image principale sélectionnée
        newGalleryPreviews: [],    // tableau de data-URLs pour les nouvelles images
        init() {
            if (this.categoryId) {
                this.loadServiceTypes();
            }
        },
        loadServiceTypes() {
            if (!this.categoryId) {
                this.serviceTypes = [];
                this.serviceTypeId = '';
                return;
            }
            fetch(`/api/categories/${this.categoryId}/service-types`)
                .then(response => response.json())
                .then(data => {
                    this.serviceTypes = data;
                });
        },
        onServiceTypeChange() {
            const selected = this.serviceTypes.find(st => st.id == this.serviceTypeId);
            if (selected) {
                this.title = selected.title;
            }
        }
    }
}
</script>
@endsection
