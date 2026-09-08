@extends($layout)

@section('title', 'Signaler un problème | ProConnect')
@section('header_title', 'Signaler un problème')
@section('header_subtitle', 'Décrivez ce qui s\'est passé, notre équipe examinera votre signalement.')

@section($contentSection)
<div class="max-w-2xl mx-auto space-y-6">

    @php
        $artisanForRequest = $serviceRequest->artisan ?? $serviceRequest->service?->artisan;
    @endphp

    {{-- Context card --}}
    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Demande concernée</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0">
                <p class="font-bold text-slate-900 truncate">{{ $serviceRequest->requested_service_name ?? 'Demande de service' }}</p>
                <p class="text-sm text-slate-500">
                    @if($artisanForRequest)
                        Avec {{ $artisanForRequest->name }} ·
                    @endif
                    {{ $serviceRequest->status_label }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div>
        <h2 class="text-lg font-bold text-slate-900 mb-2">Que s'est-il passé ?</h2>
        <p class="text-sm text-slate-500 mb-6">
            Décrivez le problème rencontré avec cet artisan (absence, retard important, travail non conforme, comportement inapproprié...).
        </p>

        <form action="{{ route('user.service-requests.report.submit', $serviceRequest->id) }}" method="POST">
            @csrf

            <textarea name="message" rows="6" required minlength="10" maxlength="2000"
                      placeholder="Expliquez ce qui s'est passé le plus précisément possible..."
                      class="w-full px-4 py-3 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm resize-none">{{ old('message') }}</textarea>
            @error('message') <p class="text-xs text-red-500 font-medium mt-2">{{ $message }}</p> @enderror

            <div class="flex flex-col sm:flex-row items-center gap-4 mt-6">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-red-500 text-white font-medium text-sm rounded-lg hover:bg-red-600 transition-colors text-center">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer le signalement
                </button>
                <a href="{{ route('user.service-requests.show', $serviceRequest->id) }}" class="w-full sm:w-auto px-6 py-2.5 text-slate-500 font-medium text-sm hover:text-slate-900 transition-colors text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
