@extends('layouts.admin')

@section('title', 'Conversation #' . $conversation->id)
@section('header_title', 'Conversation #' . $conversation->id)
@section('page_title', 'Détail Conversation')
@section('page_subtitle', $conversation->userOne->name . ' ↔ ' . $conversation->userTwo->name)

@section('content')
<div class="space-y-8 max-w-4xl">
    <!-- Participants -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Participants</h3>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="{{ $conversation->userOne->photo_url }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" alt="">
                <div>
                    <p class="font-bold text-sm text-slate-900">{{ $conversation->userOne->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $conversation->userOne->user_type ?? 'user' }}</p>
                </div>
            </div>
            <div class="text-slate-300 text-2xl">↔</div>
            <div class="flex items-center gap-3">
                <img src="{{ $conversation->userTwo->photo_url }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" alt="">
                <div>
                    <p class="font-bold text-sm text-slate-900">{{ $conversation->userTwo->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $conversation->userTwo->user_type ?? 'user' }}</p>
                </div>
            </div>
            @if($conversation->related_type && $conversation->related_type !== 'general')
            <div class="ml-auto">
                <span class="px-3 py-1.5 bg-blue-50 text-rdc-blue text-[10px] font-black uppercase tracking-widest rounded-xl border border-blue-100">
                    {{ $conversation->related_type }} #{{ $conversation->related_id }}
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Messages -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/20 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Messages ({{ $messages->count() }})</h3>
            <a href="{{ route('admin.messages.index') }}" class="text-[10px] font-black text-rdc-blue uppercase tracking-widest">← Retour</a>
        </div>
        <div class="p-6 space-y-4 bg-slate-50/30 max-h-[70vh] overflow-y-auto">
            @foreach($messages as $msg)
            @php $isUserOne = $msg->sender_id == $conversation->userOne->id ?? $conversation->user_one; @endphp
            <div class="flex {{ $isUserOne ? 'justify-start' : 'justify-end' }} items-end gap-2">
                @if($isUserOne)
                <img src="{{ $conversation->userOne->photo_url }}" class="w-6 h-6 rounded-lg object-cover border border-slate-200 mb-1" />
                @endif
                <div class="flex flex-col {{ $isUserOne ? 'items-start' : 'items-end' }} max-w-md">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1 px-1">
                        {{ $msg->sender->name ?? 'Utilisateur' }} · {{ $msg->created_at->format('d/m H:i') }}
                    </p>
                    <div class="p-3.5 {{ $isUserOne ? 'bg-white border border-slate-100 rounded-2xl rounded-bl-sm' : 'bg-rdc-blue text-white rounded-2xl rounded-br-sm' }} shadow-sm">
                        @if($msg->body ?? $msg->content)
                        <p class="text-sm leading-relaxed">{{ $msg->body ?? $msg->content }}</p>
                        @endif
                        @if($msg->attachment)
                        <div class="mt-2 pt-2 border-t {{ $isUserOne ? 'border-slate-100' : 'border-white/20' }}">
                            <a href="{{ $msg->attachment_url }}" target="_blank" class="flex items-center gap-2 text-xs font-bold hover:opacity-80">
                                <i class="fas fa-file-download"></i> Fichier joint
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @if(!$isUserOne)
                <img src="{{ $conversation->userTwo->photo_url }}" class="w-6 h-6 rounded-lg object-cover border border-slate-200 mb-1" />
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
