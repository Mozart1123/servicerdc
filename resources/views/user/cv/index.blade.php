@extends('layouts.user')

@section('title', 'Gestion de mon CV')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 pb-20">
    
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-[2rem] shadow-sm animate-fade-in flex items-center gap-4">
            <i class="fas fa-check-circle text-2xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100" data-aos="fade-up">
        {{-- Header --}}
        <div class="bg-slate-900 px-12 py-12 relative overflow-hidden text-center">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-rdc-blue/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-rdc-yellow/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="w-24 h-24 bg-rdc-blue/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-rdc-blue border border-rdc-blue/20">
                    <i class="fas fa-file-invoice text-4xl"></i>
                </div>
                <h2 class="text-3xl font-heading font-black text-white">Mon Dossier CV</h2>
                <p class="text-slate-400 text-sm mt-3 max-w-md mx-auto leading-relaxed">
                    Téléchargez votre CV au format PDF ou Word pour que les recruteurs puissent le consulter directement lors de vos candidatures.
                </p>
            </div>
        </div>

        <div class="p-12">
            {{-- Current File Status --}}
            @if($cv && $cv->cv_file)
                <div class="bg-slate-50 rounded-[2.5rem] p-10 border border-slate-100 text-center mb-10 group hover:border-rdc-blue transition-all">
                    <div class="inline-flex items-center gap-4 px-6 py-3 bg-white rounded-2xl shadow-sm border border-slate-100 mb-6">
                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                        <span class="text-sm font-black text-slate-700">Votre CV actuel est en ligne</span>
                    </div>
                    
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ $cv->cv_file_url }}" target="_blank" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition shadow-xl">
                            <i class="fas fa-eye mr-2"></i> Voir mon CV
                        </a>
                        <form action="{{ route('user.cv.destroy') }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce fichier ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-8 py-4 bg-red-50 text-red-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-100 transition">
                                <i class="fas fa-trash mr-2"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Upload Section --}}
            <form action="{{ route('user.cv.file.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <div id="drop-area" class="relative group cursor-pointer">
                    <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" class="hidden" required onchange="updateFileName(this)">
                    
                    <div onclick="document.getElementById('cv_file').click()" 
                         class="border-4 border-dashed border-slate-100 rounded-[3rem] p-16 text-center transition-all group-hover:border-rdc-blue group-hover:bg-rdc-blue/5">
                        
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-rdc-blue group-hover:text-white transition-all">
                            <i class="fas fa-cloud-upload-alt text-3xl"></i>
                        </div>
                        
                        <h3 class="text-xl font-black text-slate-900 mb-2" id="file-label">
                            {{ $cv && $cv->cv_file ? 'Remplacer mon CV' : 'Cliquez pour sélectionner un fichier' }}
                        </h3>
                        <p class="text-sm font-medium text-slate-400">PDF, DOC ou DOCX (Max 5Mo)</p>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="px-12 py-5 bg-rdc-blue text-white rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:scale-105 transition-all shadow-2xl shadow-rdc-blue/30">
                        {{ $cv && $cv->cv_file ? 'Mettre à jour mon CV' : 'Télécharger mon CV' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Recruiter Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-amber-50 rounded-[2.5rem] p-8 border border-amber-100 flex items-start gap-6">
            <div class="w-14 h-14 bg-amber-200/50 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-eye text-amber-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-black text-slate-900 mb-2">Visibilité Recruteurs</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Une fois téléversé, votre fichier CV est joint automatiquement à chacune de vos candidatures d'emploi.</p>
            </div>
        </div>

        <div class="bg-rdc-blue/5 rounded-[2.5rem] p-8 border border-rdc-blue/10 flex items-start gap-6">
            <div class="w-14 h-14 bg-rdc-blue/10 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-shield-alt text-rdc-blue text-xl"></i>
            </div>
            <div>
                <h4 class="font-black text-slate-900 mb-2">Sécurité & Accès</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">Seuls les recruteurs des offres auxquelles vous postulez peuvent consulter votre document.</p>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('file-label');
    const fileName = input.files[0].name;
    label.innerHTML = `<span class="text-rdc-blue italic">Fichier sélectionné :</span><br>${fileName}`;
}
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endsection
