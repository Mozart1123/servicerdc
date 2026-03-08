@extends('layouts.user')

@section('title', 'Paramètres de mon Profil')
@section('header_title', 'Mon Profil')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <!-- Profile Header Wrap -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="h-32 bg-gradient-to-r from-rdc-blue via-rdc-blue-dark to-slate-900 relative">
            <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
        </div>
        <div class="px-8 pb-8 flex flex-col items-center sm:items-start text-center sm:text-left">
            <div class="relative -mt-16 group">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=007FFF&color=fff&size=200" 
                     class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover" alt="Profile">
                <label class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white text-xl">
                    <i class="fas fa-camera"></i>
                    <input type="file" class="hidden">
                </label>
            </div>
            <div class="mt-4">
                <h2 class="text-2xl font-heading font-extrabold text-slate-900">{{ auth()->user()->name ?? 'Utilisateur' }}</h2>
                <p class="text-slate-500 font-medium">Kinshasa, République Démocratique du Congo 🇨🇩</p>
                <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-4">
                    <button class="px-6 py-2 bg-rdc-blue text-white text-sm font-bold rounded-xl hover:bg-rdc-blue-dark shadow-md shadow-blue-500/20 transition-all">
                        Changer la photo
                    </button>
                    <button class="px-6 py-2 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Sections -->
    <form class="space-y-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 leading-tight">Informations Personnelles</h3>
                    <p class="text-xs text-slate-400 font-medium">Mettez à jour vos informations de base.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Nom complet</label>
                    <input type="text" value="{{ auth()->user()->name }}" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-rdc-blue outline-none transition-all font-medium text-slate-900">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-rdc-blue outline-none transition-all font-medium text-slate-900" readonly>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Téléphone</label>
                    <input type="tel" placeholder="+243 ..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-rdc-blue outline-none transition-all font-medium text-slate-900">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Ville</label>
                    <select class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-rdc-blue outline-none transition-all font-medium text-slate-900">
                        <option>Kinshasa</option>
                        <option>Lubumbashi</option>
                        <option>Goma</option>
                        <option>Kisangani</option>
                        <option>Bukavu</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Bio / Résumé</label>
                <textarea rows="4" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:bg-white focus:border-rdc-blue outline-none transition-all font-medium text-slate-900" placeholder="Parlez-nous de vous..."></textarea>
            </div>
        </div>

        <!-- Professional Experience Header -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-50 text-rdc-yellow flex items-center justify-center">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 leading-tight">Expériences Professionnelles</h3>
                        <p class="text-xs text-slate-400 font-medium">Ajoutez ou modifiez vos parcours.</p>
                    </div>
                </div>
                <button type="button" class="p-2 text-rdc-blue hover:bg-blue-50 rounded-lg transition-colors">
                    <i class="fas fa-plus"></i> <span class="text-sm font-bold ml-1">Ajouter</span>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-rdc-blue text-xl font-bold">
                        <i class="fas fa-building text-slate-200"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-slate-900">Senior Designer</h5>
                        <p class="text-xs text-slate-500">Vodacom RDC | 2021 - Present</p>
                    </div>
                    <button type="button" class="text-slate-300 hover:text-slate-600 transition-colors">
                        <i class="fas fa-pencil-alt text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
            <button type="button" class="w-full sm:w-auto px-8 py-4 text-slate-500 font-bold hover:text-slate-900 transition-colors">Annuler</button>
            <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-rdc-blue text-white font-bold rounded-2xl hover:bg-rdc-blue-dark shadow-xl shadow-blue-500/30 transform hover:-translate-y-1 transition-all">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
