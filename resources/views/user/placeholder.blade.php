@extends('layouts.user')

@section('title', 'Page en Construction')

@section('content')
<div class="h-[calc(100vh-12rem)] flex flex-col items-center justify-center text-center px-4">
    <div class="relative mb-8">
        <div class="absolute inset-0 bg-rdc-blue rounded-full blur-3xl opacity-20 animate-pulse"></div>
        <div class="relative w-32 h-32 bg-white rounded-3xl shadow-xl flex items-center justify-center text-5xl text-rdc-blue border border-slate-100">
            <i class="fas fa-tools"></i>
        </div>
    </div>
    
    <h2 class="text-3xl font-heading font-extrabold text-slate-900 mb-4 tracking-tight">Cette page arrive bientôt !</h2>
    <p class="text-slate-500 max-w-md mx-auto leading-relaxed mb-8">
        Nous travaillons dur pour vous offrir la meilleure expérience possible. 
        Revenez très bientôt pour découvrir cette nouvelle fonctionnalité de <span class="text-rdc-blue font-bold">ServiceRDC</span>.
    </p>
    
    <a href="{{ route('user.dashboard') }}" class="px-8 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-rdc-blue shadow-lg shadow-slate-200 transition-all flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Retour au Dashboard
    </a>
</div>
@endsection
