@extends('layouts.admin')

@section('title', 'Comptes Suspendus')
@section('header_title', 'Signalements & Bannissements')
@section('page_title', 'Surveillance Utilisateurs')
@section('page_subtitle', 'Gérez les comptes ayant enfreint les règles ou signalés par la communauté.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-1/2 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest">Utilisateur</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Raison</th>
                        <th class="w-1/2 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="group hover:bg-red-50/30 transition-colors">
                        <td class="px-4 sm:px-8 py-4 sm:py-6">
                            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                <div class="relative shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EF4444&color=fff" class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl grayscale group-hover:grayscale-0 transition-all shadow-sm" alt="">
                                    <div class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 w-2 h-2 sm:w-4 sm:h-4 bg-rdc-red text-[6px] sm:text-[8px] flex items-center justify-center text-white rounded-full border border-white font-black">!</div>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] sm:text-sm font-black text-slate-900 truncate leading-tight">{{ $user->name }}</p>
                                    <p class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-tighter truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell px-8 py-6">
                            <span class="px-3 py-1 bg-red-100 text-rdc-red text-[9px] font-black uppercase rounded-lg tracking-widest">Suspension</span>
                        </td>
                        <td class="px-4 sm:px-8 py-4 sm:py-6 text-right">
                            <div class="flex items-center justify-end gap-1 sm:gap-2">
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 sm:px-4 py-1.5 sm:py-2 bg-emerald-500 text-white text-[7px] sm:text-[9px] font-black uppercase tracking-tighter sm:tracking-widest rounded-lg sm:rounded-xl shadow-lg shadow-emerald-500/10 hover:scale-105 transition-all">OK</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 sm:w-10 sm:h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-lg sm:rounded-xl hover:text-rdc-red hover:border-rdc-red hover:shadow-xl transition-all shadow-sm">
                                        <i class="fas fa-trash text-[8px] sm:text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-200 text-3xl mb-4">
                                    <i class="fas fa-shield-check"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-400 uppercase tracking-widest text-center">Communauté Saine</h4>
                                <p class="text-sm text-slate-300 mt-1 font-medium text-center">Aucun utilisateur suspendu.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
