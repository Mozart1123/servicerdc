@extends('layouts.super-admin')

@section('header_title', 'Matrice des Variables d\'Environnement | ADN DU SYSTÈME')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Risk Banner -->
    <div class="bg-red-500 rounded-[3.5rem] p-12 text-white shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/10 rounded-full blur-[100px] animate-pulse"></div>
        <div class="relative z-10 flex items-center justify-between gap-12">
            <div class="flex items-center gap-10">
                <div class="w-24 h-24 rounded-[2.5rem] bg-white text-red-500 flex items-center justify-center text-5xl shadow-2xl">
                    <i class="fas fa-biohazard"></i>
                </div>
                <div>
                    <h2 class="text-4xl font-heading font-black tracking-tighter uppercase mb-4">ZONE DE DANGER CRITIQUE</h2>
                    <p class="text-white/80 font-medium leading-relaxed max-w-2xl">
                        Vous accédez à la structure génétique de l'univers SRDC. Une modification erronée ici peut entraîner une désintégration totale du système ou une corruption irréparable de la réalité digitale.
                    </p>
                </div>
            </div>
            <div class="hidden xl:flex flex-col items-center gap-2">
                <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 font-mono text-[10px] font-black uppercase tracking-widest leading-none">
                    Security Level: OMNI
                </div>
                <div class="px-6 py-3 bg-black/20 backdrop-blur-md rounded-2xl border border-white/5 font-mono text-[10px] font-black uppercase tracking-widest leading-none">
                    Logs: IMMUTABLES
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-10">
        <!-- Variable Sidebar (Categories) -->
        <div class="xl:col-span-1 space-y-4">
            @php
                $categories = [
                    ['id' => 'app', 'name' => 'Configuration Core', 'icon' => 'fas fa-dna', 'count' => 12, 'active' => true],
                    ['id' => 'db', 'name' => 'Cluster Database', 'icon' => 'fas fa-database', 'count' => 8],
                    ['id' => 'auth', 'name' => 'Nexus Auth & Social', 'icon' => 'fas fa-fingerprint', 'count' => 15],
                    ['id' => 'mail', 'name' => 'Communication Mail', 'icon' => 'fas fa-envelope-open-text', 'count' => 6],
                    ['id' => 'aws', 'name' => 'Ciel AWS & S3', 'icon' => 'fab fa-aws', 'count' => 10],
                    ['id' => 'extra', 'name' => 'Codes Secrets (Third-party)', 'icon' => 'fas fa-key', 'count' => 22]
                ];
            @endphp
            @foreach($categories as $cat)
            <button class="w-full flex items-center justify-between p-6 rounded-[2.2rem] border transition-all duration-300 group {{ $cat['active'] ?? false ? 'bg-slate-900 border-slate-900 text-white shadow-xl translate-x-4' : 'bg-white border-slate-100 text-slate-500 hover:bg-slate-50' }}">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $cat['active'] ?? false ? 'bg-white/10 text-amber-500' : 'bg-slate-50 text-slate-400 group-hover:text-slate-900' }}">
                        <i class="{{ $cat['icon'] }}"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-xs uppercase tracking-tight {{ $cat['active'] ?? false ? 'text-white' : 'text-slate-900' }}">{{ $cat['name'] }}</p>
                        <p class="text-[9px] font-mono opacity-60 uppercase">{{ $cat['count'] }} Variables</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-[10px] opacity-40 group-hover:translate-x-1 transition-transform"></i>
            </button>
            @endforeach
        </div>

        <!-- The Editor (Divine Matrix) -->
        <div class="xl:col-span-3 space-y-8">
            <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 flex items-center gap-3">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-mono font-bold text-slate-400 uppercase">Fichier .env Déverrouillé</span>
                </div>
                
                <div class="p-12 border-b border-slate-50 bg-slate-50/20">
                    <h3 class="text-2xl font-heading font-black text-slate-900 uppercase">Configuration Core</h3>
                    <p class="text-slate-400 text-sm mt-3">Paramètres vitaux pour l'application Laravel et le Kernel.</p>
                </div>

                <div class="p-12 bg-white space-y-8">
                    @php
                        $vars = [
                            ['key' => 'APP_NAME', 'value' => 'ServiceRDC Master', 'type' => 'TEXT', 'locked' => false],
                            ['key' => 'APP_ENV', 'value' => 'production', 'type' => 'ENUM', 'locked' => true],
                            ['key' => 'APP_KEY', 'value' => 'base64:ak3kYWaAgyEqh... [ENCRYPTED]', 'type' => 'SECRET', 'locked' => true],
                            ['key' => 'APP_DEBUG', 'value' => 'false', 'type' => 'BOOLEAN', 'locked' => false],
                            ['key' => 'APP_URL', 'value' => 'https://servicedrc.com', 'type' => 'URL', 'locked' => false],
                        ];
                    @endphp
                    @foreach($vars as $var)
                    <div class="group grid grid-cols-1 md:grid-cols-12 gap-8 items-center p-6 rounded-[2rem] border border-transparent hover:border-slate-100 hover:bg-slate-50/50 transition-all">
                        <div class="md:col-span-4">
                            <label class="text-[11px] font-mono font-black text-slate-900 tracking-wider flex items-center gap-3">
                                {{ $var['key'] }}
                                @if($var['locked'])
                                <i class="fas fa-lock text-[10px] text-red-400" title="Protégé par le Kernel"></i>
                                @endif
                            </label>
                            <span class="text-[9px] font-mono text-slate-400 lowercase">{{ $var['type'] }}</span>
                        </div>
                        <div class="md:col-span-6">
                            <div class="relative">
                                <input type="{{ str_contains($var['type'], 'SECRET') ? 'password' : 'text' }}" 
                                       value="{{ $var['value'] }}" 
                                       {{ $var['locked'] ? 'readonly' : '' }}
                                       class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-mono focus:ring-4 focus:ring-rdc-blue/10 focus:border-rdc-blue transition-all {{ $var['locked'] ? 'opacity-50 cursor-not-allowed bg-slate-50' : '' }}">
                            </div>
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:bg-white hover:text-amber-600 shadow-sm border border-transparent hover:border-slate-100 transition-all" title="Historique">
                                <i class="fas fa-history text-xs"></i>
                            </button>
                            <button class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:bg-white hover:text-rdc-blue shadow-sm border border-transparent hover:border-slate-100 transition-all" title="Copier">
                                <i class="far fa-copy text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="p-12 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
                    <button class="px-8 py-4 bg-white border border-slate-200 text-slate-500 font-bold rounded-2xl text-[10px] uppercase tracking-[0.2em] shadow-sm hover:bg-slate-100 transition-all">Annuler les modifs</button>
                    <button class="px-10 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-105 transition-all flex items-center gap-3">
                        <i class="fas fa-save"></i>
                        Pulsar les changements
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
