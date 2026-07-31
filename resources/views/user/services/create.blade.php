@extends('layouts.user')

@section('header_title', 'Ajouter un Service')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">
    
    <div class="relative">
        <div class="absolute inset-0 bg-rdc-blue/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Proposer un service</h2>
            <p class="text-sm font-bold text-slate-400 mt-2 uppercase tracking-widest">Remplissez les détails pour publier votre offre sur la plateforme</p>
            
            <form action="{{ route('user.services.store') }}" method="POST" enctype="multipart/form-data" class="mt-10 space-y-8"
                  x-data="serviceForm()" x-init="init()">
                @csrf

                {{-- Message d'erreur de limite d'abonnement --}}
                @if(session('error'))
                    <div class="mb-2 px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 font-medium text-sm flex items-center justify-between gap-3 flex-wrap">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            {{ session('error') }}
                        </span>
                        @if(session('upgrade_url'))
                            <a href="{{ session('upgrade_url') }}" class="text-red-700 underline font-semibold whitespace-nowrap">
                                Voir les abonnements →
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Catégorie (Métier) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Catégorie de métier <span class="text-red-500">*</span></label>
                    <select name="category_id" required x-model="categoryId" @change="loadServiceTypes()"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="">Sélectionnez un domaine d'activité</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Types de services (Sous-services — Sélection multiple) -->
                <div class="space-y-3" x-show="serviceTypes.length > 0" x-transition>
                    <div class="flex items-center justify-between pl-4">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest">
                            Types de services <span class="text-red-500">*</span>
                            <span class="text-slate-400 font-medium normal-case tracking-normal ml-1">(cochez un ou plusieurs)</span>
                        </label>
                        <div class="flex gap-3">
                            <button type="button" @click="selectAllServiceTypes()"
                                    class="text-[10px] font-black text-rdc-blue hover:underline uppercase tracking-wider">Tout sélectionner</button>
                            <button type="button" @click="clearServiceTypes()"
                                    class="text-[10px] font-black text-slate-400 hover:text-slate-600 hover:underline uppercase tracking-wider">Tout vider</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="st in serviceTypes" :key="st.id">
                            <label :for="'st-' + st.id"
                                   :class="selectedTypeIds.includes(st.id) ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                                   class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                <input type="checkbox"
                                       :id="'st-' + st.id"
                                       name="service_type_ids[]"
                                       :value="st.id"
                                       @change="onCheckboxChange(st.id, $event)"
                                       :checked="selectedTypeIds.includes(st.id)"
                                       class="w-4 h-4 accent-rdc-blue rounded shrink-0">
                                <span class="text-xs font-bold text-slate-800 leading-tight" x-text="st.title"></span>
                            </label>
                        </template>
                    </div>

                    <!-- Compteur de sélection -->
                    <p x-show="selectedTypeIds.length > 0"
                       class="text-[10px] font-black text-rdc-blue pl-4 uppercase tracking-widest">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span x-text="selectedTypeIds.length + ' sous-service(s) sélectionné(s) — ' + selectedTypeIds.length + ' offre(s) sera/seront créée(s)'"></span>
                    </p>

                    @error('service_type_ids')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    @error('service_type_ids.*')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Titre -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre du service <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ex: Réparation plomberie générale" x-model="title"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    @error('title')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>
                <!-- Prix SEULEMENT (Grille mise à jour) -->
                <div class="space-y-2 relative">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Prix de base ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" step="0.01" min="0" required placeholder="0.00" value="{{ old('price') }}"
                           class="w-full pl-6 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    <span class="absolute right-4 top-[38px] text-slate-400 font-bold">$</span>
                    @error('price')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Localisation -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Localisation (Ville, Commune) <span class="text-red-500">*</span></label>
                    <input type="text" name="location" required placeholder="Ex: Kinshasa, Gombe" value="{{ old('location') }}"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    @error('location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Description détaillée (Optionnelle)</label>
                    <textarea name="description" rows="5" placeholder="Décrivez votre service en détail, ce qui est inclus, votre matériel, vos spécialités..."
                              class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none resize-none">{{ old('description') }}</textarea>
                    @error('description')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Images -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between pl-4 mb-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Images du service</label>
                        <span id="image-counter" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">0/5 images</span>
                    </div>
                    <div class="relative border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center hover:bg-slate-50 transition-colors group" id="upload-zone">
                        <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-rdc-blue text-2xl mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-1">Cliquez ou glissez vos images ici</h4>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG, GIF (Max 5 images)</p>
                    </div>

                    <!-- Grille d'aperçu -->
                    <div id="image-preview-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-4 hidden">
                        <!-- Les vignettes s'afficheront ici via JS -->
                    </div>

                    @error('images.*')<span class="text-xs text-red-500 pl-4 font-bold block mt-1">{{ $message }}</span>@enderror
                    @error('images')<span class="text-xs text-red-500 pl-4 font-bold block mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="pt-6 flex gap-4 border-t border-slate-100">
                    <a href="{{ route('user.services.my') }}" class="px-8 py-5 bg-slate-100 text-slate-600 font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Annuler</a>
                    <button type="submit" class="flex-1 px-8 py-5 bg-rdc-blue text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                        <span x-show="selectedTypeIds.length <= 1">
                            <i class="fas fa-paper-plane mr-1"></i> Publier mon service
                        </span>
                        <span x-show="selectedTypeIds.length > 1">
                            <i class="fas fa-paper-plane mr-1"></i> Publier <span x-text="selectedTypeIds.length"></span> services
                        </span>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('images-input');
        const previewContainer = document.getElementById('image-preview-container');
        const imageCounter = document.getElementById('image-counter');
        const maxFiles = 5;
        
        let dataTransfer = new DataTransfer();

        fileInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            
            newFiles.forEach(file => {
                if (dataTransfer.items.length < maxFiles) {
                    dataTransfer.items.add(file);
                }
            });

            fileInput.files = dataTransfer.files;
            updatePreviews();
        });

        function updatePreviews() {
            previewContainer.innerHTML = '';
            const files = dataTransfer.files;
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
            }

            imageCounter.textContent = `${files.length}/${maxFiles} images`;

            Array.from(files).forEach((file, index) => {
                const url = URL.createObjectURL(file);
                
                const div = document.createElement('div');
                div.className = 'relative aspect-square rounded-2xl overflow-hidden border border-slate-200 group shadow-sm bg-slate-50';
                
                const img = document.createElement('img');
                img.src = url;
                img.className = 'w-full h-full object-cover';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-2 right-2 w-7 h-7 bg-white/90 text-red-500 rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 hover:text-red-600 z-20';
                removeBtn.innerHTML = '<i class="fas fa-times text-xs"></i>';
                removeBtn.onclick = (e) => {
                    e.preventDefault();
                    removeFile(index);
                };
                
                div.appendChild(img);
                div.appendChild(removeBtn);
                previewContainer.appendChild(div);
            });
        }

        function removeFile(indexToRemove) {
            const newDataTransfer = new DataTransfer();
            const files = Array.from(dataTransfer.files);
            
            files.forEach((file, index) => {
                if (index !== indexToRemove) {
                    newDataTransfer.items.add(file);
                }
            });
            
            dataTransfer = newDataTransfer;
            fileInput.files = dataTransfer.files;
            updatePreviews();
        }
    });
