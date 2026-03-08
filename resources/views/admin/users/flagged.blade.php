@extends('layouts.admin')

@section('title', 'Comptes Suspendus')
@section('header_title', 'Signalements & Bannissements')
@section('page_title', 'Surveillance Utilisateurs')
@section('page_subtitle', 'Gérez les comptes ayant enfreint les règles ou signalés par la communauté.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Raison probable</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="group hover:bg-red-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EF4444&color=fff" class="w-12 h-12 rounded-2xl grayscale group-hover:grayscale-0 transition-all" alt="">
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-rdc-red text-[8px] flex items-center justify-center text-white rounded-full border-2 border-white font-black">!</div>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-red-100 text-rdc-red text-[9px] font-black uppercase rounded-lg tracking-widest">Suspension Manuelle</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-500/10 hover:scale-105 transition-all">Réactiver</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce compte ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rdc-red hover:border-rdc-red hover:shadow-xl transition-all">
                                        <i class="fas fa-trash text-xs"></i>
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
                                <h4 class="text-lg font-black text-slate-400 uppercase tracking-widest">Communauté Saine</h4>
                                <p class="text-sm text-slate-300 mt-1 font-medium">Aucun utilisateur n'est actuellement suspendu.</p>
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
