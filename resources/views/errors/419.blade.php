@extends('errors.layout')

@section('code', '419')
@section('icon', 'fa-clock-rotate-left')
@section('title', 'Session expirée')
@section('message')
    Votre session a expiré, probablement parce que la page est restée ouverte trop longtemps. Veuillez vous reconnecter et réessayer.
@endsection

@section('actions')
    <a href="{{ route('login') }}" class="btn"><i class="fas fa-right-to-bracket"></i> Se reconnecter</a>
    <a href="{{ url('/') }}" class="btn btn-secondary">Accueil</a>
@endsection
