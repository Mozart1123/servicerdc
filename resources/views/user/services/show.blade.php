@extends('layouts.user')

@section('title', $service->title . ' | ProConnect')
@section('header_title', 'Détails du Service')

@section('content')
<div class="space-y-10 pb-20">
    
    <!-- Top Nav -->
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rdc-blue transition-colors">
        <i class="fas fa-arrow-left"></i> Retour
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Gallery / Hero --}}
            <div class="bg-white rounded-[3rem] p-4 border border-slate-100 shadow-sm">
                @php
                    $mainImg = $service->service_image ? Storage::url($service->service_image) : null;
                    
                    // Fallback to gallery_images or legacy images column
                    if(!$mainImg) {
                        $gallery = $service->gallery_images ?? $service->images ?? [];
                        if (is_array($gallery) && count($gallery) > 0) {
                            $mainImg = Storage::url($gallery[0]);
                        }
                    }
                @endphp

                @if($mainImg)
                    <div class="h-[450px] rounded-[2.5rem] overflow-hidden relative group">
                        <img id="main-image" src="{{ $mainImg }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        
                        @if(!$service->is_verified)
                            <div class="absolute top-6 left-6 px-4 py-2 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-xl">
                                En attente de vérification
                            </div>
                        @else
                            <div class="absolute top-6 left-6 px-4 py-2 bg-rdc-blue text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-xl">
                                <i class="fas fa-check-circle mr-1"></i> Vérifié
                            </div>
                        @endif
                    </div>
                                     @php $fullGallery = array_unique(array_filter(array_merge(
                        $service->service_image ? [$service->service_image] : [],
                        is_array($service->gallery_images) ? $service->gallery_images : [],
                        is_array($service->images) ? $service->images : []
                    ))); @endphp

                    @if(count($fullGallery) > 1)
                    <div class="flex gap-4 mt-4 overflow-x-auto pb-2 custom-scrollbar">
                        @foreach($fullGallery as $img)
                            <button onclick="document.getElementById('main-image').src='{{ Storage::url($img) }}'" class="w-20 h-20 flex-none rounded-2xl overflow-hidden border-2 border-transparent focus:border-rdc-blue transition-all">
                                <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover shadow-sm">
                            </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div class="h-[400px] rounded-[2.5rem] bg-slate-50 flex flex-col items-center justify-center text-slate-300 border-2 border-dashed border-slate-200">
                        <i class="fas fa-image text-8xl mb-6 opacity-20"></i>
                        <span class="text-xs font-black uppercase tracking-[0.2em] opacity-40">Aucun visuel disponible</span>
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden" data-aos="fade-up">
                <div class="absolute top-0 right-0 w-48 h-48 bg-slate-50/50 rounded-bl-[150px] -z-0"></div>
                
                <div class="relative z-10 space-y-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="px-3 py-1 bg-rdc-blue/10 text-rdc-blue text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $service->category->name ?? 'Service' }}</span>
                            <h1 class="text-4xl font-heading font-black text-slate-900 mt-4 leading-tight">{{ $service->title }}</h1>
                            <div class="flex flex-wrap items-center gap-6 mt-6">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center">
                                    <i class="fas fa-location-dot mr-2 text-rdc-blue opacity-70"></i> {{ $service->location }}
                                </span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center">
                                    <i class="fas fa-calendar mr-2 text-rdc-blue opacity-70"></i> Mis à jour le {{ $service->updated_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                           <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Prix à partir de</div>
                           <div class="text-4xl font-black text-rdc-blue">{{ number_format($service->price, 0) }}<span class="text-lg ml-1">$</span></div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-8 mt-8">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-1 bg-rdc-blue rounded-full"></span>
                            Présentation du service
                        </h3>
                        <p class="text-slate-600 leading-relaxed text-lg whitespace-pre-line">{{ $service->description }}</p>
                    </div>

                    @if(count($fullGallery) > 0)
                    <div class="pt-8 border-t border-slate-100">
                         <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Galerie Photos</h3>
                         <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                             @foreach($fullGallery as $img)
                             <a href="{{ Storage::url($img) }}" target="_blank" class="h-32 rounded-2xl overflow-hidden hover:scale-105 transition duration-500 shadow-md">
                                 <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                             </a>
                             @endforeach
                         </div>
                    </div>
                    @endif
                </div>
            </div>
            
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            
            <!-- Action Card -->
            <div class="bg-white rounded-[3.5rem] p-8 border border-slate-100 shadow-xl relative overflow-hidden" data-aos="fade-left">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-rdc-blue/5 rounded-full blur-3xl"></div>
                
                <h3 class="font-heading font-black text-slate-900 text-xl mb-6 text-center">Intéressé par ce service ?</h3>

                @if(Auth::id() === $service->artisan_id)
                    <div class="p-6 bg-slate-50 rounded-3xl text-center border border-slate-100 mb-6">
                        <i class="fas fa-crown text-3xl text-amber-400 mb-3"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase">Ceci est votre service</p>
                    </div>
                    <a href="{{ route('user.services.edit', $service->id) }}" class="flex items-center justify-center gap-3 w-full py-5 bg-slate-900 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl">
                        <i class="fas fa-pen"></i> Modifier le service
                    </a>
                @else
                    <button onclick="document.getElementById('requestModal').classList.remove('hidden'); document.getElementById('requestModal').classList.add('flex')" 
                            class="flex items-center justify-center gap-3 w-full py-5 bg-rdc-blue text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-rdc-blue-dark transition-all transform hover:scale-[1.02] shadow-2xl shadow-rdc-blue/30 mb-4">
                        <i class="fas fa-paper-plane"></i> Demander une prestation
                    </button>

                    <a href="{{ route('user.messages.start.user', $service->artisan_id) }}" 
                       class="flex items-center justify-center gap-3 w-full py-5 bg-slate-50 text-slate-600 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all border border-slate-100">
                        <i class="fas fa-comments"></i> Discuter avec l'artisan
                    </a>
                @endif

                <div class="mt-8 pt-8 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Réponse moyenne</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">< 2 heures</p>
                    </div>
                    <div class="w-px h-8 bg-slate-100"></div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Réalisations</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">12 missions</p>
                    </div>
                </div>
            </div>

            <!-- Artisan Card -->
            <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white text-center shadow-2xl" data-aos="fade-up">
                <div class="shrink-0 relative inline-block mx-auto">
                    <img src="{{ $service->artisan->photo_url ?? 'https://ui-avatars.com/api/?name=Artisan' }}" 
                         class="w-24 h-24 rounded-[2rem] object-cover border-4 border-white/10 shadow-2xl" alt="Artisan">
                    @if($service->artisan->is_verified ?? false)
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-rdc-blue text-white rounded-full flex items-center justify-center border-4 border-slate-900 shadow-xl">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    @endif
                </div>
                
                <h3 class="font-heading font-black text-xl mt-6 tracking-tight">{{ $service->artisan->name ?? 'Artisan ProConnect' }}</h3>
                <p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-[0.2em]">{{ $service->profession ?? 'Professionnel vérifié' }}</p>
                
                <div class="flex justify-center gap-2 text-rdc-yellow my-6">
                    @for($i=0; $i<5; $i++)
                    <i class="fas fa-star text-sm"></i>
                    @endfor
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/5">
                        <i class="fas fa-passport text-rdc-blue"></i>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Identité confirmée</span>
                    </div>
                    <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/5">
                        <i class="fas fa-phone text-rdc-blue"></i>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ $service->phone_number ?? 'Contact validé' }}</span>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-8 leading-relaxed font-medium">
                    En faisant appel à cet artisan via ProConnect, vous bénéficiez de notre garantie de satisfaction.
                </p>
            </div>

        </div>
    </div>
</div>

{{-- MODAL REQUEST --}}
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
                    <input type="text" name="city" required value="{{ $service->location }}"
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue transition-all">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Degré d'urgence</label>
                    <select name="urgency" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue">
                        <option value="standard">Normal (sous quelques jours)</option>
                        <option value="urgent">Urgent (sous 24-48h)</option>
                        <option value="immediate">Immédiat (Dépannage SOS)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Votre Budget (Estimation)</label>
                <div class="relative">
                    <i class="fas fa-dollar-sign absolute left-5 top-1/2 -translate-y-1/2 text-rdc-blue"></i>
                    <input type="text" name="budget_range" placeholder="Ex: 50 - 100 $"
                           class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-rdc-blue transition-all">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Description précise du besoin</label>
                <textarea name="description" rows="4" required placeholder="Décrivez votre problème ou ce que vous attendez précisément..."
                          class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-rdc-blue transition-all resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-5 bg-rdc-blue text-white rounded-3xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl shadow-rdc-blue/30 hover:scale-[1.02] transition-all">
                Envoyer la demande
            </button>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
    .animate-fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection
