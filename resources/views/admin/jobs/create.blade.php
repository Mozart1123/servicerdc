@extends('layouts.admin')

@section('title', 'Publier une Offre d\'Emploi')
@section('header_title', 'Gestion des Emplois')
@section('page_title', 'Nouvelle Offre')
@section('page_subtitle', 'Ajoutez une nouvelle opportunité de carrière pour les talents de la plateforme.')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <form action="{{ route('admin.jobs.store') }}" method="POST" class="space-y-6 sm:space-y-8 px-4 sm:px-0">
        @csrf
        
        <!-- Company & Basic Info -->
        <div class="bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                <i class="fas fa-building text-rdc-blue"></i> Poste
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Titre</label>
                    <input type="text" name="title" required placeholder="ex: Analyste" 
                           class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Entreprise</label>
                    <input type="text" name="company_name" required placeholder="ex: Airtel" 
                           class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Logo URL</label>
                    <input type="url" name="logo_url" placeholder="https://..." 
                           class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Ville</label>
                    <input type="text" name="location" required placeholder="ex: Gombe" 
                           class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                <i class="fas fa-list-check text-amber-500"></i> Détails
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Contrat</label>
                    <select name="contract_type" required class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="Full-time">CDI</option>
                        <option value="Part-time">CDD</option>
                        <option value="Freelance">Freelance</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Salaire</label>
                    <input type="text" name="salary_range" placeholder="ex: 1500$" 
                           class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Catégorie</label>
                <input type="text" name="category" required placeholder="ex: IT" 
                       class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Description</label>
                <textarea name="description" rows="5" required placeholder="Missions..."
                          class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[9px] sm:text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Exigences</label>
                <textarea name="requirements" rows="3" placeholder="Compétences..."
                          class="w-full px-5 py-3.5 sm:px-6 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none"></textarea>
            </div>
        </div>

        <!-- Publication Logic -->
        <div class="bg-emerald-900 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 sm:w-40 sm:h-40 bg-white/5 rounded-full blur-3xl"></div>
            
            <div class="flex items-center justify-between relative z-10 gap-4">
                <div>
                    <h4 class="text-base sm:text-lg font-black uppercase tracking-tight">Publication</h4>
                    <p class="text-[10px] sm:text-xs text-white/60 font-medium">L'offre sera active immédiatement.</p>
                </div>
                <input type="hidden" name="status" value="active">
                <div class="flex items-center gap-2 animate-pulse shrink-0">
                    <i class="fas fa-broadcast-tower text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 sm:gap-4 pb-10">
            <a href="{{ route('admin.jobs.index') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-600 font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest border border-slate-200 hover:bg-slate-50 transition-all text-center">Annuler</a>
            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-rdc-blue text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                Diffuser l'Offre
            </button>
        </div>
    </form>
</div>
@endsection
