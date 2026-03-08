@extends('layouts.admin')

@section('title', 'Intégrations API')
@section('header_title', 'Connecteurs HQ')
@section('page_title', 'Services Externes')
@section('page_subtitle', 'Configurez les clés secrètes et les webhooks pour les services tiers (Paiements, Cartes, Messaging).')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden p-8 md:p-12">
        <div class="max-w-3xl mx-auto space-y-12">
            <!-- Payment Gateway -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-50 pb-4">
                    <div class="w-12 h-12 bg-blue-50 text-rdc-blue rounded-xl flex items-center justify-center text-2xl">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Passerelle de Paiement (M-Pesa/Orange Money)</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Status: <span class="text-emerald-500">Connecté</span></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Merchant ID</label>
                        <input type="password" value="XXXXXXXX-XXXX-XXXX-XXXX" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-mono outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Secret Key</label>
                        <input type="password" value="S3CR3T_K3Y_V4LU3" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-mono outline-none">
                    </div>
                </div>
            </div>

            <!-- Maps Integration -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-50 pb-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center text-2xl">
                        <i class="fas fa-map"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Google Maps Enterprise</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Status: <span class="text-slate-300">Non configuré</span></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Google API Key</label>
                        <input type="text" placeholder="Entrez votre clé API Google..." class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-mono outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-10 flex justify-end">
                <button class="px-12 py-5 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200">Enregistrer les Clés</button>
            </div>
        </div>
    </div>
</div>
@endsection
