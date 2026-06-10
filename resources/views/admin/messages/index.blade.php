@extends('layouts.admin')

@section('title', 'Messages Utilisateurs')
@section('header_title', 'Messages Utilisateurs')
@section('page_title', 'Messagerie')
@section('page_subtitle', 'Consultez toutes les conversations entre utilisateurs de la plateforme.')

@section('content')
<div class="space-y-8">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Conversations</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-comments"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $stats['today'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aujourd'hui</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $stats['messages'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Messages Total</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" class="flex gap-4">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
    </form>

    <!-- Conversations List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/20">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Toutes les Conversations</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($conversations as $conv)
            @php
                $lastMsg = $conv->messages->first();
                $userOne = $conv->userOne;
                $userTwo = $conv->userTwo;
            @endphp
            <a href="{{ route('admin.messages.show', $conv->id) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors group">
                <div class="flex -space-x-3 shrink-0">
                    <img src="{{ $userOne->photo_url }}" class="w-10 h-10 rounded-full border-2 border-white object-cover" alt="">
                    <img src="{{ $userTwo->photo_url }}" class="w-10 h-10 rounded-full border-2 border-white object-cover" alt="">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <h4 class="font-bold text-sm text-slate-900 truncate">
                            {{ $userOne->name ?? '—' }} <span class="text-slate-400 font-normal">↔</span> {{ $userTwo->name ?? '—' }}
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : $conv->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-500 truncate">
                        @if($lastMsg)
                            <span class="font-bold text-slate-700">{{ $lastMsg->sender->name ?? '?' }}:</span> {{ \Str::limit($lastMsg->body ?? $lastMsg->content ?? '', 80) }}
                        @else
                            <span class="italic">Aucun message</span>
                        @endif
                    </p>
                    @if($conv->related_type && $conv->related_type !== 'general')
                    <span class="inline-flex mt-1 px-2 py-0.5 bg-blue-50 text-rdc-blue text-[8px] font-black uppercase tracking-widest rounded border border-blue-100">{{ $conv->related_type }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-right text-slate-300 group-hover:text-rdc-blue transition-colors"></i>
            </a>
            @empty
            <div class="px-6 py-12 text-center text-slate-400">
                <i class="fas fa-comment-slash text-3xl mb-3 opacity-50"></i>
                <p class="text-sm font-bold uppercase tracking-wider">Aucune conversation</p>
            </div>
            @endforelse
        </div>

        @if($conversations->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $conversations->links() }}</div>
        @endif
    </div>
</div>
@endsection
