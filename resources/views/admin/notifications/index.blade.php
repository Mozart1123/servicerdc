@extends('layouts.admin')

@section('title', 'Notifications')
@section('header_title', 'Notifications')
@section('page_title', 'Centre de Notifications')
@section('page_subtitle', 'Retrouvez ici toute l\'activité qui vous concerne sur ProConnect.')

@section('content')
<div class="space-y-8 pb-20">

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 font-bold text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
            {{ $notifications->total() }} notification(s)
        </p>
        @if($notifications->total() > 0)
            <form action="{{ route('user.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-check-double text-emerald-500"></i> Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
            <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-50 last:border-b-0 hover:bg-slate-50/40 transition-colors {{ !$notif->is_read ? 'bg-sky-50/40' : '' }}">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas {{ $notif->is_read ? 'fa-bell-slash' : 'fa-bell' }}"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900">
                        {{ $notif->title }}
                        @if(!$notif->is_read)
                            <span class="inline-block w-2 h-2 bg-rdc-blue rounded-full ml-1 align-middle"></span>
                        @endif
                    </p>
                    <p class="text-sm text-slate-500 mt-1">{{ $notif->message }}</p>

                    <div class="flex flex-wrap items-center gap-3 mt-3">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $notif->created_at->diffForHumans() }}</span>

                        @if($notif->action_url)
                            <a href="{{ $notif->action_url }}" class="px-3 py-1.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-rdc-blue transition-all">
                                Voir l'élément
                            </a>
                        @endif

                        @if(!$notif->is_read)
                            <form action="{{ route('user.notifications.read', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[9px] font-black text-emerald-600 uppercase tracking-widest hover:underline">
                                    Marquer comme lu
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('user.notifications.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Supprimer cette notification ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[9px] font-black text-red-400 uppercase tracking-widest hover:text-red-600">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-200 flex items-center justify-center text-3xl mb-4 shadow-inner">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucune notification</h4>
                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">
                    Vous êtes à jour.
                </p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="px-6 py-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
