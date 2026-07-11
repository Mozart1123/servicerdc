@extends('layouts.admin')

@section('title', 'Candidatures d\'emploi')
@section('header_title', 'Candidatures d\'emploi')
@section('page_title', 'Gestion des candidatures')
@section('page_subtitle', 'Visualisez et gérez toutes les candidatures aux offres d\'emploi')

@section('content')
<div class="space-y-8 pb-20">
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-2xl shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-rdc-red p-4 rounded-2xl shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 bg-blue-50 text-rdc-blue rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-lines text-lg"></i>
                </div>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</p>
            <h3 class="text-xl sm:text-3xl font-black text-slate-900 font-mono mt-1">{{ $applications->total() }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Attente</p>
            <h3 class="text-xl sm:text-3xl font-black text-amber-500 font-mono mt-1">{{ $applications->where('status', 'pending')->count() }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Acceptées</p>
            <h3 class="text-xl sm:text-3xl font-black text-emerald-500 font-mono mt-1">{{ $applications->where('status', 'accepted')->count() }}</h3>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 bg-red-50 text-rdc-red rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-times-circle text-lg"></i>
                </div>
            </div>
            <p class="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Rejetées</p>
            <h3 class="text-xl sm:text-3xl font-black text-rdc-red font-mono mt-1">{{ $applications->where('status', 'rejected')->count() }}</h3>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Candidatures</h3>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead class="bg-slate-50/50 border-b border-slate-50">
                    <tr>
                        <th class="w-1/2 sm:w-auto px-4 sm:px-8 py-5 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest">Candidat</th>
                        <th class="hidden lg:table-cell px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Offre</th>
                        <th class="hidden sm:table-cell px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="w-1/6 sm:w-auto px-2 sm:px-8 py-5 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="w-1/3 sm:w-auto px-4 sm:px-8 py-5 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($applications as $application)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Candidate -->
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center gap-2 sm:gap-3 overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name ?? 'User') }}&background=29B6D1&color=fff&size=128" 
                                         class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-full border-2 border-white shadow-sm shrink-0" 
                                         alt="Avatar">
                                    <div class="overflow-hidden">
                                        <p class="font-black text-slate-900 text-[10px] sm:text-sm truncate leading-tight">{{ $application->user->name ?? 'N/A' }}</p>
                                        <p class="text-[8px] sm:text-xs text-slate-500 truncate mt-0.5 leading-tight">{{ $application->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Job Title -->
                            <td class="hidden lg:table-cell px-6 py-4 text-xs">
                                <div class="overflow-hidden">
                                    <p class="font-bold text-slate-900 truncate">{{ $application->jobOffer->title ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-slate-500 truncate leading-tight">{{ $application->jobOffer->company_name ?? 'N/A' }}</p>
                                </div>
                            </td>
                            
                            <!-- Date -->
                            <td class="hidden sm:table-cell px-6 py-4">
                                <p class="text-sm text-slate-700 font-bold tracking-tighter">{{ $application->created_at->format('d/m/Y') }}</p>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-2 sm:px-6 py-4 text-center">
                                <div class="flex justify-center">
                                    @if($application->status === 'pending')
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    @elseif($application->status === 'accepted' || $application->status === 'approved')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    @elseif($application->status === 'rejected')
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-4 sm:px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 sm:gap-2">
                                    <!-- View Details -->
                                    <button 
                                        @click="showApplicationDetails({{ $application->id }})" 
                                        class="w-7 h-7 sm:h-9 sm:px-3 flex items-center justify-center bg-blue-50 text-rdc-blue hover:bg-rdc-blue hover:text-white rounded-lg text-[10px] sm:text-xs font-bold transition-all shadow-sm">
                                        <i class="fas fa-eye text-[9px] sm:text-xs"></i>
                                    </button>
                                    
                                    <!-- Update Status Form -->
                                    @if($application->status === 'pending')
                                        <form method="POST" action="{{ route('admin.job-applications.status', $application->id) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" 
                                                    class="w-7 h-7 sm:h-9 sm:px-3 flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-[10px] sm:text-xs font-bold transition-all shadow-sm"
                                                    title="Accepter">
                                                <i class="fas fa-check text-[9px] sm:text-xs"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.job-applications.status', $application->id) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" 
                                                    class="w-7 h-7 sm:h-9 sm:px-3 flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg text-[10px] sm:text-xs font-bold transition-all shadow-sm"
                                                    title="Rejeter"
                                                    onclick="return confirm('Rejeter ?')">
                                                <i class="fas fa-times text-[9px] sm:text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-5xl mb-8 shadow-inner">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4 class="text-base font-black text-slate-400 uppercase tracking-widest">Aucune candidature</h4>
                                    <p class="text-[10px] text-slate-300 font-bold uppercase tracking-tight mt-2">Les candidatures apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="px-6 sm:px-10 py-6 border-t border-slate-50 bg-slate-50/20">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
