@extends('layouts.public')

@section('title', 'Plan du site')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-20">
    <h1 class="text-4xl font-bold text-slate-900 mb-6">Plan du site</h1>
    <p class="text-slate-600 leading-relaxed mb-6">
        Retrouvez ci-dessous l'essentiel des pages publiques de ProConnect pour accéder rapidement à nos services.
    </p>
    <ul class="space-y-3 text-slate-700">
        <li><a href="{{ route('home') }}" class="font-semibold text-rdc-blue hover:underline">Accueil</a></li>
        <li><a href="{{ route('public.services.index') }}" class="font-semibold text-rdc-blue hover:underline">Services</a></li>
        <li><a href="{{ route('public.jobs.index') }}" class="font-semibold text-rdc-blue hover:underline">Offres d'emploi</a></li>
        <li><a href="{{ route('public.artisans.index') }}" class="font-semibold text-rdc-blue hover:underline">Artisans</a></li>
        <li><a href="{{ route('about') }}" class="font-semibold text-rdc-blue hover:underline">À propos</a></li>
        <li><a href="{{ route('contact') }}" class="font-semibold text-rdc-blue hover:underline">Contact</a></li>
    </ul>
</div>
@endsection
