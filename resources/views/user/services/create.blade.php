@extends('layouts.user')

@section('header_title', 'Ajouter un Service')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">
    
    <div class="relative">
        <div class="absolute inset-0 bg-rdc-blue/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Proposer un service</h2>
            <p class="text-sm font-bold text-slate-400 mt-2 uppercase tracking-widest">Remplissez les détails pour publier votre offre sur la plateforme</p>
            
            <form action="{{ route('user.services.store') }}" method="POST" enctype="multipart/form-data" class="mt-10 space-y-8">
                @csrf
                
                <!-- Titre -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre du service <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ex: Réparation plomberie générale" value="{{ old('title') }}"
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
                        Publier mon service
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
@endsection
