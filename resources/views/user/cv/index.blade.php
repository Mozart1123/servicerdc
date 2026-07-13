@extends($layout)

@section('title', 'Créer mon CV')

@section($contentSection)
<div class="max-w-6xl mx-auto pb-20 space-y-8">
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-5 rounded-2xl shadow-sm flex items-center gap-4">
            <i class="fas fa-check-circle text-2xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 rounded-2xl shadow-sm flex items-center gap-4">
            <i class="fas fa-exclamation-circle text-2xl"></i>
            <p class="font-bold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl font-heading font-black text-slate-900">Mon CV ProConnect</h1>
                <p class="mt-3 text-sm text-slate-500 max-w-2xl">Complétez votre profil une seule fois. Il sera joint automatiquement à vos candidatures et accessible depuis votre espace personnel.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('user.jobs.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-100 text-slate-700 rounded-3xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-slate-200 transition">
                    <i class="fas fa-arrow-left"></i> Retour aux offres
                </a>
                @if($cv && $cv->cv_file_url)
                    <a href="{{ $cv->cv_file_url }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-rdc-blue text-white rounded-3xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-rdc-blue-dark transition">
                        <i class="fas fa-file-pdf"></i> Voir mon CV
                    </a>
                @endif
            </div>
        </div>

        <form action="{{ $cv ? route('user.cv.update') : route('user.cv.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
            @csrf
            @if($cv)
                @method('PUT')
            @endif
            <input type="hidden" name="return_to" value="{{ $returnTo ?? '' }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 flex items-start gap-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rdc-blue text-sm font-black text-white">1</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Étape 1</p>
                        <p class="font-bold text-slate-900">Identité</p>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 flex items-start gap-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-black text-slate-700">2</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Étape 2</p>
                        <p class="font-bold text-slate-900">Contact</p>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 flex items-start gap-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-black text-slate-700">3</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Étape 3</p>
                        <p class="font-bold text-slate-900">Profil</p>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 flex items-start gap-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-black text-slate-700">4</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Étape 4</p>
                        <p class="font-bold text-slate-900">Expérience & Compétences</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-100 rounded-full h-2 overflow-hidden mb-10">
                <div class="h-2 w-4/5 bg-rdc-blue"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-8">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">1. Identité</p>
                                <h2 class="text-xl font-bold text-slate-900">Informations personnelles</h2>
                            </div>
                            <span class="inline-flex h-11 min-w-[3rem] items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-semibold text-slate-700">Essentiel</span>
                        </div>
                        <div class="space-y-5">
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Nom complet</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $cv->full_name ?? auth()->user()->name) }}" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            @error('full_name')<p class="text-sm text-red-500">{{ $message }}</p>@enderror

                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Titre du poste souhaité</label>
                            <input type="text" name="job_title" value="{{ old('job_title', $cv->job_title ?? '') }}" placeholder="Ex: Gestionnaire de projet, Électricien..." class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            @error('job_title')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">2. Contact</p>
                                <h2 class="text-xl font-bold text-slate-900">Coordonnées</h2>
                            </div>
                            <span class="inline-flex h-11 min-w-[3rem] items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-semibold text-slate-700">Validation</span>
                        </div>
                        <div class="space-y-5">
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Téléphone</label>
                            <input type="text" name="phone" value="{{ old('phone', $cv->phone_number ?? auth()->user()->phone ?? '') }}" placeholder="+243... ou 0..." class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            @error('phone')<p class="text-sm text-red-500">{{ $message }}</p>@enderror

                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}" readonly class="w-full rounded-3xl border border-slate-200 bg-slate-100 px-5 py-4 text-sm text-slate-600 outline-none">

                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Province</label>
                            <select name="province" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                <option value="">Sélectionnez une province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" {{ old('province', $cv->province ?? '') === $province ? 'selected' : '' }}>{{ $province }}</option>
                                @endforeach
                            </select>
                            @error('province')<p class="text-sm text-red-500">{{ $message }}</p>@enderror

                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Adresse</label>
                            <input type="text" name="address" value="{{ old('address', $cv->address ?? '') }}" placeholder="Votre adresse complète" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            @error('address')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">3. Résumé professionnel</p>
                                <h2 class="text-xl font-bold text-slate-900">Présentation brève</h2>
                            </div>
                            <span class="inline-flex h-11 min-w-[3rem] items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-semibold text-slate-700">Synthèse</span>
                        </div>
                        <textarea name="summary" rows="6" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none" placeholder="Présentez vos forces et objectifs professionnels.">{{ old('summary', $cv->summary ?? '') }}</textarea>
                        @error('summary')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">4. Fichiers</p>
                                <h2 class="text-xl font-bold text-slate-900">CV et diplômes</h2>
                            </div>
                            <span class="inline-flex h-11 min-w-[3rem] items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-semibold text-slate-700">Téléversement</span>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">CV PDF / DOC</label>
                                <input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="w-full text-sm text-slate-700" />
                                @error('cv_file')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                                @if($cv && $cv->cv_file_url)
                                    <p class="mt-2 text-xs text-slate-500">CV enregistré : <a href="{{ $cv->cv_file_url }}" target="_blank" class="text-rdc-blue hover:underline">Voir</a></p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Diplôme / attestation</label>
                                <input type="file" name="diploma_file" accept=".pdf,.doc,.docx" class="w-full text-sm text-slate-700" />
                                @error('diploma_file')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                                @if($cv && $cv->diploma_file)
                                    <p class="mt-2 text-xs text-slate-500">Diplôme enregistré : <a href="{{ Storage::url($cv->diploma_file) }}" target="_blank" class="text-rdc-blue hover:underline">Voir</a></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">5. Expérience professionnelle</p>
                            <h2 class="text-xl font-bold text-slate-900">Blocs d'expérience</h2>
                        </div>
                        <button type="button" class="add-repeater inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-section="experience">
                            <i class="fas fa-plus"></i> Ajouter une expérience
                        </button>
                    </div>
                    <div id="experience-items" data-section="experience" class="space-y-5">
                        @foreach($experienceEntries as $index => $entry)
                            <div class="repeater-item bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="item-number inline-flex h-10 w-10 items-center justify-center rounded-full bg-rdc-blue text-sm font-black text-white">{{ $index + 1 }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Expérience {{ $index + 1 }}</p>
                                            <p class="text-xs text-slate-400">Poste, entreprise et période</p>
                                        </div>
                                    </div>
                                    @if(count($experienceEntries) > 1)
                                        <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="experience">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Poste</label>
                                        <input type="text" name="experience[{{ $index }}][job_title]" value="{{ old("experience.$index.job_title", $entry['job_title'] ?? '') }}" data-repeater-field="job_title" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                        @if($errors->has("experience.$index.job_title"))<p class="text-sm text-red-500">{{ $errors->first("experience.$index.job_title") }}</p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Entreprise</label>
                                        <input type="text" name="experience[{{ $index }}][company]" value="{{ old("experience.$index.company", $entry['company'] ?? '') }}" data-repeater-field="company" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                        @if($errors->has("experience.$index.company"))<p class="text-sm text-red-500">{{ $errors->first("experience.$index.company") }}</p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Début</label>
                                        <input type="text" name="experience[{{ $index }}][start_date]" value="{{ old("experience.$index.start_date", $entry['start_date'] ?? '') }}" data-repeater-field="start_date" placeholder="Ex: 01/2022" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                        @if($errors->has("experience.$index.start_date"))<p class="text-sm text-red-500">{{ $errors->first("experience.$index.start_date") }}</p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Fin / Actuel</label>
                                        <input type="text" name="experience[{{ $index }}][end_date]" value="{{ old("experience.$index.end_date", $entry['end_date'] ?? '') }}" data-repeater-field="end_date" placeholder="Ex: 12/2023" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                        @if($errors->has("experience.$index.end_date"))<p class="text-sm text-red-500">{{ $errors->first("experience.$index.end_date") }}</p>@endif
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Description</label>
                                    <textarea name="experience[{{ $index }}][description]" data-repeater-field="description" rows="4" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none" placeholder="Responsabilités, réussites, tâches...">{{ old("experience.$index.description", $entry['description'] ?? '') }}</textarea>
                                    @if($errors->has("experience.$index.description"))<p class="text-sm text-red-500">{{ $errors->first("experience.$index.description") }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">6. Formation</p>
                                <h2 class="text-xl font-bold text-slate-900">Blocs de formation</h2>
                            </div>
                            <button type="button" class="add-repeater inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-section="education">
                                <i class="fas fa-plus"></i> Ajouter une formation
                            </button>
                        </div>
                        <div id="education-items" data-section="education" class="space-y-5">
                            @foreach($educationEntries as $index => $entry)
                                <div class="repeater-item bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="item-number inline-flex h-10 w-10 items-center justify-center rounded-full bg-rdc-blue text-sm font-black text-white">{{ $index + 1 }}</span>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Formation {{ $index + 1 }}</p>
                                                <p class="text-xs text-slate-400">Diplôme, établissement et période</p>
                                            </div>
                                        </div>
                                        @if(count($educationEntries) > 1)
                                            <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="education">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Établissement</label>
                                            <input type="text" name="education[{{ $index }}][school]" value="{{ old("education.$index.school", $entry['school'] ?? '') }}" data-repeater-field="school" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                            @if($errors->has("education.$index.school"))<p class="text-sm text-red-500">{{ $errors->first("education.$index.school") }}</p>@endif
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Diplôme</label>
                                            <input type="text" name="education[{{ $index }}][degree]" value="{{ old("education.$index.degree", $entry['degree'] ?? '') }}" data-repeater-field="degree" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                            @if($errors->has("education.$index.degree"))<p class="text-sm text-red-500">{{ $errors->first("education.$index.degree") }}</p>@endif
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Début</label>
                                            <input type="text" name="education[{{ $index }}][start_date]" value="{{ old("education.$index.start_date", $entry['start_date'] ?? '') }}" data-repeater-field="start_date" placeholder="Ex: 2018" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                            @if($errors->has("education.$index.start_date"))<p class="text-sm text-red-500">{{ $errors->first("education.$index.start_date") }}</p>@endif
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Fin</label>
                                            <input type="text" name="education[{{ $index }}][end_date]" value="{{ old("education.$index.end_date", $entry['end_date'] ?? '') }}" data-repeater-field="end_date" placeholder="Ex: 2022" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                            @if($errors->has("education.$index.end_date"))<p class="text-sm text-red-500">{{ $errors->first("education.$index.end_date") }}</p>@endif
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Description</label>
                                        <textarea name="education[{{ $index }}][description]" data-repeater-field="description" rows="4" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none" placeholder="Contenu de la formation, mentions, cours clés...">{{ old("education.$index.description", $entry['description'] ?? '') }}</textarea>
                                        @if($errors->has("education.$index.description"))<p class="text-sm text-red-500">{{ $errors->first("education.$index.description") }}</p>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">7. Compétences</p>
                                <h2 class="text-xl font-bold text-slate-900">Compétences principales</h2>
                            </div>
                            <span class="inline-flex h-11 min-w-[3rem] items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-semibold text-slate-700">Rapide</span>
                        </div>
                        <div id="skill-chips" class="flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <span class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm">
                                    <span>{{ $skill }}</span>
                                    <button type="button" class="remove-skill text-slate-400 hover:text-red-600" aria-label="Supprimer compétence">×</button>
                                    <input type="hidden" name="skills[]" value="{{ $skill }}">
                                </span>
                            @endforeach
                        </div>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <input id="skill-input" type="text" placeholder="Ajouter une compétence" class="flex-1 rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            <button id="add-skill-button" type="button" class="inline-flex items-center justify-center gap-2 rounded-3xl bg-rdc-blue px-6 py-4 text-sm font-semibold text-white hover:bg-rdc-blue-dark transition">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        @if($errors->has('skills') || $errors->has('skills.*'))
                            <div class="mt-3 space-y-1 text-sm text-red-500">
                                @foreach($errors->get('skills.*') as $error)
                                    <p>{{ $error[0] ?? $error }}</p>
                                @endforeach
                                @foreach($errors->get('skills') as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">8. Langues</p>
                                <h2 class="text-xl font-bold text-slate-900">Maîtrise linguistique</h2>
                            </div>
                            <button type="button" class="add-repeater inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-section="languages">
                                <i class="fas fa-plus"></i> Ajouter une langue
                            </button>
                        </div>
                        <div id="languages-items" data-section="languages" class="space-y-4">
                            @foreach($languages as $index => $language)
                                <div class="repeater-item grid grid-cols-1 gap-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-3 lg:items-end">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Langue</label>
                                        <input type="text" name="languages[{{ $index }}][language]" value="{{ old("languages.$index.language", $language['language'] ?? '') }}" data-repeater-field="language" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                        @if($errors->has("languages.$index.language"))<p class="text-sm text-red-500">{{ $errors->first("languages.$index.language") }}</p>@endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Niveau</label>
                                        <select name="languages[{{ $index }}][level]" data-repeater-field="level" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                            @foreach(['Débutant', 'Intermédiaire', 'Excellent'] as $level)
                                                <option value="{{ $level }}" {{ old("languages.$index.level", $language['level'] ?? 'Débutant') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has("languages.$index.level"))<p class="text-sm text-red-500">{{ $errors->first("languages.$index.level") }}</p>@endif
                                    </div>
                                    <div class="flex items-center justify-between lg:justify-end">
                                        @if(count($languages) > 1)
                                            <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="languages">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Statut du CV</h2>
                    @if($cv)
                        <p class="text-sm text-slate-600">Votre CV ProConnect est enregistré.</p>
                    @else
                        <p class="text-sm text-slate-600">Aucun CV enregistré pour le moment. Complétez toutes les sections puis enregistrez.</p>
                    @endif
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3"><span class="mt-1 text-rdc-blue">•</span> Champs RDC inclus : province, téléphone, adresse.</li>
                        <li class="flex items-start gap-3"><span class="mt-1 text-rdc-blue">•</span> Sections métier, expérience, formation, compétences et langues.</li>
                        <li class="flex items-start gap-3"><span class="mt-1 text-rdc-blue">•</span> CV joint automatiquement aux candidatures.</li>
                    </ul>
                </div>

                @if($cv && ($cv->experience || $cv->education || $cv->skills || $cv->languages))
                <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Résumé rapide</h2>
                    @if($cv->summary)
                        <p class="text-sm text-slate-700 mb-4">{{ $cv->summary }}</p>
                    @endif
                    @if($cv->experience)
                        <div class="mb-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Expérience</p>
                            <ul class="list-disc list-inside text-sm text-slate-600">@foreach($cv->experience as $item)<li>{{ $item }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @if($cv->skills)
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Compétences</p>
                            <ul class="list-disc list-inside text-sm text-slate-600">@foreach($cv->skills as $item)<li>{{ $item }}</li>@endforeach</ul>
                        </div>
                    @endif
                </div>
                @endif
            </aside>

            <div class="hidden">
                <template id="experience-template">
                    <div class="repeater-item bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="item-number inline-flex h-10 w-10 items-center justify-center rounded-full bg-rdc-blue text-sm font-black text-white">0</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Nouvelle expérience</p>
                                    <p class="text-xs text-slate-400">Poste, entreprise et période</p>
                                </div>
                            </div>
                            <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="experience">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Poste</label>
                                <input type="text" data-repeater-field="job_title" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Entreprise</label>
                                <input type="text" data-repeater-field="company" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Début</label>
                                <input type="text" data-repeater-field="start_date" placeholder="Ex: 01/2022" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Fin / Actuel</label>
                                <input type="text" data-repeater-field="end_date" placeholder="Ex: 12/2023" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Description</label>
                            <textarea data-repeater-field="description" rows="4" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none" placeholder="Responsabilités, réussites, tâches..."></textarea>
                        </div>
                    </div>
                </template>
                <template id="education-template">
                    <div class="repeater-item bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="item-number inline-flex h-10 w-10 items-center justify-center rounded-full bg-rdc-blue text-sm font-black text-white">0</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Nouvelle formation</p>
                                    <p class="text-xs text-slate-400">Diplôme, établissement et période</p>
                                </div>
                            </div>
                            <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="education">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Établissement</label>
                                <input type="text" data-repeater-field="school" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Diplôme</label>
                                <input type="text" data-repeater-field="degree" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Début</label>
                                <input type="text" data-repeater-field="start_date" placeholder="Ex: 2018" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Fin</label>
                                <input type="text" data-repeater-field="end_date" placeholder="Ex: 2022" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Description</label>
                            <textarea data-repeater-field="description" rows="4" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none" placeholder="Contenu de la formation, mentions, cours clés..."></textarea>
                        </div>
                    </div>
                </template>
                <template id="languages-template">
                    <div class="repeater-item grid grid-cols-1 gap-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-3 lg:items-end">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Langue</label>
                            <input type="text" data-repeater-field="language" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Niveau</label>
                            <select data-repeater-field="level" class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none">
                                <option value="Débutant">Débutant</option>
                                <option value="Intermédiaire">Intermédiaire</option>
                                <option value="Excellent">Excellent</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between lg:justify-end">
                            <button type="button" class="remove-repeater inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100" data-section="languages">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="text-right">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-3xl bg-rdc-blue px-8 py-4 text-sm font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-rdc-blue/25 hover:bg-rdc-blue-dark transition">
                    <i class="fas fa-save"></i> Enregistrer mon CV
                </button>
            </div>
        </form>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.add-repeater').forEach(function (button) {
                        button.addEventListener('click', function () {
                            addRepeater(this.dataset.section);
                        });
                    });

                    document.body.addEventListener('click', function (event) {
                        if (event.target.closest('.remove-repeater')) {
                            event.preventDefault();
                            removeRepeater(event.target.closest('.remove-repeater'));
                        }

                        if (event.target.closest('.remove-skill')) {
                            event.preventDefault();
                            var chip = event.target.closest('span');
                            if (chip) {
                                chip.remove();
                            }
                        }
                    });

                    var addSkillButton = document.getElementById('add-skill-button');
                    var skillInput = document.getElementById('skill-input');

                    if (addSkillButton) {
                        addSkillButton.addEventListener('click', function () {
                            addSkill();
                        });
                    }

                    if (skillInput) {
                        skillInput.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                addSkill();
                            }
                        });
                    }

                    ['experience', 'education', 'languages'].forEach(function (section) {
                        refreshRepeater(section);
                    });
                });

                function addRepeater(section) {
                    var template = document.getElementById(section + '-template');
                    var container = document.getElementById(section + '-items');

                    if (!template || !container) {
                        return;
                    }

                    var clone = document.importNode(template.content, true);
                    clone.querySelectorAll('[data-repeater-field]').forEach(function (field) {
                        field.value = '';
                    });
                    container.appendChild(clone);
                    refreshRepeater(section);
                }

                function removeRepeater(button) {
                    var section = button.dataset.section;
                    var item = button.closest('.repeater-item');
                    var container = document.getElementById(section + '-items');

                    if (!item || !container) {
                        return;
                    }

                    item.remove();
                    refreshRepeater(section);
                }

                function refreshRepeater(section) {
                    var container = document.getElementById(section + '-items');
                    if (!container) {
                        return;
                    }

                    var items = Array.from(container.querySelectorAll('.repeater-item'));
                    items.forEach(function (item, index) {
                        item.querySelectorAll('[data-repeater-field]').forEach(function (field) {
                            var fieldName = field.dataset.repeaterField;
                            if (!fieldName) {
                                return;
                            }
                            if (field.tagName === 'SELECT' || field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
                                field.name = section + '[' + index + '][' + fieldName + ']';
                            }
                        });
                        var number = item.querySelector('.item-number');
                        if (number) {
                            number.textContent = index + 1;
                        }
                        var removeButton = item.querySelector('.remove-repeater');
                        if (removeButton) {
                            if (items.length <= 1) {
                                removeButton.classList.add('invisible');
                            } else {
                                removeButton.classList.remove('invisible');
                            }
                        }
                    });
                }

                function addSkill() {
                    var input = document.getElementById('skill-input');
                    var chips = document.getElementById('skill-chips');
                    if (!input || !chips) {
                        return;
                    }

                    var value = input.value.trim();
                    if (!value) {
                        return;
                    }

                    var exists = Array.from(chips.querySelectorAll('input[name="skills[]"]')).some(function (field) {
                        return field.value.toLowerCase() === value.toLowerCase();
                    });

                    if (exists) {
                        input.value = '';
                        return;
                    }

                    var tag = document.createElement('span');
                    tag.className = 'flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm';

                    var label = document.createElement('span');
                    label.textContent = value;
                    tag.appendChild(label);

                    var removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'remove-skill text-slate-400 hover:text-red-600';
                    removeButton.setAttribute('aria-label', 'Supprimer compétence');
                    removeButton.textContent = '×';
                    tag.appendChild(removeButton);

                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'skills[]';
                    hidden.value = value;
                    tag.appendChild(hidden);

                    chips.appendChild(tag);
                    input.value = '';
                }
            </script>
        @endpush
    </div>
</div>
@endsection
