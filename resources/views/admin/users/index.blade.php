@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')
@section('header_title', 'Utilisateurs')
@section('page_title', 'Gestion de la Communauté')
@section('page_subtitle', 'Gérez les accès, les rôles et la modération des utilisateurs de la plateforme.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-[300px]">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Rechercher par nom, email ou téléphone..." 
                       class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none">
            </div>
            <button class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-rdc-blue transition-all">
                Filtrer
            </button>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Trier par :</span>
            <select class="bg-transparent border-none text-sm font-bold text-slate-900 focus:ring-0 cursor-pointer">
                <option>Plus récents</option>
                <option>Rôle</option>
                <option>Statut</option>
            </select>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-[1.5rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left border-collapse table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-1/3 sm:w-auto px-2 sm:px-8 py-3 sm:py-5 text-[7px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-tighter sm:tracking-widest">Utilisateur</th>
                        <th class="w-1/6 sm:w-auto px-2 sm:px-8 py-3 sm:py-5 text-[7px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center">Rôle</th>
                        <th class="hidden sm:table-cell px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Inscription</th>
                        <th class="w-1/5 sm:w-auto px-2 sm:px-8 py-3 sm:py-5 text-[7px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-tighter sm:tracking-widest text-center">Statut</th>
                        <th class="w-1/4 sm:w-auto px-2 sm:px-8 py-3 sm:py-5 text-[7px] sm:text-[10px] font-extrabold text-slate-400 uppercase tracking-tighter sm:tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/30 transition-colors group">
                        <td class="px-2 sm:px-8 py-3 sm:py-5">
                            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden">
                                <div class="relative shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->isAdmin() ? '007FFF' : 'F1F5F9' }}&color={{ $user->isAdmin() ? 'fff' : '64748B' }}" 
                                         class="w-7 h-7 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl shadow-sm border border-slate-100" alt="">
                                    @if($user->status === 'active')
                                    <span class="absolute -bottom-0.5 -right-0.5 sm:-bottom-1 sm:-right-1 w-2 h-2 sm:w-4 sm:h-4 bg-emerald-500 border border-white rounded-full"></span>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[9px] sm:text-sm font-bold text-slate-900 group-hover:text-rdc-blue transition-colors truncate leading-tight">{{ $user->name }}</p>
                                    <p class="text-[7px] sm:text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-2 sm:px-8 py-3 sm:py-5 text-center sm:text-left">
                            @php
                                $roleClass = match($user->role) {
                                    'super_admin' => 'bg-slate-900 text-white',
                                    'admin' => 'bg-blue-50 text-rdc-blue border border-blue-100',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                            @endphp
                            <span class="inline-block px-1.5 sm:px-3 py-1 sm:py-1.5 {{ $roleClass }} text-[6px] sm:text-[10px] font-bold rounded-md sm:rounded-xl uppercase tracking-tighter sm:tracking-wider">
                                {{ $user->role === 'super_admin' ? 'S.Admin' : ($user->role === 'admin' ? 'Admin' : 'User') }}
                            </span>
                        </td>
                        <td class="hidden sm:table-cell px-8 py-5">
                            <p class="text-xs font-bold text-slate-600">{{ $user->created_at->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase">{{ $user->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-2 sm:px-8 py-3 sm:py-5 text-center">
                            @if($user->status === 'active')
                            <span class="inline-flex items-center gap-1 text-[6px] sm:text-[10px] font-extrabold text-emerald-500 uppercase bg-emerald-50 px-1.5 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-xl">
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[6px] sm:text-[10px] font-extrabold text-rdc-red uppercase bg-red-50 px-1.5 sm:px-3 py-1 sm:py-1.5 rounded-md sm:rounded-xl">
                                OFF
                            </span>
                            @endif
                        </td>
                        <td class="px-2 sm:px-8 py-3 sm:py-5 text-right">
                            <div class="flex items-center justify-end gap-1 sm:gap-2">
                                <button class="w-6 h-6 sm:w-9 sm:h-9 flex items-center justify-center rounded-md sm:rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-rdc-blue transition-all">
                                    <i class="fas fa-edit text-[8px] sm:text-xs"></i>
                                </button>
                                
                                @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-6 h-6 sm:w-9 sm:h-9 flex items-center justify-center rounded-md sm:rounded-xl bg-slate-50 {{ $user->status === 'active' ? 'text-amber-500 hover:bg-amber-50' : 'text-emerald-500 hover:bg-emerald-50' }} transition-all">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }} text-[8px] sm:text-xs"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6 h-6 sm:w-9 sm:h-9 flex items-center justify-center rounded-md sm:rounded-xl bg-slate-50 text-rdc-red hover:bg-red-50 transition-all">
                                        <i class="fas fa-trash-alt text-[8px] sm:text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 text-3xl mb-4">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Aucun utilisateur.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 sm:px-8 py-4 sm:py-6 bg-slate-50/50 border-t border-slate-50">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
