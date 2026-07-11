@extends('layouts.admin')

@section('title', 'Messages Utilisateurs')
@section('header_title', 'Messages Utilisateurs')
@section('page_title', 'Messagerie')
@section('page_subtitle', 'Consultez toutes les conversations entre utilisateurs de la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-black text-slate-900 font-mono">{{ $stats['total'] }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Conversations</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 text-rdc-blue rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-comments"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-black text-slate-900 font-mono">{{ $stats['today'] }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Aujourd'hui</p>
                </div>
                <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-black text-slate-900 font-mono">{{ $stats['messages'] }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Messages Total</p>
                </div>
                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 group">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="{{ request('search') }}"
                    class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black focus:ring-4 focus:ring-rdc-blue/10 focus:bg-white transition-all outline-none">
            </div>
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-rdc-blue transition-all shadow-xl shadow-slate-200 shrink-0">Filtrer</button>
        </form>
    </div>

    <!-- Conversations List -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/20">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Toutes les Conversations</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($conversations as $conv)
            @php
                $lastMsg = $conv->messages->first();
                $userOne = $conv->userOne;
                $userTwo = $conv->userTwo;
            @endphp
            <a href="{{ route('admin.messages.show', $conv->id) }}" class="flex items-center gap-6 px-8 py-6 hover:bg-slate-50/50 transition-colors group">
                <div class="flex -space-x-4 shrink-0">
                    <img src="{{ $userOne->photo_url }}" class="w-12 h-12 rounded-2xl border-[3px] border-white object-cover shadow-sm relative z-10" alt="">
                    <img src="{{ $userTwo->photo_url }}" class="w-12 h-12 rounded-2xl border-[3px] border-white object-cover shadow-sm relative z-0" alt="">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-black text-sm sm:text-base text-slate-900 truncate">
                            {{ $userOne->name ?? '—' }} <span class="text-slate-300 font-normal mx-2"><i class="fas fa-right-left text-[10px]"></i></span> {{ $userTwo->name ?? '—' }}
                        </h4>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : $conv->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-500 truncate font-medium">
                        @if($lastMsg)
                            <span class="font-black text-slate-900">{{ $lastMsg->sender->name ?? '?' }}:</span> {{ \Str::limit($lastMsg->body ?? $lastMsg->content ?? '', 80) }}
                        @else
                            <span class="italic text-slate-400 font-bold uppercase tracking-widest text-[9px]">Aucun message</span>
                        @endif
                    </p>
                    @if($conv->related_type && $conv->related_type !== 'general')
                    <span class="inline-flex mt-2 px-3 py-1 bg-blue-50 text-rdc-blue text-[9px] font-black uppercase tracking-widest rounded-lg border border-blue-100">{{ $conv->related_type }}</span>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-300 group-hover:text-white group-hover:bg-rdc-blue group-hover:border-rdc-blue transition-all shadow-sm">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </div>
            </a>
            @empty
            <div class="px-8 py-20 text-center">
                <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner mx-auto">
                    <i class="fas fa-comment-slash"></i>
                </div>
                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucune conversation</p>
            </div>
            @endforelse
        </div>

        @if($conversations->hasPages())
        <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/50">{{ $conversations->links() }}</div>
        @endif
    </div>
</div>
@endsection
