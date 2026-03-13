@extends('layouts.admin')

@section('title', 'Candidatures d\'emploi')
@section('header_title', 'Candidatures d\'emploi')
@section('page_title', 'Gestion des candidatures')
@section('page_subtitle', 'Visualisez et gérez toutes les candidatures aux offres d\'emploi')

@section('content')
<div class="space-y-6">
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Applications -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-[10px] sm:text-sm font-bold uppercase tracking-wide">Total</p>
                    <h3 class="text-xl sm:text-3xl font-black text-slate-900 mt-1">{{ $applications->total() }}</h3>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-blue-50 rounded-xl items-center justify-center">
                    <i class="fas fa-file-lines text-2xl text-rdc-blue"></i>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-[10px] sm:text-sm font-bold uppercase tracking-wide">Attente</p>
                    <h3 class="text-xl sm:text-3xl font-black text-amber-500 mt-1">{{ $applications->where('status', 'pending')->count() }}</h3>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-amber-50 rounded-xl items-center justify-center">
                    <i class="fas fa-clock text-2xl text-amber-500"></i>
                </div>
            </div>
        </div>

        <!-- Accepted Applications -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-[10px] sm:text-sm font-bold uppercase tracking-wide">Acceptées</p>
                    <h3 class="text-xl sm:text-3xl font-black text-emerald-500 mt-1">{{ $applications->where('status', 'accepted')->count() }}</h3>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-emerald-50 rounded-xl items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-emerald-500"></i>
                </div>
            </div>
        </div>

        <!-- Rejected Applications -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-[10px] sm:text-sm font-bold uppercase tracking-wide">Rejetées</p>
                    <h3 class="text-xl sm:text-3xl font-black text-red-500 mt-1">{{ $applications->where('status', 'rejected')->count() }}</h3>
                </div>
                <div class="hidden sm:flex w-14 h-14 bg-red-50 rounded-xl items-center justify-center">
                    <i class="fas fa-times-circle text-2xl text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-[1.5rem] sm:rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Table Header -->
        <div class="px-5 sm:px-8 py-5 sm:py-6 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Candidatures</h3>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="w-1/2 sm:w-auto px-4 sm:px-6 py-4 text-[8px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter sm:tracking-widest">Candidat</th>
                        <th class="hidden lg:table-cell px-6 py-4 text-xs font-black text-slate-600 uppercase tracking-wider">Offre</th>
                        <th class="hidden sm:table-cell px-6 py-4 text-xs font-black text-slate-600 uppercase tracking-wider">Date</th>
                        <th class="w-1/6 sm:w-auto px-2 sm:px-6 py-4 text-[8px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter sm:tracking-widest text-center">Statut</th>
                        <th class="w-1/3 sm:w-auto px-4 sm:px-6 py-4 text-[8px] sm:text-xs font-black text-slate-600 uppercase tracking-tighter sm:tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($applications as $application)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Candidate -->
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center gap-2 sm:gap-3 overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name ?? 'User') }}&background=007FFF&color=fff&size=128" 
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl sm:text-6xl text-slate-300 mb-4"></i>
                                    <p class="text-slate-500 font-bold text-sm sm:text-lg">Aucune candidature found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="px-5 sm:px-8 py-5 border-t border-slate-200 bg-slate-50/30">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
