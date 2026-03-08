@extends('layouts.admin')

@section('title', 'Créer un Nouveau Service')
@section('header_title', 'Gestion des Services')
@section('page_title', 'Nouveau Service')
@section('page_subtitle', 'Publier un service professionnel qui sera visible par tous les utilisateurs.')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <form action="{{ route('admin.moderation.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Basic Info -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-rdc-blue"></i> Informations de base
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Titre du Service</label>
                    <input type="text" name="title" required placeholder="ex: Plomberie Express Gombe" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Catégorie</label>
                    <select name="category_id" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Description détaillée</label>
                <textarea name="description" rows="4" required placeholder="Décrivez les points forts du service..."
                          class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"></textarea>
            </div>
        </div>

        <!-- Pricing & Location -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-wallet text-amber-500"></i> Prix & Localisation
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Prix de départ ($)</label>
                    <input type="number" name="price" required placeholder="0.00" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Localisation</label>
                    <input type="text" name="location" required placeholder="ex: Kinshasa, Gombe" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>
        </div>

        <!-- Verification Status -->
        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-blue/10 rounded-full blur-3xl"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <h4 class="text-lg font-black uppercase tracking-tight">Statut de Confiance</h4>
                    <p class="text-xs text-slate-400 font-medium">Définissez si ce service bénéficie du badge 'Vérifié'.</p>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_verified" value="1" class="w-6 h-6 rounded-lg border-white/20 bg-white/5 text-rdc-blue focus:ring-rdc-blue/20">
                        <span class="text-sm font-black uppercase tracking-widest">Activer Vérification</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.moderation.services') }}" class="px-8 py-4 bg-white text-slate-600 font-black rounded-2xl text-[10px] uppercase tracking-widest border border-slate-200 hover:bg-slate-50 transition-all">Annuler</a>
            <button type="submit" class="px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                Créer et Publier le Service
            </button>
        </div>
    </form>
</div>
@endsection
