@extends('layouts.admin')

@section('title', 'Modifier l\'Offre d\'Emploi')
@section('header_title', 'Modifier')

@section('content')
<div class="max-w-4xl mx-auto pb-12 px-4 sm:px-0">
    <div class="bg-white rounded-[1.5rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-50 bg-slate-50/30 text-center sm:text-left">
            <h3 class="text-lg sm:text-xl font-bold text-slate-900 flex items-center justify-center sm:justify-start gap-3">
                <i class="fas fa-edit text-rdc-blue"></i> {{ $job->title }}
            </h3>
            <p class="text-[10px] sm:text-xs text-slate-500 mt-2 font-medium">Mettez à jour les informations de l'offre d'emploi.</p>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Titre du Poste</label>
                        <input type="text" name="title" required value="{{ old('title', $job->title) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Développeur PHP">
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Entreprise</label>
                        <input type="text" name="company_name" required value="{{ old('company_name', $job->company_name) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Innovate RDC">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Domaine</label>
                        <input type="text" name="category" required value="{{ old('category', $job->category) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Technologie">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Lieu</label>
                        <input type="text" name="location" required value="{{ old('location', $job->location) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: Gombe">
                    </div>

                    <!-- Contract Type -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Contrat</label>
                        <select name="contract_type" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none cursor-pointer">
                            <option value="Full-time" {{ old('contract_type', $job->contract_type) === 'Full-time' ? 'selected' : '' }}>CDI</option>
                            <option value="Part-time" {{ old('contract_type', $job->contract_type) === 'Part-time' ? 'selected' : '' }}>CDD</option>
                            <option value="Freelance" {{ old('contract_type', $job->contract_type) === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                        </select>
                    </div>

                    <!-- Salary Range -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Salaire</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range', $job->salary_range) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: 1500$">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Statut</label>
                        <select name="status" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none cursor-pointer">
                            <option value="active" {{ old('status', $job->status) === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="expired" {{ old('status', $job->status) === 'expired' ? 'selected' : '' }}>Expiré</option>
                        </select>
                    </div>

                    <!-- Logo URL -->
                    <div class="md:col-span-2">
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Lien du Logo (URL)</label>
                        <input type="url" name="logo_url" value="{{ old('logo_url', $job->logo_url) }}"
                               class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: https://...">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Description</label>
                        <textarea name="description" required rows="6"
                                  class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                                  placeholder="Missions...">{{ old('description', $job->description) }}</textarea>
                    </div>

                    <!-- Requirements -->
                    <div class="md:col-span-2">
                        <label class="block text-[9px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Exigences</label>
                        <textarea name="requirements" rows="4"
                                  class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                                  placeholder="Compétences...">{{ old('requirements', $job->requirements) }}</textarea>
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row gap-3 sm:gap-4 border-t border-slate-50">
                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-bold active:scale-95 transition-all">
                        Enregistrer
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="w-full sm:w-auto px-10 py-4 bg-slate-100 text-slate-500 rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-bold text-center active:scale-95 transition-all">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
