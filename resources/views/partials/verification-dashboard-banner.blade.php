@auth
    @php
        $u = auth()->user();
        $isArtisan = $u->isArtisan();
        
        if ($isArtisan) {
            $status = $u->artisanLevel?->verification_status ?? 'not_submitted';
            $verifyRoute = route('user.artisan.level');
        } else {
            $status = $u->identityVerification?->verification_status ?? 'not_submitted';
            $verifyRoute = route('user.identity-verification.show');
        }
    @endphp

    @if($status === 'not_submitted')
        <div class="mb-6 p-5 bg-[#e6f7f8] border border-[#29B6D1]/30 rounded-3xl text-[#0f7a86] flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-bold leading-relaxed">
                <i class="fas fa-shield-check text-xl text-[#0f7a86] shrink-0"></i>
                <span>Boostez votre visibilité ! Les profils vérifiés reçoivent plus de demandes. Complétez votre vérification en 2 minutes.</span>
            </div>
            <a href="{{ $verifyRoute }}" class="px-5 py-2.5 bg-[#0f7a86] hover:bg-[#0c636d] text-white font-bold rounded-2xl text-xs uppercase tracking-wider transition shadow-sm shrink-0 whitespace-nowrap">
                Compléter maintenant →
            </a>
        </div>
    @elseif($status === 'pending')
        <div class="mb-6 p-5 bg-blue-50 border border-blue-200 rounded-3xl text-blue-800 flex items-center gap-3 shadow-sm">
            <i class="fas fa-hourglass-half text-lg text-blue-600 animate-pulse shrink-0"></i>
            <div class="text-sm font-bold leading-relaxed">
                <span>Votre vérification est en cours de traitement par notre équipe.</span>
            </div>
        </div>
    @elseif($status === 'rejected')
        <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-3xl text-red-900 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3 text-sm font-bold leading-relaxed">
                <i class="fas fa-triangle-exclamation text-xl text-red-600 shrink-0"></i>
                <span>Votre vérification n'a pas été validée.</span>
            </div>
            <a href="{{ $verifyRoute }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl text-xs uppercase tracking-wider transition shadow-sm shrink-0 whitespace-nowrap">
                Voir le motif et réessayer →
            </a>
        </div>
    @endif
@endauth
