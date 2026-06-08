@extends('layouts.user')

@section('title', 'Abonnement | ProConnect')
@section('header_title', 'Abonnement')

@section('content')
<div class="space-y-8 pb-20">

    {{-- Success/Error Alerts --}}
    @if(session('success'))
        <div class="relative bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 flex items-center gap-4">
            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
            <p class="font-bold text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="relative bg-red-50 border border-red-200 rounded-2xl px-6 py-4 flex items-center gap-4">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <p class="font-bold text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- HERO -->
    <section class="bg-gradient-to-br from-rdc-blue to-rdc-dark-blue text-white rounded-[2.5rem] overflow-hidden shadow-sm" data-aos="fade-down">
      <div class="max-w-7xl mx-auto px-6 sm:px-10 py-14 text-center">
        <span class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-5 font-medium backdrop-blur-sm border border-white/20">
          <i class="fas fa-crown mr-2 text-rdc-yellow"></i>Abonnement ProConnect
        </span>

        <h2 class="text-4xl md:text-5xl font-bold mb-4">
          Choisissez le plan adapté à vos besoins
        </h2>

        <p class="text-blue-100 text-lg max-w-3xl mx-auto">
          Accédez aux meilleurs services, discutez avec les artisans, postulez aux emplois et profitez d'une expérience plus rapide.
        </p>
      </div>
    </section>

    <!-- CONTENT -->
    <div class="max-w-7xl mx-auto">

      <!-- CURRENT PLAN -->
      @if(isset($activeSubscription) && $activeSubscription)
      <section class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-10" data-aos="fade-up">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <p class="text-sm text-gray-500 font-medium">Votre abonnement actuel</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">
              Plan {{ $activeSubscription->plan->name ?? 'Gratuit' }}
            </h3>
            <p class="text-gray-500 mt-2">
              Expire le : <strong>{{ $activeSubscription->ends_at?->format('d/m/Y') ?? 'Illimité' }}</strong>
              &nbsp;• {{ $activeSubscription->billing_cycle === 'yearly' ? 'Facturation annuelle' : 'Facturation mensuelle' }}
            </p>
          </div>

          <div class="flex items-center gap-4">
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
              <p class="text-sm text-green-700 font-medium">Statut</p>
              <h4 class="text-xl font-bold text-green-800">
                <i class="fas fa-circle text-xs mr-2"></i>Actif
              </h4>
            </div>

            <form action="{{ route('user.subscription.cancel') }}" method="POST">
              @csrf
              <button type="submit" onclick="return confirm('Confirmer l\'annulation de votre abonnement ?')"
                      class="px-5 py-3 bg-white border border-red-200 text-red-500 font-bold rounded-xl text-sm hover:bg-red-50 transition-all">
                <i class="fas fa-times-circle mr-2"></i>Annuler
              </button>
            </form>
          </div>
        </div>
      </section>
      @else
      <section class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-10" data-aos="fade-up">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <p class="text-sm text-gray-500 font-medium">Votre abonnement actuel</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">Plan Gratuit</h3>
            <p class="text-gray-500 mt-2">
              Vous utilisez actuellement les fonctionnalités de base de ProConnect.
            </p>
          </div>

          <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 text-center">
            <p class="text-sm text-yellow-700 font-medium">Statut</p>
            <h4 class="text-xl font-bold text-yellow-800">
              <i class="fas fa-circle text-xs mr-2"></i>Actif
            </h4>
          </div>
        </div>
      </section>
      @endif

      <!-- PRICING -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">

        @if(isset($plans) && $plans->count() > 0)
          @foreach($plans as $plan)
          @php
            $isCurrentPlan = isset($activeSubscription) && $activeSubscription && $activeSubscription->plan->slug === $plan->slug;
            $colorMap = [
              'slate' => ['icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-600', 'icon' => 'fa-user', 'border' => '', 'scale' => '', 'btn' => 'bg-gray-100 text-gray-700'],
              'blue'  => ['icon_bg' => 'bg-cyan-100', 'icon_text' => 'text-rdc-blue', 'icon' => 'fa-star', 'border' => 'border-2 border-rdc-blue shadow-xl', 'scale' => 'md:scale-105', 'btn' => 'bg-rdc-blue text-white hover:bg-rdc-blue-dark'],
              'amber' => ['icon_bg' => 'bg-yellow-100', 'icon_text' => 'text-rdc-yellow', 'icon' => 'fa-crown', 'border' => '', 'scale' => '', 'btn' => 'bg-rdc-dark-blue text-white hover:bg-black'],
            ];
            $c = $colorMap[$plan->color] ?? $colorMap['slate'];
          @endphp

          <div class="relative bg-white rounded-3xl p-8 border shadow-sm hover:shadow-xl transition {{ $c['border'] }} {{ $c['scale'] }}" data-aos="fade-up" data-aos-delay="{{ 100 * $loop->index }}">
            @if($plan->is_popular)
            <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-rdc-yellow text-gray-900 px-5 py-2 rounded-full text-sm font-bold shadow">
              Recommandé
            </span>
            @endif

            <div class="w-14 h-14 rounded-2xl {{ $c['icon_bg'] }} {{ $c['icon_text'] }} flex items-center justify-center mb-6">
              <i class="fas {{ $plan->icon ?? $c['icon'] }} text-2xl"></i>
            </div>

            <h3 class="text-2xl font-bold">{{ $plan->name }}</h3>
            <p class="text-gray-500 mt-2">{{ $plan->description }}</p>

            <div class="my-6">
              <span class="text-4xl font-bold">{{ $plan->price_monthly == 0 ? 'Gratuit' : $plan->price_monthly.'$' }}</span>
              @if($plan->price_monthly > 0)
              <span class="text-gray-500">/mois</span>
              @endif
            </div>

            <ul class="space-y-4 text-sm text-gray-600 mb-8">
              @foreach($plan->features as $feature)
              <li><i class="fas fa-check text-green-500 mr-2"></i>{{ $feature }}</li>
              @endforeach
            </ul>

            @if($isCurrentPlan)
            <button class="w-full py-3 bg-green-50 text-green-700 rounded-xl font-semibold border border-green-200">
              <i class="fas fa-check-circle mr-2"></i>Plan actuel
            </button>
            @else
            <a href="{{ route('user.subscription.checkout', ['plan' => $plan->slug, 'billing' => 'monthly']) }}"
               class="w-full block py-3 text-center {{ $c['btn'] }} rounded-xl font-semibold transition">
              Choisir {{ $plan->name }}
            </a>
            @endif
          </div>
          @endforeach
        @else
          <!-- STATIC FALLBACK PLANS -->

          <!-- FREE -->
          <div class="bg-white rounded-3xl p-8 border shadow-sm hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="100">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center mb-6">
              <i class="fas fa-user text-2xl"></i>
            </div>

            <h3 class="text-2xl font-bold">Gratuit</h3>
            <p class="text-gray-500 mt-2">Pour découvrir la plateforme.</p>

            <div class="my-6">
              <span class="text-4xl font-bold">0$</span>
              <span class="text-gray-500">/mois</span>
            </div>

            <ul class="space-y-4 text-sm text-gray-600 mb-8">
              <li><i class="fas fa-check text-green-500 mr-2"></i>Accès aux services</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Recherche d'emplois</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>3 messages par jour</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Profil basique</li>
              <li class="text-gray-400"><i class="fas fa-times mr-2"></i>Support prioritaire</li>
            </ul>

            <button class="w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold">
              Plan actuel
            </button>
          </div>

          <!-- STANDARD -->
          <div class="relative bg-white rounded-3xl p-8 border-2 border-rdc-blue shadow-xl hover:shadow-2xl transition scale-100 md:scale-105" data-aos="fade-up" data-aos-delay="200">
            <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-rdc-yellow text-gray-900 px-5 py-2 rounded-full text-sm font-bold shadow">
              Recommandé
            </span>

            <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-rdc-blue flex items-center justify-center mb-6">
              <i class="fas fa-star text-2xl"></i>
            </div>

            <h3 class="text-2xl font-bold">Standard</h3>
            <p class="text-gray-500 mt-2">Pour les clients actifs.</p>

            <div class="my-6">
              <span class="text-4xl font-bold">5$</span>
              <span class="text-gray-500">/mois</span>
            </div>

            <ul class="space-y-4 text-sm text-gray-600 mb-8">
              <li><i class="fas fa-check text-green-500 mr-2"></i>Messages illimités</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Demandes de services illimitées</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Accès aux artisans vérifiés</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Suivi des candidatures</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Support rapide</li>
            </ul>

            <button class="w-full py-3 bg-rdc-blue text-white rounded-xl font-semibold hover:bg-rdc-blue-dark transition">
              Choisir Standard
            </button>
          </div>

          <!-- PREMIUM -->
          <div class="bg-white rounded-3xl p-8 border shadow-sm hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="300">
            <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-rdc-yellow flex items-center justify-center mb-6">
              <i class="fas fa-crown text-2xl"></i>
            </div>

            <h3 class="text-2xl font-bold">Premium</h3>
            <p class="text-gray-500 mt-2">Pour une expérience complète.</p>

            <div class="my-6">
              <span class="text-4xl font-bold">10$</span>
              <span class="text-gray-500">/mois</span>
            </div>

            <ul class="space-y-4 text-sm text-gray-600 mb-8">
              <li><i class="fas fa-check text-green-500 mr-2"></i>Toutes les options Standard</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Priorité dans les demandes</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Offres d'emploi premium</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Badge client vérifié</li>
              <li><i class="fas fa-check text-green-500 mr-2"></i>Support prioritaire 24/7</li>
            </ul>

            <button class="w-full py-3 bg-rdc-dark-blue text-white rounded-xl font-semibold hover:bg-black transition">
              Choisir Premium
            </button>
          </div>
        @endif

      </section>

      <!-- PAYMENT METHODS -->
      <section class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8" data-aos="fade-in">
        <h3 class="text-2xl font-bold mb-6">Méthodes de paiement</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div class="p-5 border rounded-2xl hover:border-rdc-blue hover:bg-cyan-50 transition cursor-pointer">
            <i class="fas fa-mobile-screen-button text-3xl text-rdc-blue mb-3"></i>
            <h4 class="font-bold">Mobile Money</h4>
            <p class="text-sm text-gray-500">M-Pesa, Airtel Money, Orange Money</p>
          </div>

          <div class="p-5 border rounded-2xl hover:border-rdc-blue hover:bg-cyan-50 transition cursor-pointer">
            <i class="fas fa-credit-card text-3xl text-rdc-yellow mb-3"></i>
            <h4 class="font-bold">Carte bancaire</h4>
            <p class="text-sm text-gray-500">Visa, Mastercard</p>
          </div>

          <div class="p-5 border rounded-2xl hover:border-rdc-blue hover:bg-cyan-50 transition cursor-pointer">
            <i class="fas fa-building-columns text-3xl text-green-600 mb-3"></i>
            <h4 class="font-bold">Banque</h4>
            <p class="text-sm text-gray-500">Virement bancaire sécurisé</p>
          </div>

          <div class="p-5 border rounded-2xl hover:border-rdc-blue hover:bg-cyan-50 transition cursor-pointer">
            <i class="fas fa-money-bill-wave text-3xl text-rdc-red mb-3"></i>
            <h4 class="font-bold">Paiement cash</h4>
            <p class="text-sm text-gray-500">Paiement auprès d'un agent</p>
          </div>
        </div>
      </section>

    </div>
</div>
@endsection
