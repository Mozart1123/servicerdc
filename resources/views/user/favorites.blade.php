@extends('layouts.user')

@section('header_title', 'Mes Favoris')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Header Summary -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 px-4">
        <div>
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Ma Sélection <span class="text-red-500">Privée</span></h2>
            <p class="text-slate-500 font-medium">Retrouvez ici tous les services et emplois que vous avez marqués d'un cœur pour plus tard.</p>
        </div>
        <div class="flex items-center gap-4 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <div class="px-6 py-2 text-center border-r border-slate-100">
                <span class="block text-xl font-black text-slate-900">12</span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
            </div>
            <div class="px-6 py-2 text-center">
                <span class="block text-xl font-black text-red-500">3</span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Urgents</span>
            </div>
        </div>
    </div>

    <!-- Favorites Tabs -->
    <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl w-fit mx-auto md:mx-0">
        <button class="px-8 py-3 bg-white text-slate-900 font-black rounded-xl text-[10px] uppercase tracking-widest shadow-sm">Tout</button>
        <button class="px-8 py-3 text-slate-500 font-black rounded-xl text-[10px] uppercase tracking-widest hover:text-slate-900 transition-colors">Services</button>
        <button class="px-8 py-3 text-slate-500 font-black rounded-xl text-[10px] uppercase tracking-widest hover:text-slate-900 transition-colors">Emplois</button>
    </div>

    <!-- Favorites Grid/List -->
    <div class="space-y-6">
        @php
            $favorites = [
                ['type' => 'SERVICE', 'title' => 'Plomberie Sanitaire Express', 'author' => 'Jean Plombier', 'date' => 'Ajouté le 05 Fév', 'loc' => 'Gombe, Kinshasa'],
                ['type' => 'JOB', 'title' => 'Analyste Financier Senior', 'author' => 'Equity BCDC', 'date' => 'Ajouté le 02 Fév', 'loc' => '30 Juin, Kinshasa'],
                ['type' => 'SERVICE', 'title' => 'Consultance Juridique Business', 'author' => 'Me Mbemba', 'date' => 'Ajouté le 28 Jan', 'loc' => 'Lubumbashi'],
            ];
        @endphp

        @foreach($favorites as $fav)
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
            <!-- Type Indicator -->
            <div class="hidden md:block absolute left-0 top-0 bottom-0 w-2 bg-{{ $fav['type'] === 'JOB' ? 'rdc-blue' : 'red-500' }}"></div>
            
            <div class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center text-2xl text-slate-300 group-hover:bg-{{ $fav['type'] === 'JOB' ? 'rdc-blue' : 'red-500' }} group-hover:text-white transition-all shadow-inner">
                <i class="fas {{ $fav['type'] === 'JOB' ? 'fa-briefcase' : 'fa-tools' }}"></i>
            </div>
            
            <div class="flex-1 text-center md:text-left space-y-2">
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 mb-1">
                    <span class="text-[9px] font-black px-3 py-1 bg-slate-900 text-white rounded-lg uppercase tracking-widest">{{ $fav['type'] }}</span>
                    <span class="text-[10px] font-bold text-slate-400 capitalize">{{ $fav['date'] }}</span>
                </div>
                <h3 class="text-xl font-heading font-black text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $fav['title'] }}</h3>
                <div class="flex items-center justify-center md:justify-start gap-4 text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                    <span><i class="fas fa-building mr-1.5 opacity-50"></i>{{ $fav['author'] }}</span>
                    <span><i class="fas fa-location-dot mr-1.5 opacity-50"></i>{{ $fav['loc'] }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/10">
                    <i class="fas fa-heart text-sm"></i>
                </button>
                <button class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rdc-blue hover:scale-105 transition-all shadow-xl">
                    Voir Détails
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State (Hidden) -->
    <div class="hidden flex flex-col items-center justify-center py-20 text-center space-y-6">
        <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center text-4xl text-slate-200">
            <i class="fas fa-heart-crack"></i>
        </div>
        <div>
            <h4 class="text-xl font-heading font-black text-slate-900 uppercase">Cœur Solitaire</h4>
            <p class="text-slate-500 font-medium">Vous n'avez pas encore ajouté de favoris. Explorez la plateforme !</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl">Explorer</a>
    </div>
</div>
@endsection
