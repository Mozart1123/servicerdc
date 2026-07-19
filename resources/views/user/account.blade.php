@extends('layouts.client')

@section('header_title', 'Mon Compte')
@section('header_subtitle', 'Gérez vos informations et votre activité.')

@section('client_content')
<div class="text-center py-10 lg:hidden">
    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-hand-pointer text-2xl text-slate-400"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2">Bienvenue dans votre espace</h3>
    <p class="text-slate-500 text-sm max-w-sm mx-auto">Veuillez sélectionner une option dans le menu pour afficher son contenu.</p>
</div>
<div class="hidden lg:block text-center py-16">
    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
        <i class="fas fa-arrow-left text-3xl text-slate-400"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-800 mb-3">Sélectionnez une rubrique</h3>
    <p class="text-slate-500 text-sm max-w-md mx-auto">Choisissez une rubrique dans le menu latéral à gauche pour afficher et gérer son contenu.</p>
</div>
@endsection
