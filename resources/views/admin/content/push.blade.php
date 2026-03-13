@extends('layouts.admin')

@section('title', 'Notifications Push')
@section('header_title', 'Communication Live')
@section('page_title', 'Console Broadcast')
@section('page_subtitle', 'Envoyez des notifications en temps réel à l\'ensemble des utilisateurs de l\'écosystème ServiceRDC.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Composer -->
        <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-rdc-blue/5 rounded-full blur-[80px]"></div>
            <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-widest mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
                <i class="fas fa-paper-plane text-rdc-blue"></i> Broadcast
            </h4>
            <div class="space-y-4 sm:space-y-6 relative z-10">
                <div class="space-y-1 sm:space-y-2">
                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Cible</label>
                    <select class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold outline-none">
                        <option>Tous</option>
                        <option>Artisans</option>
                        <option>Clients</option>
                    </select>
                </div>
                <div class="space-y-1 sm:space-y-2">
                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Titre</label>
                    <input type="text" placeholder="Entrez le titre..." class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold outline-none">
                </div>
                <div class="space-y-1 sm:space-y-2">
                    <label class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-3 sm:ml-4">Message</label>
                    <textarea rows="3" placeholder="Texte..." class="w-full px-5 sm:px-8 py-3.5 sm:py-4 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold outline-none resize-none"></textarea>
                </div>
                <div class="pt-4 sm:pt-6">
                    <button class="w-full py-4 sm:py-5 bg-slate-900 text-white font-black rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200 hover:bg-rdc-blue transition-all">Envoyer</button>
                </div>
            </div>
        </div>

        <!-- Preview & Analytics -->
        <div class="space-y-6 sm:space-y-8">
            <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-40 sm:w-48 h-10 sm:h-12 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 flex items-center justify-center text-[8px] sm:text-[10px] font-black text-slate-300 uppercase tracking-widest mb-4 truncate">Aperçu</div>
                <div class="w-full max-w-[280px] bg-slate-900 rounded-[1.5rem] sm:rounded-3xl p-3 sm:p-4 text-left shadow-2xl">
                    <div class="flex items-center gap-2 sm:gap-3 mb-2">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 bg-rdc-blue rounded-md flex items-center justify-center text-[7px] text-white font-bold">SRDC</div>
                        <span class="text-[8px] sm:text-[9px] font-black text-white/50 uppercase truncate">HQ</span>
                    </div>
                    <p class="text-[9px] sm:text-[10px] font-black text-white mb-1 truncate">Boostez votre visibilité !</p>
                    <p class="text-[8px] sm:text-[9px] text-white/40 leading-tight">Découvrez les nouvelles fonctionnalités premium.</p>
                </div>
            </div>
            
            <div class="bg-blue-50 p-6 sm:p-8 rounded-[2rem] sm:rounded-[3rem] border border-blue-100">
                <h4 class="text-[8px] sm:text-[10px] font-black text-rdc-blue uppercase tracking-widest mb-4 sm:mb-6 text-center">Taux d'ouverture</h4>
                <div class="flex items-end justify-center gap-1 h-24 sm:h-32 mb-4 sm:mb-6">
                    @foreach([40, 60, 45, 80, 55, 70, 90] as $h)
                        <div class="flex-1 bg-rdc-blue rounded-full" style="height: <?php echo $h; ?>%;"></div>
                    @endforeach
                </div>
                <p class="text-center text-xl sm:text-2xl font-black text-slate-900 truncate">74.8<span class="text-xs uppercase">%</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
