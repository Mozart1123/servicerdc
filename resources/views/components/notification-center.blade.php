@props([
    'notifications',
    'total'         => 0,
    'unreadTotal'   => 0,
    'readTotal'     => 0,
    'activeStatus'  => 'all',
    'activeSearch'  => '',
    'activeCategory'=> '',
    'categoryOptions' => [],
    'indexRoute'    => 'user.notifications.index',
    'readAllRoute'  => 'user.notifications.read-all',   // route POST "Tout marquer"
    'readRoute'     => 'user.notifications.read',       // route POST "Marquer lu"
    'variant'       => 'rdc',
    'subtitle'      => "Restez informé de l'activité de votre compte.",
])

@php
    $accent       = $variant === 'super' ? 'var(--accent, #2563eb)' : '#29B6D1';
    $accentHover  = $variant === 'super' ? 'var(--accent-hover, #1d4ed8)' : '#159bb5';
    $accentSoft   = $variant === 'super' ? 'rgba(37, 99, 235, .08)' : 'rgba(41, 182, 209, .08)';
    $accentSofter = $variant === 'super' ? 'rgba(37, 99, 235, .04)' : 'rgba(41, 182, 209, .04)';
    $readUrlTemplate = route($readRoute, ['notification' => '__ID__']);
    $tabs = [
        'all'    => ['label' => 'Toutes',   'count' => $total],
        'unread' => ['label' => 'Non lues', 'count' => $unreadTotal],
        'read'   => ['label' => 'Lues',     'count' => $readTotal],
    ];
    $hasFilters    = $activeStatus !== 'all' || $activeSearch !== '' || $activeCategory !== '';
    $selectedInitial = optional($notifications->first())->id;
@endphp

<style>
    .notification-center {
        --n-accent: {!! $accent !!};
        --n-accent-hover: {!! $accentHover !!};
        --n-accent-soft: {!! $accentSoft !!};
        --n-accent-softer: {!! $accentSofter !!};
    }

    .notification-center .n-focus:focus-visible {
        outline: 3px solid var(--n-accent-soft);
        outline-offset: 3px;
    }
</style>

