@props(['notification', 'variant' => 'rdc'])

{{--
    One notification row + its detail modal, shared by the client/artisan,
    admin and super-admin notification centers so all three stay visually
    and behaviorally in sync.

    Expects an ancestor element declaring
    `x-data="{ openId: null, confirmDeleteId: null }"` (each index view
    declares this once, on the list wrapper) — Alpine resolves scope from
    the rendered DOM regardless of Blade component boundaries, so every
    row here reads/writes that same shared state.

    `variant` picks the accent color used for the unread indicator, the
    "Nouveau" badge and the active look — 'rdc' (default, rdc-blue) for the
    client/artisan and admin panels, 'super' (blue-600) for the super-admin
    panel, which keeps its own distinct accent rather than rdc-blue.
--}}

@php
    $n = $notification;
    $unread = ! $n->is_read;

    $isSuper = $variant === 'super';
    $accentBorder = $isSuper ? 'border-blue-600' : 'border-rdc-blue';
    $accentBg     = $isSuper ? 'bg-blue-50/70' : 'bg-rdc-blue/5';
    $accentBadge  = $isSuper ? 'bg-blue-600' : 'bg-rdc-blue';
    $accentHover  = $isSuper ? 'hover:bg-blue-600' : 'hover:bg-rdc-blue';
    $accentText   = $isSuper ? 'text-blue-600' : 'text-rdc-blue';

    [$iconBg, $iconText] = $n->iconClasses();
@endphp

<div class="flex items-start gap-4 md:gap-6 p-6 md:p-8 border-b border-slate-50 last:border-b-0 border-l-4 transition-colors {{ $unread ? "$accentBg $accentBorder" : 'border-transparent' }}">
    <x-notification-icon :notification="$n" />

    <div class="flex-1 min-w-0">
        <div class="cursor-pointer group/open" @click="openId = {{ $n->id }}">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 min-w-0">
                    <h4 class="font-black text-slate-900 text-sm md:text-base truncate group-hover/open:{{ $accentText }} transition-colors">{{ $n->title }}</h4>
                    @if($unread)
                        <span class="{{ $accentBadge }} text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wide shrink-0">Nouveau</span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter whitespace-nowrap">{{ $n->created_at->diffForHumans() }}</span>
                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover/open:translate-x-0.5 transition-transform"></i>
                </div>
            </div>
            <p class="text-slate-600 text-sm leading-relaxed mt-1.5 line-clamp-2">{{ $n->message }}</p>
        </div>

        <div class="flex items-center gap-2 mt-4" x-show="confirmDeleteId !== {{ $n->id }}">
            @if($n->action_url)
                <a href="{{ $n->action_url }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest {{ $accentHover }} transition-all">
                    Voir l'élément
                </a>
            @endif

            @if($unread)
                <form action="{{ route('user.notifications.read', $n->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Marquer comme lu">
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </form>
            @endif

            <button type="button" @click="confirmDeleteId = {{ $n->id }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors" title="Supprimer">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>

        <div class="flex items-center gap-3 mt-4 bg-red-50 rounded-xl px-4 py-2.5" x-show="confirmDeleteId === {{ $n->id }}" x-cloak>
            <span class="text-[11px] font-bold text-red-700 flex-1">Supprimer cette notification ?</span>
            <button type="button" @click="confirmDeleteId = null" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-600">Annuler</button>
            <form action="{{ route('user.notifications.destroy', $n->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase">Confirmer</button>
            </form>
        </div>
    </div>
</div>

{{-- Detail view: full, un-truncated content --}}
<div
    x-show="openId === {{ $n->id }}"
    x-cloak
    @click="openId = null"
    @keydown.escape.window="openId = null"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4 sm:p-6"
>
    <div @click.stop
         x-show="openId === {{ $n->id }}"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="bg-white rounded-[2rem] shadow-2xl w-full max-w-xl overflow-hidden">

        <div class="flex items-start justify-between gap-4 p-6 sm:p-8 pb-6">
            <div class="flex items-center gap-4 min-w-0">
                <x-notification-icon :notification="$n" size="lg" />
                <div class="min-w-0">
                    <span class="inline-block {{ $iconBg }} {{ $iconText }} text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full mb-2">{{ $n->categoryLabel() }}</span>
                    <h3 class="font-black text-slate-900 text-lg sm:text-xl leading-snug">{{ $n->title }}</h3>
                </div>
            </div>
            <button type="button" @click="openId = null" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 rounded-full text-slate-500 transition-colors shrink-0">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="px-6 sm:px-8 pb-7">
            <p class="text-xs font-bold text-slate-400 flex items-center gap-2 mb-4">
                <i class="far fa-clock"></i>
                <span>{{ $n->created_at->translatedFormat('j F Y à H:i') }} <span class="text-slate-300">·</span> {{ $n->created_at->diffForHumans() }}</span>
            </p>
            <p class="text-slate-700 text-base leading-relaxed">{{ $n->message }}</p>
        </div>

        <div class="flex items-center justify-between gap-3 px-6 sm:px-8 py-6 bg-slate-50/70 border-t border-slate-100" x-show="confirmDeleteId !== {{ $n->id }}">
            <button type="button" @click="confirmDeleteId = {{ $n->id }}" class="flex items-center gap-2 text-[12px] font-bold text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-trash text-xs"></i> Supprimer
            </button>
            <div class="flex items-center gap-3">
                <button type="button" @click="openId = null" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl text-[11px] font-black uppercase tracking-widest text-slate-600 transition-colors">Fermer</button>
                @if($n->action_url)
                    <a href="{{ $n->action_url }}" class="px-5 py-2.5 {{ $accentBadge }} text-white rounded-xl text-[11px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-md">Voir l'élément</a>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 px-6 sm:px-8 py-6 bg-red-50 border-t border-red-100" x-show="confirmDeleteId === {{ $n->id }}" x-cloak>
            <span class="text-[12px] font-bold text-red-700 flex-1">Supprimer définitivement cette notification ?</span>
            <button type="button" @click="confirmDeleteId = null" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-600">Annuler</button>
            <form action="{{ route('user.notifications.destroy', $n->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase">Confirmer</button>
            </form>
        </div>
    </div>
</div>
