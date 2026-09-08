@extends($layout)

@section('header_title', 'Sécurité')
@section('header_subtitle', 'Gérez votre mot de passe et vos sessions actives.')

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section($contentSection)
<div class="space-y-10">

    {{-- Espace Client affiche déjà les messages flash globalement dans son
         layout ; on ne les répète ici que pour artisan/recruteur (layouts.user
         ne les affiche pas). --}}
    @unless ($isClient)
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif
    @endunless

    <!-- 1. Change Password -->
    <section>
        <h2 class="text-lg font-bold text-slate-900 mb-6">Changer le mot de passe</h2>

        <form method="POST" action="{{ route('user.security.password.update') }}" class="space-y-6 max-w-2xl">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Mot de passe actuel</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm @error('current_password', 'passwordUpdate') border-red-400 @enderror">
                @error('current_password', 'passwordUpdate')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm @error('password', 'passwordUpdate') border-red-400 @enderror">
                    @error('password', 'passwordUpdate')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                </div>
            </div>

            <div class="flex justify-start pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>
    </section>

    <hr class="border-slate-100">

    <!-- 2. Active Sessions -->
    <section>
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Sessions actives</h2>
            <p class="text-sm text-slate-500">Appareils connectés à votre compte récemment.</p>
        </div>

        <div class="space-y-3 max-w-3xl">
            @forelse ($sessions as $userSession)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-slate-200 rounded-xl gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 {{ $userSession->is_current ? 'bg-emerald-50 text-emerald-500' : 'bg-slate-50 text-slate-500' }} rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas {{ $userSession->is_mobile ? 'fa-mobile-screen' : 'fa-laptop' }}"></i>
                        </div>
                        <div>
                            <h4 class="font-{{ $userSession->is_current ? 'bold' : 'medium' }} text-slate-900 text-sm">
                                {{ $userSession->browser }}@if($userSession->ip_address) &bull; {{ $userSession->ip_address }}@endif
                            </h4>
                            @if ($userSession->is_current)
                                <p class="text-xs text-emerald-600 font-medium mt-0.5">Session actuelle</p>
                            @else
                                <p class="text-xs text-slate-500 mt-0.5">Dernière activité : {{ $userSession->last_active->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                    @unless ($userSession->is_current)
                        <form method="POST" action="{{ route('user.security.sessions.revoke', $userSession->id) }}"
                              onsubmit="return confirm('Déconnecter cet appareil ?');"
                              class="self-start sm:self-auto ml-14 sm:ml-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors">
                                Déconnecter
                            </button>
                        </form>
                    @endunless
                </div>
            @empty
                <p class="text-sm text-slate-500">Aucune session active trouvée.</p>
            @endforelse
        </div>
    </section>

    <hr class="border-slate-100">

    <!-- 3. Account Deletion -->
    <section x-data="{ confirming: {{ $errors->deleteAccount->any() ? 'true' : 'false' }} }">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-red-600 mb-1">Zone de danger</h2>
            <p class="text-sm text-slate-600 max-w-2xl">La suppression de votre compte est définitive. Vos informations personnelles seront anonymisées et vous serez déconnecté.</p>
        </div>

        <button type="button" x-show="!confirming" @click="confirming = true"
                class="px-6 py-2.5 bg-white border border-red-200 text-red-600 font-medium text-sm rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors">
            Supprimer mon compte
        </button>

        <form x-show="confirming" x-cloak method="POST" action="{{ route('user.security.destroy') }}"
              onsubmit="return confirm('Cette action est définitive. Confirmer la suppression du compte ?');"
              class="max-w-md space-y-3 p-4 bg-red-50 border border-red-200 rounded-xl">
            @csrf
            @method('DELETE')
            <label class="block text-sm font-medium text-slate-700">Confirmez avec votre mot de passe</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition-all text-sm @error('password', 'deleteAccount') border-red-400 @enderror">
            @error('password', 'deleteAccount')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex gap-3 pt-1">
                <button type="submit" class="px-5 py-2 bg-red-600 text-white font-medium text-sm rounded-lg hover:bg-red-700 transition-colors">
                    Confirmer la suppression
                </button>
                <button type="button" @click="confirming = false" class="px-5 py-2 bg-white border border-slate-300 text-slate-600 font-medium text-sm rounded-lg hover:bg-slate-50 transition-colors">
                    Annuler
                </button>
            </div>
        </form>
    </section>

</div>
@endsection
