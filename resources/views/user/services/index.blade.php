@extends('layouts.user')

@section('title', 'Services | ProConnect')
@section('header_title', 'Services disponibles')

@section('content')
<div class="space-y-8 pb-20">

  <!-- HERO -->
  <section class="bg-gradient-to-br from-rdc-blue to-rdc-dark-blue text-white rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-down">
    <div class="max-w-7xl mx-auto px-6 sm:px-10 py-14">
      <div class="max-w-3xl">
        <span class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-5 font-medium backdrop-blur-sm border border-white/20">
          <i class="fas fa-tools mr-2 text-rdc-yellow"></i>Services ProConnect
        </span>

        <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
          Trouvez rapidement un artisan qualifié près de vous
        </h2>

        <p class="text-blue-100 text-lg mb-8 max-w-2xl">
          Recherchez un service, comparez les artisans disponibles et envoyez votre demande en quelques clics.
        </p>
      </div>

      <!-- SEARCH -->
      <form action="{{ route('user.services.index') }}" method="GET" class="bg-white rounded-2xl p-3 shadow-2xl max-w-5xl">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div class="md:col-span-2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un service..."
                   class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-gray-800 outline-none focus:ring-2 focus:ring-rdc-blue transition-all">
          </div>

          <select name="location" class="px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none focus:ring-2 focus:ring-rdc-blue transition-all hidden sm:block">
            <option value="">Toutes les Villes</option>
            <option value="Kinshasa" {{ request('location') == 'Kinshasa' ? 'selected' : '' }}>Kinshasa</option>
            <option value="Lubumbashi" {{ request('location') == 'Lubumbashi' ? 'selected' : '' }}>Lubumbashi</option>
            <option value="Goma" {{ request('location') == 'Goma' ? 'selected' : '' }}>Goma</option>
            <option value="Kisangani" {{ request('location') == 'Kisangani' ? 'selected' : '' }}>Kisangani</option>
          </select>

          <button type="submit" class="py-4 bg-rdc-yellow text-gray-900 font-bold rounded-xl hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">
            Rechercher
          </button>
        </div>
      </form>
    </div>
  </section>

  <!-- CONTENT -->
  <div class="max-w-7xl mx-auto">

    <!-- FILTERS -->
    <div class="flex flex-wrap gap-3 mb-8" data-aos="fade-in" data-aos-delay="300">
      <a href="{{ route('user.services.index') }}" class="px-5 py-2 rounded-full {{ !request('category') ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">
        Tous
      </a>
      @if(isset($categories) && count($categories) > 0)
          @foreach((is_array($categories) ? array_slice($categories, 0, 6) : $categories->take(6)) as $category)
          <a href="{{ route('user.services.index', ['category' => $category->id]) }}" class="px-5 py-2 rounded-full {{ request('category') == $category->id ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">
            {{ $category->name }}
          </a>
          @endforeach
      @else
          <!-- Fillers si pas de données -->
          <a href="#" class="px-5 py-2 rounded-full bg-white border hover:bg-cyan-50">Maison</a>
          <a href="#" class="px-5 py-2 rounded-full bg-white border hover:bg-cyan-50">Mode</a>
          <a href="#" class="px-5 py-2 rounded-full bg-white border hover:bg-cyan-50">Auto</a>
          <a href="#" class="px-5 py-2 rounded-full bg-white border hover:bg-cyan-50">Informatique</a>
          <a href="#" class="px-5 py-2 rounded-full bg-white border hover:bg-cyan-50">Construction</a>
      @endif
    </div>

    <!-- SERVICES GRID -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      @if(isset($services) && $services->count() > 0)
          @foreach($services as $service)
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col" data-aos="fade-up" data-aos-delay="{{ 100 * ($loop->index % 6) }}">
            <div class="flex justify-between items-start mb-5">
              @php
                $thumbnail = $service->service_image ? Storage::url($service->service_image) : null;
                if(!$thumbnail) {
                    $gallery = $service->gallery_images ?? $service->images ?? [];
                    if(is_array($gallery) && count($gallery) > 0) {
                        $thumbnail = Storage::url($gallery[0]);
                    }
                }
              @endphp
              
              @if($thumbnail)
                <div class="w-14 h-14 rounded-xl overflow-hidden shadow-md border-2 border-slate-50">
                    <img src="{{ $thumbnail }}" class="w-full h-full object-cover">
                </div>
              @else
                <div class="w-14 h-14 rounded-xl bg-cyan-100 text-rdc-blue flex items-center justify-center">
                    <i class="fas {{ $service->category->icon ?? 'fa-tools' }} text-2xl"></i>
                </div>
              @endif
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">{{ $service->price }}$ / req</span>
            </div>

            <h3 class="text-xl font-bold mb-1 text-slate-800">{{ $service->title }}</h3>
            <p class="text-[11px] font-bold text-rdc-blue uppercase tracking-wider mb-3">Par {{ $service->artisan->name ?? 'Artisan Pro' }}</p>

            <p class="text-gray-500 text-sm mb-5 flex-1 break-all overflow-hidden text-ellipsis line-clamp-3">
              {{ Str::limit($service->description, 90) }}
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span class="flex items-center text-xs font-medium"><i class="fas fa-map-marker-alt text-rdc-red mr-1.5 p-1.5 bg-red-50 rounded-md"></i>{{ $service->location ?? 'Kinshasa' }}</span>
              <span class="flex items-center font-bold text-slate-700"><i class="fas fa-star text-rdc-yellow mr-1"></i>{{ number_format($service->rating ?? 4.8, 1) }}</span>
            </div>

            <a href="{{ route('user.services.show', $service->id) }}" class="w-full text-center block py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-500/20">
              Voir le service
            </a>
          </div>
          @endforeach

          <div class="col-span-full mt-8">
              {{ $services->links() }}
          </div>
      @else
          <!-- EXEMPLES STATIQUES (Fallback Demo) -->
          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col" data-aos="fade-up" data-aos-delay="100">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-cyan-100 text-rdc-blue flex items-center justify-center">
                <i class="fas fa-bolt text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">15 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Électriciens</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Installation électrique, dépannage, maintenance et réparation urgente.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Kinshasa</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.8</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col" data-aos="fade-up" data-aos-delay="200">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-yellow-100 text-rdc-yellow flex items-center justify-center">
                <i class="fas fa-tools text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">9 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Plombiers</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Réparation de fuites, installation sanitaire, débouchage et entretien.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Lubumbashi</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.7</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col" data-aos="fade-up" data-aos-delay="300">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-red-100 text-rdc-red flex items-center justify-center">
                <i class="fas fa-cut text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">22 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Couturiers</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Tenues sur mesure, retouches, uniformes, robes, costumes et réparations.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Goma</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.9</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>
          
          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                <i class="fas fa-hammer text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">12 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Maçons</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Construction, rénovation, carrelage, finition et petits travaux bâtiment.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Kisangani</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.6</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="fas fa-laptop-code text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">18 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Informaticiens</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Maintenance ordinateur, installation logiciel, réseau, support technique.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Kinshasa</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.8</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition flex flex-col">
            <div class="flex justify-between items-start mb-5">
              <div class="w-14 h-14 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-car text-2xl"></i>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">11 disponibles</span>
            </div>

            <h3 class="text-xl font-bold mb-2">Mécaniciens</h3>
            <p class="text-gray-500 text-sm mb-5 flex-1">
              Diagnostic auto, réparation moteur, entretien, vidange et assistance.
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-5">
              <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Bukavu</span>
              <span><i class="fas fa-star text-rdc-yellow mr-1"></i>4.7</span>
            </div>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Voir les artisans
            </button>
          </div>
      @endif

    </section>
  </div>
</div>
@endsection
