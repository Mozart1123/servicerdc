@extends('layouts.admin')

@section('title', 'Gestion des Catégories')
@section('header_title', 'Catégories')
@section('page_title', 'Architecture des Services')
@section('page_subtitle', 'Gérez les domaines d\'expertise et les catégories de services disponibles sur la plateforme.')

@section('content')
<div x-data="categoryManager()" class="space-y-6">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 font-bold text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="px-5 py-4 bg-red-50 border border-red-100 rounded-2xl text-red-700 font-bold text-sm">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                {{ $categories->total() }} domaine(s) enregistré(s)
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCreate('import')"
                    class="flex items-center gap-2 px-4 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i class="fas fa-file-import text-rdc-blue"></i>
                Importer en masse (Texte)
            </button>

            <button @click="openCreate('form')"
                    class="flex items-center gap-2 px-5 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rdc-blue transition-all shadow-sm hover:shadow-lg hover:shadow-blue-500/20">
                <i class="fas fa-plus text-[10px]"></i>
                Nouvelle catégorie
            </button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- LISTE DES CATÉGORIES                                         --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">

        @forelse($categories as $category)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-50 last:border-b-0 hover:bg-slate-50/40 transition-colors group">

                {{-- Miniature --}}
                <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 bg-gradient-to-br from-[#1E9CB5] to-[#090D16] flex items-center justify-center shadow-sm">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <i class="{{ $category->icon ?? 'fas fa-tags' }} text-white text-sm"></i>
                    @endif
                </div>

                {{-- Infos principale --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900 truncate">{{ $category->name }}</p>
                    @if($category->description)
                        <p class="text-[10px] text-slate-400 font-bold truncate mt-0.5">{{ Str::limit($category->description, 60) }}</p>
                    @endif
                </div>

                {{-- Sous-services résumé --}}
                <div class="hidden md:block flex-1 min-w-0">
                    @if($category->serviceTypes->count() > 0)
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">
                            {{ $category->serviceTypes->count() }} sous-service(s)
                        </p>
                        <p class="text-[10px] text-slate-400 font-bold truncate">
                            {{ $category->serviceTypes->take(3)->pluck('title')->join(', ') }}{{ $category->serviceTypes->count() > 3 ? '...' : '' }}
                        </p>
                    @else
                        <span class="text-[10px] text-slate-300 font-bold italic">Aucun sous-service</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="openEdit({{ json_encode([
                        'id'           => $category->id,
                        'name'         => $category->name,
                        'slug'         => $category->slug,
                        'description'  => $category->description ?? '',
                        'image_url'    => $category->image_url,
                        'service_types'=> $category->serviceTypes->pluck('title')->toArray(),
                    ]) }})"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue hover:border-rdc-blue shadow-sm transition-all"
                            title="Modifier">
                        <i class="fas fa-pen text-xs"></i>
                    </button>

                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Supprimer « {{ $category->name }} » ? Les sous-services associés seront également supprimés.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 shadow-sm transition-all">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-200 flex items-center justify-center text-3xl mb-4 shadow-inner">
                    <i class="fas fa-folder-tree"></i>
                </div>
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucune catégorie</h4>
                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">
                    Cliquez sur « Nouvelle catégorie » pour commencer.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
        <div class="px-6 py-4">{{ $categories->links() }}</div>
    @endif


    {{-- ============================================================ --}}
    {{-- PANNEAU LATÉRAL (CRÉATION / MODIFICATION / IMPORT)          --}}
    {{-- ============================================================ --}}
    <div x-show="panelOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex items-start justify-end"
         style="display:none">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" @click="closePanel()"></div>

        {{-- Panel --}}
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative z-50 w-full max-w-xl h-screen bg-white shadow-2xl flex flex-col overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="text-base font-black text-slate-900 uppercase tracking-wide"
                        x-text="isEdit ? 'Modifier la catégorie' : 'Création de catégories'"></h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                       x-text="isEdit ? 'Mettez à jour les détails de la catégorie' : 'Ajoutez une ou plusieurs catégories d\'un coup'"></p>
                </div>
                <button @click="closePanel()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Tabs Header (Only in Create Mode) --}}
            <template x-if="!isEdit">
                <div class="px-8 border-b border-slate-100 flex gap-6 bg-slate-50/50 shrink-0">
                    <button type="button"
                            @click="activeTab = 'form'"
                            :class="activeTab === 'form' ? 'border-rdc-blue text-rdc-blue font-black' : 'border-transparent text-slate-400 font-bold hover:text-slate-600'"
                            class="py-3.5 text-xs uppercase tracking-widest border-b-2 transition-all flex items-center gap-2">
                        <i class="fas fa-layer-group text-xs"></i>
                        Formulaire en lot (<span x-text="items.length"></span>)
                    </button>

                    <button type="button"
                            @click="activeTab = 'import'"
                            :class="activeTab === 'import' ? 'border-rdc-blue text-rdc-blue font-black' : 'border-transparent text-slate-400 font-bold hover:text-slate-600'"
                            class="py-3.5 text-xs uppercase tracking-widest border-b-2 transition-all flex items-center gap-2">
                        <i class="fas fa-file-alt text-xs"></i>
                        Import Texte (<span x-text="parsedImport.length"></span>)
                    </button>
                </div>
            </template>

            {{-- Panel Body --}}
            <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">

                {{-- ============================================================ --}}
                {{-- TAB 1: FORMULAIRE RÉPÉTABLE (MODE CRÉATION)                 --}}
                {{-- ============================================================ --}}
                <div x-show="!isEdit && activeTab === 'form'">
                    <form id="createBatchForm"
                          action="{{ route('admin.categories.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf

                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-6 bg-slate-50/70 border border-slate-100 rounded-3xl space-y-4 relative group">

                                {{-- Card Header & Remove Button --}}
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                    <span class="text-xs font-black text-rdc-blue uppercase tracking-widest flex items-center gap-2">
                                        <i class="fas fa-folder text-[10px]"></i>
                                        <span x-text="'Catégorie #' + (index + 1)"></span>
                                    </span>

                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="px-2.5 py-1 text-[10px] font-black text-red-500 hover:text-white hover:bg-red-500 rounded-lg uppercase tracking-wider transition-all">
                                        <i class="fas fa-trash-alt mr-1"></i> Supprimer
                                    </button>
                                </div>

                                {{-- Main inputs: Photo + Nom & Description --}}
                                <div class="flex flex-col sm:flex-row gap-4 items-start">

                                    {{-- Photo Upload --}}
                                    <div class="shrink-0">
                                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Photo</label>
                                        <label :for="'img-input-' + index"
                                               class="w-20 h-20 rounded-2xl bg-white border-2 border-dashed border-slate-200 flex items-center justify-center cursor-pointer hover:border-rdc-blue transition-all overflow-hidden relative shadow-sm group">
                                            <img :id="'preview-' + index" src="" alt="" class="w-full h-full object-cover hidden absolute inset-0 rounded-2xl">
                                            <div :id="'placeholder-' + index" class="flex flex-col items-center gap-1 text-slate-300 group-hover:text-rdc-blue transition-colors">
                                                <i class="fas fa-camera text-lg"></i>
                                                <span class="text-[7px] font-black uppercase tracking-wider">Photo</span>
                                            </div>
                                        </label>
                                        <input type="file"
                                               :id="'img-input-' + index"
                                               :name="'categories[' + index + '][image]'"
                                               accept="image/*"
                                               class="hidden"
                                               @change="onFileChange($event, index)">
                                    </div>

                                    {{-- Nom + Description --}}
                                    <div class="flex-1 w-full space-y-3">
                                        <div>
                                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Nom du domaine <span class="text-red-500">*</span></label>
                                            <input type="text"
                                                   :name="'categories[' + index + '][name]'"
                                                   x-model="item.name"
                                                   required
                                                   class="w-full px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                                                   placeholder="ex: Plomberie">
                                        </div>

                                        <div>
                                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Description courte</label>
                                            <input type="text"
                                                   :name="'categories[' + index + '][description]'"
                                                   x-model="item.description"
                                                   class="w-full px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                                                   placeholder="ex: Travaux d'installation et de dépannage...">
                                        </div>
                                    </div>
                                </div>

                                {{-- Sous-services (Pleine largeur) --}}
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                        Sous-services <span class="text-[7px] font-bold text-slate-300 normal-case tracking-normal">(un par ligne)</span>
                                    </label>
                                    <textarea :name="'categories[' + index + '][services]'"
                                              x-model="item.services"
                                              rows="3"
                                              class="w-full px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                                              placeholder="ex:&#10;Réparation de fuites&#10;Débouchage canalisation&#10;Pose de robinets"></textarea>
                                </div>

                            </div>
                        </template>

                        {{-- Bouton Ajouter une autre catégorie --}}
                        <button type="button"
                                @click="addItem()"
                                class="w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 border border-slate-200/60">
                            <i class="fas fa-plus text-[10px]"></i>
                            + Ajouter une autre catégorie
                        </button>
                    </form>
                </div>


                {{-- ============================================================ --}}
                {{-- TAB 2: IMPORT TEXTE STRUCTURÉ (MODE CRÉATION)               --}}
                {{-- ============================================================ --}}
                <div x-show="!isEdit && activeTab === 'import'" class="space-y-6">
                    <form id="importTextForm"
                          action="{{ route('admin.categories.store') }}"
                          method="POST"
                          class="space-y-5">
                        @csrf

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    Coller le texte structuré <span class="text-red-500">*</span>
                                </label>
                                <button type="button" @click="insertExampleText()" class="text-[10px] font-bold text-rdc-blue hover:underline">
                                    Charger un exemple
                                </button>
                            </div>

                            <textarea name="import_text"
                                      x-model="importRawText"
                                      @input="parseText()"
                                      rows="8"
                                      required
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-mono font-medium text-slate-800 focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none leading-relaxed"
                                      placeholder="## Nom de la catégorie&#10;Description de la catégorie&#10;- Sous-service 1&#10;- Sous-service 2&#10;&#10;## Nom de la deuxième catégorie&#10;Description...&#10;- Autre sous-service"></textarea>
                        </div>

                        {{-- Guide de formatage rapide --}}
                        <div class="p-4 bg-blue-50/50 border border-blue-100/80 rounded-2xl text-[11px] text-slate-600 space-y-1">
                            <p class="font-bold text-rdc-blue uppercase tracking-wide text-[9px] mb-1">
                                <i class="fas fa-info-circle"></i> Format attendu :
                            </p>
                            <p>• <code class="font-mono bg-white px-1 py-0.5 rounded text-rdc-blue">## Nom</code> : Crée une nouvelle catégorie</p>
                            <p>• Texte juste après : Description de la catégorie</p>
                            <p>• <code class="font-mono bg-white px-1 py-0.5 rounded text-rdc-blue">- Sous-service</code> ou <code class="font-mono bg-white px-1 py-0.5 rounded text-rdc-blue">* Sous-service</code> : Crée un sous-service</p>
                        </div>
                    </form>

                    {{-- Prévisualisation dynamique --}}
                    <div class="space-y-3 border-t border-slate-100 pt-5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-eye text-rdc-blue"></i>
                                Aperçu avant création
                            </h4>
                            <span class="text-[10px] font-black bg-rdc-blue/10 text-rdc-blue px-2.5 py-1 rounded-full uppercase"
                                  x-text="parsedImport.length + ' catégorie(s) détectée(s)'"></span>
                        </div>

                        <div x-show="parsedImport.length === 0" class="p-6 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-400 font-bold">Collez votre texte ci-dessus pour voir l'aperçu en temps réel.</p>
                        </div>

                        <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(cat, cIdx) in parsedImport" :key="cIdx">
                                <div class="p-4 bg-white border border-slate-100 rounded-2xl space-y-2 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-xs font-black text-slate-900" x-text="cat.name"></h5>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="cat.services.length + ' sous-service(s)'"></span>
                                    </div>
                                    <p x-show="cat.description" class="text-[10px] text-slate-400 font-medium" x-text="cat.description"></p>
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        <template x-for="(sTitle, sIdx) in cat.services" :key="sIdx">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-bold rounded-lg" x-text="sTitle"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>


                {{-- ============================================================ --}}
                {{-- FORMULAIRE DE MODIFICATION (MODE ÉDITION)                   --}}
                {{-- ============================================================ --}}
                <div x-show="isEdit">
                    <form id="editForm"
                          :action="'{{ url('admin/categories') }}/' + editData.id"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Photo --}}
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Photo</label>
                            <div class="flex items-center gap-4">
                                <label for="edit-image" class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 flex items-center justify-center cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition-all overflow-hidden shrink-0 relative group">
                                    <img id="edit-preview" :src="editData.image_url || ''" alt=""
                                         :class="editData.image_url ? 'block' : 'hidden'"
                                         class="w-full h-full object-cover absolute inset-0 rounded-2xl">
                                    <div :class="editData.image_url ? 'hidden' : 'flex'" class="flex-col items-center gap-1 text-slate-300">
                                        <i class="fas fa-camera text-xl"></i>
                                        <span class="text-[8px] font-black uppercase tracking-wider">Photo</span>
                                    </div>
                                </label>
                                <input type="file" id="edit-image" name="image" accept="image/*" class="hidden"
                                       onchange="previewImage(this, 'edit-preview', null, true)">
                                <p class="text-[10px] text-slate-400 font-bold">Cliquez pour remplacer<br>JPG, PNG — max 2 Mo</p>
                            </div>
                        </div>

                        {{-- Nom --}}
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editData.name" required
                                   class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none">
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Description</label>
                            <input type="text" name="description" x-model="editData.description"
                                   class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none">
                        </div>

                        {{-- Sous-services --}}
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                Sous-services <span class="text-[8px] font-bold text-slate-300 normal-case tracking-normal">(un par ligne)</span>
                            </label>
                            <textarea name="services" x-model="editData.servicesText" rows="5"
                                      class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"></textarea>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Panel Footer --}}
            <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0 bg-white">
                <button type="button" @click="closePanel()"
                        class="px-5 py-3 bg-slate-100 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Annuler
                </button>

                <template x-if="isEdit">
                    <button type="button"
                            @click="document.getElementById('editForm').submit()"
                            class="flex-1 py-3.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rdc-blue hover:shadow-lg transition-all">
                        Enregistrer les modifications
                    </button>
                </template>

                <template x-if="!isEdit && activeTab === 'form'">
                    <button type="button"
                            @click="document.getElementById('createBatchForm').submit()"
                            class="flex-1 py-3.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rdc-blue hover:shadow-lg transition-all">
                        Créer les <span x-text="items.length"></span> catégorie(s)
                    </button>
                </template>

                <template x-if="!isEdit && activeTab === 'import'">
                    <button type="button"
                            :disabled="parsedImport.length === 0"
                            @click="document.getElementById('importTextForm').submit()"
                            :class="parsedImport.length === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-slate-900 text-white hover:bg-rdc-blue hover:shadow-lg'"
                            class="flex-1 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                        Importer <span x-text="parsedImport.length"></span> catégorie(s)
                    </button>
                </template>
            </div>

        </div>
    </div>

