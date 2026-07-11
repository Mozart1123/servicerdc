@extends('layouts.user')

@section('title', 'Publier une Offre d\'Emploi')
@section('header_title', 'Nouvelle Publication')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('user.jobs.my-offers') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 hover:text-rdc-blue transition-colors uppercase tracking-widest">
            <i class="fas fa-arrow-left"></i>
            Retour à mes offres
        </a>
        <h2 class="text-3xl font-black text-slate-900 font-heading mt-4">Publier une Offre</h2>
        <p class="text-sm text-slate-400 font-medium">Remplissez les détails du poste pour attirer les meilleurs candidats.</p>
    </div>

    <form action="{{ route('user.jobs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-6">
            {{-- Section 1: Informations de base --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Titre du poste</label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                               placeholder="ex: Développeur Web Senior">
                    </div>
                    @error('title') <p class="text-[10px] text-rdc-red font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de l'entreprise</label>
                    <div class="relative">
                        <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="company_name" value="{{ old('company_name', auth()->user()->company_name) }}" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                               placeholder="ex: ProConnect SARL">
                    </div>
                </div>
            </div>

            {{-- Images upload (Logo & Cover) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Logo de l'entreprise (Optionnel)</label>
                    <input type="file" name="company_logo" accept="image/*"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-rdc-blue/10 file:text-rdc-blue hover:file:bg-rdc-blue/20">
                    @error('company_logo') <p class="text-[10px] text-rdc-red font-bold ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Image de couverture (Optionnel)</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-rdc-blue/10 file:text-rdc-blue hover:file:bg-rdc-blue/20">
                    @error('cover_image') <p class="text-[10px] text-rdc-red font-bold ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Localisation</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="location" value="{{ old('location') }}" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                               placeholder="ex: Kinshasa, Gombe">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Type de contrat</label>
                    <div class="relative">
                        <i class="fas fa-file-contract absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <select name="contract_type" required 
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue appearance-none transition-all">
                            <option value="CDI" {{ old('contract_type') == 'CDI' ? 'selected' : '' }}>CDI</option>
                            <option value="CDD" {{ old('contract_type') == 'CDD' ? 'selected' : '' }}>CDD</option>
                            <option value="Freelance" {{ old('contract_type') == 'Freelance' ? 'selected' : '' }}>Freelance / Consultant</option>
                            <option value="Stage" {{ old('contract_type') == 'Stage' ? 'selected' : '' }}>Stage</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date limite (Optionnel)</label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catégorie / Secteur d'activité</label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="category" required 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue appearance-none transition-all">
                        <option value="">Sélectionner un secteur</option>
                        <option value="Informatique" {{ old('category') == 'Informatique' ? 'selected' : '' }}>Informatique & Technologie</option>
                        <option value="Santé" {{ old('category') == 'Santé' ? 'selected' : '' }}>Santé & Médical</option>
                        <option value="Éducation" {{ old('category') == 'Éducation' ? 'selected' : '' }}>Éducation</option>
                        <option value="Finance" {{ old('category') == 'Finance' ? 'selected' : '' }}>Finance & Comptabilité</option>
                        <option value="Commerce" {{ old('category') == 'Commerce' ? 'selected' : '' }}>Vente & Commerce</option>
                        <option value="BTP" {{ old('category') == 'BTP' ? 'selected' : '' }}>BTP & Construction</option>
                        <option value="Transport" {{ old('category') == 'Transport' ? 'selected' : '' }}>Transport & Logistique</option>
                        <option value="Hôtellerie" {{ old('category') == 'Hôtellerie' ? 'selected' : '' }}>Hôtellerie & Restauration</option>
                        <option value="Autre" {{ old('category') == 'Autre' ? 'selected' : '' }}>Autre secteur</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fourchette salariale (Optionnel)</label>
                <div class="relative">
                    <i class="fas fa-money-bill-wave absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="salary_range" value="{{ old('salary_range') }}" 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                           placeholder="ex: 800$ - 1200$ / mois">
                </div>
            </div>

            {{-- Section 2: Description & Prérequis --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description du poste</label>
                <textarea name="description" rows="6" required 
                          class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                          placeholder="Décrivez les missions et le contexte du poste...">{{ old('description') }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prérequis & Compétences</label>
                <textarea name="requirements" rows="4" 
                          class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                          placeholder="Listez les compétences, années d'expérience et diplômes requis...">{{ old('requirements') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <button type="reset" class="px-8 py-3 text-sm font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Réinitialiser</button>
            <button type="submit" class="px-10 py-4 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 transform hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                Publier l'offre
            </button>
        </div>
    </form>
</div>
@endsection
