@extends('layouts.admin')

@section('title', 'Suggestion & Feedback')
@section('header_title', 'Voix de la Communauté')
@section('page_title', 'Boîte à Idées')
@section('page_subtitle', 'Consultez les suggestions d\'amélioration soumises par les utilisateurs pour faire évoluer ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    toggleStatus(id) {
        fetch(`/admin/support-hq/suggestions/${id}/toggle`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}">
    <div class="bg-white rounded-[2rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/10 gap-4">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Suggestions de la Communauté</h3>
            <div class="hidden sm:flex gap-2">
                <span class="px-4 py-2 bg-blue-50 text-rdc-blue text-[9px] font-black uppercase rounded-xl tracking-widest">{{ $suggestions->count() }} Idées</span>
            </div>
        </div>
        
        <div class="divide-y divide-slate-50">
            @forelse($suggestions as $suggestion)
                <div class="px-8 py-8 flex items-start gap-6 group hover:bg-slate-50/50 transition-colors">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $suggestion->created_at->format('d M Y') }}</span>
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest
                                    {{ $suggestion->status == 'pending' ? 'bg-amber-50 text-amber-600' : ($suggestion->status == 'reviewed' ? 'bg-blue-50 text-rdc-blue' : 'bg-emerald-50 text-emerald-600') }}">
                                    {{ $suggestion->status }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">{{ $suggestion->content }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-900 uppercase">{{ $suggestion->user->name ?? 'Anonyme' }}</span>
                            </div>
                            <button @click="toggleStatus({{ $suggestion->id }})" class="text-[9px] font-black text-rdc-blue uppercase tracking-widest hover:underline">Changer Statut</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 sm:p-20 text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 mx-auto text-opacity-40">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="text-lg sm:text-xl font-black text-slate-900/20 uppercase tracking-tight">Aucune suggestion</h4>
                    <p class="text-xs sm:text-sm text-slate-400 font-medium mt-2 max-w-sm mx-auto leading-relaxed">Les idées d'amélioration soumises par les utilisateurs apparaîtront ici.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
