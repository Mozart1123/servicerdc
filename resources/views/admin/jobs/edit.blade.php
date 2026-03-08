@extends('layouts.admin')

@section('title', 'Modifier l\'Offre d\'Emploi')
@section('header_title', 'Modifier')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-edit text-rdc-blue"></i> Modification : {{ $job->title }}
            </h3>
            <p class="text-xs text-slate-500 mt-2 font-medium">Mettez à jour les informations de l'offre d'emploi ci-dessous.</p>
        </div>
        
        <div class="p-8">
            <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Titre du Poste</label>
                        <input type="text" name="title" required value="{{ old('title', $job->title) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Développeur Full-Stack PHP / Laravel">
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nom de l'Entreprise</label>
                        <input type="text" name="company_name" required value="{{ old('company_name', $job->company_name) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Innovate RDC">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Domaine d'Activité</label>
                        <input type="text" name="category" required value="{{ old('category', $job->category) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Technologie, Santé, BTP...">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Localisation</label>
                        <input type="text" name="location" required value="{{ old('location', $job->location) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Kinshasa, Gombe">
                    </div>

                    <!-- Contract Type -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Type de Contrat</label>
                        <select name="contract_type" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none cursor-pointer">
                            <option value="Full-time" {{ old('contract_type', $job->contract_type) === 'Full-time' ? 'selected' : '' }}>Temps Plein (Full-time)</option>
                            <option value="Part-time" {{ old('contract_type', $job->contract_type) === 'Part-time' ? 'selected' : '' }}>Temps Partiel (Part-time)</option>
                            <option value="Freelance" {{ old('contract_type', $job->contract_type) === 'Freelance' ? 'selected' : '' }}>Freelance / Consultant</option>
                        </select>
                    </div>

                    <!-- Salary Range -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Fourchette Salariale (Optionnel)</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range', $job->salary_range) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: 1500$ - 3000$">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Statut de l'Offre</label>
                        <select name="status" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none cursor-pointer">
                            <option value="active" {{ old('status', $job->status) === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="expired" {{ old('status', $job->status) === 'expired' ? 'selected' : '' }}>Expiré</option>
                        </select>
                    </div>

                    <!-- Logo URL -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Lien du Logo de l'Entreprise (URL)</label>
                        <input type="url" name="logo_url" value="{{ old('logo_url', $job->logo_url) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: https://example.com/logo.png">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Description du Poste</label>
                        <textarea name="description" required rows="6"
                                  class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                                  placeholder="Décrivez les responsabilités, l'environnement de travail...">{{ old('description', $job->description) }}</textarea>
                    </div>

                    <!-- Requirements -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Profil Recherché / Pré-requis</label>
                        <textarea name="requirements" rows="4"
                                  class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                                  placeholder="Listez les compétences, diplômes ou expériences requis...">{{ old('requirements', $job->requirements) }}</textarea>
                    </div>
                </div>

                <div class="pt-8 flex flex-wrap gap-4 border-t border-slate-50">
                    <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-rdc-blue hover:shadow-lg hover:shadow-blue-500/20 transition-all">
                        Mettre à jour
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="px-8 py-4 bg-slate-100 text-slate-500 rounded-2xl text-sm font-bold hover:bg-slate-200 transition-all">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
