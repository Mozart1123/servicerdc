@extends('layouts.user')

@section('header_title', 'Vérification d\'Identité')

@section('content')
<div class="space-y-8 pb-20 max-w-4xl mx-auto">

    <!-- Welcome Verification Banner (Post-registration) -->
    @if(session('welcome_verification'))
        <div class="p-6 bg-[#e6f7f8] border border-[#29B6D1]/30 rounded-2xl text-[#0f7a86] flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-sparkles text-2xl text-[#29B6D1]"></i>
                <div class="font-bold text-sm leading-relaxed">{{ session('welcome_verification') }}</div>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-white text-[#0f7a86] font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-slate-50 transition border border-[#29B6D1]/20 shrink-0 shadow-sm">
                Plus tard → Accéder au dashboard
            </a>
        </div>
    @else
        <div class="flex justify-end">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                <i class="fas fa-arrow-left"></i> Plus tard — Accéder au dashboard
            </a>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Identity Verification Card -->
    <div class="bg-white border border-slate-100 p-8 rounded-2xl shadow-sm space-y-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-rdc-blue/10 flex items-center justify-center text-rdc-blue text-xl">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase">Vérification d'Identité</h3>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">
                    Obtenez le badge "{{ $user->verified_badge_label }}"
                </p>
            </div>
        </div>

        @if($verification->verification_status === 'approved')
            <!-- Approved State -->
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-emerald-950 uppercase tracking-wider">Identité Vérifiée</h4>
                        <p class="text-xs text-emerald-800 font-medium mt-0.5">Validée le {{ $verification->verified_at ? $verification->verified_at->format('d/m/Y') : 'Récemment' }} par notre équipe de modération.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($verification->identity_document_path)
                        <a href="{{ route('user.identity-verification.general.download', ['file' => 'document']) }}" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            <i class="fas fa-eye mr-1.5"></i> Document
                        </a>
                    @endif
                    @if($verification->selfie_path)
                        <a href="{{ route('user.identity-verification.general.download', ['file' => 'selfie']) }}" target="_blank" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            <i class="fas fa-user-circle mr-1.5"></i> Selfie
                        </a>
                    @endif
                </div>
            </div>

        @elseif($verification->verification_status === 'pending')
            <!-- Pending State -->
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30 animate-pulse">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-blue-950 uppercase tracking-wider">Vérification en cours</h4>
                        <p class="text-xs text-blue-800 font-medium mt-0.5">Votre dossier est en cours d'examen par notre équipe. Vous recevrez une notification dès validation.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($verification->identity_document_path)
                        <a href="{{ route('user.identity-verification.general.download', ['file' => 'document']) }}" target="_blank" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            <i class="fas fa-file-alt mr-1.5"></i> Document
                        </a>
                    @endif
                    @if($verification->selfie_path)
                        <a href="{{ route('user.identity-verification.general.download', ['file' => 'selfie']) }}" target="_blank" class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            <i class="fas fa-camera mr-1.5"></i> Selfie
                        </a>
                    @endif
                </div>
            </div>

        @else
            <!-- Not Submitted or Rejected State -->
            @if($verification->verification_status === 'rejected')
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 mb-4 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-red-500 text-white flex items-center justify-center text-lg shadow-md flex-none mt-0.5">
                        <i class="fas fa-times"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-red-950 uppercase tracking-wider">Demande refusée</h4>
                        <p class="text-xs text-red-800 font-medium mt-1">
                            <span class="font-bold">Motif du refus :</span> {{ $verification->verification_rejection_reason ?? 'Document non conforme ou illisible.' }}
                            @if($verification->verification_rejection_comment)
                                <br><span class="text-slate-600 font-normal">Précision : {{ $verification->verification_rejection_comment }}</span>
                            @endif
                        </p>
                        <p class="text-[11px] text-red-700 font-medium mt-1">Veuillez soumettre à nouveau vos documents ci-dessous.</p>
                    </div>
                </div>
            @endif

            <!-- Explicatif exact du cahier des charges -->
            <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-2xl space-y-3">
                <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-shield-halved text-[#0f7a86] text-base"></i>
                    <span>Vérification de sécurité</span>
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Pour protéger notre communauté contre les faux profils et garantir la confiance entre utilisateurs, nous vous demandons une photo de vous tenant votre pièce d'identité à côté de votre visage. Pourquoi cette étape ? Elle nous permet de confirmer que le document appartient bien à la personne qui l'utilise — une pratique standard sur les plateformes sérieuses (banques, services de paiement, applications de transport). Vos données sont protégées. Cette photo est utilisée uniquement pour la vérification par notre équipe et n'est jamais partagée publiquement, ni visible par les autres utilisateurs.
                </p>
                <div class="pt-2 border-t border-slate-200/50">
                    <p class="text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Conseils pratiques :</p>
                    <ul class="text-xs text-slate-600 space-y-1 font-medium list-disc list-inside">
                        <li>Assurez-vous que votre visage et le document soient bien visibles et lisibles</li>
                        <li>Prenez la photo dans un endroit bien éclairé</li>
                        <li>Évitez les reflets ou l'écran de votre téléphone si vous photographiez un document numérique</li>
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('identity-verification.general.store') }}" enctype="multipart/form-data" class="space-y-5 bg-white border border-slate-100 p-6 rounded-2xl">
                @csrf

                <div>
                    <!-- Type of Document -->
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Type de pièce d'identité</label>
                    <select name="identity_document_type" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-medium focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all">
                        <option value="" disabled selected>Sélectionnez un document</option>
                        <option value="voter_card" {{ old('identity_document_type') == 'voter_card' ? 'selected' : '' }}>Carte d'électeur RDC</option>
                        <option value="passport" {{ old('identity_document_type') == 'passport' ? 'selected' : '' }}>Passeport</option>
                        <option value="national_id" {{ old('identity_document_type') == 'national_id' ? 'selected' : '' }}>Carte Nationale d'Identité</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- File Upload — Document -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">1. Pièce d'identité (Scan / Photo)</label>
                        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs font-medium text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rdc-blue/10 file:text-rdc-blue hover:file:bg-rdc-blue/20 transition-all">
                        <span class="text-[10px] text-slate-400 mt-1 block">Formats : PDF, JPG, PNG (Max 5 Mo).</span>
                    </div>

                    <!-- File Upload — Selfie -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">2. Photo Selfie (Visage + Document)</label>
                        <input type="file" name="selfie" accept=".jpg,.jpeg,.png" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs font-medium text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rdc-blue/10 file:text-rdc-blue hover:file:bg-rdc-blue/20 transition-all">
                        <span class="text-[10px] text-slate-400 mt-1 block">Formats : JPG, PNG (Max 5 Mo). Non publique.</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-rdc-blue to-rdc-blue-dark text-white font-black rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                    <i class="fas fa-upload"></i>
                    Soumettre mon dossier de vérification
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
