@extends('layouts.admin')

@section('title', 'Catégories d\'Emploi')
@section('header_title', 'Catégories d\'Emploi')
@section('page_title', 'Secteurs d\'Activité')
@section('page_subtitle', 'Gérez les secteurs proposés aux recruteurs lors de la publication d\'une offre d\'emploi.')

@section('content')
<div x-data="jobCategoryManager()" class="space-y-6">

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
                {{ $jobCategories->total() }} secteur(s) enregistré(s)
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCreate()"
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

        @forelse($jobCategories as $jobCategory)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-50 last:border-b-0 hover:bg-slate-50/40 transition-colors group">

                {{-- Miniature --}}
                <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 bg-gradient-to-br from-[#1E9CB5] to-[#090D16] flex items-center justify-center shadow-sm">
                    @if($jobCategory->image)
                        <img src="{{ Storage::url($jobCategory->image) }}"
                             alt="{{ $jobCategory->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <i class="{{ $jobCategory->icon ?? 'fas fa-briefcase' }} text-white text-sm"></i>
                    @endif
                </div>

                {{-- Infos principale --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900 truncate">{{ $jobCategory->name }}</p>
                    @if($jobCategory->description)
                        <p class="text-[10px] text-slate-400 font-bold truncate mt-0.5">{{ Str::limit($jobCategory->description, 60) }}</p>
                    @endif
                </div>

                {{-- Compteur d'offres --}}
                <div class="hidden md:block shrink-0 text-right">
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-wider rounded-xl">
                        {{ $jobCategory->job_offers_count }} offre{{ $jobCategory->job_offers_count > 1 ? 's' : '' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="openEdit({{ json_encode([
                        'id'          => $jobCategory->id,
                        'name'        => $jobCategory->name,
                        'slug'        => $jobCategory->slug,
                        'description' => $jobCategory->description ?? '',
                        'image_url'   => $jobCategory->image_url,
                    ]) }})"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rdc-blue hover:border-rdc-blue shadow-sm transition-all"
                            title="Modifier">
                        <i class="fas fa-pen text-xs"></i>
                    </button>

                    <form action="{{ route('admin.job-categories.destroy', $jobCategory->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Supprimer « {{ $jobCategory->name }} » ? Les offres liées garderont leur secteur affiché mais perdront le lien structuré.');">
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
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucune catégorie d'emploi</h4>
                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">
                    Cliquez sur « Nouvelle catégorie » pour commencer.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($jobCategories->hasPages())
        <div class="px-6 py-4">{{ $jobCategories->links() }}</div>
    @endif


    {{-- ============================================================ --}}
    {{-- PANNEAU LATÉRAL (CRÉATION / MODIFICATION)                    --}}
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
                       x-text="isEdit ? 'Mettez à jour les détails du secteur' : 'Ajoutez un ou plusieurs secteurs d\'un coup'"></p>
                </div>
                <button @click="closePanel()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Panel Body --}}
            <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">

                {{-- ============================================================ --}}
                {{-- FORMULAIRE RÉPÉTABLE (MODE CRÉATION)                        --}}
                {{-- ============================================================ --}}
                <div x-show="!isEdit">
                    <form id="createBatchForm"
                          action="{{ route('admin.job-categories.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf

                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-6 bg-slate-50/70 border border-slate-100 rounded-3xl space-y-4 relative group">

                                {{-- Card Header & Remove Button --}}
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                    <span class="text-xs font-black text-rdc-blue uppercase tracking-widest flex items-center gap-2">
                                        <i class="fas fa-briefcase text-[10px]"></i>
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
                                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Nom du secteur <span class="text-red-500">*</span></label>
                                            <input type="text"
                                                   :name="'categories[' + index + '][name]'"
                                                   x-model="item.name"
                                                   required
                                                   class="w-full px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                                                   placeholder="ex: Informatique & Technologie">
                                        </div>

                                        <div>
                                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Description courte</label>
                                            <input type="text"
                                                   :name="'categories[' + index + '][description]'"
                                                   x-model="item.description"
                                                   class="w-full px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                                                   placeholder="ex: Développement, réseaux, support technique...">
                                        </div>
                                    </div>
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
                {{-- FORMULAIRE DE MODIFICATION (MODE ÉDITION)                   --}}
                {{-- ============================================================ --}}
                <div x-show="isEdit">
                    <form id="editForm"
                          :action="'{{ url('admin/job-categories') }}/' + editData.id"
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
                                       onchange="previewJobCategoryImage(this, 'edit-preview')">
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

                <template x-if="!isEdit">
                    <button type="button"
                            @click="document.getElementById('createBatchForm').submit()"
                            class="flex-1 py-3.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rdc-blue hover:shadow-lg transition-all">
                        Créer les <span x-text="items.length"></span> catégorie(s)
                    </button>
                </template>
            </div>

        </div>
    </div>

</div>

<script>
function jobCategoryManager() {
    return {
        panelOpen: false,
        isEdit: false,
        items: [
            { name: '', description: '' }
        ],
        editData: {
            id: null,
            name: '',
            slug: '',
            description: '',
            image_url: null,
        },

        openCreate() {
            this.isEdit = false;
            this.items = [{ name: '', description: '' }];
            this.editData = { id: null, name: '', slug: '', description: '', image_url: null };
            this.panelOpen = true;
        },

        addItem() {
            this.items.push({ name: '', description: '' });
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

        openEdit(category) {
            this.isEdit = true;
            this.editData = {
                id:          category.id,
                name:        category.name,
                slug:        category.slug,
                description: category.description || '',
                image_url:   category.image_url,
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

function previewJobCategoryImage(input, previewId) {
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
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
