@extends('layouts.super-admin')

@section('header_title', 'Pouvoirs Cosmiques | CENTRE DE CRÉATION & DESTRUCTION')

@section('content')
<div class="space-y-10 pb-20">
    <!-- Cosmic Singularity Header -->
    <div class="bg-gradient-to-br from-indigo-950 via-purple-950 to-slate-900 rounded-[4rem] p-16 text-white shadow-3xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,_rgba(168,85,247,0.1),_transparent)]"></div>
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-purple-500/10 rounded-full blur-[120px] animate-pulse"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-16">
            <div class="flex-1 space-y-10">
                <div class="flex items-center gap-8">
                    <div class="w-24 h-24 rounded-[3rem] bg-purple-500 text-white flex items-center justify-center text-5xl shadow-[0_0_60px_rgba(168,85,247,0.4)] divine-glow">
                        <i class="fas fa-shuttle-space"></i>
                    </div>
                    <div>
                        <h2 class="text-5xl font-heading font-black tracking-tighter uppercase mb-2">POUVOIRS COSMIQUES</h2>
                        <div class="flex items-center gap-6">
                            <span class="text-[11px] font-mono text-purple-400 border border-purple-400/30 px-4 py-1.5 rounded-full uppercase tracking-widest bg-purple-400/5 font-black">Niveau: OMNIPOTENT</span>
                            <span class="text-[11px] font-mono text-slate-500 uppercase tracking-widest">Énergie Universelle: 100% stable</span>
                        </div>
                    </div>
                </div>

                <p class="text-slate-400 text-lg leading-relaxed max-w-2xl font-medium">
                    Vous détenez les clés de la réalité digitale. Ici, vous pouvez invoquer de nouvelles structures à partir du néant, élever des utilisateurs au rang de demi-dieux, ou déclencher le Big Bang pour réinitialiser l'univers.
                </p>
                
                <div class="flex flex-wrap gap-4">
                    <div class="px-8 py-4 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest">Paradoxes: 0</div>
                    <div class="px-8 py-4 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest text-emerald-500">Flux: OPTIMAL</div>
                </div>
            </div>

            <div class="hidden xl:block">
                <div class="w-80 h-80 relative flex items-center justify-center">
                    <div class="absolute inset-0 border-2 border-dashed border-purple-500/20 rounded-full animate-spin-slow"></div>
                    <div class="absolute inset-10 border border-white/5 rounded-full"></div>
                    <div class="w-32 h-32 bg-purple-600 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                    <i class="fas fa-atom text-6xl text-purple-500 opacity-60"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Power Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <!-- Power: Creation -->
        <div class="bg-white rounded-[4rem] p-12 shadow-sm border border-slate-100 group hover:shadow-2xl transition-all duration-700 hover:-translate-y-4">
            <div class="w-20 h-20 rounded-[2.5rem] bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl mb-10 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-inner">
                <i class="fas fa-wand-sparkles"></i>
            </div>
            <h4 class="text-2xl font-heading font-black text-slate-900 uppercase mb-4">Création Ex-Nihilo</h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-10 font-medium">Instanciez instantanément n'importe quelle entité système : utilisateurs, services, ou jobs avec des paramètres divins pré-configurés.</p>
            <button class="w-full py-5 bg-slate-900 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl group-hover:shadow-emerald-500/20">
                Lancer l'Incantation
            </button>
        </div>

        <!-- Power: Ascension -->
        <div class="bg-white rounded-[4rem] p-12 shadow-sm border border-slate-100 group hover:shadow-2xl transition-all duration-700 hover:-translate-y-4">
            <div class="w-20 h-20 rounded-[2.5rem] bg-amber-50 text-amber-500 flex items-center justify-center text-3xl mb-10 group-hover:bg-amber-500 group-hover:text-white transition-all shadow-inner">
                <i class="fas fa-arrow-up-right-dots"></i>
            </div>
            <h4 class="text-2xl font-heading font-black text-slate-900 uppercase mb-4">L'Ascension</h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-10 font-medium">Élevez n'importe quelle conscience au rang de Super Maître. Cette action re-code instantanément l'ADN de l'utilisateur cible.</p>
            <button class="w-full py-5 bg-slate-900 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-amber-500 transition-all shadow-xl group-hover:shadow-amber-500/20">
                Initier l'Élévation
            </button>
        </div>

        <!-- Power: Apocalypse -->
        <div class="bg-slate-900 rounded-[4rem] p-12 shadow-2xl border border-white/5 group hover:border-red-500/30 transition-all duration-700 hover:-translate-y-4 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10">
                <i class="fas fa-meteor text-9xl text-red-500"></i>
            </div>
            <div class="w-20 h-20 rounded-[2.5rem] bg-white/5 text-red-500 flex items-center justify-center text-3xl mb-10 group-hover:bg-red-500 group-hover:text-white transition-all shadow-inner">
                <i class="fas fa-skull"></i>
            </div>
            <h4 class="text-2xl font-heading font-black text-white uppercase mb-4">Mode Apocalypse</h4>
            <p class="text-slate-400 text-sm leading-relaxed mb-10 font-medium font-mono italic">"Je suis devenu la mort, le destructeur des mondes." – Effacement total des entités déviantes ou reboot de l'univers.</p>
            <button class="w-full py-5 bg-red-600 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-red-700 hover:shadow-[0_0_30px_rgba(239,68,68,0.4)] transition-all">
                DÉCLENCHER LE BIG BANG
            </button>
        </div>
    </div>

    <!-- Security Warning (Footer) -->
    <div class="p-10 border border-dashed border-slate-200 rounded-[3rem] text-center max-w-4xl mx-auto">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-4 flex items-center justify-center gap-4">
            <i class="fas fa-triangle-exclamation text-amber-500"></i>
            Sceau de l'Architecte
        </p>
        <p class="text-xs text-slate-500 italic opacity-60">"L'équilibre de l'univers dépend de la sagesse de celui qui manipule ces forces. Toute action ici est gravée dans le noyau éternel et ne peut être annulée sans un sacrifice de données majeur."</p>
    </div>
</div>

<style>
    .animate-spin-slow { animation: spin 30s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