</script>

<script>
function serviceForm() {
    return {
        categoryId: '{{ old('category_id') }}',
        title: '{{ old('title') }}',
        serviceTypes: [],
        selectedTypeIds: @json(old('service_type_ids', [])),

        init() {
            if (this.categoryId) {
                this.loadServiceTypes();
            }
        },

        loadServiceTypes() {
            if (!this.categoryId) {
                this.serviceTypes = [];
                this.selectedTypeIds = [];
                return;
            }
            fetch(`/api/categories/${this.categoryId}/service-types`)
                .then(r => r.json())
                .then(data => {
                    this.serviceTypes = data;
                    // Restore old checked state on validation error
                    this.selectedTypeIds = this.selectedTypeIds.map(id => parseInt(id));
                });
        },

        onCheckboxChange(id, event) {
            id = parseInt(id);
            if (event.target.checked) {
                if (!this.selectedTypeIds.includes(id)) {
                    this.selectedTypeIds.push(id);
                }
            } else {
                this.selectedTypeIds = this.selectedTypeIds.filter(x => x !== id);
            }
        },

        selectAllServiceTypes() {
            this.selectedTypeIds = this.serviceTypes.map(st => st.id);
        },

        clearServiceTypes() {
            this.selectedTypeIds = [];
        },
    }
}
</script>
@endsection
