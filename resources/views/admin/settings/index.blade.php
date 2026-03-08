@extends('layouts.admin')

@section('title', 'Configuration Système')
@section('header_title', 'Paramètres Globaux')
@section('page_title', 'Cœur du Système')
@section('page_subtitle', 'Gérez les variables d\'environnement, les accès API et les préférences de la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Navigation Settings -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <nav class="space-y-2">
                    <a href="#" class="flex items-center gap-4 px-6 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200">
                        <i class="fas fa-sliders-h"></i> Général
                    </a>
                    <a href="{{ route('admin.settings-hq.geo') }}" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-rdc-blue transition-all font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-map-location-dot"></i> Geo-Fencing
                    </a>
                    <a href="{{ route('admin.settings-hq.api') }}" class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-rdc-blue transition-all font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-key"></i> Clés API
                    </a>
                </nav>
            </div>
            
            <div class="bg-indigo-50 p-8 rounded-[2.5rem] border border-indigo-100 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
                <h4 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-4">Besoin d'aide ?</h4>
                <p class="text-[11px] text-indigo-500 font-bold leading-relaxed mb-6 italic">Pour toute modification structurelle de la base de données, contactez le Support Technique.</p>
                <a href="{{ route('admin.support-hq.tickets') }}" class="inline-block px-6 py-3 bg-white text-indigo-600 font-black rounded-xl text-[9px] uppercase tracking-widest shadow-lg shadow-indigo-100">Contacter le dev</a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-[10px]">01</span>
                                Informations Plateforme
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nom de la Plateforme</label>
                                    <input type="text" name="app_name" value="ServiceRDC HQ" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Email de Contact</label>
                                    <input type="email" name="contact_email" value="admin@servicerdc.cd" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-[10px]">02</span>
                                Localisation & Devises
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Fuseau Horaire</label>
                                    <select name="timezone" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="Africa/Kinshasa">Africa/Kinshasa (GMT+1)</option>
                                        <option value="Africa/Lubumbashi">Africa/Lubumbashi (GMT+2)</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Devise Principale</label>
                                    <select name="currency" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none font-mono">
                                        <option value="USD">USD ($)</option>
                                        <option value="CDF">CDF (FC)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="px-12 py-5 bg-rdc-blue text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/30 hover:scale-105 transition-all">
                            Enregistrer la Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