<div
    class="notification-center max-w-7xl mx-auto space-y-6 pb-20"
    x-data="{
        selectedId: {{ $selectedInitial ? (int) $selectedInitial : 'null' }},
        mobileDetailOpen: false,
        readIds: [],
        loading: false,
        readUrlTemplate: @js($readUrlTemplate),
        isUnread(id, initialUnread) {
            return initialUnread && !this.readIds.includes(id);
        },
        select(id, initialUnread) {
            this.selectedId = id;
            this.mobileDetailOpen = true;
            if (initialUnread) {
                this.markRead(id);
            }
        },
        markRead(id) {
            if (!this.readIds.includes(id)) {
                this.readIds.push(id);
            }

            fetch(this.readUrlTemplate.replace('__ID__', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || @js(csrf_token()),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(() => {});
        }
    }"
    @keydown.escape.window="mobileDetailOpen = false"
>
    @if(session('success'))
        <div class="px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 font-bold text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    <header class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
            <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background: var(--n-accent);">
                <i class="fas fa-bell"></i>
            </span>
            <div>
                <h1 class="text-[22px] md:text-2xl font-black text-slate-900 tracking-tight">Notifications</h1>
                <p class="text-sm text-slate-500 font-medium">{{ $subtitle }}</p>
            </div>
        </div>
    </header>

    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 md:p-5 space-y-4">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Filtres des notifications">
                @foreach($tabs as $status => $tab)
                    @php
                        $tabUrl = route($indexRoute, array_filter([
                            'status' => $status === 'all' ? null : $status,
                            'q' => $activeSearch ?: null,
                            'category' => $activeCategory ?: null,
                        ]));
                        $isActive = $activeStatus === $status;
                    @endphp
                    <a
                        href="{{ $tabUrl }}"
                        @click="loading = true"
                        aria-current="{{ $isActive ? 'page' : 'false' }}"
                        class="n-focus inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-black uppercase tracking-widest transition-all {{ $isActive ? 'text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                        style="{{ $isActive ? 'background: var(--n-accent);' : '' }}"
                    >
                        {{ $tab['label'] }}
                        <span class="{{ $isActive ? 'bg-white/20 text-white' : 'bg-white text-slate-500' }} rounded-full px-2 py-0.5 text-[10px]">{{ $tab['count'] }}</span>
                    </a>
                @endforeach
            </nav>

            @if($unreadTotal > 0)
                <form action="{{ route($readAllRoute) }}" method="POST">
                    @csrf
                    <button type="submit" class="n-focus inline-flex items-center justify-center gap-2 rounded-2xl border bg-white px-4 py-2.5 text-xs font-black uppercase tracking-widest transition-all hover:shadow-sm" style="border-color: var(--n-accent); color: var(--n-accent);">
                        <i class="fas fa-check-double"></i>
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        <form method="GET" action="{{ route($indexRoute) }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_260px]" @submit="loading = true">
            @if($activeStatus !== 'all')
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif
            <label class="relative block">
                <span class="sr-only">Rechercher une notification</span>
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="search"
                    name="q"
                    value="{{ $activeSearch }}"
                    placeholder="Rechercher une notification..."
                    class="n-focus w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:ring-0"
                >
            </label>
            <label class="relative block">
                <span class="sr-only">Filtrer par catégorie</span>
                <i class="fas fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select
                    name="category"
                    class="n-focus w-full appearance-none rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-10 text-sm font-bold text-slate-700 focus:border-slate-300 focus:ring-0"
                    @change="$el.form.requestSubmit()"
                >
                    <option value="">Toutes les catégories</option>
                    @foreach($categoryOptions as $category => $label)
                        <option value="{{ $category }}" @selected($activeCategory === $category)>{{ $label }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            </label>
        </form>
    </section>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(380px,.9fr)]">
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div x-show="loading" x-cloak class="divide-y divide-slate-100">
                @for($i = 0; $i < 4; $i++)
                    <div class="p-5 md:p-6 flex gap-4 animate-pulse">
                        <div class="w-10 h-10 rounded-full bg-slate-100"></div>
                        <div class="flex-1 space-y-3">
                            <div class="h-4 bg-slate-100 rounded w-2/3"></div>
                            <div class="h-3 bg-slate-100 rounded w-full"></div>
                            <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <div x-show="!loading">
                @forelse($notifications as $notification)
                    @php
                        $isUnread = ! $notification->is_read;
                        [$iconBg, $iconText, $icon] = $notification->iconClasses();
                    @endphp

                    <button
                        type="button"
                        class="n-focus group relative flex w-full items-start gap-4 border-b border-slate-100 p-5 text-left transition-colors last:border-b-0 hover:bg-slate-50"
                        :class="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }}) ? 'bg-[var(--n-accent-softer)]' : 'bg-white'"
                        @click="select({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                        @keydown.enter.prevent="select({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                    >
                        <span
                            x-show="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                            class="absolute left-0 top-6 h-9 w-1 rounded-r-full"
                            style="background: var(--n-accent);"
                        ></span>
                        <span class="{{ $iconBg }} {{ $iconText }} flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-base">
                            <i class="fas {{ $icon }}"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-start justify-between gap-4">
                                <span class="min-w-0">
                                    <span class="block truncate text-sm text-slate-900" :class="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }}) ? 'font-black' : 'font-semibold text-slate-600'">
                                        {{ $notification->title }}
                                    </span>
                                    <span class="mt-1 block truncate text-sm text-slate-500">{{ $notification->message }}</span>
                                </span>
                                <span class="shrink-0 text-[11px] font-bold text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                            <span class="mt-3 flex items-center justify-between gap-3">
                                <span
                                    x-show="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                    class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white"
                                    style="background: var(--n-accent);"
                                >Nouveau</span>
                                <span
                                    x-show="!isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                    class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500"
                                >Vu</span>
                                <i class="fas fa-chevron-right text-xs text-slate-300 transition-transform group-hover:translate-x-0.5"></i>
                            </span>
                        </span>
                    </button>
                @empty
                    <div class="py-24 text-center px-6">
                        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-3xl text-slate-200">
                            <i class="fas {{ $hasFilters ? 'fa-magnifying-glass' : 'fa-bell-slash' }}"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-800">{{ $hasFilters ? 'Aucun résultat' : 'Aucune notification' }}</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm text-slate-400">
                            {{ $hasFilters ? 'Aucune notification ne correspond à cette recherche ou à ce filtre.' : 'Vous êtes à jour. Les prochaines notifications apparaîtront ici.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @if($notifications->onFirstPage())
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-100 text-slate-300"><i class="fas fa-chevron-left text-xs"></i></span>
                        @else
                            <a class="n-focus flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50" href="{{ $notifications->previousPageUrl() }}"><i class="fas fa-chevron-left text-xs"></i></a>
                        @endif

                        @php
                            $lastRenderedPage = 0;
                        @endphp
                        @foreach($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                            @php
                                $shouldRenderPage = ! (
                                    $notifications->lastPage() > 7
                                    && abs($page - $notifications->currentPage()) > 2
                                    && ! in_array($page, [1, $notifications->lastPage()], true)
                                );
                            @endphp
                            @if($shouldRenderPage)
                                @if($lastRenderedPage && $page > $lastRenderedPage + 1)
                                    <span class="flex h-9 min-w-9 items-center justify-center rounded-full px-2 text-xs font-black text-slate-400">...</span>
                                @endif
                                @if($page === $notifications->currentPage())
                                    <span class="flex h-9 min-w-9 items-center justify-center rounded-full px-3 text-xs font-black text-white" style="background: var(--n-accent);">{{ $page }}</span>
                                @else
                                    <a class="n-focus flex h-9 min-w-9 items-center justify-center rounded-full border border-slate-200 px-3 text-xs font-black text-slate-500 hover:bg-slate-50" href="{{ $url }}">{{ $page }}</a>
                                @endif
                                @php
                                    $lastRenderedPage = $page;
                                @endphp
                            @endif
                        @endforeach

                        @if($notifications->hasMorePages())
                            <a class="n-focus flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50" href="{{ $notifications->nextPageUrl() }}"><i class="fas fa-chevron-right text-xs"></i></a>
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-100 text-slate-300"><i class="fas fa-chevron-right text-xs"></i></span>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <aside
            class="fixed inset-0 z-50 overflow-y-auto bg-white p-4 shadow-2xl lg:sticky lg:top-6 lg:z-auto lg:block lg:max-h-[calc(100vh-3rem)] lg:rounded-2xl lg:border lg:border-slate-100 lg:p-0 lg:shadow-sm"
            x-show="mobileDetailOpen || window.innerWidth >= 1024"
            x-transition
            x-cloak
        >
            <div class="lg:hidden mb-3 flex justify-end">
                <button type="button" class="n-focus flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500" @click="mobileDetailOpen = false">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    @php
                        $isUnread = ! $notification->is_read;
                        [$iconBg, $iconText, $icon] = $notification->iconClasses();
                        $actionLabel = $notification->action_url ? "Voir l'élément" : 'Aucune action';
                    @endphp
                    <article x-show="selectedId === {{ $notification->id }}" class="bg-white lg:rounded-2xl">
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
                            <div class="flex min-w-0 items-center gap-4">
                                <span class="{{ $iconBg }} {{ $iconText }} flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-xl">
                                    <i class="fas {{ $icon }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <span
                                        x-show="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                        class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white"
                                        style="background: var(--n-accent);"
                                    >Nouveau</span>
                                    <span
                                        x-show="!isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500"
                                    >Lu</span>
                                </div>
                            </div>
                            <button type="button" class="n-focus hidden h-9 w-9 items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 lg:flex" @click="selectedId = null">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>

                        <div class="space-y-5 p-5">
                            <div>
                                <h2 class="text-lg md:text-xl font-black leading-snug text-slate-900">{{ $notification->title }}</h2>
                                <p class="mt-2 text-xs font-bold text-slate-400">
                                    {{ $notification->created_at->diffForHumans() }} <span class="text-slate-300">•</span> {{ $notification->created_at->translatedFormat('j F Y à H:i') }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold leading-relaxed text-slate-600">
                                {{ \Illuminate\Support\Str::limit($notification->message, 150) }}
                            </div>

                            <section>
                                <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">Détails de la notification</h3>
                                <dl class="space-y-3">
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">Type</dt>
                                        <dd class="font-bold text-slate-800">{{ $notification->categoryLabel() }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">Statut</dt>
                                        <dd>
                                            <span x-show="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})" class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white" style="background: var(--n-accent);">Non lue</span>
                                            <span x-show="!isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})" class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Lue</span>
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">Destinataire</dt>
                                        <dd class="truncate font-bold text-slate-800">{{ auth()->user()->name ?? 'Utilisateur' }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">Expéditeur</dt>
                                        <dd class="truncate font-bold text-slate-800">{{ data_get($notification->data, 'sender_name', 'ProConnect') }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">Action</dt>
                                        <dd class="font-bold">
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="inline-flex items-center gap-1" style="color: var(--n-accent);">{{ $actionLabel }} <i class="fas fa-arrow-right text-[10px]"></i></a>
                                            @else
                                                <span class="text-slate-400">Aucune</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <dt class="text-slate-400">ID</dt>
                                        <dd class="font-mono text-xs font-bold text-slate-800">#{{ $notification->id }}</dd>
                                    </div>
                                </dl>
                            </section>

                            <section>
                                <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">Contenu du message</h3>
                                <div class="rounded-2xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-700">{{ $notification->message }}</div>
                            </section>

                            <div class="space-y-3 pt-1">
                                <button
                                    type="button"
                                    class="n-focus flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-white shadow-sm transition-all"
                                    style="background: var(--n-accent);"
                                    x-show="isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                    @click="markRead({{ $notification->id }})"
                                >
                                    <i class="fas fa-check"></i>
                                    Marquer comme lu
                                </button>
                                <div
                                    x-show="!isUnread({{ $notification->id }}, {{ $isUnread ? 'true' : 'false' }})"
                                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-500"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    Déjà lue
                                </div>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="n-focus flex w-full items-center justify-center gap-2 rounded-2xl border px-4 py-3 text-sm font-black transition-all" style="border-color: var(--n-accent); color: var(--n-accent);">
                                        {{ $actionLabel }}
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <div x-show="selectedId === null" class="hidden h-full min-h-[420px] flex-col items-center justify-center p-8 text-center lg:flex">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-2xl text-slate-300">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-700">Sélectionnez une notification</h3>
                    <p class="mt-2 max-w-xs text-sm text-slate-400">Son contenu complet apparaitra ici.</p>
                </div>
            @else
                <div class="hidden h-full min-h-[420px] flex-col items-center justify-center p-8 text-center lg:flex">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-2xl text-slate-300">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-700">Aucun detail disponible</h3>
                </div>
            @endif
        </aside>
    </div>
</div>
