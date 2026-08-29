@extends('errors.layout')

@section('code', '404')
@section('icon', 'fa-compass')
@section('title', 'Page introuvable')
@section('message')
    La page que vous cherchez n'existe pas, a été déplacée, ou l'adresse comporte une erreur.
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn"><i class="fas fa-house"></i> Retour à l'accueil</a>
    <a href="javascript:history.back()" class="btn btn-secondary">Page précédente</a>
@endsection