</div>

<script>
function categoryManager() {
    return {
        panelOpen: false,
        isEdit: false,
        activeTab: 'form', // 'form' or 'import'
        items: [
            { name: '', description: '', services: '' }
        ],
        importRawText: '',
        parsedImport: [],
        editData: {
            id: null,
            name: '',
            slug: '',
            description: '',
            image_url: null,
            servicesText: '',
        },

        openCreate(tab = 'form') {
            this.isEdit = false;
            this.activeTab = tab;
            this.items = [{ name: '', description: '', services: '' }];
            this.importRawText = '';
            this.parsedImport = [];
            this.editData = { id: null, name: '', slug: '', description: '', image_url: null, servicesText: '' };
            this.panelOpen = true;
        },

        addItem() {
            this.items.push({ name: '', description: '', services: '' });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        onFileChange(event, index) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const prev = document.getElementById('preview-' + index);
                const ph   = document.getElementById('placeholder-' + index);
                if (prev) {
                    prev.src = e.target.result;
                    prev.classList.remove('hidden');
                    prev.classList.add('block');
                }
                if (ph) {
                    ph.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        },

        parseText() {
            const lines = this.importRawText.split(/\r?\n/);
            const categories = [];
            let current = null;

            for (let line of lines) {
                const trimmed = line.trim();
                if (!trimmed) continue;

                if (trimmed.startsWith('##') || trimmed.startsWith('#')) {
                    if (current) categories.push(current);
                    current = {
                        name: trimmed.replace(/^#+\s*/, ''),
                        description: '',
                        services: []
                    };
                    continue;
                }

                if (trimmed.startsWith('-') || trimmed.startsWith('*')) {
                    if (current) {
                        const sTitle = trimmed.replace(/^[-*]\s*/, '');
                        if (sTitle) current.services.push(sTitle);
                    }
                    continue;
                }

                if (current) {
                    if (current.description) {
                        current.description += ' ' + trimmed;
                    } else {
                        current.description = trimmed;
                    }
                }
            }

            if (current) categories.push(current);
            this.parsedImport = categories;
        },

        insertExampleText() {
            this.importRawText = `## Électricité
Travaux d'installation, maintenance et dépannage électrique.
- Installation prise & interrupteur
- Réparation tableau électrique
- Diagnostic et dépannage panne

## Plomberie
Installations sanitaires, tuyauterie et débouchage.
- Débouchage canalisation
- Réparation fuite d'eau
- Installation robinetterie & chauffe-eau

## Couture & Stylisme
Création de vêtements sur mesure et retouches.
- Confection tenue sur mesure
- Retouche et ajustement
- Broderie et finitions`;
            this.parseText();
        },

        openEdit(category) {
            this.isEdit = true;
            this.editData = {
                id:           category.id,
                name:         category.name,
                slug:         category.slug,
                description:  category.description || '',
                image_url:    category.image_url,
                servicesText: Array.isArray(category.service_types) ? category.service_types.join('\n') : '',
            };
            this.$nextTick(() => {
                const fi = document.getElementById('edit-image');
                if (fi) fi.value = '';
            });
            this.panelOpen = true;
        },

        closePanel() {
            this.panelOpen = false;
        },
    }
}

function previewImage(input, previewId, placeholderId, showOnEdit) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const prev = document.getElementById(previewId);
        if (prev) {
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            prev.classList.add('block');
        }
        if (placeholderId) {
            const ph = document.getElementById(placeholderId);
            if (ph) ph.classList.add('hidden');
        }
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
