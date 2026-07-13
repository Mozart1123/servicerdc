@extends($layout)

@section('title', 'Emplois | ProConnect')
@section('header_title', 'Opportunités d\'emploi')

@section($contentSection)
<div class="space-y-8 pb-20">

  <!-- HERO -->
  <section class="bg-gradient-to-br from-rdc-blue to-rdc-dark-blue text-white rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-down">
    <div class="max-w-7xl mx-auto px-6 sm:px-10 py-14">
      <div class="max-w-3xl">
        <span class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-5 font-medium backdrop-blur-sm border border-white/20">
          <i class="fas fa-briefcase mr-2 text-rdc-yellow"></i>Emplois ProConnect
        </span>

        <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
          Trouvez votre prochain emploi en RDC
        </h2>

        <p class="text-blue-100 text-lg mb-8 max-w-2xl">
          Recherchez des offres, postulez facilement et suivez vos candidatures depuis votre espace client.
        </p>
      </div>

      <!-- SEARCH -->
      <form action="{{ route('user.jobs.index') }}" method="GET" class="bg-white rounded-2xl p-3 shadow-2xl max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
          <div class="md:col-span-2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre du poste, entreprise..."
              class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-gray-800 outline-none focus:ring-2 focus:ring-rdc-blue transition-all">
          </div>

          <select name="location" class="px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none focus:ring-2 focus:ring-rdc-blue transition-all hidden sm:block">
            <option value="">Ville</option>
            <option value="Kinshasa" {{ request('location') == 'Kinshasa' ? 'selected' : '' }}>Kinshasa</option>
            <option value="Lubumbashi" {{ request('location') == 'Lubumbashi' ? 'selected' : '' }}>Lubumbashi</option>
            <option value="Goma" {{ request('location') == 'Goma' ? 'selected' : '' }}>Goma</option>
            <option value="Kampala" {{ request('location') == 'Kampala' ? 'selected' : '' }}>Kampala</option>
            <option value="Remote" {{ request('location') == 'Remote' ? 'selected' : '' }}>Remote</option>
          </select>

          <select name="contract_type" class="px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none focus:ring-2 focus:ring-rdc-blue transition-all hidden sm:block">
            <option value="">Type</option>
            @foreach($contractTypes as $type)
            <option value="{{ $type }}" {{ request('contract_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
            <option value="Temps plein" {{ request('contract_type') == 'Temps plein' ? 'selected' : '' }}>Temps plein</option>
            <option value="Temps partiel" {{ request('contract_type') == 'Temps partiel' ? 'selected' : '' }}>Temps partiel</option>
          </select>

          <button type="submit" class="py-4 bg-rdc-yellow text-gray-900 font-bold rounded-xl hover:bg-yellow-400 transition-colors shadow-lg shadow-yellow-500/20">
            Rechercher
          </button>
        </div>
      </form>
    </div>
  </section>

  <!-- CONTENT -->
  <div class="max-w-7xl mx-auto">

    <!-- STATS -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <p class="text-sm text-gray-500 font-medium">Offres actives</p>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ \App\Models\JobOffer::active()->count() ?? '1,200+' }}</h3>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <p class="text-sm text-gray-500 font-medium">Candidatures envoyées</p>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ count($userApplicationIds ?? []) }}</h3>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <p class="text-sm text-gray-500 font-medium">Total Candidatures Platforme</p>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ \App\Models\JobApplication::count() ?? '8,500+' }}</h3>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <p class="text-sm text-gray-500 font-medium">Entreprises (Employeurs)</p>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ \App\Models\User::where('user_type', 'business')->count() ?? '350+' }}</h3>
      </div>
    </section>

    <!-- FILTERS -->
    <div class="flex flex-wrap gap-3 mb-8" data-aos="fade-in" data-aos-delay="200">
      <a href="{{ route('user.jobs.index') }}" class="px-5 py-2 rounded-full {{ !request('contract_type') && !request('location') ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">Tous</a>
      @foreach($contractTypes as $type)
      <a href="{{ route('user.jobs.index', ['contract_type' => $type]) }}" class="px-5 py-2 rounded-full {{ request('contract_type') == $type ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">{{ $type }}</a>
      @endforeach
      <a href="{{ route('user.jobs.index', ['contract_type' => 'Temps plein']) }}" class="px-5 py-2 rounded-full {{ request('contract_type') == 'Temps plein' ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">Temps plein</a>
      <a href="{{ route('user.jobs.index', ['location' => 'Remote']) }}" class="px-5 py-2 rounded-full {{ request('location') == 'Remote' ? 'bg-rdc-blue text-white font-semibold shadow-md' : 'bg-white border text-gray-600 hover:bg-cyan-50 transition-colors' }}">Télétravail</a>
    </div>

    <!-- GRID -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- JOB LIST -->
      <div class="lg:col-span-2 space-y-6">

        @if(isset($jobs) && $jobs->count() > 0)
            @foreach($jobs as $job)
            @php
                $hasApplied = in_array($job->id, $userApplicationIds ?? []);
            @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ 100 * ($loop->index % 4) }}">
              <div class="flex flex-col md:flex-row md:justify-between gap-4">
                <div class="flex gap-4">
                  <div class="w-14 h-14 rounded-xl {{ $loop->index % 2 == 0 ? 'bg-cyan-100 text-rdc-blue' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center shrink-0">
                    <i class="fas {{ $loop->index % 3 == 0 ? 'fa-code' : ($loop->index % 3 == 1 ? 'fa-laptop-code' : 'fa-screwdriver-wrench') }} text-2xl"></i>
                  </div>

                  <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $job->title }}</h3>
                    <p class="text-gray-500 mt-1 font-medium">{{ $job->user->name ?? 'Entreprise' }}</p>

                    <div class="flex flex-wrap gap-3 mt-3 text-[13px] text-gray-500 font-medium">
                      <span class="flex items-center"><i class="fas fa-map-marker-alt text-rdc-red mr-1.5 p-1.5 bg-red-50 rounded-md border border-red-100"></i>{{ $job->location ?? 'Non spécifié' }}</span>
                      <span class="flex items-center"><i class="fas fa-dollar-sign text-green-600 mr-1.5 p-1.5 bg-green-50 rounded-md border border-green-100"></i>{{ $job->salary ?? 'À négocier' }}</span>
                      <span class="flex items-center"><i class="fas fa-clock text-rdc-blue mr-1.5 p-1.5 bg-blue-50 rounded-md border border-blue-100"></i>{{ $job->contract_type ?? 'Temps plein' }}</span>
                    </div>
                  </div>
                </div>

                @if($job->is_urgent || $loop->index == 0)
                <span class="h-fit px-3 py-1 bg-red-100 text-rdc-red rounded-full text-[11px] font-bold tracking-widest shrink-0 border border-red-200">
                  URGENT
                </span>
                @elseif($hasApplied)
                <span class="h-fit px-3 py-1 bg-green-100 text-green-700 rounded-full text-[11px] font-bold tracking-widest shrink-0 border border-green-200">
                  POSTULÉ
                </span>
                @endif
              </div>

              <p class="text-gray-600 text-sm mt-5 leading-relaxed">
                {{ Str::limit($job->description ?? 'Rejoignez notre équipe et participez à des projets innovants et stimulants.', 150) }}
              </p>

              @if($job->requirements)
              <div class="flex flex-wrap gap-2 mt-5 bg-slate-50 p-3 rounded-xl border border-slate-100">
                 @foreach(array_slice(explode(',', $job->requirements), 0, 4) as $req)
                 <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px] font-semibold text-slate-700 shadow-sm">{{ trim($req) }}</span>
                 @endforeach
              </div>
              @endif

              @if($hasApplied)
              <a href="{{ route('user.applications.index') }}" class="mt-5 block w-full py-4 bg-green-50 text-green-700 text-center rounded-xl font-bold border border-green-200 hover:bg-green-100 transition shadow-sm">
                <i class="fas fa-check-circle mr-2"></i>Candidature envoyée
              </a>
              @else
              <a href="{{ route('user.jobs.apply.form', $job->id) }}" class="mt-5 block w-full py-4 bg-rdc-blue text-white text-center rounded-xl font-bold hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-500/20">
                Postuler maintenant
              </a>
              @endif
            </div>
            @endforeach

            <div class="col-span-full pt-4">
                {{ $jobs->links() }}
            </div>
        @else
            <!-- DEMO FALLBACK -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition" data-aos="fade-up">
              <div class="flex flex-col md:flex-row md:justify-between gap-4">
                <div class="flex gap-4">
                  <div class="w-14 h-14 rounded-xl bg-cyan-100 text-rdc-blue flex items-center justify-center">
                    <i class="fas fa-code text-2xl"></i>
                  </div>

                  <div>
                    <h3 class="text-xl font-bold text-gray-900">Développeur Laravel Senior</h3>
                    <p class="text-gray-500 mt-1">NovaCore Technology</p>

                    <div class="flex flex-wrap gap-3 mt-3 text-sm text-gray-500">
                      <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Kampala</span>
                      <span><i class="fas fa-dollar-sign text-green-600 mr-1"></i>800$ - 1500$</span>
                      <span><i class="fas fa-clock text-rdc-blue mr-1"></i>Temps plein</span>
                    </div>
                  </div>
                </div>

                <span class="h-fit px-3 py-1 bg-red-100 text-rdc-red rounded-full text-xs font-bold">
                  URGENT
                </span>
              </div>

              <p class="text-gray-600 text-sm mt-5">
                Nous recherchons un développeur Laravel capable de créer des plateformes web modernes, sécurisées et performantes.
              </p>

              <div class="flex flex-wrap gap-2 mt-4">
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">Laravel</span>
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">PHP</span>
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">MySQL</span>
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">Tailwind</span>
              </div>

              <button class="mt-5 w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
                Postuler maintenant
              </button>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border hover:shadow-xl hover:-translate-y-1 transition" data-aos="fade-up" data-aos-delay="100">
              <div class="flex gap-4">
                <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                  <i class="fas fa-laptop-code text-2xl"></i>
                </div>

                <div>
                  <h3 class="text-xl font-bold text-gray-900">Développeur Frontend React</h3>
                  <p class="text-gray-500 mt-1">Digital Africa Solutions</p>

                  <div class="flex flex-wrap gap-3 mt-3 text-sm text-gray-500">
                    <span><i class="fas fa-map-marker-alt text-rdc-red mr-1"></i>Remote</span>
                    <span><i class="fas fa-dollar-sign text-green-600 mr-1"></i>700$ - 1300$</span>
                    <span><i class="fas fa-clock text-rdc-blue mr-1"></i>Télétravail</span>
                  </div>
                </div>
              </div>

              <p class="text-gray-600 text-sm mt-5">
                Création d’interfaces modernes, responsives et interactives avec React, Tailwind CSS et API REST.
              </p>

              <div class="flex flex-wrap gap-2 mt-4">
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">React</span>
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">JavaScript</span>
                <span class="px-3 py-1 bg-gray-100 rounded-full text-xs">Tailwind</span>
              </div>

              <button class="mt-5 w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
                Postuler maintenant
              </button>
            </div>
        @endif

      </div>

      <!-- SIDEBAR -->
      <aside class="space-y-6" data-aos="fade-left">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
          <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-rdc-blue to-rdc-dark-blue text-white flex items-center justify-center mb-4 relative shadow-lg shadow-blue-500/20">
               @if(auth()->check() && auth()->user()->profile_photo_url)
                 <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full rounded-full object-cover">
               @else
                 <i class="fas fa-user-graduate text-3xl"></i>
               @endif
            </div>
            <h3 class="font-bold text-lg text-slate-800">{{ auth()->user()->name ?? 'Profil candidat' }}</h3>
            <p class="text-sm text-gray-500 font-medium">Voir mon profil public</p>

            <a href="{{ route('user.profile') }}" class="block mt-5 w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition text-center shadow-md">
              Gérer mon profil
            </a>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
          <h3 class="font-bold text-lg mb-4 text-slate-800">Emplois recommandés</h3>

          <div class="space-y-4 text-sm font-medium">
             @if(isset($jobs) && $jobs->count() > 0)
                @foreach($jobs->take(4) as $rec_job)
                <a href="{{ route('user.jobs.show', $rec_job->id) }}" class="flex items-start gap-4 group">
                  <div class="mt-0.5 w-8 h-8 rounded-full bg-cyan-50 flex items-center justify-center text-rdc-blue group-hover:bg-rdc-blue group-hover:text-white transition-colors border border-cyan-100 shrink-0">
                    <i class="fas fa-briefcase text-[10px]"></i>
                  </div>
                  <div>
                    <h4 class="text-slate-800 group-hover:text-rdc-blue transition-colors line-clamp-1 font-bold">{{ $rec_job->title }}</h4>
                    <p class="text-[11px] text-slate-400 mt-1 uppercase tracking-wider">{{ Str::limit($rec_job->location ?? 'Localisation flexible', 20) }}</p>
                  </div>
                </a>
                @endforeach
             @else
                <!-- Fallback recommendations -->
                <p class="flex items-center text-slate-600"><i class="fas fa-check-circle text-green-500 mr-2.5"></i>Laravel Developer</p>
                <p class="flex items-center text-slate-600"><i class="fas fa-check-circle text-green-500 mr-2.5"></i>System Administrator</p>
                <p class="flex items-center text-slate-600"><i class="fas fa-check-circle text-green-500 mr-2.5"></i>IT Support</p>
                <p class="flex items-center text-slate-600"><i class="fas fa-check-circle text-green-500 mr-2.5"></i>Frontend Developer</p>
             @endif
          </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 shadow-sm">
          <h3 class="font-bold text-yellow-800 mb-2 flex items-center">
            <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>Conseil carrière
          </h3>
          <p class="text-sm text-yellow-700 leading-relaxed">
            Ajoutez vos compétences et votre expérience pour augmenter vos chances d’être sélectionné par nos recruteurs partenaires.
          </p>
        </div>
      </aside>

    </section>
  </div>
</div>
@endsection
