@extends($layout)

@section('title', 'Profil de l\'entreprise ' . $recruiter->name . ' | ProConnect')
@section('header_title', 'Profil Entreprise')

@section($contentSection)
<div class="space-y-8 pb-20">

    {{-- Back Button --}}
    <div>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('user.jobs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-100 rounded-2xl text-slate-600 hover:text-rdc-blue font-black text-[10px] uppercase tracking-widest transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Retour</span>
        </a>
    </div>

    <!-- Company Header -->
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden" data-aos="fade-up">
        <div class="p-10">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <div class="w-32 h-32 rounded-[2rem] bg-slate-50 border border-slate-100 p-6 shrink-0 flex items-center justify-center">
                    @if($recruiter->profile_photo)
                        <img src="{{ Storage::url($recruiter->profile_photo) }}" class="w-full h-full object-contain" alt="{{ $recruiter->name }}">
                    @else
                        <i class="fas fa-building text-5xl text-slate-200"></i>
                    @endif
                </div>
                
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-heading font-black text-slate-900 tracking-tight mb-2">{{ $recruiter->name }}</h1>
                    <div class="flex flex-wrap items-center gap-6 mt-4">
                        <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                            <i class="fas fa-map-marker-alt text-[#16a3b0] opacity-70"></i>
                            <span>{{ $recruiter->city ?? 'Ville non précisée' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 font-bold text-sm">
                            <i class="fas fa-calendar-alt text-[#16a3b0] opacity-70"></i>
                            <span>Membre depuis {{ $stats['member_since'] }}</span>
                        </div>
                    </div>

                    @if($recruiter->company_description)
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4">À propos</h3>
                            <p class="text-slate-600 leading-relaxed text-base whitespace-pre-line">{{ $recruiter->company_description }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 text-center shrink-0 w-full md:w-48">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Offres Actives</p>
                    <p class="font-heading font-black text-[#16a3b0] text-4xl">{{ $stats['active_offers'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Offers List -->
    <div class="space-y-6">
        <h3 class="text-xl font-heading font-black text-slate-900 uppercase tracking-tight ml-2">Toutes les offres de cette entreprise</h3>
        
        @if($jobOffers->isNotEmpty())
            <div class="grid grid-cols-1 gap-4">
                @foreach($jobOffers as $job)
                    <a href="{{ route('user.jobs.show', $job->id) }}" 
                       class="flex flex-col md:flex-row md:items-center gap-6 p-6 bg-white border border-slate-100 rounded-3xl hover:border-[#16a3b0] hover:shadow-xl transition-all group">
                        
                        <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100 p-3 hidden sm:flex">
                            @if($job->company_logo)
                                <img src="{{ Storage::url($job->company_logo) }}" class="w-full h-full object-contain">
                            @elseif($recruiter->profile_photo)
                                <img src="{{ Storage::url($recruiter->profile_photo) }}" class="w-full h-full object-contain">
                            @else
                                <i class="fas fa-briefcase text-slate-200 text-3xl"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="px-3 py-1.5 bg-[#16a3b0]/10 text-[#16a3b0] text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $job->category ?? 'Secteur non spécifié' }}</span>
                                <span class="px-3 py-1.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-lg">{{ $job->contract_type }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-lg group-hover:text-[#16a3b0] transition-colors mb-1">{{ $job->title }}</h4>
                            <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-2">
                                <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt"></i> {{ $job->location }}</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-clock"></i> {{ $job->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0 flex items-center gap-4 text-sm font-bold text-slate-400 group-hover:text-[#16a3b0] transition-colors">
                            Voir l'offre <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($jobOffers->hasPages())
                <div class="mt-8">
                    {{ $jobOffers->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-3xl p-10 text-center border border-slate-100">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-2xl text-slate-300"></i>
                </div>
                <p class="text-slate-500 font-bold">Aucune offre active pour le moment.</p>
            </div>
        @endif
    </div>

</div>
@endsection
