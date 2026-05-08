@extends('layouts.admin')

@section('title', 'Exportations Massives')
@section('header_title', 'Extracteur de Données')
@section('page_title', 'Exports Système')
@section('page_subtitle', 'Exportez vos bases de données utilisateurs, services et transactions en formats standards.')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-20 rounded-[3rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 mb-6">
            <i class="fas fa-file-export text-3xl"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Exports Massifs</h2>
        <p class="text-slate-500 max-w-md mx-auto">Ici vous pouvez générer des exports CSV/Excel complets. Sélectionnez le module ci-dessous.</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-12 w-full max-w-2xl">
            <a href="{{ route('admin.reports-hq.export.users') }}" class="p-10 bg-white border border-slate-100 rounded-[2.5rem] hover:border-rdc-blue hover:shadow-2xl hover:shadow-blue-500/10 transition-all cursor-pointer group flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-50 text-rdc-blue rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-900">Base Utilisateurs</h4>
                <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Export complet au format CSV</p>
            </a>
            
            <a href="{{ route('admin.reports-hq.export.services') }}" class="p-10 bg-white border border-slate-100 rounded-[2.5rem] hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all cursor-pointer group flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-900">Catalogue Services</h4>
                <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Export complet au format CSV</p>
            </a>
        </div>
    </div>
</div>
@endsection
