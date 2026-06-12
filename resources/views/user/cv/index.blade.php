@extends('layouts.user')

@section('title', 'Creer mon CV')

@section('content')
<div x-data="cvBuilder()" class="max-w-7xl mx-auto space-y-8 pb-20">

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-5 rounded-2xl shadow-sm flex items-center gap-4">
            <i class="fas fa-check-circle text-2xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-10 py-10 text-center relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-rdc-blue/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="w-20 h-20 bg-rdc-blue/20 rounded-3xl flex items-center justify-center mx-auto mb-4 text-rdc-blue border border-rdc-blue/20">
                    <i class="fas fa-file-invoice text-3xl"></i>
                </div>
                <h2 class="text-3xl font-heading font-black text-white">Creer mon CV</h2>
                <p class="text-slate-400 text-sm mt-2 max-w-md mx-auto">Repondez aux 7 questions et votre CV se genere automatiquement. Tout est modifiable !</p>
            </div>
        </div>

        {{-- Tab navigation --}}
        <div class="flex border-b border-slate-100">
            <button @click="tab = 'builder'" :class="tab === 'builder' ? 'border-rdc-blue text-rdc-blue bg-rdc-blue/5' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 py-4 text-sm font-bold uppercase tracking-widest border-b-2 transition">
                <i class="fas fa-edit mr-2"></i> Questionnaire
            </button>
            <button @click="tab = 'preview'" :class="tab === 'preview' ? 'border-rdc-blue text-rdc-blue bg-rdc-blue/5' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 py-4 text-sm font-bold uppercase tracking-widest border-b-2 transition">
                <i class="fas fa-eye mr-2"></i> Apercu CV
            </button>
            <button @click="tab = 'upload'" :class="tab === 'upload' ? 'border-rdc-blue text-rdc-blue bg-rdc-blue/5' : 'border-transparent text-slate-500 hover:text-slate-700'" class="flex-1 py-4 text-sm font-bold uppercase tracking-widest border-b-2 transition">
                <i class="fas fa-upload mr-2"></i> Importer un fichier
            </button>
        </div>
    </div>

    {{-- TAB 1: BUILDER (7 MCQ) --}}
    <div x-show="tab === 'builder'" x-cloak>
        <form action="{{ route('user.cv.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Personal Info --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-rdc-blue text-white flex items-center justify-center text-sm"><i class="fas fa-user"></i></span>
                    Informations personnelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Nom complet</label>
                        <input type="text" name="full_name" value="{{ $cv->full_name ?? auth()->user()->name }}" x-model="formData.full_name"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Titre du poste souhaite</label>
                        <input type="text" name="job_title" value="{{ $cv->job_title ?? '' }}" x-model="formData.job_title" placeholder="Ex: Developpeur Web, Plombier, Comptable..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Telephone</label>
                        <input type="text" name="phone" value="{{ $cv->phone_number ?? '' }}" x-model="formData.phone"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Adresse</label>
                        <input type="text" name="address" value="{{ $cv->address ?? '' }}" x-model="formData.address"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Resume professionnel</label>
                        <textarea name="summary" rows="3" x-model="formData.summary" placeholder="Decrivez-vous en quelques lignes..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- 7 MCQ Questions --}}
            @if(isset($questions))
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">
                <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm"><i class="fas fa-list-check"></i></span>
                    7 Questions a choix multiples
                </h3>
                <p class="text-sm text-slate-400 -mt-4 mb-4">Selectionnez une reponse pour chaque question. Ces reponses seront affichees sur votre CV.</p>

                @foreach($questions as $i => $q)
                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-rdc-blue text-white flex items-center justify-center text-[10px]">{{ $i + 1 }}</span>
                        {{ $q['question'] }}
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mt-3">
                        @foreach($q['options'] as $option)
                        <label class="cursor-pointer">
                            <input type="radio" name="{{ $q['key'] }}" value="{{ $option }}" class="hidden peer"
                                {{ ($cv->template_answers[$q['key']] ?? '') === $option ? 'checked' : '' }}
                                @change="formData.{{ $q['key'] }} = '{{ addslashes($option) }}'">
                            <span class="block px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-600 text-center peer-checked:bg-rdc-blue peer-checked:text-white peer-checked:border-rdc-blue transition hover:border-rdc-blue/50">
                                {{ $option }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="text-center">
                <button type="submit" class="px-12 py-4 bg-rdc-blue text-white rounded-2xl font-bold text-sm uppercase tracking-widest hover:bg-rdc-blue-dark transition shadow-xl shadow-rdc-blue/20 hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Enregistrer mon CV
                </button>
            </div>
        </form>
    </div>

    {{-- TAB 2: LIVE PREVIEW --}}
    <div x-show="tab === 'preview'" x-cloak>
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-4 bg-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Apercu de votre CV</span>
                <button onclick="window.print()" class="px-4 py-2 bg-rdc-blue text-white text-xs font-bold rounded-lg hover:bg-rdc-blue-dark transition">
                    <i class="fas fa-print mr-1"></i> Imprimer / PDF
                </button>
            </div>

            <div id="cv-preview" class="p-10 max-w-3xl mx-auto">
                {{-- CV Header --}}
                <div class="border-b-4 border-rdc-blue pb-6 mb-6">
                    <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight" x-text="formData.full_name || '{{ auth()->user()->name }}'"></h1>
                    <p class="text-lg text-rdc-blue font-bold mt-1" x-text="formData.job_title || 'Titre du poste'"></p>
                    <div class="flex flex-wrap gap-4 mt-3 text-xs text-slate-500">
                        <span x-show="formData.phone"><i class="fas fa-phone mr-1 text-rdc-blue"></i><span x-text="formData.phone"></span></span>
                        <span><i class="fas fa-envelope mr-1 text-rdc-blue"></i>{{ auth()->user()->email }}</span>
                        <span x-show="formData.address"><i class="fas fa-map-marker-alt mr-1 text-rdc-blue"></i><span x-text="formData.address"></span></span>
                    </div>
                </div>

                {{-- Summary --}}
                <div x-show="formData.summary" class="mb-6">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Profil</h2>
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="formData.summary"></p>
                </div>

                {{-- MCQ Answers as CV sections --}}
                <div class="grid grid-cols-2 gap-6">
                    <div x-show="formData.experience_level">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Experience</h2>
                        <p class="text-sm text-slate-600" x-text="formData.experience_level"></p>
                    </div>
                    <div x-show="formData.education_level">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Formation</h2>
                        <p class="text-sm text-slate-600" x-text="formData.education_level"></p>
                    </div>
                    <div x-show="formData.preferred_sector">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Secteur</h2>
                        <p class="text-sm text-slate-600" x-text="formData.preferred_sector"></p>
                    </div>
                    <div x-show="formData.languages_spoken">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Langues</h2>
                        <p class="text-sm text-slate-600" x-text="formData.languages_spoken"></p>
                    </div>
                    <div x-show="formData.availability">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Disponibilite</h2>
                        <p class="text-sm text-slate-600" x-text="formData.availability"></p>
                    </div>
                    <div x-show="formData.salary_expectation">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Salaire attendu</h2>
                        <p class="text-sm text-slate-600" x-text="formData.salary_expectation"></p>
                    </div>
                    <div x-show="formData.key_strength" class="col-span-2">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2 border-b border-slate-200 pb-1">Force principale</h2>
                        <p class="text-sm text-slate-600" x-text="formData.key_strength"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: FILE UPLOAD --}}
    <div x-show="tab === 'upload'" x-cloak>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-10">
            @if($cv && $cv->cv_file)
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-center mb-8">
                <i class="fas fa-file-pdf text-red-500 text-3xl mb-3"></i>
                <p class="font-bold text-slate-700">Votre CV actuel est en ligne</p>
                <div class="flex justify-center gap-3 mt-4">
                    <a href="{{ $cv->cv_file_url }}" target="_blank" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:scale-105 transition shadow-lg">
                        <i class="fas fa-eye mr-2"></i> Voir
                    </a>
                    <form action="{{ route('user.cv.destroy') }}" method="POST" onsubmit="return confirm('Supprimer ce fichier ?')">
                        @csrf @method('DELETE')
                        <button class="px-6 py-3 bg-red-50 text-red-500 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-red-100 transition">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <form action="{{ route('user.cv.file.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div onclick="document.getElementById('cv_file').click()" class="border-4 border-dashed border-slate-100 rounded-3xl p-14 text-center cursor-pointer hover:border-rdc-blue hover:bg-rdc-blue/5 transition">
                    <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" class="hidden" required onchange="document.getElementById('file-label').textContent = this.files[0].name">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <i class="fas fa-cloud-upload-alt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1" id="file-label">Cliquez pour selectionner un fichier</h3>
                    <p class="text-sm text-slate-400">PDF, DOC ou DOCX (Max 5Mo)</p>
                </div>
                <div class="text-center mt-6">
                    <button type="submit" class="px-10 py-4 bg-rdc-blue text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:scale-105 transition shadow-xl shadow-rdc-blue/20">
                        Telecharger mon CV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cvBuilder() {
    return {
        tab: 'builder',
        formData: {
            full_name: '{{ $cv->full_name ?? auth()->user()->name }}',
            job_title: '{{ $cv->job_title ?? '' }}',
            phone: '{{ $cv->phone_number ?? '' }}',
            address: '{{ $cv->address ?? '' }}',
            summary: '{{ $cv->summary ?? '' }}',
            experience_level: '{{ $cv->template_answers['experience_level'] ?? '' }}',
            availability: '{{ $cv->template_answers['availability'] ?? '' }}',
            preferred_sector: '{{ $cv->template_answers['preferred_sector'] ?? '' }}',
            education_level: '{{ $cv->template_answers['education_level'] ?? '' }}',
            languages_spoken: '{{ $cv->template_answers['languages_spoken'] ?? '' }}',
            salary_expectation: '{{ $cv->template_answers['salary_expectation'] ?? '' }}',
            key_strength: '{{ $cv->template_answers['key_strength'] ?? '' }}',
        }
    }
}
</script>

<style>
    @media print {
        body * { visibility: hidden; }
        #cv-preview, #cv-preview * { visibility: visible; }
        #cv-preview { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
@endsection
