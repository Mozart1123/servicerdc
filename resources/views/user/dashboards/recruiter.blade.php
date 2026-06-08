@extends('layouts.user')

@section('title', 'Tableau de bord Recruteur')
@section('header_title', 'Tableau de bord Recruteur')

@push('styles')
<style>
/* ===== Counter Animation ===== */
@keyframes countUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeSlideRight {
    from { opacity: 0; transform: translateX(-30px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes pulse-ring {
    0%   { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(41, 182, 209, 0.5); }
    70%  { transform: scale(1);   box-shadow: 0 0 0 12px rgba(41, 182, 209, 0); }
    100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(41, 182, 209, 0); }
}
@keyframes shimmer {
    0%   { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33%       { transform: translateY(-10px) rotate(2deg); }
    66%       { transform: translateY(-5px) rotate(-1deg); }
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
@keyframes blob {
    0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
    50%       { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
}

.stat-card {
    animation: fadeSlideUp 0.6s ease forwards;
    opacity: 0;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.hero-banner {
    animation: fadeSlideDown 0.7s ease forwards;
}
.recent-section {
    animation: fadeSlideUp 0.7s ease 0.5s forwards;
    opacity: 0;
}
.quick-action {
    animation: fadeSlideUp 0.6s ease forwards;
    opacity: 0;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
}
.quick-action:nth-child(1) { animation-delay: 0.6s; }
.quick-action:nth-child(2) { animation-delay: 0.75s; }
.quick-action:nth-child(3) { animation-delay: 0.9s; }
.quick-action:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 24px 48px rgba(0,0,0,0.1);
}

.app-row {
    animation: fadeSlideRight 0.5s ease forwards;
    opacity: 0;
    transition: background 0.2s ease;
}
.app-row:hover { background: rgba(248, 250, 252, 0.8); }

.blob-shape {
    animation: blob 7s infinite ease-in-out;
}
.float-icon {
    animation: float 4s ease-in-out infinite;
}
.pulse-dot {
    animation: pulse-ring 2s infinite;
}
.spin-ring {
    animation: spin-slow 8s linear infinite;
}

.counter-num {
    display: inline-block;
    transition: all 0.3s ease;
}
.shimmer-line {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
}

/* Glassmorphism for banner content */
.glass-pill {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.25);
}
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- HERO BANNER                                                   --}}
    {{-- ============================================================ --}}
    <div class="hero-banner relative bg-gradient-to-br from-[#29B6D1] via-blue-500 to-indigo-600 rounded-3xl p-8 overflow-hidden shadow-2xl shadow-blue-500/30">
        {{-- Animated background blobs --}}
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 blob-shape" style="animation-delay:0s;"></div>
        <div class="absolute bottom-0 left-10 w-48 h-48 bg-white/10 blob-shape" style="animation-delay:3.5s;"></div>
        <div class="absolute top-8 right-48 w-32 h-32 bg-yellow-300/20 blob-shape" style="animation-delay:1.5s;"></div>

        {{-- Spinning decorative ring --}}
        <div class="absolute right-12 top-1/2 -translate-y-1/2 w-40 h-40 rounded-full border-4 border-dashed border-white/20 spin-ring hidden md:block"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 glass-pill px-4 py-1.5 rounded-full mb-4">
                    <span class="w-2 h-2 bg-emerald-300 rounded-full pulse-dot"></span>
                    <span class="text-white/90 text-xs font-bold uppercase tracking-widest">Recruteur / Entreprise</span>
                </div>
                <p class="text-blue-100 text-sm font-medium mb-1">Bienvenue,</p>
                <h2 class="text-3xl lg:text-4xl font-black text-white font-heading leading-tight">{{ auth()->user()->name }}</h2>
                <p class="text-blue-100/80 text-sm mt-3 max-w-sm">
                    Gérez vos offres, suivez les candidatures et trouvez les meilleurs talents pour votre équipe.
                </p>
            </div>
            <a href="{{ route('user.jobs.create') }}"
               class="flex-none flex items-center gap-3 bg-white text-rdc-blue font-black text-sm px-6 py-3.5 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:scale-105 group">
                <span class="w-8 h-8 bg-rdc-blue/10 rounded-xl flex items-center justify-center group-hover:bg-rdc-blue group-hover:text-white transition-all">
                    <i class="fas fa-plus text-sm"></i>
                </span>
                Publier une offre
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STATS GRID                                                    --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Offres publiées --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden cursor-default">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full"></div>
            <div class="absolute -right-1 -top-1 w-12 h-12 bg-rdc-blue/10 rounded-full flex items-center justify-center">
                <i class="fas fa-briefcase text-rdc-blue text-sm"></i>
            </div>
            <p class="text-3xl font-black text-slate-900 counter-num mt-1" data-target="{{ $stats['my_job_offers_count'] ?? 0 }}">0</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Offres publiées</p>
            <div class="mt-4 h-1 rounded-full bg-blue-100 overflow-hidden">
                <div class="h-full bg-rdc-blue rounded-full" style="width:{{ min(($stats['my_job_offers_count'] ?? 0) * 20, 100) }}%; transition: width 1.5s ease 0.5s;"></div>
            </div>
        </div>

        {{-- Candidatures reçues --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden cursor-default">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full"></div>
            <div class="absolute -right-1 -top-1 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-green-500 text-sm"></i>
            </div>
            <p class="text-3xl font-black text-slate-900 counter-num mt-1" data-target="{{ $stats['applications_received'] ?? 0 }}">0</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Candidatures reçues</p>
            <div class="mt-4 h-1 rounded-full bg-green-100 overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width:{{ min(($stats['applications_received'] ?? 0) * 10, 100) }}%; transition: width 1.5s ease 0.6s;"></div>
            </div>
        </div>

        {{-- En attente --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden cursor-default">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-50 rounded-full"></div>
            <div class="absolute -right-1 -top-1 w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-amber-500 text-sm"></i>
            </div>
            <p class="text-3xl font-black text-slate-900 counter-num mt-1" data-target="{{ $stats['pending_applications_count'] ?? 0 }}">0</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">En attente</p>
            <div class="mt-4 h-1 rounded-full bg-amber-100 overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full" style="width:{{ min(($stats['pending_applications_count'] ?? 0) * 15, 100) }}%; transition: width 1.5s ease 0.7s;"></div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden cursor-default">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-50 rounded-full"></div>
            <div class="absolute -right-1 -top-1 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bell text-purple-500 text-sm"></i>
            </div>
            <p class="text-3xl font-black text-slate-900 counter-num mt-1" data-target="{{ $stats['unread_notifications'] ?? 0 }}">0</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Notifications</p>
            <div class="mt-4 h-1 rounded-full bg-purple-100 overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full" style="width:{{ min(($stats['unread_notifications'] ?? 0) * 20, 100) }}%; transition: width 1.5s ease 0.8s;"></div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- RECENT APPLICATIONS                                           --}}
    {{-- ============================================================ --}}
    <div class="recent-section bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
                <h3 class="font-heading font-black text-slate-900">Dernières candidatures</h3>
                <p class="text-xs text-slate-400 mt-0.5">Candidats ayant postulé à vos offres</p>
            </div>
            <a href="{{ route('user.applications.received') }}"
               class="text-xs font-bold text-rdc-blue hover:text-rdc-blue-dark transition-colors flex items-center gap-1 group">
               Voir tout
               <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform text-[10px]"></i>
            </a>
        </div>

        @if(isset($recentApplications) && $recentApplications->count())
            <div class="divide-y divide-slate-50">
                @foreach($recentApplications as $i => $app)
                @php
                    $statusMap = [
                        'pending'  => ['label' => 'En attente',  'bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'dot' => 'bg-amber-400'],
                        'approved' => ['label' => 'Approuvée',   'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-400'],
                        'accepted' => ['label' => 'Acceptée',    'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-400'],
                        'rejected' => ['label' => 'Refusée',     'bg' => 'bg-red-50',     'text' => 'text-red-500',     'dot' => 'bg-red-400'],
                        'interview'=> ['label' => 'Entretien',   'bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'dot' => 'bg-blue-400'],
                        'hired'    => ['label' => 'Embauché(e)', 'bg' => 'bg-purple-50',  'text' => 'text-purple-600',  'dot' => 'bg-purple-400'],
                    ];
                    $sc = $statusMap[$app->status] ?? $statusMap['pending'];
                @endphp
                <div class="app-row px-6 py-4 flex items-center gap-4" style="animation-delay: {{ $i * 0.08 }}s;">
                    <div class="relative flex-none">
                        <img src="{{ $app->user?->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($app->user?->name ?? 'U').'&background=29B6D1&color=fff&size=64' }}"
                             class="w-11 h-11 rounded-2xl object-cover border-2 border-white shadow-sm" alt="">
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 {{ $sc['dot'] }} border-2 border-white rounded-full {{ $app->status === 'pending' ? 'pulse-dot' : '' }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 text-sm truncate">{{ $app->user?->name ?? 'Inconnu' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $app->jobOffer?->title ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 flex-none">
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 {{ $sc['bg'] }} {{ $sc['text'] }} text-[10px] font-black rounded-full uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 {{ $sc['dot'] }} rounded-full"></span>
                            {{ $sc['label'] }}
                        </span>
                        <span class="text-[10px] text-slate-300 font-semibold whitespace-nowrap">{{ $app->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 float-icon">
                    <i class="fas fa-inbox text-3xl text-slate-200"></i>
                </div>
                <p class="font-bold text-slate-700 text-base mb-2">Aucune candidature reçue</p>
                <p class="text-sm text-slate-400 max-w-xs mx-auto mb-6">Publiez une offre pour commencer à attirer des candidats talentueux.</p>
                <a href="{{ route('user.jobs.create') }}"
                   class="inline-flex items-center gap-2 bg-rdc-blue text-white text-xs font-black px-6 py-3 rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle"></i> Publier votre première offre
                </a>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- QUICK ACTIONS                                                 --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <a href="{{ route('user.jobs.create') }}" class="quick-action flex items-center gap-4 p-5 bg-gradient-to-br from-rdc-blue/5 to-blue-50 border border-rdc-blue/20 rounded-2xl group">
            <span class="w-14 h-14 bg-rdc-blue rounded-2xl flex items-center justify-center shadow-lg shadow-rdc-blue/30 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plus text-white text-xl"></i>
            </span>
            <div>
                <p class="font-black text-slate-900 text-sm group-hover:text-rdc-blue transition-colors">Nouvelle offre</p>
                <p class="text-xs text-slate-400 mt-0.5">Publier un poste</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-slate-200 group-hover:text-rdc-blue group-hover:translate-x-1 transition-all text-sm"></i>
        </a>

        <a href="{{ route('user.applications.received') }}" class="quick-action flex items-center gap-4 p-5 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-2xl group">
            <span class="w-14 h-14 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-white text-xl"></i>
            </span>
            <div>
                <p class="font-black text-slate-900 text-sm group-hover:text-green-600 transition-colors">Candidatures</p>
                <p class="text-xs text-slate-400 mt-0.5">Gérer les dossiers</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-slate-200 group-hover:text-green-500 group-hover:translate-x-1 transition-all text-sm"></i>
        </a>

        <a href="{{ route('user.messages.index') }}" class="quick-action flex items-center gap-4 p-5 bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl group">
            <span class="w-14 h-14 bg-purple-500 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-inbox text-white text-xl"></i>
            </span>
            <div>
                <p class="font-black text-slate-900 text-sm group-hover:text-purple-600 transition-colors">Messages</p>
                <p class="text-xs text-slate-400 mt-0.5">Contacter les candidats</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-slate-200 group-hover:text-purple-500 group-hover:translate-x-1 transition-all text-sm"></i>
        </a>
    </div>

</div>

@push('scripts')
<script>
// ===== Animated Number Counters =====
function animateCounter(el) {
    const target = parseInt(el.dataset.target) || 0;
    if (target === 0) { el.textContent = '0'; return; }
    const duration = 1200;
    const step = 16;
    const totalSteps = duration / step;
    let current = 0;
    const increment = target / totalSteps;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target;
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current);
        }
    }, step);
}

// Start counters when element enters viewport
const counters = document.querySelectorAll('.counter-num');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

counters.forEach(c => observer.observe(c));

// ===== Stagger app-row animations =====
document.querySelectorAll('.app-row').forEach((row, i) => {
    row.style.opacity = '0';
    setTimeout(() => {
        row.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        row.style.transform = 'translateX(0)';
        row.style.opacity = '1';
    }, 600 + i * 80);
});
</script>
@endpush

@endsection
