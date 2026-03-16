@extends('layouts.admin')

@section('title', 'Configuration Système')
@section('header_title', 'Paramètres Globaux')
@section('page_title', 'Cœur du Système')
@section('page_subtitle', 'Gérez les variables d\'environnement, les accès API et les préférences de la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Sidebar Navigation Settings -->
        <div class="lg:col-span-1 space-y-4 sm:space-y-6">
            <div class="bg-white p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2.5rem] border border-slate-100 shadow-sm">
                <nav class="flex sm:flex-col gap-2 overflow-x-auto sm:overflow-x-visible pb-2 sm:pb-0 no-scrollbar">
                    <a href="#" class="flex-none sm:flex-1 flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 bg-slate-900 text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest shadow-lg sm:shadow-xl shadow-slate-200">
                        <i class="fas fa-sliders-h text-xs sm:text-base"></i> Général
                    </a>
                    <a href="{{ route('admin.settings-hq.geo') }}" class="flex-none sm:flex-1 flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 text-slate-400 hover:text-rdc-blue transition-all font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-nowrap">
                        <i class="fas fa-map-location-dot text-xs sm:text-base"></i> Geo
                    </a>
                    <a href="{{ route('admin.settings-hq.api') }}" class="flex-none sm:flex-1 flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 text-slate-400 hover:text-rdc-blue transition-all font-black text-[9px] sm:text-[10px] uppercase tracking-widest text-nowrap">
                        <i class="fas fa-key text-xs sm:text-base"></i> API
                    </a>
                </nav>
            </div>
            
            <div class="bg-indigo-50 p-6 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] border border-indigo-100 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
                <h4 class="text-[10px] sm:text-xs font-black text-indigo-900 uppercase tracking-widest mb-3 sm:mb-4">Support</h4>
                <p class="text-[9px] sm:text-[11px] text-indigo-500 font-bold leading-relaxed mb-4 sm:mb-6 italic">Pour toute modification structurelle, contactez le Support.</p>
                <a href="{{ route('admin.support-hq.tickets') }}" class="inline-block px-5 sm:px-6 py-2.5 sm:py-3 bg-white text-indigo-600 font-black rounded-lg sm:rounded-xl text-[8px] sm:text-[9px] uppercase tracking-widest shadow-md sm:shadow-lg shadow-indigo-100 active:scale-95 transition-all">Support</a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8 sm:space-y-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6 sm:space-y-8">
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 flex items-center justify-center text-[8px] sm:text-[10px]">01</span>
                                Plateforme
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-1 sm:space-y-2">
                                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Nom</label>
                                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'ServiceRDC HQ' }}" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                </div>
                                <div class="space-y-1 sm:space-y-2">
                                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Email</label>
                                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'admin@servicerdc.cd' }}" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 flex items-center justify-center text-[8px] sm:text-[10px]">02</span>
                                Géo & Devises
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-1 sm:space-y-2">
                                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">FUSEAU HORAIRE</label>
                                    <select name="timezone" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none">
                                        <option value="Africa/Kinshasa" {{ ($settings['timezone'] ?? '') == 'Africa/Kinshasa' ? 'selected' : '' }}>Kinshasa (GMT+1)</option>
                                        <option value="Africa/Lubumbashi" {{ ($settings['timezone'] ?? '') == 'Africa/Lubumbashi' ? 'selected' : '' }}>Lubumbashi (GMT+2)</option>
                                    </select>
                                </div>
                                <div class="space-y-1 sm:space-y-2">
                                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">DEVISE</label>
                                    <select name="currency" class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold focus:ring-4 focus:ring-rdc-blue/10 transition-all outline-none font-mono">
                                        <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="CDF" {{ ($settings['currency'] ?? '') == 'CDF' ? 'selected' : '' }}>CDF (FC)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 sm:pt-10 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-10 sm:px-12 py-4 sm:py-5 bg-rdc-blue text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
