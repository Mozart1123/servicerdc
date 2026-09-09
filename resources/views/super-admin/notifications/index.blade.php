@extends('layouts.super-admin')

@section('header_title', 'Centre de Notifications')

@section('content')
<div class="space-y-8 pb-20" x-data="{ openId: null, confirmDeleteId: null }">

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 font-bold text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-wrap">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                {{ $total }} notification(s)
            </p>
            @if($unreadTotal > 0)
                <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wide">{{ $unreadTotal }} non lue{{ $unreadTotal > 1 ? 's' : '' }}</span>
            @endif
        </div>
        <form action="{{ route('user.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="px-5 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2 {{ $unreadTotal === 0 ? 'opacity-40 pointer-events-none' : '' }}">
                <i class="fas fa-check-double text-emerald-500"></i> Tout marquer comme lu
            </button>
        </form>
    </div>

    <!-- Filter tabs -->
    <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-2xl">
        <a href="{{ route('super-admin.notifications.index') }}" class="px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all {{ !request()->boolean('unread') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
            Toutes <span class="opacity-50">{{ $total }}</span>
        </a>
        <a href="{{ route('super-admin.notifications.index', ['unread' => 1]) }}" class="px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all {{ request()->boolean('unread') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
            Non lues <span class="opacity-50">{{ $unreadTotal }}</span>
        </a>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        @forelse($notifications as $n)
            <x-notification-row :notification="$n" variant="super" />
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-200 flex items-center justify-center text-3xl mb-4 shadow-inner">
                    <i class="fas {{ request()->boolean('unread') ? 'fa-circle-check' : 'fa-bell-slash' }}"></i>
                </div>
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ request()->boolean('unread') ? 'Tout est à jour' : 'Aucune notification' }}</h4>
                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">
                    {{ request()->boolean('unread') ? 'Vous avez lu toutes vos notifications.' : 'Vous êtes à jour.' }}
                </p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="px-6 py-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
