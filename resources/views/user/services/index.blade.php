@extends('layouts.user')

@section('title', 'Découvrir des Services')
@section('header_title', 'Marketplace de Services')

@section('content')
<div class="space-y-12 pb-20">
    <!-- Hero Section -->
    <div class="relative bg-white rounded-[3.5rem] p-12 shadow-2xl shadow-blue-500/5 border border-slate-100 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2 opacity-50"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-rdc-blue rounded-full border border-blue-100 animate-bounce">
                    <i class="fas fa-sparkles text-xs"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Nouveaux Artisans vérifiés</span>
                </div>
                <h2 class="text-5xl font-heading font-black text-slate-900 leading-tight">Accédez au meilleur du <span class="text-rdc-blue">Savoir-faire</span> local</h2>
                <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-md">Trouvez instantanément des professionnels qualifiés pour tous vos besoins domestiques et professionnels.</p>
                
                <form action="{{ route('user.services.index') }}" method="GET" class="flex items-center gap-4 bg-slate-50 p-2 rounded-3xl border border-slate-100">
                    <div class="flex-1 relative group pl-4">
                        <i class="fas fa-magnifying-glass absolute left-0 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" placeholder="Quel service recherchez-vous ?" 
                               class="w-full pl-8 pr-4 py-4 bg-transparent border-none focus:ring-0 font-bold text-slate-900">
                    </div>
                    <button type="submit" class="px-8 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20">
                        Trouver
                    </button>
                </form>
            </div>
            
            <div class="hidden lg:grid grid-cols-2 gap-6 p-4">
                <div class="space-y-6 mt-12">
                    <img src="https://images.unsplash.com/photo-1581578731522-aa792bc490fb?auto=format&fit=crop&w=400&q=80" class="rounded-[2.5rem] shadow-2xl border-4 border-white" alt="Clean">
                    <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=400&q=80" class="rounded-[2.5rem] shadow-2xl border-4 border-white" alt="Electric">
                </div>
                <div class="space-y-6">
                    <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=400&q=80" class="rounded-[2.5rem] shadow-2xl border-4 border-white" alt="Construct">
                    <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=400&q=80" class="rounded-[2.5rem] shadow-2xl border-4 border-white" alt="Kitchen">
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Bar -->
    <div class="flex items-center gap-4 overflow-x-auto pb-4 no-scrollbar px-4">
        <button class="px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl">Tout</button>
        @foreach(['Ménage', 'Plomberie', 'Électricité', 'Informatique', 'Construction', 'Livraison', 'Peinture'] as $cat)
        <button class="px-8 py-3 bg-white border border-slate-200 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-rdc-blue hover:text-rdc-blue transition-all whitespace-nowrap">{{ $cat }}</button>
        @endforeach
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 px-4">
        @php
            $mockServices = [
                ['name' => 'Nettoyage Industriel', 'author' => 'Alpha Clean Sarl', 'price' => '150$', 'rating' => '4.9', 'reviews' => '128', 'img' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Installation Panneaux Solaire', 'author' => 'PowerRDC', 'price' => '800$', 'rating' => '5.0', 'reviews' => '56', 'img' => 'https://images.unsplash.com/photo-1509391366360-feaffa648b8b?auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Traiteur Événementiel', 'author' => 'Délices de Kin', 'price' => '25$/pers', 'rating' => '4.7', 'reviews' => '230', 'img' => 'https://images.unsplash.com/photo-1555244162-803834f70033?auto=format&fit=crop&w=600&q=80'],
                ['name' => 'Déménagement & Stockage', 'author' => 'MoveIt Kin', 'price' => '120$', 'rating' => '4.5', 'reviews' => '89', 'img' => 'https://images.unsplash.com/photo-1586528116311-ad86d7c4420e?auto=format&fit=crop&w=600&q=80'],
            ];
        @endphp

        @foreach($mockServices as $s)
        <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all group">
            <div class="relative h-60">
                <img src="{{ $s['img'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Service">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <button class="absolute top-6 right-6 w-12 h-12 bg-white/90 backdrop-blur-sm rounded-2xl flex items-center justify-center text-slate-400 hover:text-red-500 hover:scale-110 transition-all shadow-xl">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            
            <div class="p-8 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-black text-rdc-blue uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-lg">Vérifié</span>
                    <div class="flex items-center gap-1.5 text-rdc-yellow text-xs font-black">
                        <i class="fas fa-star"></i>
                        <span>{{ $s['rating'] }}</span>
                        <span class="text-slate-300 font-bold ml-1">({{ $s['reviews'] }})</span>
                    </div>
                </div>
                
                <h4 class="text-lg font-heading font-black text-slate-900 leading-tight group-hover:text-rdc-blue transition-colors">{{ $s['name'] }}</h4>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Par <span class="text-slate-600 underline cursor-pointer">{{ $s['author'] }}</span></p>
                
                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <div>
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Prix départ</p>
                        <p class="text-xl font-black text-slate-900">{{ $s['price'] }}</p>
                    </div>
                    <button class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center group-hover:bg-rdc-blue transition-all shadow-lg">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
