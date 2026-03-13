@extends('layouts.admin')

@section('title', 'Validation Utilisateurs')
@section('header_title', 'Approbation des Comptes')
@section('page_title', 'Comptes en Attente')
@section('page_subtitle', 'Vérifiez et activez les nouveaux utilisateurs souhaitant rejoindre la plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-1/2 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest">Utilisateur</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date d'inscription</th>
                        <th class="w-1/2 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="px-4 sm:px-8 py-4 sm:py-6">
                            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007FFF&color=fff" class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl shadow-sm shrink-0" alt="">
                                <div class="overflow-hidden">
                                    <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate">{{ $user->name }}</p>
                                    <p class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell px-8 py-6">
                            <span class="text-xs font-bold text-slate-500">{{ $user->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-4 sm:px-8 py-4 sm:py-6 text-right">
                            <div class="flex items-center justify-end gap-1 sm:gap-2">
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 sm:px-4 py-1.5 sm:py-2 bg-rdc-blue text-white text-[7px] sm:text-[9px] font-black uppercase tracking-tighter sm:tracking-widest rounded-lg sm:rounded-xl shadow-lg shadow-blue-500/10 hover:scale-105 transition-all">OK</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Refuser ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 sm:px-4 py-1.5 sm:py-2 bg-white border border-slate-100 text-rdc-red text-[7px] sm:text-[9px] font-black uppercase tracking-tighter sm:tracking-widest rounded-lg sm:rounded-xl hover:bg-red-50 transition-all">Non</button>
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
        <div class="px-4 sm:px-8 py-4 sm:py-6 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
