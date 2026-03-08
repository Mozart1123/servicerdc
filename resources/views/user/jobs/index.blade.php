@extends('layouts.user')

@section('title', 'Opportunités d\'Emploi')
@section('header_title', 'Jobs & Carrières')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Search Header -->
    <div class="bg-slate-900 rounded-[3.5rem] p-12 text-white overflow-hidden shadow-2xl relative">
        <div class="absolute inset-0 bg-gradient-to-br from-rdc-blue/30 to-transparent"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-rdc-blue/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-8">
            <div class="max-w-xl">
                <h2 class="text-4xl font-heading font-black uppercase leading-tight">Prochaine <span class="text-rdc-blue">Destination</span> Professionnelle</h2>
                <p class="text-slate-400 font-medium mt-4">Explorez les opportunités de carrière dans les plus grandes entreprises de la RDC.</p>
            </div>
            
            <form action="{{ route('user.jobs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 bg-white/5 p-2 rounded-[2.5rem] backdrop-blur-md border border-white/10">
                <div class="flex-1 relative group">
                    <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-white transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Intitulé du poste ou entreprise..." 
                           class="w-full pl-16 pr-6 py-4 bg-transparent border-none focus:ring-0 text-white font-bold placeholder-slate-500">
                </div>
                <div class="md:w-64 relative group border-t md:border-t-0 md:border-l border-white/10">
                    <i class="fas fa-location-dot absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-white transition-colors"></i>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Partout en RDC" 
                           class="w-full pl-16 pr-6 py-4 bg-transparent border-none focus:ring-0 text-white font-bold placeholder-slate-500">
                </div>
                <button type="submit" class="px-10 py-4 bg-rdc-blue text-white font-black rounded-[2rem] text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                    Filtrer
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        <!-- Sidebar Filters -->
        <aside class="space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Type de Contrat</h3>
                <div class="space-y-3">
                    @foreach(['Temps plein', 'Contractuel', 'Freelance', 'Stage'] as $type)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded-lg border-slate-200 text-rdc-blue focus:ring-rdc-blue/20 transition-all">
                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">{{ $type }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-rdc-blue p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-500/10">
                <i class="fas fa-bell text-2xl mb-4"></i>
                <h4 class="font-bold text-lg leading-tight">Alerte Job</h4>
                <p class="text-[10px] opacity-70 uppercase tracking-widest font-black mt-2">Recevez les nouveaux jobs par email</p>
                <button class="w-full mt-6 py-3 bg-white text-slate-900 font-black rounded-xl text-[9px] uppercase tracking-widest">Activer</button>
            </div>
        </aside>

        <!-- Jobs Grid -->
        <div class="lg:col-span-3 space-y-6">
            @php
                $mockJobs = [
                    ['title' => 'Senior Frontend Developer', 'company' => 'Vodacom RDC', 'loc' => 'Gombe, Kinshasa', 'salary' => '2,500$ - 3,500$', 'time' => 'Il y a 2h', 'tag' => 'Urgent', 'color' => 'red'],
                    ['title' => 'Directeur de Succursale', 'company' => 'Rawbank', 'loc' => 'Lubumbashi', 'salary' => '4,000$+', 'time' => 'Il y a 5h', 'tag' => 'Premium', 'color' => 'amber'],
                    ['title' => 'Analyste Cybersécurité', 'company' => 'Orange RDC', 'loc' => 'Kinshasa', 'salary' => '1,800$ - 2,200$', 'time' => 'Aujourd\'hui', 'tag' => 'Tech', 'color' => 'blue'],
                    ['title' => 'Chef de Chantier', 'company' => 'SOGEDIE', 'loc' => 'Kolwezi', 'salary' => 'Confidentiel', 'time' => 'Hier', 'tag' => 'Mining', 'color' => 'emerald'],
                ];
            @endphp

            @foreach($mockJobs as $job)
            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all group flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-{{ $job['color'] }}-500 opacity-0 group-hover:opacity-100 transition-all"></div>
                
                <div class="w-20 h-20 rounded-[2rem] bg-slate-50 flex items-center justify-center text-3xl text-slate-300 group-hover:bg-rdc-blue group-hover:text-white transition-all shadow-inner">
                    <i class="fas fa-building-columns"></i>
                </div>
                
                <div class="flex-1 text-center md:text-left space-y-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-1">
                        <span class="text-[9px] font-black px-3 py-1 bg-{{ $job['color'] }}-50 text-{{ $job['color'] }}-600 rounded-lg uppercase tracking-widest">{{ $job['tag'] }}</span>
                        <span class="text-[10px] font-bold text-slate-400 italic">{{ $job['time'] }}</span>
                    </div>
                    <h3 class="text-xl font-heading font-black text-slate-900 group-hover:text-rdc-blue transition-colors">{{ $job['title'] }}</h3>
                    <div class="flex items-center justify-center md:justify-start gap-4 text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                        <span><i class="fas fa-building mr-1.5 opacity-50"></i>{{ $job['company'] }}</span>
                        <span><i class="fas fa-location-dot mr-1.5 opacity-50"></i>{{ $job['loc'] }}</span>
                        <span class="text-slate-900"><i class="fas fa-wallet mr-1.5 opacity-30"></i>{{ $job['salary'] }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl hover:text-red-500 transition-all">
                        <i class="far fa-heart text-sm"></i>
                    </button>
                    <button class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rdc-blue hover:scale-105 transition-all shadow-xl">
                        Candidater
                    </button>
                </div>
            </div>
            @endforeach

            <!-- Pagination Simulation -->
            <div class="flex justify-center pt-10">
                <div class="flex items-center gap-2">
                    <button class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-50"><i class="fas fa-chevron-left"></i></button>
                    <button class="w-12 h-12 rounded-2xl bg-rdc-blue text-white font-black">1</button>
                    <button class="w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-600 font-bold hover:bg-slate-50 transition-all">2</button>
                    <button class="w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-600 font-bold hover:bg-slate-50 transition-all">3</button>
                    <button class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-50"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
