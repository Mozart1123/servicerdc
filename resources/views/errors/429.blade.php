@extends('errors.layout')

@section('code', '429')
@section('icon', 'fa-hourglass-half')
@section('title', 'Trop de tentatives')
@section('message')
    Vous avez effectué trop de tentatives en peu de temps. Merci de patienter quelques instants avant de réessayer.
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn"><i class="fas fa-house"></i> Retour à l'accueil</a>
@endsection
