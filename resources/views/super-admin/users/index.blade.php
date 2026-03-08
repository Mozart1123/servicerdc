@extends('layouts.super-admin')

@section('header_title', 'Hiérarchie Administrative | ARCHITECTURE DES MAÎTRES')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Hierarchy Stats HUD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-[2rem] bg-amber-500 text-slate-900 flex items-center justify-center text-3xl mb-6 shadow-xl divine-glow">
                    <i class="fas fa-crown"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Super Masters</p>
                <div class="flex items-end gap-4">
                    <h3 class="text-5xl font-heading font-black text-white leading-none">{{ $users->where('role', 'super_admin')->count() }}</h3>
                    <span class="text-amber-500 text-[10px] font-black uppercase mb-1 tracking-widest">Autorité Totale</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3.5rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:border-rdc-blue/30 transition-all">
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-[2rem] bg-rdc-blue/10 text-rdc-blue flex items-center justify-center text-3xl mb-6 shadow-sm group-hover:bg-rdc-blue group-hover:text-white transition-all">
                    <i class="fas fa-user-shield"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Admin Stations</p>
                <div class="flex items-end gap-4">
                    <h3 class="text-5xl font-heading font-black text-slate-900 leading-none">{{ $users->where('role', 'admin')->count() }}</h3>
                    <span class="text-rdc-blue text-[10px] font-black uppercase mb-1 tracking-widest">Gardiens Actifs</span>
                </div>
            </div>
        </div>

        <div class="bg-emerald-500 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/20 to-transparent"></div>
            <div class="relative z-10 text-white">
                <div class="w-16 h-16 rounded-[2rem] bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl mb-6 shadow-sm">
                    <i class="fas fa-microchip"></i>
                </div>
                <p class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] mb-2">Status Synchronisation</p>
                <h3 class="text-3xl font-heading font-black text-white leading-tight uppercase">INTÉGRITÉ <br>ABSOLUE</h3>
            </div>
        </div>
    </div>

    <!-- Master Registry -->
    <div class="bg-white rounded-[4rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-10 border-b border-slate-50 bg-slate-50/30 flex flex-wrap items-center justify-between gap-8">
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xl shadow-xl">
                    <i class="fas fa-dna"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 font-heading tracking-tight uppercase">REGISTRE DES HAUTS GRADÉS</h3>
                    <p class="text-slate-400 text-[10px] font-mono uppercase tracking-[0.2em] mt-1">Gérez le code source humain du système.</p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="relative group">
                    <input type="text" placeholder="RECHERCHE BIOMÉTRIQUE..." class="pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-[10px] font-black w-80 focus:ring-4 focus:ring-amber-500/10 transition-all outline-none uppercase tracking-widest">
                    <i class="fas fa-fingerprint absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-500 transition-colors"></i>
                </div>
                <button class="px-10 py-5 bg-amber-500 text-slate-900 font-black rounded-2xl text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:bg-amber-600 transition-all flex items-center gap-3">
                    <i class="fas fa-plus-circle"></i> INVOQUER UN NOUVEAU MAÎTRE
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-12 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Identité Cosmique</th>
                        <th class="px-12 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Niveau de Privilège</th>
                        <th class="px-12 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Uplink Status</th>
                        <th class="px-12 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Intervention Directe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-100/50 transition-all group">
                        <td class="px-12 py-8">
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <div class="absolute -inset-1 bg-gradient-to-r {{ $user->isSuperAdmin() ? 'from-amber-500 to-amber-200' : 'from-rdc-blue to-blue-200' }} rounded-3xl blur opacity-0 group-hover:opacity-40 transition-all"></div>
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->isSuperAdmin() ? '000' : 'F1F5F9' }}&color={{ $user->isSuperAdmin() ? 'F59E0B' : '64748B' }}" 
                                         class="relative w-16 h-16 rounded-3xl shadow-xl border-2 border-white transition-transform group-hover:scale-110" alt="">
                                    @if($user->isSuperAdmin())
                                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-amber-500 rounded-2xl border-2 border-white flex items-center justify-center shadow-lg">
                                        <i class="fas fa-crown text-[10px] text-white"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <p class="text-base font-black text-slate-900 group-hover:text-amber-600 transition-colors uppercase tracking-tight">{{ $user->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono tracking-widest lowercase">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-12 py-8">
                            @if($user->isSuperAdmin())
                            <div class="space-y-3">
                                <div class="flex items-center gap-4">
                                    <span class="text-[11px] font-black text-amber-600 uppercase tracking-[0.2em]">LEVEL : ∞</span>
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-600 text-[8px] font-black rounded-lg uppercase">Omnipotent</span>
                                </div>
                                <div class="flex gap-1">
                                    @for($i=0; $i<8; $i++) <div class="w-4 h-1.5 bg-amber-500 rounded-full"></div> @endfor
                                </div>
                            </div>
                            @else
                            <div class="space-y-3">
                                <div class="flex items-center gap-4">
                                    <span class="text-[11px] font-black text-rdc-blue uppercase tracking-[0.2em]">LEVEL : 04</span>
                                    <span class="px-3 py-1 bg-rdc-blue/10 text-rdc-blue text-[8px] font-black rounded-lg uppercase">Guardian</span>
                                </div>
                                <div class="flex gap-1">
                                    @for($i=0; $i<4; $i++) <div class="w-4 h-1.5 bg-rdc-blue rounded-full"></div> @endfor
                                    @for($i=0; $i<4; $i++) <div class="w-4 h-1.5 bg-slate-100 rounded-full"></div> @endfor
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="px-12 py-8">
                            <div class="space-y-2">
                                <p class="text-[11px] font-black text-slate-800 uppercase tracking-tighter">HQ-MAIN-KINSHASA</p>
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[9px] text-slate-400 font-mono uppercase tracking-widest">Dernier active: {{ $user->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-12 py-8 text-right">
                            <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                <button title="Accès total" class="w-12 h-12 rounded-2xl bg-white shadow-xl hover:bg-slate-900 hover:text-white transition-all border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button title="Modifier privilèges" class="w-12 h-12 rounded-2xl bg-white shadow-xl hover:bg-amber-500 hover:text-white transition-all border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-key text-sm"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('super-admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('RÉVOQUER CET ACCÈS MAÎTRE ? CETTE ACTION EST IRRÉVERSIBLE.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-12 h-12 rounded-2xl bg-red-500 text-white shadow-xl hover:bg-red-600 transition-all flex items-center justify-center">
                                        <i class="fas fa-user-xmark text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-12 border-t border-slate-50 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Séquence Administrative v4.2.0-DIVINE</p>
                <div class="flex gap-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
