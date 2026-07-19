@extends('layouts.user')

@section('title', 'Candidatures Reçues | ProConnect')
@section('header_title', 'Gestion des Candidats')

@section('content')
<div class="space-y-8 pb-10">

    {{-- Flash Messages --}}
    @foreach(['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
    @if(session($type))
    <div class="flex items-center gap-3 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-5 py-4 rounded-2xl" data-aos="fade-down">
        <i class="fas fa-circle-info text-xl shrink-0"></i>
        <p class="font-semibold">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate', 'icon' => 'fa-list'],
            ['label' => 'En attente', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => 'fa-clock'],
            ['label' => 'Approuvées', 'value' => $stats['approved'], 'color' => 'emerald', 'icon' => 'fa-check'],
            ['label' => 'Refusées', 'value' => $stats['rejected'], 'color' => 'red', 'icon' => 'fa-times'],
            ['label' => 'Entretien', 'value' => $stats['interview'], 'color' => 'blue', 'icon' => 'fa-comments'],
            ['label' => 'Embauchés', 'value' => $stats['hired'], 'color' => 'purple', 'icon' => 'fa-trophy'],
        ] as $index => $stat)
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-{{ $stat['color'] }}-400" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400 text-sm"></i>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('user.applications.received') }}" class="flex flex-col md:flex-row gap-4" data-aos="fade-up" data-aos-delay="100">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Rechercher un candidat…" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rdc-blue/20 outline-none">
        </div>
        <select name="status" class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-rdc-blue">
            <option value="">Tous les statuts</option>
            @foreach(['pending' => 'En attente', 'approved' => 'Approuvée', 'rejected' => 'Refusée', 'interview' => 'Entretien', 'hired' => 'Embauché'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark transition">Filtrer</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('user.applications.received') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition text-center">Réinitialiser</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
        @if($applications->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Candidat</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Poste</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Date</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($applications as $app)
                    @php
                        $sc = match($app->status) {
                            'approved','accepted' => ['bg'=>'bg-emerald-50','text'=>'text-emerald-600','border'=>'border-emerald-100','label'=>'Approuvée'],
                            'rejected'  => ['bg'=>'bg-red-50','text'=>'text-red-500','border'=>'border-red-100','label'=>'Refusée'],
                            'interview' => ['bg'=>'bg-blue-50','text'=>'text-blue-600','border'=>'border-blue-100','label'=>'Entretien'],
                            'hired'     => ['bg'=>'bg-purple-50','text'=>'text-purple-600','border'=>'border-purple-100','label'=>'Embauché(e)'],
                            default     => ['bg'=>'bg-amber-50','text'=>'text-amber-600','border'=>'border-amber-100','label'=>'En attente'],
                        };
                        $initials = collect(explode(' ', $app->user->name ?? 'U'))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                    @endphp
                    <tr class="group hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($app->user->photo_url)
                                    <img src="{{ $app->user->photo_url }}"
                                         class="w-10 h-10 rounded-xl object-cover border border-slate-100" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-[#16a3b0] text-white flex items-center justify-center text-sm font-bold border border-[#16a3b0]/30 shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $app->user->name }}</div>
                                    <div class="text-[10px] text-rdc-blue font-bold hover:underline">
                                        <a href="mailto:{{ $app->user->email }}">{{ $app->user->email }}</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-700 text-sm truncate max-w-[180px]">{{ $app->jobOffer->title ?? '—' }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-black tracking-tight mt-0.5">{{ $app->jobOffer->contract_type ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $sc['bg'] }} {{ $sc['text'] }} text-[10px] font-black rounded-full uppercase tracking-widest border {{ $sc['border'] }}">
                                @if($app->status === 'pending')<span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>@endif
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-xs font-bold text-slate-600">{{ $app->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $app->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                {{-- Voir dossier --}}
                                <button onclick="openCvModal({{ $app->id }})"
                                    class="px-3 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-xl hover:bg-rdc-blue hover:text-white transition uppercase tracking-widest">
                                    Dossier
                                </button>

                                {{-- Discuss when approved --}}
                                @if(in_array($app->status, ['approved', 'accepted', 'interview', 'hired']))
                                <a href="{{ route('user.messages.start.user', $app->user_id) }}"
                                   class="px-3 py-1.5 bg-rdc-blue text-white text-[10px] font-black rounded-xl hover:bg-rdc-blue-dark transition uppercase tracking-widest inline-flex items-center gap-1">
                                    <i class="fas fa-comments"></i> Discuter
                                </a>
                                @endif

                                @if($app->status === 'pending')
                                {{-- Approve --}}
                                <form action="{{ route('user.applications.approve', $app->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition shadow-sm" title="Approuver">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                {{-- Interview --}}
                                <form action="{{ route('user.applications.interview', $app->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition shadow-sm" title="Inviter à un entretien">
                                        <i class="fas fa-phone-alt text-xs"></i>
                                    </button>
                                </form>
                                {{-- Reject — opens modal --}}
                                <button type="button"
                                        onclick="openRejectModal({{ $app->id }}, '{{ addslashes($app->user->name) }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition shadow-sm" title="Refuser">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                @elseif($app->status === 'interview')
                                {{-- Hire --}}
                                <form action="{{ route('user.applications.hire', $app->id) }}" method="POST"
                                      onsubmit="return confirm('Marquer comme embauché ?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-black rounded-xl hover:bg-purple-500 hover:text-white transition uppercase tracking-widest">
                                        <i class="fas fa-trophy mr-1"></i> Embaucher
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
        @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $applications->links() }}</div>
        @endif
        @else
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-users text-3xl text-slate-200"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Aucune candidature reçue</h3>
            <p class="text-slate-400 text-sm mt-1 max-w-xs mx-auto">Les candidatures pour vos offres apparaîtront ici.</p>
        </div>
        @endif
    </div>
</div>

{{-- ═══ REJECT MODAL ═══ --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-heading font-black text-slate-900">Refuser la candidature</h3>
                <p id="rejectModalSubtitle" class="text-sm text-slate-500 mt-0.5"></p>
            </div>
            <button onclick="closeRejectModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-red-500 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" class="p-8 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Motif du refus <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required maxlength="1000"
                    placeholder="Expliquez brièvement pourquoi cette candidature n'a pas été retenue. Ce message sera envoyé automatiquement au candidat."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 resize-none transition-all"></textarea>
                <p class="text-xs text-slate-400 mt-1.5">Ce motif sera envoyé automatiquement au candidat dans la messagerie.</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 px-5 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition text-sm">
                    Annuler
                </button>
                <button type="submit"
                    class="flex-1 px-5 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition text-sm shadow-lg shadow-red-500/20">
                    <i class="fas fa-times mr-1.5"></i> Confirmer le refus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CV Modal --}}
<div id="cvModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden" id="cvModalBox">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xl font-heading font-black text-slate-900">Dossier de candidature</h3>
            <button onclick="closeCvModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-red-500 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div id="cvModalContent" class="p-8 max-h-[70vh] overflow-y-auto space-y-6">
            <div class="text-center py-8"><i class="fas fa-circle-notch fa-spin text-3xl text-rdc-blue"></i></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Reject Modal ──────────────────────────────────────────
function openRejectModal(appId, candidateName) {
    document.getElementById('rejectModalSubtitle').textContent = 'Candidat : ' + candidateName;
    document.getElementById('rejectForm').action = `/user/applications/${appId}/reject`;
    document.querySelector('#rejectForm textarea[name=rejection_reason]').value = '';
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// ── CV Modal ──────────────────────────────────────────────
function openCvModal(appId) {
    document.getElementById('cvModal').classList.remove('hidden');
    document.getElementById('cvModal').classList.add('flex');
    document.getElementById('cvModalContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-circle-notch fa-spin text-3xl text-rdc-blue"></i></div>';

    fetch(`/user/received-applications/${appId}/details`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const app = data.application;
            const cv = app.user?.cv;
            const name = app.user?.name || 'Candidat';
            const initials = name.split(' ').map(w => w[0] || '').join('').toUpperCase().slice(0, 2);
            const photoUrl = cv?.profile_photo
                ? `/storage/${cv.profile_photo}`
                : null;

            const avatarHtml = photoUrl
                ? `<img src="${photoUrl}" class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow" />`
                : `<div class="w-20 h-20 rounded-2xl bg-[#16a3b0] text-white flex items-center justify-center text-2xl font-black border-2 border-white shadow">${initials}</div>`;

            let fallbackCvHtml = '';
            if (!app.cv_attachment && cv && cv.cv_file) {
                fallbackCvHtml = `<a href="/storage/${cv.cv_file}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 rounded-xl transition">
                                   <i class="fas fa-file-pdf"></i>
                                   <span>Ancien profil CV complet</span>
                               </a>`;
            }

            document.getElementById('cvModalContent').innerHTML = `
                <div class="flex items-center gap-5 p-5 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                    ${avatarHtml}
                    <div>
                        <h4 class="text-xl font-black text-slate-900">${name}</h4>
                        <p class="text-xs text-rdc-blue font-bold uppercase tracking-widest mb-1">${cv ? (cv.full_name || name) : 'Candidat'}</p>
                        <p class="text-xs text-slate-500">${app.user.email} ${app.user.phone ? '• ' + app.user.phone : ''}</p>
                    </div>
                </div>
                
                ${app.message ? `
                <div class="mb-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Message</p>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-700 whitespace-pre-wrap">${app.message}</div>
                </div>` : ''}

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fichier CV</p>
                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-50 text-sm">
                        ${app.cv_attachment 
                            ? `<a href="/storage/${app.cv_attachment}" target="_blank" class="inline-flex items-center gap-2 text-rdc-blue font-bold hover:underline">
                                   <i class="fas fa-file-download"></i>
                                   <span>Télécharger le CV</span>
                               </a>`
                            : fallbackCvHtml || '<span class="text-slate-500 italic">Aucun CV joint.</span>'}
                    </div>
                </div>
            `;
        })
        .catch(() => {
            document.getElementById('cvModalContent').innerHTML = '<p class="text-red-500 text-center">Erreur lors du chargement.</p>';
        });
}

function closeCvModal() {
    document.getElementById('cvModal').classList.add('hidden');
    document.getElementById('cvModal').classList.remove('flex');
}

document.getElementById('cvModal').addEventListener('click', function(e) {
    if (e.target === this) closeCvModal();
});
</script>
@endpush
@endsection
