@extends('layouts.admin')

@section('title', 'Validation Utilisateurs')
@section('header_title', 'Approbation des Comptes')
@section('page_title', 'Comptes en Attente')
@section('page_subtitle', 'Vérifiez et activez les nouveaux utilisateurs souhaitant rejoindre la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date d'inscription</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007FFF&color=fff" class="w-12 h-12 rounded-2xl shadow-sm" alt="">
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-slate-500">{{ $user->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-rdc-blue text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/10 hover:scale-105 transition-all">Approuver</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Refuser et supprimer cet utilisateur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-white border border-slate-100 text-rdc-red text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-red-50 transition-all">Refuser</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 text-3xl mb-4">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-400 uppercase tracking-widest">Aucune attente</h4>
                                <p class="text-sm text-slate-300 mt-1 font-medium">Tous les utilisateurs sont à jour.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-8 py-6 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
