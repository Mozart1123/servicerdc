@extends('layouts.public')

@section('title', 'Contactez-nous')
@section('meta_description', 'Contactez l\'équipe ProConnect pour toute question sur nos services, nos offres d\'emploi ou votre compte.')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-20">
    <div class="text-center mb-12">
        <span class="inline-block px-6 py-2 bg-[#29B6D1]/10 text-[#29B6D1] rounded-full font-semibold mb-4">
            <i class="fas fa-headset mr-2"></i>Contact
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Une question ? Contactez-nous</h1>
        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
            Notre équipe est là pour vous aider — problème technique, question sur un paiement,
            ou simplement un retour à nous faire.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Infos de contact --}}
        <div class="space-y-6">
            <div class="p-6 bg-white rounded-2xl shadow-md border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-[#29B6D1]/10 flex items-center justify-center mb-4">
                    <i class="fas fa-envelope text-[#29B6D1]"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">Email</h3>
                <p class="text-slate-600 text-sm">contact@proconnect.cd</p>
            </div>
            <div class="p-6 bg-white rounded-2xl shadow-md border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-[#29B6D1]/10 flex items-center justify-center mb-4">
                    <i class="fas fa-phone text-[#29B6D1]"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">Téléphone</h3>
                <p class="text-slate-600 text-sm">+243 81 234 5678</p>
            </div>
            <div class="p-6 bg-white rounded-2xl shadow-md border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-[#29B6D1]/10 flex items-center justify-center mb-4">
                    <i class="fas fa-location-dot text-[#29B6D1]"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">Adresse</h3>
                <p class="text-slate-600 text-sm">Gombe, Kinshasa, RDC</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="md:col-span-2 bg-white rounded-2xl shadow-md border border-slate-100 p-8">
            @auth
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Sujet</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#29B6D1] focus:ring-1 focus:ring-[#29B6D1] outline-none">
                        @error('subject')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
                        <textarea name="message" rows="6" required maxlength="2000"
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#29B6D1] focus:ring-1 focus:ring-[#29B6D1] outline-none">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#29B6D1] text-white font-semibold rounded-xl hover:opacity-90 transition">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
            @else
                <div class="text-center py-10">
                    <i class="fas fa-lock text-3xl text-slate-300 mb-4"></i>
                    <p class="text-slate-600 mb-6">
                        Connectez-vous pour nous envoyer un message directement — il sera suivi dans votre espace personnel.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}"
                           class="px-6 py-3 bg-[#29B6D1] text-white font-semibold rounded-xl hover:opacity-90 transition">Se connecter</a>
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition">Créer un compte</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection
