@extends($layout)

@section('title', 'Mes Notifications')

@section($contentSection)
<div class="max-w-5xl mx-auto space-y-8 pb-20" x-data="{ openId: null, confirmDeleteId: null }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-down">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-3xl font-black text-slate-900 font-heading tracking-tight uppercase">Centre de Notifications</h2>
                @if($unreadTotal > 0)
                    <span class="bg-rdc-blue/10 text-rdc-blue text-[11px] font-black px-3 py-1 rounded-full uppercase tracking-wide">{{ $unreadTotal }} non lue{{ $unreadTotal > 1 ? 's' : '' }}</span>
                @endif
            </div>
            <p class="text-slate-500 text-sm font-medium mt-1 uppercase tracking-widest">Restez informé de vos activités sur ProConnect</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('user.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2 {{ $unreadTotal === 0 ? 'opacity-40 pointer-events-none' : '' }}">
                    <i class="fas fa-check-double text-emerald-500"></i> Tout marquer comme lu
                </button>
            </form>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-2xl">
        <a href="{{ route('user.notifications.index') }}" class="px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all {{ !request()->boolean('unread') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
            Toutes <span class="opacity-50">{{ $total }}</span>
        </a>
        <a href="{{ route('user.notifications.index', ['unread' => 1]) }}" class="px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all {{ request()->boolean('unread') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }}">
            Non lues <span class="opacity-50">{{ $unreadTotal }}</span>
        </a>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden" data-aos="fade-up">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($notifications as $n)
                    <x-notification-row :notification="$n" variant="rdc" />
                @endforeach
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="py-24 text-center px-6">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas {{ request()->boolean('unread') ? 'fa-circle-check' : 'fa-bell-slash' }} text-4xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase">{{ request()->boolean('unread') ? 'Tout est à jour' : 'Boîte vide' }}</h3>
                <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto">
                    {{ request()->boolean('unread') ? 'Vous avez lu toutes vos notifications.' : "Vous n'avez aucune notification pour le moment. Nous vous préviendrons dès qu'il y aura du nouveau !" }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
