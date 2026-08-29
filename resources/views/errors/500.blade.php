@extends('errors.layout')

@section('code', '500')
@section('icon', 'fa-triangle-exclamation')
@section('title', 'Une erreur est survenue')
@section('message')
    Quelque chose s'est mal passé de notre côté. Ce n'est pas de votre faute — réessayez dans un instant, et si le problème persiste, contactez le support.
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn"><i class="fas fa-house"></i> Retour à l'accueil</a>
    <a href="mailto:{{ config('mail.from.address') }}" class="btn btn-secondary">Contacter le support</a>
@endsection
