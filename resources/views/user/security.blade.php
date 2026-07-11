@extends($layout)

@section('header_title', 'Sécurité')
@section('header_subtitle', 'Gérez votre mot de passe et vos sessions actives.')

@section($contentSection)
<div class="space-y-10">
    
    <!-- 1. Change Password -->
    <section>
        <h2 class="text-lg font-bold text-slate-900 mb-6">Changer le mot de passe</h2>
        
        <form class="space-y-6 max-w-2xl">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Mot de passe actuel</label>
                <input type="password" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                    <input type="password" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
                    <input type="password" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
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
            <!-- Current Session -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-slate-200 rounded-xl gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-500 shrink-0">
                        <i class="fas fa-mobile-screen"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">iPhone 14 Pro Max • Kinshasa</h4>
                        <p class="text-xs text-emerald-600 font-medium mt-0.5">Session actuelle • Chrome Mobile</p>
                    </div>
                </div>
            </div>

            <!-- Other Session -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-slate-200 rounded-xl gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-500 shrink-0">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-900 text-sm">MacBook Air • Lubumbashi</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Dernière connexion : Il y a 2 jours</p>
                    </div>
                </div>
                <button type="button" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors self-start sm:self-auto ml-14 sm:ml-0">
                    Déconnecter
                </button>
            </div>
        </div>
    </section>

    <hr class="border-slate-100">

    <!-- 3. Account Deletion -->
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-red-600 mb-1">Zone de danger</h2>
            <p class="text-sm text-slate-600 max-w-2xl">La suppression de votre compte est définitive. Toutes vos données seront effacées.</p>
        </div>
        <button type="button" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 font-medium text-sm rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors">
            Supprimer mon compte
        </button>
    </section>

</div>
@endsection
