@extends($layout)

@section('title', 'Postuler - ' . $job->title . ' | ProConnect')
@section('header_title', 'Postuler à l\'offre')

@section($contentSection)
<div class="space-y-8 pb-20">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-2/3 bg-white rounded-[3rem] shadow-xl border border-slate-100 p-8" data-aos="fade-up">
                <div class="mb-8">
                    <a href="{{ route('user.jobs.show', $job->id) }}" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-100 text-slate-700 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">
                        <i class="fas fa-arrow-left"></i>
                        Retour à l'offre
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-heading font-black text-slate-900 leading-tight">Postuler à : {{ $job->title }}</h1>
                            <p class="text-sm text-slate-500 mt-2">{{ $job->company_name }} — {{ $job->location }} • {{ $job->contract_type }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-rdc-blue/10 text-rdc-blue text-xs font-black uppercase tracking-widest rounded-full">{{ $job->category ?? 'Catégorie inconnue' }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Salaire</p>
                            <p class="font-black text-slate-900">{{ $job->salary_range ?? 'À négocier' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Candidatures</p>
                            <p class="font-black text-slate-900">{{ $job->applications->count() }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Date limite</p>
                            <p class="font-black text-slate-900">{{ $job->deadline ? $job->deadline->format('d M Y') : 'Aucune' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:w-1/3 space-y-6">
                <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-8" data-aos="fade-left">
                    <h2 class="text-xl font-heading font-black text-slate-900 mb-4">Conseils de candidature</h2>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><span class="text-rdc-blue">•</span> Votre CV ProConnect sera joint automatiquement si vous en avez un.</li>
                        <li class="flex gap-3"><span class="text-rdc-blue">•</span> L’ajout d’un document supplémentaire est optionnel.</li>
                        <li class="flex gap-3"><span class="text-rdc-blue">•</span> Une lettre de motivation personnelle augmente vos chances.</li>
                        <li class="flex gap-3"><span class="text-rdc-blue">•</span> Vérifiez vos coordonnées avant l’envoi.</li>
                    </ul>
                </div>
                <div class="bg-slate-50 rounded-[3rem] border border-slate-100 p-8 text-sm text-slate-700" data-aos="fade-left">
                    <h3 class="font-black text-slate-900 mb-3">Statut du CV</h3>
                    @if($userCv)
                        <p class="mb-3">Votre CV ProConnect est prêt et sera joint automatiquement.</p>
                        <a href="{{ route('user.cv.index') }}" class="inline-flex items-center gap-2 px-4 py-3 bg-white rounded-3xl text-slate-700 font-bold border border-slate-200 hover:bg-slate-100 transition">
                            <i class="fas fa-file-alt"></i> Voir mon CV
                        </a>
                    @else
                        <p>Vous n’avez pas de CV ProConnect. Vous pouvez télécharger un fichier pour postuler.</p>
                        <a href="{{ route('user.cv.create', ['return_to' => route('user.jobs.apply.form', $job->id)]) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-3 bg-rdc-blue text-white rounded-3xl font-bold hover:bg-rdc-blue-dark transition">
                            <i class="fas fa-plus"></i> Créer mon CV</a>
                    @endif
                </div>
            </aside>
        </div>

        <div class="mt-10 bg-white rounded-[3rem] shadow-xl border border-slate-100 p-8" data-aos="fade-up">
            @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
                @if(session($type))
                    <div class="mb-6 bg-{{ $color }}-50 border-l-4 border-{{ $color }}-500 text-{{ $color }}-700 p-5 rounded-2xl shadow-sm">
                        <p class="font-bold">{{ session($type) }}</p>
                    </div>
                @endif
            @endforeach

            <form method="POST" action="{{ route('user.jobs.apply', $job->id) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-4">Votre CV ProConnect</p>
                    <div class="space-y-3 text-sm text-slate-700">
                        <p><span class="font-bold">Nom :</span> {{ $user->name }}</p>
                        <p><span class="font-bold">Email :</span> {{ $user->email }}</p>
                        <p><span class="font-bold">Téléphone :</span> {{ $user->phone ?? 'Non renseigné' }}</p>
                        @if($userCv->job_title)
                            <p><span class="font-bold">Titre :</span> {{ $userCv->job_title }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Lettre de motivation</label>
                    <textarea name="cover_letter" rows="7"
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none"
                        placeholder="Expliquez en quelques lignes pourquoi vous souhaitez ce poste...">{{ old('cover_letter') }}</textarea>
                    @error('cover_letter')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Lettre de motivation</label>
                    <textarea name="cover_letter" rows="7"
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none"
                        placeholder="Expliquez en quelques lignes pourquoi vous souhaitez ce poste...">{{ old('cover_letter') }}</textarea>
                    @error('cover_letter')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-slate-50 p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Sélection de CV</p>
                    @if($userCv)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="font-black text-slate-900">CV ProConnect</p>
                                <p class="text-sm text-slate-600">Votre CV sera joint automatiquement à la candidature.</p>
                            </div>
                            <a href="{{ route('user.cv.index') }}" class="inline-flex items-center gap-2 px-4 py-3 bg-white rounded-3xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition">
                                <i class="fas fa-eye"></i> Voir mon CV
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-slate-600">Vous n’avez pas de CV ProConnect enregistré. Veuillez joindre un document pour postuler.</p>
                    @endif
                </div>

                <button type="submit" class="w-full py-5 bg-rdc-blue text-white font-heading font-black text-sm uppercase tracking-[0.2em] rounded-[2rem] shadow-2xl shadow-rdc-blue/30 hover:bg-rdc-blue-dark transition-all">
                    Envoyer ma candidature
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
