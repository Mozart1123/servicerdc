@extends($layout)

@section('title', 'Postuler - ' . $job->title . ' | ProConnect')
@section('header_title', 'Postuler à l\'offre')

@section($contentSection)
<div class="space-y-8 pb-20">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-8" data-aos="fade-up">
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
            </div>

            <div class="mt-10">
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
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-4">Vos coordonnées</p>
                        <div class="space-y-3 text-sm text-slate-700">
                            <p><span class="font-bold">Nom :</span> {{ $user->name }}</p>
                            <p><span class="font-bold">Email :</span> {{ $user->email }}</p>
                            <p><span class="font-bold">Téléphone :</span> {{ $user->phone ?? 'Non renseigné' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Joindre votre CV (PDF, DOCX) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="file" name="cv_attachment" accept=".pdf,.doc,.docx" required
                                class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-rdc-blue/10 file:text-rdc-blue hover:file:bg-rdc-blue/20 cursor-pointer">
                        </div>
                        @error('cv_attachment')<p class="text-sm text-red-500 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Message pour le recruteur (Optionnel)</label>
                        <textarea name="message" rows="5"
                            class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-900 focus:border-rdc-blue focus:ring-2 focus:ring-rdc-blue/10 outline-none resize-none"
                            placeholder="Un petit mot pour appuyer votre candidature (optionnel)...">{{ old('message') }}</textarea>
                        @error('message')<p class="text-sm text-red-500 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full py-5 bg-rdc-blue text-white font-heading font-black text-sm uppercase tracking-[0.2em] rounded-[2rem] shadow-2xl shadow-rdc-blue/30 hover:bg-rdc-blue-dark transition-all">
                        Envoyer ma candidature
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
