@extends('layouts.user')

@section('header_title', 'Modifier le Service')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">
    
    <div class="relative">
        <div class="absolute inset-0 bg-amber-500/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Modifier: {{ $service->title }}</h2>
            <p class="text-sm font-bold text-slate-400 mt-2 uppercase tracking-widest">Mettez à jour les détails de votre offre</p>
            
            <form action="{{ route('user.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="mt-10 space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Titre & Statut -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre du service <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title', $service->title) }}"
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

                <!-- Prix SEULEMENT (Grille mise à jour) -->
                <div class="space-y-2 relative">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Prix de base ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" step="0.01" min="0" required value="{{ old('price', $service->price) }}"
                           class="w-full pl-6 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    <span class="absolute right-4 top-[38px] text-slate-400 font-bold">$</span>
                    @error('price')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Localisation -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Localisation (Ville, Commune) <span class="text-red-500">*</span></label>
                    <input type="text" name="location" required value="{{ old('location', $service->location) }}"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    @error('location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
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
                        @if($service->service_image)
                            <div class="relative w-40 h-40 rounded-2xl overflow-hidden border-2 border-slate-100 shadow-sm flex-none">
                                <img src="{{ Storage::url($service->service_image) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-[8px] font-black uppercase">Actuelle</span>
                                </div>
                            </div>
                        @else
                            <div class="w-40 h-40 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300 flex-none">
                                <i class="fas fa-image text-2xl mb-2"></i>
                                <span class="text-[8px] font-black uppercase">Aucune</span>
                            </div>
                        @endif

                        <div class="flex-1 w-full relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:bg-slate-50 transition-colors group">
                            <input type="file" name="service_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-rdc-blue text-lg mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-camera"></i>
                            </div>
                            <h4 class="font-bold text-slate-900 text-[11px] mb-1">Remplacer l'image principale</h4>
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
                            <div class="relative flex-none w-32 h-32 rounded-2xl overflow-hidden group">
                                <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" onclick="if(confirm('Supprimer cette image ?')) document.getElementById('deleteImgForm-{{ $index }}').submit()" class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center hover:scale-110 shadow-lg">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Nouvelles Images -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Ajouter de nouvelles images (Max 5)</label>
                    <div class="relative border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center hover:bg-slate-50 transition-colors group">
                        <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-amber-500 text-2xl mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-1">Cliquez ou glissez vos images ici</h4>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG, GIF (Max 2MB)</p>
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

@endsection
