@extends('layouts.user')

@section('header_title', 'Ajouter un Service')

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto">

    <div class="relative">
        <div class="absolute inset-0 bg-rdc-blue/5 rounded-[3rem] blur-3xl opacity-50"></div>
        <div class="relative bg-white border border-slate-100 p-10 rounded-[3.5rem] shadow-sm">
            <h2 class="text-3xl font-heading font-black text-slate-900 uppercase">Proposer un service</h2>
            <p class="text-sm font-bold text-slate-400 mt-2 uppercase tracking-widest">Remplissez les détails pour publier votre offre sur la plateforme</p>

            <form action="{{ route('user.services.store') }}" method="POST" enctype="multipart/form-data" class="mt-10 space-y-8"
                  x-data="serviceForm()" x-init="init()">
                @csrf

                {{-- Message d'erreur de limite d'abonnement --}}
                @if(session('error'))
                    <div class="mb-2 px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 font-medium text-sm flex items-center justify-between gap-3 flex-wrap">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            {{ session('error') }}
                        </span>
                        @if(session('upgrade_url'))
                            <a href="{{ session('upgrade_url') }}" class="text-red-700 underline font-semibold whitespace-nowrap">
                                Voir les abonnements →
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Résumé des erreurs de validation (titre/prix/photos par offre notamment) --}}
                @if($errors->any())
                    <div class="px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-xs font-semibold space-y-1">
                        @foreach($errors->all() as $error)
                            <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @php
                    // Données propres à chaque offre, indexées par service_type_id : { [id]: { title, price, ... } }
                    // (calculées ici, hors de @json(), car @json() scinde son argument sur toutes les
                    // virgules — une expression imbriquée comme old("titles.$id", '') le ferait échouer)
                    $perTypeDataOld = collect(old('service_type_ids', []))->mapWithKeys(function ($id) {
                        return [(int) $id => [
                            'title'            => old("titles.$id", ''),
                            'pricing_type'     => old("pricing_types.$id", 'fixed'),
                            'price'            => old("prices.$id", ''),
                            'duration'         => old("durations.$id", ''),
                            'service_location' => old("service_locations.$id", ''),
                            'availability'     => old("availabilities.$id", ''),
                            'min_notice'       => old("min_notices.$id", 'none'),
                            'description'      => old("descriptions.$id", ''),
                        ]];
                    })->all();
                @endphp

                <!-- Catégorie (Métier) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Catégorie de métier <span class="text-red-500">*</span></label>
                    <select name="category_id" required x-model="categoryId" @change="loadServiceTypes()"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        <option value="">Sélectionnez un domaine d'activité</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Types de services (Sous-services — Sélection multiple) -->
                <div class="space-y-3" x-show="serviceTypes.length > 0" x-transition>
                    <div class="flex items-center justify-between pl-4">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest">
                            Types de services <span class="text-red-500">*</span>
                            <span class="text-slate-400 font-medium normal-case tracking-normal ml-1">(cochez un ou plusieurs)</span>
                        </label>
                        <div class="flex gap-3">
                            <button type="button" @click="selectAllServiceTypes()"
                                    class="text-[10px] font-black text-rdc-blue hover:underline uppercase tracking-wider">Tout sélectionner</button>
                            <button type="button" @click="clearServiceTypes()"
                                    class="text-[10px] font-black text-slate-400 hover:text-slate-600 hover:underline uppercase tracking-wider">Tout vider</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="st in serviceTypes" :key="st.id">
                            <label :for="'st-' + st.id"
                                   :class="selectedTypeIds.includes(st.id) ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                                   class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                <input type="checkbox"
                                       :id="'st-' + st.id"
                                       name="service_type_ids[]"
                                       :value="st.id"
                                       @change="onCheckboxChange(st.id, $event)"
                                       :checked="selectedTypeIds.includes(st.id)"
                                       class="w-4 h-4 accent-rdc-blue rounded shrink-0">
                                <span class="text-xs font-bold text-slate-800 leading-tight" x-text="st.title"></span>
                            </label>
                        </template>
                    </div>

                    <!-- Compteur de sélection -->
                    <p x-show="selectedTypeIds.length > 0"
                       class="text-[10px] font-black text-rdc-blue pl-4 uppercase tracking-widest">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span x-text="selectedTypeIds.length + ' sous-service(s) sélectionné(s) — ' + selectedTypeIds.length + ' offre(s) sera/seront créée(s), chacune avec son propre titre et ses propres photos'"></span>
                    </p>

                    @error('service_type_ids')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    @error('service_type_ids.*')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Localisation (commune à toutes les offres créées en une fois) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Localisation (Ville, Commune) <span class="text-red-500">*</span></label>
                    <input type="text" name="location" required placeholder="Ex: Kinshasa, Gombe" value="{{ old('location') }}"
                           class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                    @error('location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                </div>

                <!-- Offre unique — aucun type de service coché (ou catégorie sans sous-types) -->
                <div x-show="selectedTypeIds.length === 0" class="space-y-8">
                    <!-- Titre -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre du service <span class="text-red-500">*</span></label>
                        <input type="text" name="title" placeholder="Ex: Réparation plomberie générale" x-model="title"
                               :required="selectedTypeIds.length === 0"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                        @error('title')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                    </div>

                    <!-- Tarification -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Tarification <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label :class="pricingType === 'fixed' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                                   class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                <input type="radio" name="pricing_type" value="fixed" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                                <span class="text-xs font-bold text-slate-800 leading-tight">Prix fixe</span>
                            </label>
                            <label :class="pricingType === 'starting_from' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                                   class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                <input type="radio" name="pricing_type" value="starting_from" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                                <span class="text-xs font-bold text-slate-800 leading-tight">À partir de</span>
                            </label>
                            <label :class="pricingType === 'quote' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                                   class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                <input type="radio" name="pricing_type" value="quote" x-model="pricingType" class="w-4 h-4 accent-rdc-blue">
                                <span class="text-xs font-bold text-slate-800 leading-tight">Sur devis</span>
                            </label>
                        </div>
                        @error('pricing_type')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror

                        <!-- Prix USD -->
                        <div class="space-y-2 relative" x-show="pricingType !== 'quote'" x-transition>
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">
                                <span x-text="pricingType === 'starting_from' ? 'Prix minimum (USD)' : 'Prix (USD)'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" step="0.01" min="0" placeholder="0.00" x-model="price"
                                   :required="pricingType !== 'quote' && selectedTypeIds.length === 0"
                                   :disabled="pricingType === 'quote'"
                                   class="w-full pl-6 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            <span class="absolute right-4 top-[38px] text-slate-400 font-bold">$</span>
                            @error('price')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Durée approximative + Lieu de prestation (côte à côte sur desktop) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Durée approximative <span class="text-red-500">*</span></label>
                            <select name="duration" x-model="duration" :required="selectedTypeIds.length === 0"
                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                <option value="">Sélectionnez la durée</option>
                                <option value="30_minutes">30 minutes</option>
                                <option value="45_minutes">45 minutes</option>
                                <option value="1_hour">1 heure</option>
                                <option value="1h30">1 h 30</option>
                                <option value="2_hours">2 heures</option>
                                <option value="3_hours">3 heures</option>
                                <option value="half_day">Demi-journée</option>
                                <option value="full_day">Journée complète</option>
                                <option value="multiple_days">Plusieurs jours</option>
                                <option value="to_define">À définir avec le client</option>
                            </select>
                            @error('duration')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Lieu de prestation <span class="text-red-500">*</span></label>
                            <select name="service_location" x-model="serviceLocation" :required="selectedTypeIds.length === 0"
                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                <option value="">Où proposez-vous ce service ?</option>
                                <option value="home">À domicile</option>
                                <option value="provider">Chez moi</option>
                                <option value="both">Les deux</option>
                            </select>
                            <p class="text-[10px] text-slate-400 font-bold pl-4">Sélectionnez où vous proposez habituellement ce service.</p>
                            @error('service_location')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Disponibilité -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Disponibilité <span class="text-red-500">*</span></label>
                            <select name="availability" x-model="availability" :required="selectedTypeIds.length === 0"
                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                <option value="">Sélectionnez votre disponibilité</option>
                                <option value="daily">Tous les jours</option>
                                <option value="weekdays">Du lundi au vendredi</option>
                                <option value="monday_saturday">Du lundi au samedi</option>
                                <option value="weekends">Week-ends uniquement</option>
                                <option value="appointment">Sur rendez-vous</option>
                                <option value="availability_based">Selon disponibilité</option>
                            </select>
                            @error('availability')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>

                        <!-- Préavis minimum si sur rendez-vous -->
                        <div class="space-y-2" x-show="availability === 'appointment'" x-transition>
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Préavis minimum <span class="text-red-500">*</span></label>
                            <select name="min_notice" x-model="minNotice" :required="availability === 'appointment' && selectedTypeIds.length === 0"
                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                <option value="none">Aucun</option>
                                <option value="2_hours">2 heures</option>
                                <option value="6_hours">6 heures</option>
                                <option value="12_hours">12 heures</option>
                                <option value="24_hours">24 heures</option>
                                <option value="48_hours">48 heures</option>
                                <option value="72_hours">72 heures</option>
                            </select>
                            @error('min_notice')<span class="text-xs text-red-500 pl-4 font-bold">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Description détaillée (Optionnelle)</label>
                        <textarea name="description" rows="5" placeholder="Décrivez votre service en détail, ce qui est inclus, votre matériel, vos spécialités..."
                                  class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none resize-none">{{ old('description') }}</textarea>
                    </div>
                    <!-- Images -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between pl-4 mb-2">
                            <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Images du service</label>
                        </div>
                        <div class="relative border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center hover:bg-slate-50 transition-colors group">
                            <input type="file" name="images[]" multiple accept="image/*" @change="previewImages($event, 'single')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-rdc-blue text-2xl mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-1">Cliquez ou glissez vos images ici</h4>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG, GIF (Max 5 images)</p>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-4" x-show="(previews['single'] || []).length > 0">
                            <template x-for="(src, idx) in (previews['single'] || [])" :key="idx">
                                <div class="relative aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-50">
                                    <img :src="src" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Une offre par type de service coché — titre, prix, description et photos propres à chacune -->
                <div x-show="selectedTypeIds.length > 0" class="space-y-6">
                    <template x-for="id in selectedTypeIds" :key="'offer-' + id">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-3xl p-6 space-y-5">
                            <h4 class="text-xs font-black text-rdc-blue uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-tag"></i>
                                <span x-text="serviceTypeTitle(id)"></span>
                            </h4>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Titre de cette offre <span class="text-red-500">*</span></label>
                                <input type="text" :name="'titles[' + id + ']'" x-model="perTypeData[id].title" required
                                       class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                            </div>

                            <!-- Tarification perType -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Tarification <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <label :class="perTypeData[id].pricing_type === 'fixed' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                           class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                        <input type="radio" :name="'pricing_types[' + id + ']'" value="fixed" x-model="perTypeData[id].pricing_type" class="w-4 h-4 accent-rdc-blue">
                                        <span class="text-xs font-bold text-slate-800 leading-tight">Prix fixe</span>
                                    </label>
                                    <label :class="perTypeData[id].pricing_type === 'starting_from' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                           class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                        <input type="radio" :name="'pricing_types[' + id + ']'" value="starting_from" x-model="perTypeData[id].pricing_type" class="w-4 h-4 accent-rdc-blue">
                                        <span class="text-xs font-bold text-slate-800 leading-tight">À partir de</span>
                                    </label>
                                    <label :class="perTypeData[id].pricing_type === 'quote' ? 'border-rdc-blue bg-rdc-blue/5 ring-2 ring-rdc-blue/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                           class="flex items-center gap-3 px-5 py-4 rounded-2xl cursor-pointer transition-all border group">
                                        <input type="radio" :name="'pricing_types[' + id + ']'" value="quote" x-model="perTypeData[id].pricing_type" class="w-4 h-4 accent-rdc-blue">
                                        <span class="text-xs font-bold text-slate-800 leading-tight">Sur devis</span>
                                    </label>
                                </div>

                                <div class="space-y-2 relative" x-show="perTypeData[id].pricing_type !== 'quote'" x-transition>
                                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">
                                        <span x-text="perTypeData[id].pricing_type === 'starting_from' ? 'Prix minimum (USD)' : 'Prix (USD)'"></span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" :name="'prices[' + id + ']'" step="0.01" min="0" placeholder="0.00"
                                           x-model="perTypeData[id].price"
                                           :required="perTypeData[id].pricing_type !== 'quote'"
                                           :disabled="perTypeData[id].pricing_type === 'quote'"
                                           class="w-full pl-6 pr-12 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                    <span class="absolute right-4 top-[38px] text-slate-400 font-bold">$</span>
                                </div>
                            </div>

                            <!-- Durée & Lieu perType -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Durée approximative <span class="text-red-500">*</span></label>
                                    <select :name="'durations[' + id + ']'" x-model="perTypeData[id].duration" required
                                            class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="">Sélectionnez la durée</option>
                                        <option value="30_minutes">30 minutes</option>
                                        <option value="45_minutes">45 minutes</option>
                                        <option value="1_hour">1 heure</option>
                                        <option value="1h30">1 h 30</option>
                                        <option value="2_hours">2 heures</option>
                                        <option value="3_hours">3 heures</option>
                                        <option value="half_day">Demi-journée</option>
                                        <option value="full_day">Journée complète</option>
                                        <option value="multiple_days">Plusieurs jours</option>
                                        <option value="to_define">À définir avec le client</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Lieu de prestation <span class="text-red-500">*</span></label>
                                    <select :name="'service_locations[' + id + ']'" x-model="perTypeData[id].service_location" required
                                            class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="">Où proposez-vous ce service ?</option>
                                        <option value="home">À domicile</option>
                                        <option value="provider">Chez moi</option>
                                        <option value="both">Les deux</option>
                                    </select>
                                    <p class="text-[10px] text-slate-400 font-bold pl-4">Sélectionnez où vous proposez habituellement ce service.</p>
                                </div>
                            </div>

                            <!-- Disponibilité perType -->
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Disponibilité <span class="text-red-500">*</span></label>
                                    <select :name="'availabilities[' + id + ']'" x-model="perTypeData[id].availability" required
                                            class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="">Sélectionnez votre disponibilité</option>
                                        <option value="daily">Tous les jours</option>
                                        <option value="weekdays">Du lundi au vendredi</option>
                                        <option value="monday_saturday">Du lundi au samedi</option>
                                        <option value="weekends">Week-ends uniquement</option>
                                        <option value="appointment">Sur rendez-vous</option>
                                        <option value="availability_based">Selon disponibilité</option>
                                    </select>
                                </div>

                                <div class="space-y-2" x-show="perTypeData[id].availability === 'appointment'" x-transition>
                                    <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Préavis minimum <span class="text-red-500">*</span></label>
                                    <select :name="'min_notices[' + id + ']'" x-model="perTypeData[id].min_notice"
                                            :required="perTypeData[id].availability === 'appointment'"
                                            class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="none">Aucun</option>
                                        <option value="2_hours">2 heures</option>
                                        <option value="6_hours">6 heures</option>
                                        <option value="12_hours">12 heures</option>
                                        <option value="24_hours">24 heures</option>
                                        <option value="48_hours">48 heures</option>
                                        <option value="72_hours">72 heures</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Description (Optionnelle)</label>
                                <textarea :name="'descriptions[' + id + ']'" x-model="perTypeData[id].description" rows="3" placeholder="Ce qui est inclus dans cette offre..."
                                          class="w-full px-6 py-4 bg-white border-none rounded-2xl text-xs font-bold text-slate-900 focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none resize-none"></textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-900 uppercase tracking-widest pl-4">Photos de cette offre</label>
                                <div class="relative border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:bg-white transition-colors group">
                                    <input type="file" :name="'images_by_type[' + id + '][]'" multiple accept="image/*" @change="previewImages($event, id)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <i class="fas fa-cloud-upload-alt text-rdc-blue text-xl mb-1"></i>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">JPG, PNG (Max 5)</p>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-3" x-show="(previews[id] || []).length > 0">
                                    <template x-for="(src, idx) in (previews[id] || [])" :key="idx">
                                        <div class="relative aspect-square rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white">
                                            <img :src="src" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-6 flex gap-4 border-t border-slate-100">
                    <a href="{{ route('user.services.my') }}" class="px-8 py-5 bg-slate-100 text-slate-600 font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Annuler</a>
                    <button type="submit" class="flex-1 px-8 py-5 bg-rdc-blue text-white font-black rounded-3xl text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">
                        <span x-show="selectedTypeIds.length <= 1">
                            <i class="fas fa-paper-plane mr-1"></i> Publier mon service
                        </span>
                        <span x-show="selectedTypeIds.length > 1">
                            <i class="fas fa-paper-plane mr-1"></i> Publier <span x-text="selectedTypeIds.length"></span> services
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function serviceForm() {
    return {
        categoryId: '{{ old('category_id') }}',
        title: '{{ old('title') }}',
        pricingType: '{{ old('pricing_type', 'fixed') }}',
        price: '{{ old('price') }}',
        duration: '{{ old('duration') }}',
        serviceLocation: '{{ old('service_location') }}',
        availability: '{{ old('availability') }}',
        minNotice: '{{ old('min_notice', 'none') }}',
        serviceTypes: [],
        selectedTypeIds: @json(array_map('intval', old('service_type_ids', []))),
        // Castées en objet PHP pour que l'encodage JSON produise bien {} et non [] quand la liste est vide
        perTypeData: @json((object) $perTypeDataOld),
        // Aperçus des photos choisies, indexés par service_type_id (ou 'single' pour l'offre unique)
        previews: {},

        init() {
            if (this.categoryId) {
                this.loadServiceTypes();
            }
        },

        loadServiceTypes() {
            if (!this.categoryId) {
                this.serviceTypes = [];
                this.selectedTypeIds = [];
                return;
            }
            fetch(`/api/categories/${this.categoryId}/service-types`)
                .then(r => r.json())
                .then(data => {
                    this.serviceTypes = data;
                    // Restore old checked state on validation error
                    this.selectedTypeIds = this.selectedTypeIds.map(id => parseInt(id));
                    this.selectedTypeIds.forEach(id => this.ensurePerTypeData(id));
                });
        },

        serviceTypeTitle(id) {
            const st = this.serviceTypes.find(s => s.id === id);
            return st ? st.title : ('Service #' + id);
        },

        ensurePerTypeData(id) {
            if (!this.perTypeData[id]) {
                this.perTypeData[id] = {
                    title: this.serviceTypeTitle(id),
                    pricing_type: 'fixed',
                    price: '',
                    duration: '',
                    service_location: '',
                    availability: '',
                    min_notice: 'none',
                    description: '',
                };
            }
        },

        onCheckboxChange(id, event) {
            id = parseInt(id);
            if (event.target.checked) {
                if (!this.selectedTypeIds.includes(id)) {
                    this.selectedTypeIds.push(id);
                }
                this.ensurePerTypeData(id);
            } else {
                this.selectedTypeIds = this.selectedTypeIds.filter(x => x !== id);
            }
        },

        selectAllServiceTypes() {
            this.selectedTypeIds = this.serviceTypes.map(st => st.id);
            this.selectedTypeIds.forEach(id => this.ensurePerTypeData(id));
        },

        clearServiceTypes() {
            this.selectedTypeIds = [];
        },

        // Génère un aperçu des photos choisies pour une offre donnée (clé =
        // service_type_id, ou 'single' pour l'offre unique sans type).
        previewImages(event, key) {
            const files = Array.from(event.target.files || []).slice(0, 5);
            this.previews[key] = files.map(file => URL.createObjectURL(file));
        },
    }
}
</script>
@endsection
