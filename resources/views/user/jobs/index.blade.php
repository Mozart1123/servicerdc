@extends($layout)

@section('title', 'Mes Candidatures | ProConnect')
@section('header_title', 'Mes Candidatures')
@section('header_subtitle', 'Suivez l\'état de vos candidatures envoyées.')

@section($contentSection)
<div class="space-y-6 pb-20 max-w-5xl mx-auto">
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 px-5 py-4 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-info-circle text-blue-500"></i> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-100 rounded-xl text-red-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    @if($applications->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($applications as $app)
            <a href="{{ route('user.jobs.show', $app->jobOffer->id) }}" class="block bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-[#16a3b0]/30 transition-all group">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex gap-4 items-center">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                            @if($app->jobOffer->company_logo)
                                <img src="{{ Storage::url($app->jobOffer->company_logo) }}" class="w-8 h-8 object-contain">
                            @else
                                <i class="fas fa-building text-slate-400 text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-[#16a3b0] transition-colors">{{ $app->jobOffer->title }}</h3>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">{{ $app->jobOffer->company_name ?? ($app->jobOffer->user->name ?? 'Entreprise') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 w-full sm:w-auto mt-2 sm:mt-0 pt-3 sm:pt-0 border-t border-slate-100 sm:border-0">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'accepted' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'hired' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                'interview' => 'bg-blue-100 text-blue-700 border-blue-200',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'accepted' => 'Acceptée',
                                'approved' => 'Acceptée',
                                'hired' => 'Embauché',
                                'rejected' => 'Refusée',
                                'interview' => 'Entretien',
                            ];
                            $colorClass = $statusColors[$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            $label = $statusLabels[$app->status] ?? ucfirst($app->status);
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $colorClass }}">
                            {{ $label }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">Postulé le {{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->format('d/m/Y') : $app->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $applications->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl p-10 text-center border border-slate-100 shadow-sm">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-paper-plane text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Vous n'avez pas encore postulé</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">Vous n'avez envoyé aucune candidature pour le moment. Explorez nos offres d'emploi et trouvez celle qui vous correspond.</p>
            <a href="{{ route('public.jobs.index') }}" class="inline-block px-6 py-3 bg-[#16a3b0] text-white font-medium rounded-xl hover:bg-[#138b96] transition-colors shadow-lg shadow-[#16a3b0]/20">
                Explorer les offres d'emploi
            </a>
        </div>
    @endif
</div>
@endsection
