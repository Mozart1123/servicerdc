@extends('layouts.user')

@section('header_title', 'Nouvelles Opportunités')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Hero Filter -->
    <div class="relative bg-slate-900 rounded-[3.5rem] p-12 text-white overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-rdc-blue/40 to-transparent"></div>
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rdc-blue/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 text-center md:text-left">
                <span class="px-4 py-1.5 bg-rdc-blue text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full">
                    Actualité du Nexus
                </span>
                <h2 class="text-4xl font-heading font-black uppercase leading-tight">Les dernières <span class="text-rdc-blue">Incursions</span> digitales</h2>
                <p class="text-slate-400 font-medium max-w-md">Découvrez les services et opportunités d'emploi qui viennent d'apparaître sur la plateforme ce matin.</p>
            </div>
            
            <div class="flex gap-4">
                <button class="px-8 py-4 bg-white text-slate-900 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:scale-105 transition-all">
                    Services
                </button>
                <button class="px-8 py-4 bg-slate-800 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:scale-105 transition-all">
                    Emplois
                </button>
            </div>
        </div>
    </div>

    <!-- Discovery Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @php
            $items = [
                [
                    'type' => 'SERVICE', 
                    'title' => 'Expertise Solaire & Energie', 
                    'author' => 'Jean-Luc Kahasha', 
                    'badge' => 'Premium', 
                    'color' => 'amber',
                    'image' => 'https://images.unsplash.com/photo-1509391366360-feaffa648b8b?auto=format&fit=crop&w=800&q=80'
                ],
                [
                    'type' => 'JOB', 
                    'title' => 'Directeur des Opérations', 
                    'author' => 'Bolloré Logistics RDC', 
                    'badge' => 'Urgent', 
                    'color' => 'blue',
                    'image' => 'https://images.unsplash.com/photo-1586528116311-ad86d7c4420e?auto=format&fit=crop&w=800&q=80'
                ],
                [
                    'type' => 'SERVICE', 
                    'title' => 'Design d\'Intérieur High-Tech', 
                    'author' => 'Studio Lubumbashi', 
                    'badge' => 'Nouveau', 
                    'color' => 'purple',
                    'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80'
                ],
                [
                    'type' => 'JOB', 
                    'title' => 'Développeur Mobile Flutter', 
                    'author' => 'Rawbank Tech', 
                    'badge' => 'Hot', 
                    'color' => 'emerald',
                    'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=800&q=80'
                ],
                [
                    'type' => 'SERVICE', 
                    'title' => 'Traduction Juridique FR/EN', 
                    'author' => 'Cabinet Maître Ngoy', 
                    'badge' => 'Vérifié', 
                    'color' => 'rdc-blue',
                    'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80'
                ],
            ];
        @endphp

        @foreach($items as $i)
        <div class="bg-white rounded-[2.5rem] p-4 border border-slate-100 shadow-sm hover:shadow-2xl transition-all group">
            <div class="aspect-video rounded-[2rem] mb-6 relative overflow-hidden">
                <img src="{{ $i['image'] }}" alt="{{ $i['title'] }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute top-4 left-4 px-3 py-1 bg-{{ $i['color'] }}-500 text-white text-[8px] font-black uppercase tracking-widest rounded-lg shadow-lg z-10">
                    {{ $i['type'] }}
                </div>
            </div>
            
            <div class="px-4 pb-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $i['author'] }}</span>
                    <span class="text-[9px] font-black text-{{ $i['color'] }}-600 bg-{{ $i['color'] }}-50 px-2 py-1 rounded-md">{{ $i['badge'] }}</span>
                </div>
                <h4 class="text-base font-heading font-black text-slate-900 group-hover:text-rdc-blue transition-colors mb-4">{{ $i['title'] }}</h4>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Kinshasa</span>
                    <button class="w-10 h-10 bg-slate-50 rounded-xl text-slate-400 hover:bg-rdc-blue hover:text-white transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Infinite scroll simulation card -->
        <div class="bg-slate-50 rounded-[2.5rem] p-8 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center space-y-4">
            <div class="w-12 h-12 rounded-full border-4 border-slate-200 border-t-rdc-blue animate-spin"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Séquençage des données en cours...</p>
        </div>
    </div>
</div>
@endsection
