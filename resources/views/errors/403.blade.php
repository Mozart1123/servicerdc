@extends('errors.layout')

@section('code', '403')
@section('icon', 'fa-lock')
@section('title', 'Accès refusé')
@section('message')
    {{ $exception->getMessage() ?: "Vous n'avez pas la permission d'effectuer cette action." }}
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn"><i class="fas fa-house"></i> Retour à l'accueil</a>
    <a href="javascript:history.back()" class="btn btn-secondary">Page précédente</a>
@endsection
