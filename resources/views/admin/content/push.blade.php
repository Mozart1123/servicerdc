@extends('layouts.admin')

@section('title', 'Notifications Push')
@section('header_title', 'Communication Live')
@section('page_title', 'Console Broadcast')
@section('page_subtitle', 'Envoyez des notifications en temps réel à l\'ensemble des utilisateurs de l\'écosystème ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Composer -->
        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-rdc-blue/5 rounded-full blur-[80px]"></div>
            <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-4">
                <i class="fas fa-paper-plane text-rdc-blue"></i> Nouveau Message Push
            </h4>
            <div class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Cible de diffusion</label>
                    <select class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold outline-none">
                        <option>Tous les utilisateurs</option>
                        <option>Artisans uniquement</option>
                        <option>Clients actifs</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Titre de la notification</label>
                    <input type="text" placeholder="Entrez le titre..." class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Message</label>
                    <textarea rows="4" placeholder="Tapez votre message ici..." class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold outline-none resize-none"></textarea>
                </div>
                <div class="pt-6">
                    <button class="w-full py-5 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200 hover:bg-rdc-blue transition-all">Envoyer maintenant</button>
                </div>
            </div>
        </div>

        <!-- Preview & Analytics -->
        <div class="space-y-8">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-48 h-12 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center text-[10px] font-black text-slate-300 uppercase tracking-widest mb-4">Aperçu Mobile</div>
                <div class="w-64 h-32 bg-slate-900 rounded-3xl p-4 text-left shadow-2xl">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-6 h-6 bg-rdc-blue rounded-md flex items-center justify-center text-[8px] text-white font-bold">SRDC</div>
                        <span class="text-[9px] font-black text-white/50 uppercase">ServiceRDC HQ</span>
                    </div>
                    <p class="text-[10px] font-black text-white mb-1">Boostez votre visibilité !</p>
                    <p class="text-[9px] text-white/40 leading-tight">Découvrez les nouvelles fonctionnalités premium disponibles sur votre dashboard.</p>
                </div>
            </div>
            
            <div class="bg-blue-50 p-8 rounded-[3rem] border border-blue-100">
                <h4 class="text-[10px] font-black text-rdc-blue uppercase tracking-widest mb-4 text-center">Taux d'ouverture global</h4>
                <div class="flex items-end justify-center gap-1 h-32">
                    @foreach([40, 60, 45, 80, 55, 70, 90] as $h)
                        <div class="flex-1 bg-rdc-blue rounded-full" style="height: {{ $h }}%"></div>
                    @endforeach
                </div>
                <p class="text-center mt-6 text-2xl font-black text-slate-900">74.8<span class="text-xs uppercase">%</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
