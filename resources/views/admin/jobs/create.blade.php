@extends('layouts.admin')

@section('title', 'Publier une Offre d\'Emploi')
@section('header_title', 'Gestion des Emplois')
@section('page_title', 'Nouvelle Offre')
@section('page_subtitle', 'Ajoutez une nouvelle opportunité de carrière pour les talents de la plateforme.')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <form action="{{ route('admin.jobs.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Company & Basic Info -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-building text-rdc-blue"></i> Entreprise & Poste
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Titre du Poste</label>
                    <input type="text" name="title" required placeholder="ex: Senior Developer PHP" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Nom de l'Entreprise</label>
                    <input type="text" name="company_name" required placeholder="ex: Vodacom RDC" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Logo officiel (URL)</label>
                    <input type="url" name="logo_url" placeholder="https://..." 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Localisation</label>
                    <input type="text" name="location" required placeholder="ex: Gombe, Kinshasa" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-list-check text-amber-500"></i> Spécificités
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Type de Contrat</label>
                    <select name="contract_type" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="Full-time">Temps-plein (CDI)</option>
                        <option value="Part-time">Temps-partiel</option>
                        <option value="Freelance">Freelance / Consultant</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Fourchette Salariale</label>
                    <input type="text" name="salary_range" placeholder="ex: 1500$ - 2500$" 
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Catégorie</label>
                <input type="text" name="category" required placeholder="ex: Informatique, Finance..." 
                       class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Description du poste</label>
                <textarea name="description" rows="5" required placeholder="Détaillez les missions..."
                          class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Exigences / Prérequis</label>
                <textarea name="requirements" rows="3" placeholder="Compétences recherchées, années d'expérience..."
                          class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"></textarea>
            </div>
        </div>

        <!-- Publication Logic -->
        <div class="bg-emerald-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <h4 class="text-lg font-black uppercase tracking-tight">Publication Immédiate</h4>
                    <p class="text-xs text-white/60 font-medium">L'offre sera publiée en mode 'Actif' dès la validation.</p>
                </div>
                <input type="hidden" name="status" value="active">
                <div class="flex items-center gap-2 animate-pulse">
                    <i class="fas fa-broadcast-tower text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.jobs.index') }}" class="px-8 py-4 bg-white text-slate-600 font-black rounded-2xl text-[10px] uppercase tracking-widest border border-slate-200 hover:bg-slate-50 transition-all">Annuler</a>
            <button type="submit" class="px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                Diffuser l'Offre d'Emploi
            </button>
        </div>
    </form>
</div>
@endsection
