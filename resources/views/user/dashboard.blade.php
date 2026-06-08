@extends('layouts.user')

@section('header_title', 'Tableau de bord')

@section('content')
<div class="space-y-8 pb-20">

  <!-- WELCOME -->
  <section class="bg-gradient-to-r from-rdc-blue to-rdc-dark-blue rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-down">
    <div class="max-w-7xl mx-auto px-6 sm:px-10 py-12 text-white">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Bienvenue, {{ auth()->user()->name ?? 'Client' }} 👋</h2>
      <p class="text-blue-100 max-w-2xl text-lg mb-8">
        Gérez vos demandes de services, suivez vos candidatures et trouvez rapidement les meilleurs artisans près de vous.
      </p>

      <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('user.service-requests.index') }}" class="px-6 py-3.5 bg-rdc-yellow text-gray-900 font-bold rounded-xl hover:bg-yellow-400 transition inline-flex items-center justify-center shadow-lg shadow-yellow-500/20">
          <i class="fas fa-plus-circle mr-2"></i>Faire une demande
        </a>
        <a href="{{ route('user.services.index') }}" class="px-6 py-3.5 bg-white/10 border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition inline-flex items-center justify-center backdrop-blur-sm">
          <i class="fas fa-search mr-2"></i>Trouver un service
        </a>
      </div>
    </div>
  </section>

  <!-- CONTENT -->
  <div class="max-w-7xl mx-auto">
      
      <!-- STATS -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="100">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-gray-500 font-medium">Demandes envoyées</p>
              <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ auth()->check() ? auth()->user()->serviceRequests()->count() : 0 }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-cyan-50 text-rdc-blue border border-cyan-100 flex items-center justify-center">
              <i class="fas fa-paper-plane text-xl"></i>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="200">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-gray-500 font-medium">Services actifs</p>
              <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['active_missions'] ?? 0 }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-yellow-50 text-rdc-yellow border border-yellow-100 flex items-center justify-center">
              <i class="fas fa-tools text-xl"></i>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="300">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-gray-500 font-medium">Candidatures</p>
              <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['applied_jobs_count'] ?? 0 }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
              <i class="fas fa-briefcase text-xl"></i>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="400">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-gray-500 font-medium">Messages non lus</p>
              <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['unread_notifications'] ?? 0 }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-red-50 text-rdc-red border border-red-100 flex items-center justify-center">
              <i class="fas fa-envelope text-xl"></i>
            </div>
          </div>
        </div>
      </section>

      <!-- GRID -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- LEFT -->
    <div class="lg:col-span-2 space-y-8">

      <!-- QUICK ACTIONS -->
      <div class="bg-white rounded-2xl shadow-sm border p-6" data-aos="fade-up">
        <h2 class="text-xl font-bold mb-6">Actions rapides</h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <a href="{{ route('user.services.index') }}" class="p-5 rounded-xl bg-cyan-50 hover:bg-cyan-100 transition text-center block">
            <i class="fas fa-search text-2xl text-rdc-blue mb-3"></i>
            <h3 class="font-semibold text-slate-800">Chercher artisan</h3>
          </a>

          <a href="{{ route('user.service-requests.index') }}" class="p-5 rounded-xl bg-yellow-50 hover:bg-yellow-100 transition text-center block">
            <i class="fas fa-file-circle-plus text-2xl text-rdc-yellow mb-3"></i>
            <h3 class="font-semibold text-slate-800">Nouvelle demande</h3>
          </a>

          <a href="{{ route('user.jobs.index') }}" class="p-5 rounded-xl bg-blue-50 hover:bg-blue-100 transition text-center block">
            <i class="fas fa-briefcase text-2xl text-blue-600 mb-3"></i>
            <h3 class="font-semibold text-slate-800">Voir emplois</h3>
          </a>
        </div>
      </div>

      <!-- RECENT REQUESTS -->
      <div class="bg-white rounded-2xl shadow-sm border p-6" data-aos="fade-up">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Mes demandes récentes</h2>
          <a href="{{ route('user.service-requests.index') }}" class="text-rdc-blue font-semibold text-sm hover:underline">Voir tout</a>
        </div>

        <div class="space-y-4">
          @if(auth()->check() && auth()->user()->serviceRequests()->count() > 0)
              @foreach(auth()->user()->serviceRequests()->latest()->take(3)->get() as $request)
              <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                <div class="flex items-center gap-4">
                  <div class="w-11 h-11 bg-cyan-100 text-rdc-blue rounded-xl flex items-center justify-center">
                    <i class="fas fa-bolt"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold">{{ $request->title }}</h3>
                    <p class="text-sm text-gray-500">{{ $request->location ?? 'En ligne' }} • {{ $request->created_at->diffForHumans() }}</p>
                  </div>
                </div>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">{{ ucfirst($request->status ?? 'En attente') }}</span>
              </div>
              @endforeach
          @else
              <div class="text-center py-6">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-xl mx-auto mb-3">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-gray-500 font-medium">Vous n'avez effectué aucune demande pour le moment.</p>
              </div>
          @endif
        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <aside class="space-y-8" data-aos="fade-left">

      <!-- PROFILE -->
      <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="text-center">
          <div class="w-20 h-20 mx-auto rounded-full shadow-sm border-2 border-white mb-4 relative">
             <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name ?? 'Client').'&background=29B6D1&color=fff&size=80' }}" class="w-full h-full rounded-full object-cover" alt="Profile">
          </div>
          <h2 class="text-lg font-bold">{{ auth()->user()->name ?? 'Client ProConnect' }}</h2>
          <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'client@email.com' }}</p>

          <a href="{{ route('user.profile') }}" class="mt-5 w-full block py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition text-center">
            Modifier profil
          </a>
        </div>
      </div>

      <!-- RECOMMENDED -->
      <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h2 class="text-xl font-bold mb-5">Services recommandés</h2>

        <div class="space-y-4">
          @if(isset($recentServices) && $recentServices->count() > 0)
              @foreach($recentServices->take(3) as $service)
              <div class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-lg transition">
                <div class="w-10 h-10 rounded-lg bg-cyan-100 text-rdc-blue flex items-center justify-center">
                  <i class="fas {{ $service->category->icon ?? 'fa-bolt' }}"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-semibold text-sm line-clamp-1"><a href="{{ route('user.services.show', $service->id) }}">{{ $service->title }}</a></h3>
                  <p class="text-xs text-gray-500">{{ Str::limit($service->location ?? 'Disponibles près de vous', 25) }}</p>
                </div>
              </div>
              @endforeach
          @else
              <p class="text-gray-500 text-sm text-center">Aucun service recommandé pour l'instant.</p>
          @endif
        </div>
      </div>

      <!-- TIP -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5" data-aos="zoom-in">
        <h3 class="font-bold text-yellow-800 mb-2">
          <i class="fas fa-lightbulb mr-2"></i>Conseil
        </h3>
        <p class="text-sm text-yellow-700">
          Complétez votre profil pour recevoir de meilleures recommandations.
        </p>
      </div>

    </aside>
  </section>

  </div> <!-- Close max-w-7xl container -->
</div> <!-- Close space-y-8 container -->
@endsection
