@extends($layout)

@section('title', 'Messages | ProConnect')
@section('header_title', 'Messages')
@section('header_subtitle', 'Échangez avec vos prestataires de services.')

{{--
    IMPORTANT : Cette page overridie la card blanche du layout pour prendre toute
    la hauteur disponible. On injecte directement dans 'client_content' mais on
    retire le padding par défaut via une classe négative sur le wrapper parent.
    Le layout injecte le contenu dans : <div class="bg-white rounded-2xl border ... p-6 sm:p-8">
    On contourne ce padding en utilisant -m-6 sm:-m-8 sur le wrapper interne.
--}}

@section($contentSection)
<div class="{{ $isClient ? 'overflow-hidden rounded-2xl' : 'bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden' }}" id="chat-page-wrapper" style="height: {{ $isClient ? 'calc(100vh - 220px)' : 'calc(100vh - 120px)' }}; min-height: 500px;">
    <div class="flex h-full">

        {{-- ═══════════════════════════════════════════════
             PANNEAU GAUCHE — Liste des conversations
        ═══════════════════════════════════════════════ --}}
        <aside id="conv-panel"
               class="flex flex-col bg-white border-r border-slate-100 transition-all duration-300
                      {{ request()->has('id') ? 'hidden md:flex w-0 md:w-80' : 'flex w-full md:w-80' }}">

            {{-- Header liste --}}
            <div class="px-4 py-4 border-b border-slate-100 shrink-0">
                <h2 class="font-bold text-slate-900 text-base mb-3">Conversations</h2>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Rechercher un contact..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg bg-slate-50 border border-slate-200 outline-none focus:ring-2 focus:ring-[#16a3b0]/20 focus:border-[#16a3b0]/40 text-sm transition-all">
                </div>
            </div>

            {{-- Liste --}}
            <div class="flex-1 overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: #E2E8F0 transparent;">
                @forelse($conversations as $conv)
                @php
                    $other   = $conv->otherUser(auth()->id());
                    $lastMsg = $conv->messages->first();
                    $isActive = isset($activeConversation) && $activeConversation->id == $conv->id;
                    $unread  = $conv->unreadCountFor(auth()->id());
                    $isMe    = $lastMsg && $lastMsg->sender_id == auth()->id();
                @endphp
                <a href="{{ route('user.messages.index', ['id' => $conv->id]) }}"
                   class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-50 transition-colors
                          {{ $isActive
                              ? 'bg-[#16a3b0]/8 border-l-[3px] border-l-[#16a3b0]'
                              : 'hover:bg-slate-50 border-l-[3px] border-l-transparent' }}">

                    {{-- Avatar avec indicateur en ligne --}}
                    <div class="relative shrink-0">
                        <img src="{{ $other->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($other->name ?? 'U').'&background=16a3b0&color=fff&size=80' }}"
                             class="w-12 h-12 rounded-full object-cover border border-slate-200" alt="{{ $other->name }}">
                        {{-- Indicateur en ligne (géré dynamiquement via JS) --}}
                        <span id="online-status-list-{{ $other->id }}" class="absolute bottom-0 right-0 w-3 h-3 bg-slate-300 border-2 border-white rounded-full transition-colors"></span>
                        {{-- Badge non lus --}}
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">{{ $unread }}</span>
                        @endif
                    </div>

                    {{-- Info conversation --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <h4 class="font-semibold text-slate-900 text-sm truncate {{ $unread > 0 ? 'font-bold' : '' }}">
                                {{ $other->name ?? 'Utilisateur' }}
                            </h4>
                            <span class="text-[10px] text-slate-400 shrink-0">
                                {{ $lastMsg ? $lastMsg->created_at->format('H:i') : '' }}
                            </span>
                        </div>
                        <p class="text-xs truncate {{ $unread > 0 ? 'font-semibold text-slate-800' : 'text-slate-500' }}">
                            @if($lastMsg)
                                @if($isMe)<span class="text-[#16a3b0]">Vous : </span>@endif
                                {{ $lastMsg->body }}
                            @else
                                <span class="italic text-slate-400">Commencer la discussion</span>
                            @endif
                        </p>
                        @if($conv->related_type && $conv->related_type !== 'general')
                        <span class="mt-1 inline-block px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-semibold uppercase rounded">
                            {{ $conv->related_type }}
                        </span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="flex flex-col items-center justify-center h-64 text-center p-6">
                    <div class="w-14 h-14 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mb-3 text-slate-300 text-2xl">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Aucune conversation</p>
                    <p class="text-xs text-slate-400 mt-1">Vos échanges avec les prestataires apparaîtront ici.</p>
                </div>
                @endforelse
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════════
             PANNEAU DROIT — Zone de conversation active
        ═══════════════════════════════════════════════ --}}
        <main id="chat-panel"
              class="flex-1 flex flex-col min-w-0 bg-slate-50/50
                     {{ request()->has('id') ? 'flex' : 'hidden md:flex' }}"
              style="min-height: 0;">

            @if(isset($activeConversation))
            @php $other = $activeConversation->otherUser(auth()->id()); @endphp

            {{-- ─── CHAT HEADER FIXE ─── --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-5 sm:py-3.5 bg-white border-b border-slate-100 shrink-0 shadow-sm">
                <div class="flex items-center gap-3">
                    {{-- Bouton retour (mobile) --}}
                    <a href="{{ route('user.messages.index') }}"
                       class="md:hidden flex items-center justify-center text-slate-500 hover:text-slate-700 transition-colors mr-1">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>

                    {{-- Avatar du contact --}}
                    <div class="shrink-0">
                        <img src="{{ $other->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($other->name ?? 'U').'&background=16a3b0&color=fff&size=80' }}"
                             class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover" alt="{{ $other->name }}">
                    </div>

                    {{-- Nom + statut --}}
                    <div class="flex flex-col">
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-tight">{{ $other->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span id="online-status-header-{{ $other->id }}" class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-slate-300 rounded-full transition-colors duration-300"></span>
                            <p id="online-text-header-{{ $other->id }}" class="text-[11px] sm:text-xs text-slate-400 font-medium leading-none">Hors ligne</p>
                        </div>
                    </div>
                </div>

                {{-- Référence (si applicable) --}}
                @if($activeConversation->related_type && $activeConversation->related_type !== 'general')
                <span class="hidden sm:inline-flex px-3 py-1 bg-slate-50 text-slate-500 text-xs font-medium rounded-lg border border-slate-200">
                    Réf : {{ ucfirst($activeConversation->related_type) }} #{{ $activeConversation->related_id }}
                </span>
                @endif
            </div>

            {{-- ─── BULLES DE MESSAGES (scrollable) ─── --}}
            <div class="chat-body"
                 id="chat-messages"
                 style="scrollbar-width: thin; scrollbar-color: #E2E8F0 transparent;">

                {{-- Séparateur début --}}
                <div class="flex justify-center mb-4">
                    <span class="px-4 py-1 bg-white border border-slate-100 rounded-full text-[11px] text-slate-500 font-medium shadow-sm">
                        Aujourd'hui
                    </span>
                </div>

                @foreach($messages as $msg)
                @php $isMe = $msg->sender_id == auth()->id(); @endphp

                <div class="bubble-wrapper flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} w-full mb-2">
                    <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[75%] {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">
                        
                        {{-- Avatar contact (messages reçus) --}}
                        @if(!$isMe)
                        <img src="{{ $other->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($other->name ?? 'U').'&background=16a3b0&color=fff&size=80' }}"
                             class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover shadow-sm" alt="">
                        @endif

                        {{-- Contenu du message --}}
                        <div class="min-w-0">
                            <div class="bubble shadow-sm {{ $isMe ? 'bg-[#16a3b0] text-white' : 'bg-white text-slate-800 border border-slate-200' }}">
                                @if($msg->body)
                                <p>{{ $msg->body }}</p>
                                @endif

                                @if($msg->attachment)
                                @php $ext = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)); @endphp
                                <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-slate-100' }}">
                                    @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
                                        <a href="{{ $msg->attachment_url }}" target="_blank" class="block rounded-lg overflow-hidden">
                                            <img src="{{ $msg->attachment_url }}" class="max-w-full h-auto hover:opacity-90 transition rounded-lg">
                                        </a>
                                    @else
                                        <a href="{{ $msg->attachment_url }}" target="_blank"
                                           class="flex items-center gap-2 p-2 bg-black/10 rounded-lg text-xs font-medium hover:bg-black/15 transition">
                                            <i class="fas fa-file-download"></i>
                                            <span class="truncate">Fichier {{ strtoupper($ext) }}</span>
                                        </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Heure + lu (placée sous la bulle) --}}
                    <div class="bubble-time {{ !$isMe ? 'ml-[44px] sm:ml-[48px]' : 'mr-1' }}">
                        {{ $msg->created_at->format('H:i') }}
                        @if($isMe) <i class="fas fa-check-double ml-0.5 {{ $msg->is_read ? 'text-[#16a3b0]' : 'text-slate-300' }}"></i> @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ─── BARRE DE SAISIE FIXE EN BAS ─── --}}
            <div class="shrink-0 bg-slate-50/50 border-t border-slate-100 px-3 py-3 sm:px-4 sm:py-4">
                <form action="{{ route('user.messages.send', $activeConversation->id) }}"
                      method="POST" enctype="multipart/form-data" id="message-form">
                    @csrf

                    {{-- Preview pièce jointe --}}
                    <div id="preview-bar" class="hidden mb-2 px-3 py-2 bg-white border border-slate-200 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2 text-[#16a3b0] text-xs font-medium">
                            <i class="fas fa-file"></i>
                            <span id="filename-preview" class="truncate max-w-xs"></span>
                        </div>
                        <button type="button"
                            onclick="document.querySelector('input[name=attachment]').value=''; document.getElementById('preview-bar').classList.add('hidden')"
                            class="text-slate-400 hover:text-red-500 transition-colors ml-2">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <div class="relative flex items-end bg-white border border-slate-200 rounded-[1.5rem] shadow-sm focus-within:ring-2 focus-within:ring-[#16a3b0]/20 focus-within:border-[#16a3b0]/40 transition-all p-1">
                        
                        {{-- Emoji Picker Container --}}
                        <div id="emoji-picker-container" class="absolute bottom-full left-0 mb-2 hidden z-50 shadow-xl rounded-xl border border-slate-200 overflow-hidden bg-white">
                            <emoji-picker class="light"></emoji-picker>
                        </div>

                        {{-- Boutons icônes (gauche) --}}
                        <div class="flex items-center gap-0.5 shrink-0 sm:ml-1">
                            {{-- Bouton Emoji --}}
                            <button type="button" id="emoji-button" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-[#16a3b0] transition-colors rounded-full">
                                <i class="far fa-smile text-xl"></i>
                            </button>

                            {{-- Bouton pièce jointe --}}
                            <label class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-[#16a3b0] transition-colors rounded-full cursor-pointer mb-0">
                                <i class="fas fa-paperclip text-[1.1rem]"></i>
                                <input type="file" name="attachment" class="hidden"
                                       onchange="document.getElementById('filename-preview').innerText = this.files[0].name; document.getElementById('preview-bar').classList.remove('hidden')">
                            </label>
                        </div>

                        {{-- Champ de texte --}}
                        <div class="flex-1 min-w-0">
                            <textarea id="message-textarea" name="content" placeholder="Écrire un message..." rows="1"
                                class="w-full py-3 px-2 bg-transparent text-sm sm:text-base outline-none resize-none"
                                style="max-height: 120px; min-height: 44px; line-height: 1.4;"
                                oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight, 120)+'px'"></textarea>
                        </div>

                        {{-- Bouton envoyer --}}
                        <div class="shrink-0 mr-0.5">
                            <button type="submit"
                                class="w-10 h-10 rounded-full bg-[#16a3b0] text-white hover:bg-[#138b96] transition-colors flex items-center justify-center shadow-sm">
                                <i class="fas fa-paper-plane text-sm -ml-0.5 mt-0.5"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Emoji Picker Component --}}
            <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

            {{-- Script principal : AJAX + Echo messages + Presence --}}
            <script type="module">
            (function () {
                // ── Fix layout Client : supprime le padding de la card ──────
                const clientCard = document.getElementById('client-main-card');
                if (clientCard) {
                    clientCard.style.setProperty('padding', '0', 'important');
                    clientCard.style.overflow = 'hidden';
                }

                // ── Adapte la hauteur du chat selon l'espace réel disponible ─
                const chatWrapper = document.getElementById('chat-page-wrapper');
                function adjustChatHeight() {
                    if (!chatWrapper) return;
                    const rect = chatWrapper.getBoundingClientRect();
                    const available = window.innerHeight - rect.top;
                    chatWrapper.style.height = Math.max(available - 16, 350) + 'px';
                }
                adjustChatHeight();
                window.addEventListener('resize', adjustChatHeight);

                // ── Helpers DOM ──────────────────────────────────────────
                const chatBox = document.getElementById('chat-messages');
                if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

                const form = document.getElementById('message-form');
                const ta   = document.querySelector('textarea[name=content]');

                // ── Emoji Picker Logic ───────────────────────────────────
                const emojiButton = document.getElementById('emoji-button');
                const emojiPickerContainer = document.getElementById('emoji-picker-container');
                const emojiPicker = document.querySelector('emoji-picker');

                if (emojiButton && emojiPickerContainer && ta) {
                    emojiButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        emojiPickerContainer.classList.toggle('hidden');
                    });

                    emojiPicker.addEventListener('emoji-click', event => {
                        const cursorPosition = ta.selectionStart;
                        const textBefore = ta.value.substring(0, cursorPosition);
                        const textAfter = ta.value.substring(cursorPosition);
                        ta.value = textBefore + event.detail.unicode + textAfter;
                        
                        ta.selectionStart = ta.selectionEnd = cursorPosition + event.detail.unicode.length;
                        ta.dispatchEvent(new Event('input')); // auto-resize
                        ta.focus();
                    });

                    document.addEventListener('click', (e) => {
                        if (!emojiPickerContainer.contains(e.target) && e.target !== emojiButton && !emojiButton.contains(e.target)) {
                            emojiPickerContainer.classList.add('hidden');
                        }
                    });
                }

                // ── Statut en ligne (updateOnlineStatus) ─────────────────
                function updateOnlineStatus(userId, isOnline) {
                    const listStatus   = document.getElementById(`online-status-list-${userId}`);
                    const headerStatus = document.getElementById(`online-status-header-${userId}`);
                    const headerText   = document.getElementById(`online-text-header-${userId}`);

                    [listStatus, headerStatus].forEach(el => {
                        if (!el) return;
                        el.classList.toggle('bg-emerald-500', isOnline);
                        el.classList.toggle('bg-slate-300', !isOnline);
                    });

                    if (headerText) {
                        headerText.textContent = isOnline ? 'En ligne' : 'Hors ligne';
                        headerText.classList.toggle('text-emerald-600', isOnline);
                        headerText.classList.toggle('text-slate-400', !isOnline);
                    }
                }

                // ── Presence Channel ─────────────────────────────────────
                if (window.Echo) {
                    window.Echo.join('presence-chat')
                        .here((users) => {
                            // Initialisation : tous les users déjà connectés sont en ligne
                            users.forEach(u => updateOnlineStatus(u.id, true));
                        })
                        .joining((user) => {
                            updateOnlineStatus(user.id, true);
                        })
                        .leaving((user) => {
                            updateOnlineStatus(user.id, false);
                        })
                        .error((err) => {
                            console.warn('[Presence] Erreur canal présence:', err);
                        });
                }

                // ── Enter pour envoyer ───────────────────────────────────
                if (ta) {
                    ta.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            if (this.value.trim() !== '') {
                                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                            }
                        }
                    });
                }

                // ── AJAX Submit ──────────────────────────────────────────
                if (form) {
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const btn = form.querySelector('button[type=submit]');
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

                        try {
                            const res = await fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            if (res.ok) {
                                const data = await res.json();
                                if (data.success) {
                                    appendMessage(data.message, true);
                                    form.reset();
                                    ta.style.height = 'auto';
                                    document.getElementById('preview-bar').classList.add('hidden');
                                }
                            }
                        } catch (err) {
                            console.error('[Chat] Erreur envoi:', err);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-paper-plane text-xs"></i>';
                        }
                    });
                }

                // ── Injecter une bulle dynamiquement ────────────────────
                function appendMessage(msg, isMe) {
                    if (!chatBox) return;
                    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const text = (msg.content || msg.body || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const fallbackInitial = (msg.sender && msg.sender.name) ? msg.sender.name.charAt(0).toUpperCase() : 'U';
                    const avatarSrc = (msg.sender && msg.sender.photo_url)
                        ? msg.sender.photo_url
                        : `https://ui-avatars.com/api/?name=${fallbackInitial}&background=16a3b0&color=fff&size=40`;

                    const html = `
                        <div class="bubble-wrapper flex flex-col ${isMe ? 'items-end' : 'items-start'} w-full mb-2 animate-fade-in-up">
                            <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[75%] ${isMe ? 'flex-row-reverse' : 'flex-row'}">
                                ${!isMe ? `<img src="${avatarSrc}" class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover shadow-sm" alt="">` : ''}
                                <div class="min-w-0">
                                    <div class="bubble shadow-sm ${isMe ? 'bg-[#16a3b0] text-white' : 'bg-white text-slate-800 border border-slate-200'}">
                                        <p>${text}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bubble-time ${!isMe ? 'ml-[44px] sm:ml-[48px]' : 'mr-1'}">
                                ${time}
                                ${isMe ? '<i class="fas fa-check-double ml-0.5 text-slate-300"></i>' : ''}
                            </div>
                        </div>`;

                    chatBox.insertAdjacentHTML('beforeend', html);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }

                // ── Echo : messages en temps réel ────────────────────────
                if (window.Echo) {
                    const conversationId = {{ $activeConversation->id }};
                    const myId = {{ auth()->id() }};

                    window.Echo.private(`chat.${conversationId}`)
                        .listen('MessageSent', (e) => {
                            if (e.message.sender_id !== myId) {
                                appendMessage(e.message, false);

                                fetch('{{ route("user.messages.read", $activeConversation->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                });
                            }
                        })
                        .error((err) => {
                            console.warn('[Echo] Erreur canal privé:', err);
                        });
                }

            })();
            </script>

            @else
            {{-- ─── ÉTAT VIDE (aucune conv sélectionnée) ─── --}}
            <div class="flex-1 flex flex-col items-center justify-center p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-white border border-slate-200 flex items-center justify-center mb-4 text-[#16a3b0] text-2xl shadow-sm">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-base mb-1">Sélectionnez une conversation</h3>
                <p class="text-sm text-slate-500 max-w-xs">
                    Choisissez un contact dans la liste pour afficher vos échanges.
                </p>
            </div>
            @endif

        </main>

    </div>
</div>

<style>
    /* ═══════════════════════════════════════════════════════
       Layout chat : structure flex correcte
    ═══════════════════════════════════════════════════════ */

    /* Client layout : supprime le padding + overflow de la card blanche */
    #client-main-card {
        padding: 0 !important;
        overflow: hidden !important;
    }

    /* Wrapper chat : prend toute la hauteur dispo */
    #chat-page-wrapper {
        display: flex;
        flex-direction: column;
    }

    /* Le panneau de droite (main) doit propager min-height:0
       pour que ses enfants flex puissent avoir overflow interne */
    #chat-panel {
        display: flex;
        flex-direction: column;
        min-height: 0;      /* CRITIQUE : sans ça, flex-child ne peut pas scroller */
        height: 100%;
    }

    /* Header : taille fixe, ne rétrécit pas */
    #chat-panel > div:first-child {
        flex-shrink: 0;
    }

    /* Zone scrollable des messages */
    .chat-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 1.25rem;  /* plus de marge droite/gauche */
        display: flex;
        flex-direction: column;
        align-items: flex-start; /* empêche les bulles de s'étirer sur toute la largeur */
        gap: 10px;
    }

    /* Barre de saisie : taille fixe, colle en bas */
    #chat-panel > div:last-of-type {
        flex-shrink: 0;
    }

    /* ═══════════════════════════════════════════════════════
       Bulles de message
    ═══════════════════════════════════════════════════════ */

    /* La bulle elle-même */
    .bubble {
        width: fit-content;
        padding: 10px 13px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.45;
        max-width: 100%;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    .bubble p {
        white-space: pre-wrap;
        margin: 0;
    }

    .bubble-time {
        font-size: 10.5px;
        color: #8296a1;
        margin-top: 3px;
    }

    /* Mobile */
    @media (max-width: 640px) {
        .chat-body { padding: 0.75rem 0.875rem; gap: 8px; }
        .bubble { font-size: 13.5px; padding: 8px 12px; }
    }

    /* ═══════════════════════════════════════════════════════
       Animations
    ═══════════════════════════════════════════════════════ */
    .animate-fade-in-up {
        animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
