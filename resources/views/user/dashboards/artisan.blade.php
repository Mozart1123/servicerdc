@extends('layouts.user')

@section('header_title', 'Tableau de bord Artisan')

@section('content')
<div class="space-y-12 pb-20">

    @include('partials.verification-dashboard-banner')
    <!-- Welcome Artisan -->
    <div class="relative">
        <div class="absolute inset-0 bg-amber-500/5 rounded-2xl blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-8 rounded-2xl shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 rounded-xl bg-amber-100 flex items-center justify-center text-amber-500 text-3xl shadow-inner">
                <i class="fas fa-hammer"></i>
            </div>
            <div class="flex-1 w-full text-center md:text-left">
                <h2 class="text-2xl font-black text-slate-900 uppercase">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Gérez vos services et développez votre activité</p>
            </div>
            <div class="flex gap-4 w-full md:w-auto mt-4 md:mt-0">
                <a href="{{ route('user.services.create') }}" class="flex-1 text-center md:flex-none px-8 py-5 bg-amber-500 text-white font-black rounded-xl text-[10px] uppercase tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 transition-all">
                    + Nouveau Service
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-box-open"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['my_services_count'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mes Services</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-hard-hat"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['active_missions'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Missions Actives</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-bell"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['pending_demands_count'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Demandes en attente</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-star-half-alt"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ number_format($stats['avg_rating'] ?? 0, 1, ',', '') }} <span class="text-sm font-bold text-slate-400">({{ $stats['reviews_count'] ?? 0 }})</span></p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mes Avis</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-envelope"></i></div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $stats['unread_notifications'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Notifications</p>
            </div>
        </div>
    </div>

    <!-- Point 10 : Section Disponibilité Artisan -->
    @php
        $artisan = Auth::user();
        $isAvailable = (bool) ($artisan->is_available ?? true);
        $vacationUntil = $artisan->available_until ? $artisan->available_until->format('Y-m-d') : null;
        $isOnVacation = $artisan->available_until && $artisan->available_until->isFuture();
        $activeDays = $artisan->availability_days ?? ['lun', 'mar', 'mer', 'jeu', 'ven'];
        $weekDays = [
            'lun' => 'Lun',
            'mar' => 'Mar',
            'mer' => 'Mer',
            'jeu' => 'Jeu',
            'ven' => 'Ven',
            'sam' => 'Sam',
            'dim' => 'Dim',
        ];
    @endphp
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8"
         x-data="{
            isAvailable: {{ $isAvailable ? 'true' : 'false' }},
            showDetails: false,
            loadingToggle: false,
            toggleAvailability() {
                this.loadingToggle = true;
                fetch('{{ route('user.artisan.availability.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    this.isAvailable = data.is_available;
                    this.loadingToggle = false;
                })
                .catch(err => {
                    this.loadingToggle = false;
                    alert('Erreur lors du changement de disponibilité.');
                });
            }
         }">
        
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl transition-all"
                     :class="isAvailable ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'">
                    <i class="fas" :class="isAvailable ? 'fa-circle-check' : 'fa-circle-pause'"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-black text-slate-900 uppercase">Ma Disponibilité</h3>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all"
                              :class="isAvailable ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                            <span class="w-2 h-2 rounded-full" :class="isAvailable ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                            <span x-text="isAvailable ? 'Disponible pour missions' : 'Indisponible'"></span>
                        </span>
                        @if($isOnVacation)
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded-full">
                                <i class="fas fa-umbrella-beach mr-1"></i> Absent jusqu'au {{ $artisan->available_until->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Horaires habituels : <strong>{{ $artisan->working_hours_start ?? '08:00' }} - {{ $artisan->working_hours_end ?? '18:00' }}</strong>
                        @if($artisan->intervention_zone)
                            • Communes : <strong>{{ $artisan->intervention_zone }}</strong>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Interrupteur 1-click + Bouton configurer -->
            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                <button type="button"
                        @click="toggleAvailability()"
                        :disabled="loadingToggle"
                        class="flex items-center gap-3 px-5 py-3 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all shadow-sm border"
                        :class="isAvailable ? 'bg-emerald-500 hover:bg-emerald-600 text-white border-emerald-500' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'">
                    <span x-show="!loadingToggle" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                          :class="isAvailable ? 'bg-white/40' : 'bg-slate-300'">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="isAvailable ? 'translate-x-4' : 'translate-x-0'"></span>
                    </span>
                    <i x-show="loadingToggle" class="fas fa-spinner fa-spin text-sm"></i>
                    <span x-text="isAvailable ? 'Je suis disponible' : 'Je suis indisponible'"></span>
                </button>

                <button type="button"
                        @click="showDetails = !showDetails"
                        class="px-4 py-3 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 border border-slate-200/80">
                    <i class="fas fa-sliders text-slate-400"></i>
                    <span x-text="showDetails ? 'Masquer réglages' : 'Gérer planning'"></span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform" :class="showDetails ? 'rotate-180' : ''"></i>
                </button>
            </div>
        </div>

        <!-- Formulaire de réglages complet (Horaires, Jours, Congés, Communes) -->
        <div x-show="showDetails" x-collapse class="pt-6 mt-2">
            <form action="{{ route('user.artisan.availability.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- 1. Jours de travail -->
                    <div class="lg:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest block">Jours de travail habituels</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($weekDays as $slug => $label)
                                <label class="cursor-pointer select-none">
                                    <input type="checkbox" name="availability_days[]" value="{{ $slug }}" class="peer sr-only"
                                           {{ in_array($slug, $activeDays) ? 'checked' : '' }}>
                                    <span class="inline-flex items-center justify-center w-11 h-10 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-600 peer-checked:bg-rdc-blue peer-checked:border-rdc-blue peer-checked:text-white transition-all">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Heures de travail -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest block">Horaires de travail</label>
                        <div class="flex items-center gap-2">
                            <input type="time" name="working_hours_start" value="{{ old('working_hours_start', $artisan->working_hours_start ?? '08:00') }}"
                                   class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue outline-none">
                            <span class="text-xs text-slate-400 font-bold">à</span>
                            <input type="time" name="working_hours_end" value="{{ old('working_hours_end', $artisan->working_hours_end ?? '18:00') }}"
                                   class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue outline-none">
                        </div>
                    </div>

                    <!-- 3. Congés / Absent jusqu'au -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest block">Congés : Absent jusqu'au</label>
                        <input type="date" name="available_until" value="{{ old('available_until', $vacationUntil) }}"
                               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue outline-none">
                    </div>

                    <!-- 4. Communes d'intervention -->
                    <div class="lg:col-span-3 space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest block">Communes & Zones d'intervention</label>
                        <input type="text" name="intervention_zone" value="{{ old('intervention_zone', $artisan->intervention_zone) }}"
                               placeholder="Ex: Gombe, Kintambo, Ngaliema, Limete (séparées par des virgules)"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rdc-blue outline-none">
                    </div>

                    <!-- 5. Bouton Enregistrer -->
                    <div class="lg:col-span-1 flex items-end">
                        <button type="submit" class="w-full py-2.5 px-5 bg-rdc-blue hover:bg-rdc-blue-dark text-white rounded-xl text-xs font-bold transition-all shadow-md">
                            <i class="fas fa-save mr-1.5"></i> Enregistrer
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="space-y-6">
        
        <!-- Mes Missions Actives -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4" data-aos="fade-down">
                <div class="flex items-center gap-4">
                    <h3 class="text-xl font-heading font-black text-slate-900 uppercase">Nouvelles demandes & Missions</h3>
                </div>
                <a href="{{ route('user.missions.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Voir tout</a>
            </div>

            <div class="space-y-4">
                @if(($recentDemands ?? collect())->isEmpty() && collect($artisanMissions ?? [])->isEmpty())
                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm text-center" data-aos="fade-up">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-xl flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p class="text-sm text-slate-500 font-bold">Aucune demande ou mission en cours pour le moment.</p>
                    </div>
                @endif

                {{-- Nouvelles demandes --}}
                @foreach($recentDemands ?? [] as $demand)
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition-all group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-16 h-16 bg-amber-50 rounded-xl flex items-center justify-center text-2xl text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="font-bold text-slate-900">Demande: {{ $demand->service->title ?? 'Service' }}</h4>
                            <p class="text-xs text-slate-500 mt-1"><i class="fas fa-user mr-1"></i> {{ $demand->user->name ?? 'Client' }}</p>
                            @if($demand->budget)
                                <p class="text-xs text-slate-500 mt-1"><i class="fas fa-money-bill mr-1"></i> Rémunération proposée: {{ $demand->budget }} $</p>
                            @endif
                        </div>
                        <div class="text-center md:text-right">
                            <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full mb-2">Nouvelle demande</span>
                            <div class="flex gap-2">
                                <a href="{{ route('user.service-requests.show', $demand->id) }}" class="px-4 py-2 bg-rdc-blue text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition">Répondre</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Missions en cours --}}
                @foreach($artisanMissions ?? [] as $mission)
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition-all group" data-aos="fade-up" data-aos-delay="{{ ($loop->index + 3) * 100 }}">
                        <div class="w-16 h-16 bg-emerald-50 rounded-xl flex items-center justify-center text-2xl text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            <i class="fas {{ $mission->status === 'completed' ? 'fa-check' : 'fa-tools' }}"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h4 class="font-bold text-slate-900">{{ $mission->service->title ?? 'Mission' }}</h4>
                            <p class="text-xs text-slate-500 mt-1"><i class="fas fa-user mr-1"></i> {{ $mission->client->name ?? 'Client' }}</p>
                        </div>
                        <div class="text-center md:text-right">
                            @if($mission->status === 'in_progress')
                                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full mb-2">En cours</span>
                            @elseif($mission->status === 'completed')
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full mb-2">Terminée</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-full mb-2">{{ ucfirst($mission->status) }}</span>
                            @endif
                            <div class="flex gap-2 justify-center md:justify-end">
                                <a href="{{ route('user.missions.show', $mission->id) }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">Détails</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
