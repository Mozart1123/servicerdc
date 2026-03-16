@extends('layouts.admin')

@section('title', 'Intégrations API')
@section('header_title', 'Connecteurs HQ')
@section('page_title', 'Services Externes')
@section('page_subtitle', 'Configurez les clés secrètes et les webhooks pour les services tiers (Paiements, Cartes, Messaging).')

@section('content')
<div class="space-y-6 sm:space-y-8 pb-20 px-1 sm:px-0">
    <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-12">
        @csrf
        @method('PUT')
        <div class="max-w-3xl mx-auto space-y-10 sm:space-y-12">
            <!-- Payment Gateway -->
            <div class="space-y-5 sm:space-y-6">
                <div class="flex items-center gap-3 sm:gap-4 border-b border-slate-50 pb-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-rdc-blue rounded-xl flex items-center justify-center text-xl sm:text-2xl shrink-0">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest truncate">Paiement (M-Pesa/Orange)</h4>
                        <p class="text-[8px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 sm:mt-1">Status: <span class="text-emerald-500">Connecté</span></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5 sm:space-y-2">
                        <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Merchant ID</label>
                        <input type="password" name="payment_merchant_id" value="{{ $settings['payment_merchant_id'] ?? '' }}" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-mono outline-none focus:ring-2 focus:ring-rdc-blue/20">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Secret Key</label>
                        <input type="password" name="payment_secret_key" value="{{ $settings['payment_secret_key'] ?? '' }}" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-mono outline-none focus:ring-2 focus:ring-rdc-blue/20">
                    </div>
                </div>
            </div>
            <!-- Maps Integration -->
            <div class="space-y-5 sm:space-y-6">
                <div class="flex items-center gap-3 sm:gap-4 border-b border-slate-50 pb-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center text-xl sm:text-2xl shrink-0">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest truncate">Google Maps Enterprise</h4>
                        <p class="text-[8px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 sm:mt-1">Status: <span class="text-slate-300">Off</span></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5 sm:space-y-2">
                        <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">API Key</label>
                        <input type="text" name="google_maps_key" value="{{ $settings['google_maps_key'] ?? '' }}" placeholder="Clé Google..." class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-mono outline-none focus:ring-2 focus:ring-rdc-blue/20">
                    </div>
                </div>
            </div>

            <div class="pt-6 sm:pt-10 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 sm:py-5 bg-slate-900 text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200 hover:bg-rdc-blue active:scale-95 transition-all">Enregistrer les Clés</button>
            </div>
        </div>
    </form>
</div>
@endsection
