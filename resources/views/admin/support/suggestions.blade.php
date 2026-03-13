@extends('layouts.admin')

@section('title', 'Suggestion & Feedback')
@section('header_title', 'Voix de la Communauté')
@section('page_title', 'Boîte à Idées')
@section('page_subtitle', 'Consultez les suggestions d\'amélioration soumises par les utilisateurs pour faire évoluer ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/10 gap-4">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Suggestions</h3>
            <div class="hidden sm:flex gap-2">
                <span class="px-4 py-2 bg-blue-50 text-rdc-blue text-[9px] font-black uppercase rounded-xl tracking-widest">Analytics</span>
            </div>
        </div>
        
        <div class="p-10 sm:p-20 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 mx-auto">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h4 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">Vide</h4>
            <p class="text-xs sm:text-sm text-slate-400 font-medium mt-2 max-w-sm mx-auto leading-relaxed">Les suggestions d'amélioration soumises par les utilisateurs apparaîtront ici.</p>
        </div>
    </div>
</div>
@endsection
