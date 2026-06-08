@extends('layouts.user')

@section('title', 'Messages | ProConnect')
@section('header_title', 'Centre de messages')

@section('content')
<div class="space-y-8 pb-10">

    <!-- HERO -->
    <section class="bg-gradient-to-r from-rdc-blue to-rdc-dark-blue rounded-3xl p-8 text-white mb-6 shadow-sm" data-aos="fade-down">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <span class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-4 font-medium backdrop-blur-sm border border-white/20">
            <i class="fas fa-comments mr-2 text-rdc-yellow"></i>Messagerie ProConnect
          </span>
          <h2 class="text-3xl font-bold mb-2">Vos Conversations</h2>
          <p class="text-blue-100 max-w-xl text-md opacity-90 font-medium">
            Contactez vos clients, vos prestataires ou les recruteurs directement depuis cet espace sécurisé.
          </p>
        </div>

        <div class="bg-white/10 border border-white/20 rounded-2xl p-6 min-w-[200px] backdrop-blur-sm">
          <p class="text-xs text-blue-100 font-bold uppercase tracking-widest mb-1">Messages non lus</p>
          <h3 class="text-4xl font-black text-rdc-yellow leading-none">{{ $unreadCount ?? 0 }}</h3>
        </div>
      </div>
    </section>

    <!-- MESSAGING LAYOUT -->
    <section class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden h-[75vh]" data-aos="fade-up" data-aos-delay="100">
      <div class="grid grid-cols-1 lg:grid-cols-4 h-full">

        <!-- CONVERSATIONS LIST -->
        <aside class="col-span-1 border-r border-slate-100 bg-slate-50/50 flex flex-col h-full overflow-hidden {{ request()->has('id') ? 'hidden lg:flex' : 'flex' }}">
          
          <!-- Search Header -->
          <div class="p-4 bg-white border-b border-slate-100">
            <div class="relative group">
              <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
              <input type="text" placeholder="Rechercher..."
                class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border-none outline-none focus:ring-2 focus:ring-rdc-blue/20 text-sm transition-all">
            </div>
          </div>
          
          <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($conversations as $conv)
                @php
                    $other = $conv->otherUser(auth()->id());
                    $lastMsg = $conv->messages->first();
                    $isActive = isset($activeConversation) && $activeConversation->id == $conv->id;
                    $unread = $conv->unreadCountFor(auth()->id());
                @endphp
                <a href="{{ route('user.messages.index', ['id' => $conv->id]) }}" 
                   class="flex items-center gap-3 p-4 border-b border-slate-100 transition-all hover:bg-slate-100 {{ $isActive ? 'bg-cyan-50/50 border-l-4 border-l-rdc-blue shadow-inner' : 'border-l-4 border-l-transparent' }}">
                    <div class="relative shrink-0">
                        <img src="{{ $other->photo_url ?? 'https://ui-avatars.com/api/?name=User' }}" 
                             class="w-12 h-12 rounded-2xl object-cover border border-slate-200" alt="">
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rdc-red text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm">{{ $unread }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <h4 class="font-bold text-slate-900 truncate text-sm">{{ $other->name ?? 'Utilisateur' }}</h4>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $lastMsg ? $lastMsg->created_at->format('H:i') : '' }}</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate {{ $unread > 0 ? 'font-bold text-slate-900' : '' }}">
                            @if($lastMsg)
                                @if($lastMsg->sender_id == auth()->id()) <span class="text-rdc-blue mr-1">Vous:</span> @endif
                                {{ $lastMsg->body }}
                            @else
                                <span class="italic">Commencer la discussion</span>
                            @endif
                        </p>
                        @if($conv->related_type && $conv->related_type !== 'general')
                        <div class="mt-1">
                            <span class="px-2 py-0.5 bg-blue-50 text-rdc-blue text-[8px] font-black uppercase tracking-widest rounded border border-blue-100">
                                {{ $conv->related_type }}
                            </span>
                        </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-comment-slash text-3xl mb-3 opacity-50"></i>
                    <p class="text-xs font-bold uppercase tracking-wider">Aucune discussion</p>
                </div>
            @endforelse
          </div>
        </aside>

        <!-- CHAT AREA -->
        <main class="col-span-3 flex flex-col bg-white h-full relative overflow-hidden {{ request()->has('id') ? 'flex' : 'hidden lg:flex' }}">

          @if(isset($activeConversation))
          @php $other = $activeConversation->otherUser(auth()->id()); @endphp
          
          <!-- Chat Header -->
          <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white/95 backdrop-blur-sm z-10 sticky top-0 shrink-0">
            <div class="flex items-center gap-3">
              <a href="{{ route('user.messages.index') }}" class="lg:hidden w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rdc-blue">
                <i class="fas fa-arrow-left"></i>
              </a>
              <div class="relative shrink-0">
                <img src="{{ $other->photo_url }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100 shadow-sm" />
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 leading-none mb-1">{{ $other->name }}</h3>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">En ligne</span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue/10 hover:text-rdc-blue transition flex items-center justify-center">
                    <i class="fas fa-phone-alt text-xs"></i>
                </button>
                <div class="w-px h-6 bg-slate-100 mx-1"></div>
                @if($activeConversation->related_type)
                <span class="hidden sm:inline-flex px-3 py-1.5 bg-slate-50 text-slate-500 text-[10px] font-black rounded-xl uppercase tracking-widest border border-slate-100">
                    Ref: {{ $activeConversation->related_type }} #{{ $activeConversation->related_id }}
                </span>
                @endif
            </div>
          </div>

          <!-- Messages -->
          <div class="flex-1 p-6 bg-slate-50/50 overflow-y-auto space-y-4 custom-scrollbar" id="chat-messages">
            <div class="flex justify-center mb-8">
                <span class="px-4 py-1.5 bg-white border border-slate-200 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest shadow-sm">
                    Début de sécurisé ProConnect
                </span>
            </div>

            @foreach($messages as $msg)
                @php $isMe = $msg->sender_id == auth()->id(); @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} group items-end gap-2 px-1">
                    @if(!$isMe)
                    <img src="{{ $other->photo_url }}" class="w-6 h-6 rounded-lg object-cover border border-slate-200 mb-1" />
                    @endif

                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[80%] sm:max-w-md">
                        <div class="p-3.5 {{ $isMe ? 'bg-rdc-blue text-white rounded-2xl rounded-br-sm shadow-md shadow-blue-500/10' : 'bg-white text-slate-700 border border-slate-100 rounded-2xl rounded-bl-sm shadow-sm' }} relative">
                            {{-- Content --}}
                            @if($msg->body)
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->body }}</p>
                            @endif

                            {{-- Attachment --}}
                            @if($msg->attachment)
                            <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-slate-100' }}">
                                @php $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION); @endphp
                                @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                                    <a href="{{ $msg->attachment_url }}" target="_blank" class="block rounded-lg overflow-hidden border {{ $isMe ? 'border-white/30' : 'border-slate-200' }}">
                                        <img src="{{ $msg->attachment_url }}" class="max-w-full h-auto hover:opacity-90 transition" />
                                    </a>
                                @else
                                    <a href="{{ $msg->attachment_url }}" target="_blank" class="flex items-center gap-2 p-2 bg-black/5 rounded-lg text-xs font-bold hover:bg-black/10 transition">
                                        <i class="fas fa-file-download text-lg"></i>
                                        <span class="truncate">Fichier {{ strtoupper($ext) }}</span>
                                    </a>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-1 px-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $msg->created_at->format('H:i') }}</span>
                            @if($isMe)
                            <i class="fas fa-check-double text-[10px] {{ $msg->is_read ? 'text-rdc-blue' : 'text-slate-300' }}"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
          </div>

          <!-- Input area -->
          <div class="p-4 border-t border-slate-100 bg-white shrink-0">
              <form action="{{ route('user.messages.send', $activeConversation->id) }}" method="POST" enctype="multipart/form-data" id="message-form">
                @csrf
                <div class="relative flex items-center gap-2">
                    {{-- Attachment Toggle --}}
                    <label class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue/10 hover:text-rdc-blue transition flex items-center justify-center cursor-pointer group">
                        <i class="fas fa-paperclip text-sm group-hover:rotate-12 transition"></i>
                        <input type="file" name="attachment" class="hidden" onchange="document.getElementById('filename-preview').innerText = this.files[0].name; document.getElementById('preview-bar').classList.remove('hidden')">
                    </label>

                    {{-- Text Field --}}
                    <div class="flex-1 relative">
                        <textarea name="content" placeholder="Écrire un message..." rows="1"
                            class="w-full pl-4 pr-12 py-3 bg-slate-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-rdc-blue/20 resize-none max-h-32"
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg bg-rdc-blue text-white hover:bg-rdc-blue-dark transition-all transform hover:scale-105 flex items-center justify-center shadow-md">
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Attachment Preview --}}
                <div id="preview-bar" class="hidden mt-2 p-2 bg-blue-50 rounded-lg border border-blue-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-blue-700 text-[10px] font-black uppercase">
                        <i class="fas fa-file"></i>
                        <span id="filename-preview"></span>
                    </div>
                    <button type="button" onclick="document.querySelector('input[name=attachment]').value=''; document.getElementById('preview-bar').classList.add('hidden')" class="text-blue-400 hover:text-red-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
              </form>
          </div>

          <script>
            // Scroll to bottom
            const chatBox = document.getElementById('chat-messages');
            chatBox.scrollTop = chatBox.scrollHeight;

            // Submit on enter
            document.querySelector('textarea[name=content]').addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if(this.value.trim() !== '' || document.querySelector('input[name=attachment]').value !== '') {
                        document.getElementById('message-form').submit();
                    }
                }
            });
          </script>

          @else
          <!-- Empty State -->
          <div class="flex-1 flex flex-col items-center justify-center bg-slate-50/30 p-10 text-center">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-xl shadow-slate-200/50 mb-6 border border-slate-100">
                <i class="fas fa-comment-medical text-3xl text-rdc-blue"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Sélectionnez une discussion</h3>
            <p class="text-sm text-slate-400 max-w-xs leading-relaxed">
                Choisissez un contact à gauche pour commencer une discussion instantanée sécurisée.
            </p>
          </div>
          @endif

        </main>
      </div>
    </section>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #CBD5E1; }
</style>
@endsection
