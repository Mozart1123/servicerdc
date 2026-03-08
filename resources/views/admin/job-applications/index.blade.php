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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font bold uppercase tracking-wide">Total</p>
                    <h3 class="text-3xl font-heading font-extrabold text-slate-900 mt-1">{{ $applications->total() }}</h3>
                </div>
                <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-2xl text-rdc-blue"></i>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wide">En attente</p>
                    <h3 class="text-3xl font-heading font-extrabold text-amber-500 mt-1">{{ $applications->where('status', 'pending')->count() }}</h3>
                </div>
                <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-amber-500"></i>
                </div>
            </div>
        </div>

        <!-- Accepted Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wide">Acceptées</p>
                    <h3 class="text-3xl font-heading font-extrabold text-emerald-500 mt-1">{{ $applications->where('status', 'accepted')->count() }}</h3>
                </div>
                <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-emerald-500"></i>
                </div>
            </div>
        </div>

        <!-- Rejected Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wide">Rejetées</p>
                    <h3 class="text-3xl font-heading font-extrabold text-red-500 mt-1">{{ $applications->where('status', 'rejected')->count() }}</h3>
                </div>
                <div class="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-2xl text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-8 py-6 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-lg font-heading font-extrabold text-slate-900">Liste des candidatures</h3>
            <p class="text-sm text-slate-500 mt-1">Visualisez et gérez les candidatures en temps réel</p>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Candidat</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Offre d'emploi</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($applications as $application)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Candidate -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name ?? 'User') }}&background=007FFF&color=fff&size=128" 
                                         class="w-10 h-10 rounded-full border-2 border-white shadow-md" 
                                         alt="Avatar">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $application->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500">{{ $application->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Job Title -->
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $application->jobOffer->title ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500">{{ $application->jobOffer->company_name ?? 'N/A' }}</p>
                                </div>
                            </td>
                            
                            <!-- Date -->
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700">{{ $application->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $application->created_at->format('H:i') }}</p>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($application->status === 'pending')
                                    <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-black uppercase rounded-full border border-amber-200">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                @elseif($application->status === 'accepted' || $application->status === 'approved')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-black uppercase rounded-full border border-emerald-200">
                                        <i class="fas fa-check mr-1"></i> Acceptée
                                    </span>
                                @elseif($application->status === 'rejected')
                                    <span class="px-3 py-1 bg-red-50 text-red-700 text-xs font-black uppercase rounded-full border border-red-200">
                                        <i class="fas fa-times mr-1"></i> Rejetée
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-black uppercase rounded-full border border-slate-200">
                                        {{ $application->status }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <!-- View Details -->
                                    <button 
                                        @click="showApplicationDetails({{ $application->id }})" 
                                        class="px-3 py-2 bg-blue-50 text-rdc-blue hover:bg-rdc-blue hover:text-white rounded-lg text-xs font-bold transition-all">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- Update Status Form -->
                                    @if($application->status === 'pending')
                                        <form method="POST" action="{{ route('admin.job-applications.status', $application->id) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" 
                                                    class="px-3 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition-all"
                                                    title="Accepter">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.job-applications.status', $application->id) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" 
                                                    class="px-3 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg text-xs font-bold transition-all"
                                                    title="Rejeter"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette candidature ?')">
                                                <i class="fas fa-times"></i>
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
                                    <i class="fas fa-inbox text-6xl text-slate-300 mb-4"></i>
                                    <p class="text-slate-500 font-bold text-lg">Aucune candidature trouvée</p>
                                    <p class="text-slate-400 text-sm mt-1">Les candidatures apparaîtront ici une fois soumises par les utilisateurs.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="px-8 py-6 border-t border-slate-200 bg-slate-50/30">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
