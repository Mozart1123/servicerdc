@extends('layouts.user')

@section('title', 'Demande de service | ProConnect')
@section('header_title', 'Détail de la demande')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-10">

    {{-- Flash Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
    @if(session($type))
    <div class="flex items-center gap-3 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-5 py-4 rounded-2xl">
        <i class="fas fa-circle-info text-xl shrink-0"></i>
        <p class="font-semibold">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" data-aos="fade-up">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-16 h-16 rounded-2xl bg-rdc-blue/10 flex items-center justify-center shrink-0 overflow-hidden">
                @if($serviceRequest->service?->service_image)
                    <img src="{{ Storage::url($serviceRequest->service->service_image) }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <i class="fas fa-tools text-rdc-blue text-2xl"></i>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-slate-900">{{ $serviceRequest->requested_service_name ?? 'Demande de service' }}</h2>
                    @php $c = match($serviceRequest->status) {'accepted'=>'emerald','rejected'=>'red','completed'=>'blue','cancelled'=>'slate',default=>'amber'}; @endphp
                    <span class="px-2.5 py-0.5 bg-{{ $c }}-50 text-{{ $c }}-600 text-xs font-bold uppercase rounded-full border border-{{ $c }}-200">
                        {{ $serviceRequest->status_label }}
                    </span>
                </div>
                <p class="text-sm text-slate-400">Envoyée le {{ $serviceRequest->created_at->format('d M Y à H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-2 gap-5" data-aos="fade-up">
        @foreach([
            ['label' => 'Service lié', 'value' => $serviceRequest->service?->title, 'icon' => 'fa-tools'],
            ['label' => 'Artisan', 'value' => $serviceRequest->artisan?->name ?? $serviceRequest->service?->artisan?->name, 'icon' => 'fa-user-hard-hat'],
            ['label' => 'Ville', 'value' => $serviceRequest->city, 'icon' => 'fa-map-marker-alt'],
            ['label' => 'Urgence', 'value' => $serviceRequest->urgency_label, 'icon' => 'fa-exclamation-triangle'],
            ['label' => 'Budget', 'value' => $serviceRequest->budget_range, 'icon' => 'fa-money-bill-wave'],
            ['label' => 'Téléphone', 'value' => $serviceRequest->phone, 'icon' => 'fa-phone'],
        ] as $detail)
        @if($detail['value'])
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $detail['label'] }}</p>
            <p class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas {{ $detail['icon'] }} text-rdc-blue w-4"></i>
                {{ $detail['value'] }}
            </p>
        </div>
        @endif
        @endforeach

        @if($serviceRequest->description)
        <div class="sm:col-span-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Description</p>
            <p class="text-slate-700 leading-relaxed">{{ $serviceRequest->description }}</p>
        </div>
        @endif

        @if($serviceRequest->admin_response)
        <div class="sm:col-span-2 bg-blue-50 rounded-xl p-4 border border-blue-100">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">Réponse reçue</p>
            <p class="text-blue-800 leading-relaxed">{{ $serviceRequest->admin_response }}</p>
        </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3" data-aos="fade-up">
        @if(in_array($serviceRequest->status, ['accepted', 'completed']) && isset($conversation))
        <a href="{{ route('user.messages.index', ['id' => $conversation->id]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition shadow-lg shadow-blue-200">
            <i class="fas fa-comments"></i> Discuter avec l'artisan
        </a>
        @endif

        @if($serviceRequest->status === 'pending' && $serviceRequest->user_id === auth()->id())
        <form action="{{ route('user.service-requests.cancel', $serviceRequest->id) }}" method="POST"
              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 border border-red-200 transition">
                <i class="fas fa-times"></i> Annuler la demande
            </button>
        </form>
        @endif

        <a href="{{ route('user.service-requests.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@endsection
