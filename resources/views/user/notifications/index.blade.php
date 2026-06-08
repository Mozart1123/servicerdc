@extends('layouts.user')

@section('title', 'Mes Notifications')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-20">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-down">
        <div>
            <h2 class="text-3xl font-black text-slate-900 font-heading tracking-tight uppercase">Centre de Notifications</h2>
            <p class="text-slate-500 text-sm font-medium mt-1 uppercase tracking-widest">Restez informé de vos activités sur ProConnect</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('user.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-check-double text-emerald-500"></i> Tout marquer comme lu
                </button>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden" data-aos="fade-up">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($notifications as $n)
                    @php
                        $icon = match($n->type) {
                            'service_request' => ['fa-bell', 'bg-blue-50', 'text-blue-600'],
                            'service_accepted' => ['fa-check-circle', 'bg-emerald-50', 'text-emerald-600'],
                            'service_rejected' => ['fa-times-circle', 'bg-red-50', 'text-red-500'],
                            'job_application' => ['fa-briefcase', 'bg-indigo-50', 'text-indigo-600'],
                            'application_approved' => ['fa-trophy', 'bg-amber-50', 'text-amber-600'],
                            'message' => ['fa-comment-alt', 'bg-cyan-50', 'text-cyan-600'],
                            'service_view' => ['fa-eye', 'bg-slate-50', 'text-slate-600'],
                            default => ['fa-info-circle', 'bg-slate-50', 'text-slate-600']
                        };
                    @endphp
                    <div class="p-6 md:p-8 flex items-start gap-4 md:gap-6 transition-all hover:bg-slate-50/50 group {{ !$n->is_read ? 'bg-blue-50/30' : '' }}">
                        <div class="w-12 h-12 {{ $icon[1] }} {{ $icon[2] }} rounded-2xl flex items-center justify-center shrink-0 text-xl shadow-sm">
                            <i class="fas {{ $icon[0] }}"></i>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-black text-slate-900 text-sm md:text-base {{ !$n->is_read ? 'font-black' : 'font-bold' }}">
                                    {{ $n->title }}
                                    @if(!$n->is_read)
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-1"></span>
                                    @endif
                                </h4>
                                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap uppercase tracking-tighter">{{ $n->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <p class="text-slate-600 text-sm leading-relaxed mb-4 {{ !$n->is_read ? 'font-medium' : '' }}">
                                {{ $n->message }}
                            </p>
                            
                            <div class="flex items-center gap-3">
                                @if($n->action_url)
                                    <a href="{{ $n->action_url }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rdc-blue transition-all">
                                        Voir l'élément
                                    </a>
                                @endif
                                
                                @if(!$n->is_read)
                                <form action="{{ route('user.notifications.read', $n->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:underline px-2 py-2">
                                        Marquer comme lu
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('user.notifications.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Supprimer cette notification ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-black text-red-400 uppercase tracking-widest hover:text-red-600 px-2 py-2">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="p-6 border-t border-slate-50">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="py-24 text-center px-6">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-bell-slash text-4xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase">Boîte vide</h3>
                <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto">Vous n'avez aucune notification pour le moment. Nous vous préviendrons dès qu'il y aura du nouveau !</p>
            </div>
        @endif
    </div>
</div>
@endsection
