@extends('layouts.admin')

@section('title', 'Suggestion & Feedback')
@section('header_title', 'Voix de la Communauté')
@section('page_title', 'Boîte à Idées')
@section('page_subtitle', 'Consultez les suggestions d\'amélioration soumises par les utilisateurs pour faire évoluer ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/10">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Suggestions Récentes</h3>
            <div class="flex gap-2">
                <span class="px-4 py-2 bg-blue-50 text-rdc-blue text-[9px] font-black uppercase rounded-xl tracking-widest">Analytics Mode</span>
            </div>
        </div>
        
        <div class="p-20 text-center">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-3xl mb-6 mx-auto">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Aucune suggestion pour le moment</h4>
            <p class="text-slate-400 font-medium mt-2 max-w-sm mx-auto">La boîte à idées est vide. Les retours utilisateurs apparaîtront ici dès qu'ils seront soumis via l'interface frontale.</p>
        </div>
    </div>
</div>
@endsection
