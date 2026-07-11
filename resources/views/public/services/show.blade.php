@extends('layouts.public')

@section('title', $service->title)
@section('meta_description', Str::limit($service->description, 160))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs font-bold text-slate-400 mb-8 flex items-center gap-2">
        <a href="{{ route('public.services.index') }}" class="hover:text-[#29B6D1] transition-colors">Services</a>
        <i class="fas fa-chevron-right text-[9px]"></i>
        <span class="text-slate-600">{{ Str::limit($service->title, 40) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left / Main --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Cover Image --}}
            <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-[#29B6D1]/10 to-slate-100 h-64 sm:h-80">
                @php
                    $gallery = is_array($service->gallery_images) ? $service->gallery_images : (is_string($service->gallery_images) ? json_decode($service->gallery_images, true) ?? [] : []);
                    $mainImg = $service->service_image ?? ($gallery[0] ?? null);
                @endphp
                @if($mainImg)
                    <img src="{{ Storage::url($mainImg) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-tools text-6xl text-[#29B6D1]/30"></i>
                    </div>
                @endif
            </div>

            {{-- Gallery --}}
            @if(count($gallery) > 1)
            <div class="flex gap-3 overflow-x-auto pb-2">
                @foreach($gallery as $img)
                    <img src="{{ Storage::url($img) }}" alt="" class="w-20 h-20 rounded-xl object-cover flex-shrink-0 border-2 border-slate-100 hover:border-[#29B6D1] transition-colors cursor-pointer">
                @endforeach
            </div>
            @endif

            {{-- Title & Info --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                @if($service->category)
                    <span class="inline-block px-3 py-1 bg-[#29B6D1]/10 text-[#29B6D1] text-[10px] font-black uppercase tracking-widest rounded-lg mb-3">{{ $service->category->name }}</span>
                @endif
                <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $service->title }}</h1>
                <p class="text-sm text-slate-400 font-medium mb-4">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $service->location ?? $service->city ?? 'RDC' }}
                </p>

                @if($service->description)
                <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">
                    {!! nl2br(e($service->description)) !!}
                </div>
                @endif
            </div>
        </div>

        {{-- Right / Sidebar --}}
        <div class="space-y-5">

            {{-- Price & CTA Card --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="text-3xl font-black text-[#29B6D1] mb-1">
                    @if($service->price)
                        ${{ number_format($service->price, 0) }}
                    @else
                        Sur devis
                    @endif
                </div>
                <p class="text-xs text-slate-400 font-medium mb-5">Prix de base</p>

                @auth
                    <button onclick="document.getElementById('requestModal').classList.remove('hidden'); document.getElementById('requestModal').classList.add('flex')"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm">
                        Demander ce service
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center px-5 py-3 bg-[#29B6D1] text-white font-bold rounded-2xl hover:bg-[#1E9CB5] transition-all shadow-md shadow-[#29B6D1]/20 text-sm">
                        Connexion pour commander
                    </a>
                @endauth
            </div>

            {{-- Artisan Card --}}
            @if($service->artisan)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Prestataire</h3>
                <div class="flex items-center gap-3 mb-4">
                    @if($service->artisan->profile_photo)
                        <img src="{{ Storage::url($service->artisan->profile_photo) }}" class="w-14 h-14 rounded-2xl object-cover" alt="">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($service->artisan->name) }}&background=29B6D1&color=fff&size=100" class="w-14 h-14 rounded-2xl object-cover" alt="">
                    @endif
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $service->artisan->name }}</div>
                        <div class="text-xs text-slate-400 font-medium">{{ $service->artisan->profession ?? 'Artisan' }}</div>
                        <div class="text-xs text-slate-400 font-medium"><i class="fas fa-map-marker-alt mr-1"></i>{{ $service->artisan->city ?? 'RDC' }}</div>
                    </div>
                </div>
                <a href="{{ route('public.artisans.show', $service->artisan->id) }}"
                   class="block w-full text-center px-4 py-2 bg-slate-50 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-100 transition-colors">
                    Voir le profil complet
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Services --}}
    @if($related->isNotEmpty())
    <div class="mt-14">
        <h2 class="text-lg font-bold text-slate-900 mb-5">Services similaires</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($related as $rel)
                <a href="{{ route('public.services.show', $rel->id) }}"
                   class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all overflow-hidden group">
                    <div class="h-32 bg-gradient-to-br from-[#29B6D1]/10 to-slate-50 overflow-hidden">
                        @if($rel->service_image)
                            <img src="{{ Storage::url($rel->service_image) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-tools text-2xl text-[#29B6D1]/30"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-slate-900 text-xs line-clamp-2 group-hover:text-[#29B6D1] transition-colors">{{ $rel->title }}</h4>
                        <p class="text-[#29B6D1] font-black text-sm mt-1">
                            @if($rel->price) ${{ number_format($rel->price, 0) }} @else Sur devis @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- MODAL REQUEST --}}
@auth
<div id="requestModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-md p-4">
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-10 py-8 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-black text-slate-900 font-heading">Passer commande</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Étape 1: Détails de votre besoin</p>
            </div>
            <button onclick="document.getElementById('requestModal').classList.add('hidden'); document.getElementById('requestModal').classList.remove('flex')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-slate-400 hover:text-red-500 transition shadow-sm">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <form action="{{ route('user.service-requests.store') }}" method="POST" class="p-10 space-y-6">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}">
            <input type="hidden" name="requested_service_name" value="{{ $service->title }}">

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Ville / Lieu</label>
                    <input type="text" name="city" required value="{{ $service->location ?? '' }}"
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-[#29B6D1] transition-all">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Degré d'urgence</label>
                    <select name="urgency" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-[#29B6D1]">
                        <option value="standard">Normal (sous quelques jours)</option>
                        <option value="urgent">Urgent (sous 24-48h)</option>
                        <option value="immediate">Immédiat (Dépannage SOS)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Votre Budget (Estimation)</label>
                <div class="relative">
                    <i class="fas fa-dollar-sign absolute left-5 top-1/2 -translate-y-1/2 text-[#29B6D1]"></i>
                    <input type="text" name="budget_range" placeholder="Ex: 50 - 100 $"
                           class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-[#29B6D1] transition-all">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Description précise du besoin</label>
                <textarea name="description" rows="4" required placeholder="Décrivez votre problème ou ce que vous attendez précisément..."
                          class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-[#29B6D1] transition-all resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-5 bg-[#29B6D1] text-white rounded-3xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl shadow-[#29B6D1]/30 hover:scale-[1.02] transition-all">
                Envoyer la demande
            </button>
        </form>
    </div>
</div>
@endauth

<style>
    .animate-fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
