@extends('layouts.admin')

@section('title', 'Gestion Newsletter')
@section('header_title', 'Engagement Communautaire')
@section('page_title', 'Campagnes E-mail')
@section('page_subtitle', 'Gérez vos listes de diffusion et concevez des newsletters percutantes pour vos abonnés.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Newsletter Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Abonnés</p>
            <h4 class="text-2xl font-black text-slate-900">12,482</h4>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Taux de Clic</p>
            <h4 class="text-2xl font-black text-emerald-500">18.4%</h4>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Désabonnements</p>
            <h4 class="text-2xl font-black text-rdc-red">0.4%</h4>
        </div>
        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl text-white">
            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-2">Dernier Envoi</p>
            <h4 class="text-2xl font-black">Hier, 14:20</h4>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Lists -->
        <div class="lg:col-span-1 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">Listes de Diffusion</h4>
            <div class="space-y-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-slate-900">Utilisateurs Actifs</p>
                        <p class="text-[9px] font-bold text-slate-400">8.2k abonnés</p>
                    </div>
                    <i class="fas fa-users text-slate-200"></i>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-slate-900">Artisans Certifiés</p>
                        <p class="text-[9px] font-bold text-slate-400">4.1k abonnés</p>
                    </div>
                    <i class="fas fa-screwdriver-wrench text-slate-200"></i>
                </div>
            </div>
            <button class="w-full mt-6 py-4 border-2 border-dashed border-slate-100 rounded-2xl text-[9px] font-black text-slate-300 uppercase tracking-widest hover:border-rdc-blue hover:text-rdc-blue transition-all">+ Nouvelle Liste</button>
        </div>

        <!-- Campaign Creator Mock -->
        <div class="lg:col-span-2 bg-slate-900 p-10 rounded-[4rem] text-white shadow-2xl flex flex-col items-center justify-center text-center relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rdc-blue/10 rounded-full blur-[100px]"></div>
            <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-4xl mb-6 border border-white/10">
                <i class="fas fa-pen-nib"></i>
            </div>
            <h3 class="text-2xl font-black uppercase tracking-tighter mb-4">Éditeur de Campagne HQ</h3>
            <p class="text-white/40 text-sm font-medium max-w-sm mb-10">Concevez des emails responsive avec notre éditeur Drag & Drop. L'intégration de Mailchimp Sync est opérationnelle.</p>
            <button class="px-10 py-4 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/20 hover:scale-110 transition-all">Créer une Newsletter</button>
        </div>
    </div>
</div>
@endsection
