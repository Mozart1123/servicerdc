@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')
@section('header_title', 'Gestion des Utilisateurs')
@section('page_title', 'Édition du Profil')
@section('page_subtitle', 'Modifiez les informations personnelles et le statut du compte utilisateur.')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom complet -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Nom Complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-rdc-blue transition-colors">
                    @error('name')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-rdc-blue transition-colors">
                    @error('email')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-rdc-blue transition-colors">
                    @error('phone')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type de Profil -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Type de Profil</label>
                    <select name="user_type" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-rdc-blue transition-colors">
                        <option value="client" @selected(old('user_type', $user->user_type) === 'client')>Client / Demandeur</option>
                        <option value="artisan" @selected(old('user_type', $user->user_type) === 'artisan')>Artisan / Prestataire</option>
                        <option value="recruiter" @selected(old('user_type', $user->user_type) === 'recruiter')>Recruteur / Entreprise</option>
                        <option value="job_seeker" @selected(old('user_type', $user->user_type) === 'job_seeker')>Chercheur d'emploi</option>
                    </select>
                    @error('user_type')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut du compte -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Statut du Compte</label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-rdc-blue transition-colors">
                        <option value="active" @selected(old('status', $user->status) === 'active')>Actif (Validé)</option>
                        <option value="pending" @selected(old('status', $user->status) === 'pending')>En Attente</option>
                        <option value="suspended" @selected(old('status', $user->status) === 'suspended')>Suspendu / Banni</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold text-xs rounded-xl hover:bg-rdc-blue-dark transition-colors shadow-lg shadow-rdc-blue/20">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
