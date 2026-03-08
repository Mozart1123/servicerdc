@extends('layouts.user')

@section('header_title', 'Sécurité & Accès')

@section('content')
<div class="max-w-4xl mx-auto space-y-12 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-6">
        <div class="w-20 h-20 rounded-[2.5rem] bg-slate-900 text-white flex items-center justify-center text-3xl shadow-xl shadow-slate-200">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div>
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Coffre-fort <span class="text-rdc-blue">Numérique</span></h2>
            <p class="text-slate-500 font-medium">Gérez votre mot de passe, vos sessions actives et protégez votre identité sur ServiceRDC.</p>
        </div>
    </div>

    <!-- Security Sections -->
    <div class="grid grid-cols-1 gap-10">
        
        <!-- 1. Change Password -->
        <div class="bg-white rounded-[3.5rem] p-10 md:p-14 shadow-sm border border-slate-100 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full bg-rdc-blue"></div>
            
            <h3 class="text-xl font-heading font-black text-slate-900 uppercase mb-10 flex items-center gap-4">
                <i class="fas fa-key text-rdc-blue"></i>
                Changer mon Mot de Passe
            </h3>

            <form class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Mot de passe actuel</label>
                    <input type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-slate-900">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Nouveau mot de passe</label>
                        <input type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-slate-900">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Confirmer le nouveau</label>
                        <input type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-slate-900">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-blue-500/20">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Active Sessions -->
        <div class="bg-white rounded-[3.5rem] p-10 md:p-14 shadow-sm border border-slate-100">
            <h3 class="text-xl font-heading font-black text-slate-900 uppercase mb-10 flex items-center gap-4">
                <i class="fas fa-desktop text-slate-300"></i>
                Sessions Actives
            </h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 group hover:bg-white hover:shadow-lg transition-all">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl text-emerald-500 shadow-inner">
                            <i class="fas fa-mobile-screen"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 text-sm uppercase">iPhone 14 Pro Max • Kinshasa</h4>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1">Session Actuelle • En ligne</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Chrome Mobile</span>
                </div>

                <div class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 opacity-60">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl text-slate-400 shadow-inner">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 text-sm uppercase">MacBook Air • Lubumbashi</h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Dernière connexion: 2 jours</p>
                        </div>
                    </div>
                    <button class="text-[9px] font-black text-red-500 uppercase tracking-widest hover:underline">Déconnecter</button>
                </div>
            </div>
        </div>

        <!-- 3. Account Deletion (Danger Zone) -->
        <div class="bg-red-50 rounded-[3.5rem] p-10 md:p-14 border border-red-100 space-y-4">
            <h3 class="text-xl font-heading font-black text-red-600 uppercase flex items-center gap-4">
                <i class="fas fa-skull-crossbones"></i>
                Zone de Danger
            </h3>
            <p class="text-sm font-medium text-red-600/70 max-w-md">Une fois votre compte supprimé, toutes vos données (profil, favoris, historique) seront définitivement effacées du Nexus ServiceRDC.</p>
            <button class="mt-4 px-10 py-4 bg-red-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-500/20">
                Supprimer mon compte
            </button>
        </div>

    </div>
</div>
@endsection
