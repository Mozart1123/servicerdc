@extends('layouts.user')

@section('title', 'Modifier l\'Offre d\'Emploi')
@section('header_title', 'Édition de l\'Offre')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('user.jobs.my-offers') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 hover:text-rdc-blue transition-colors uppercase tracking-widest">
            <i class="fas fa-arrow-left"></i>
            Retour à mes offres
        </a>
        <h2 class="text-3xl font-black text-slate-900 font-heading mt-4">Modifier l'Offre</h2>
        <p class="text-sm text-slate-400 font-medium">Mettez à jour les informations de votre offre d'emploi.</p>
    </div>

    <form action="{{ route('user.jobs.update', $job->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-6">
            {{-- Section 1: Informations de base --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Titre du poste</label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="title" value="{{ old('title', $job->title) }}" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                               placeholder="ex: Développeur Web Senior">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de l'entreprise</label>
                    <div class="relative">
                        <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="company_name" value="{{ old('company_name', $job->company_name) }}" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                               placeholder="ex: ProConnect SARL">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Localisation</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="location" value="{{ old('location', $job->location) }}" required 
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
                            <option value="CDI" {{ old('contract_type', $job->contract_type) == 'CDI' ? 'selected' : '' }}>CDI</option>
                            <option value="CDD" {{ old('contract_type', $job->contract_type) == 'CDD' ? 'selected' : '' }}>CDD</option>
                            <option value="Freelance" {{ old('contract_type', $job->contract_type) == 'Freelance' ? 'selected' : '' }}>Freelance / Consultant</option>
                            <option value="Stage" {{ old('contract_type', $job->contract_type) == 'Stage' ? 'selected' : '' }}>Stage</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date limite</label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="date" name="deadline" value="{{ old('deadline', $job->deadline ? $job->deadline->format('Y-m-d') : '') }}" 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Statut de l'offre</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="status" value="active" class="peer sr-only" {{ old('status', $job->status) == 'active' ? 'checked' : '' }}>
                        <div class="px-4 py-2 rounded-xl border border-slate-100 bg-slate-50 text-xs font-bold text-slate-400 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-600 transition-all">Ouverte</div>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="status" value="closed" class="peer sr-only" {{ old('status', $job->status) == 'closed' ? 'checked' : '' }}>
                        <div class="px-4 py-2 rounded-xl border border-slate-100 bg-slate-50 text-xs font-bold text-slate-400 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600 transition-all">Fermée</div>
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catégorie / Secteur d'activité</label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="category" required 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue appearance-none transition-all">
                        @php $cats = ['Informatique', 'Santé', 'Éducation', 'Finance', 'Commerce', 'BTP', 'Transport', 'Hôtellerie', 'Autre']; @endphp
                        @foreach($cats as $cat)
                            <option value="{{ $cat }}" {{ old('category', $job->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fourchette salariale (Optionnel)</label>
                <div class="relative">
                    <i class="fas fa-money-bill-wave absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="salary_range" value="{{ old('salary_range', $job->salary_range) }}" 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                           placeholder="ex: 800$ - 1200$ / mois">
                </div>
            </div>

            {{-- Section 2: Description & Prérequis --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description du poste</label>
                <textarea name="description" rows="6" required 
                          class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                          placeholder="Décrivez les missions...">{{ old('description', $job->description) }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prérequis & Compétences</label>
                <textarea name="requirements" rows="4" 
                          class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue/20 focus:bg-white focus:border-rdc-blue transition-all"
                          placeholder="Listez les compétences requis...">{{ old('requirements', $job->requirements) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('user.jobs.my-offers') }}" class="px-8 py-3 text-sm font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Annuler</a>
            <button type="submit" class="px-10 py-4 bg-amber-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:shadow-amber-500/40 transform hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
