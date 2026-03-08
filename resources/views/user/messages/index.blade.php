@extends('layouts.user')

@section('title', 'Messagerie')
@section('header_title', 'Messagerie')

@section('content')
<div class="h-[calc(100vh-12rem)] min-h-[600px] flex bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Conversations List -->
    <div class="w-full md:w-80 lg:w-96 border-r border-slate-100 flex flex-col shrink-0">
        <div class="p-6 border-b border-slate-50">
            <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-4">Conversations</h3>
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rdc-blue transition-colors"></i>
                <input type="text" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-inner">
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <!-- Active Conv -->
            <div class="p-4 bg-blue-50/50 border-l-4 border-rdc-blue cursor-pointer">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Vodacom+Recrutement&background=EE0000&color=fff" class="w-12 h-12 rounded-xl object-cover shadow-sm" alt="Avatar">
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-0.5">
                            <h4 class="font-bold text-slate-900 truncate">Vodacom RDC</h4>
                            <span class="text-[10px] font-bold text-rdc-blue">14:20</span>
                        </div>
                        <p class="text-xs text-rdc-blue font-bold truncate">Vous: D'accord, je serai présent...</p>
                    </div>
                </div>
            </div>

            <!-- Other Conv -->
            <div class="p-4 hover:bg-slate-50 border-l-4 border-transparent cursor-pointer transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Jean+M&background=007FFF&color=fff" class="w-12 h-12 rounded-xl object-cover shadow-sm" alt="Avatar">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-0.5">
                            <h4 class="font-bold text-slate-900 truncate group-hover:text-rdc-blue transition-colors">Jean Mutamba</h4>
                            <span class="text-[10px] text-slate-400 font-medium">Hier</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">Merci pour votre retour.</p>
                    </div>
                    <div class="w-2 h-2 bg-rdc-red rounded-full shrink-0"></div>
                </div>
            </div>

            <div class="p-4 hover:bg-slate-50 border-l-4 border-transparent cursor-pointer transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Service+Support&background=333&color=fff" class="w-12 h-12 rounded-xl object-cover shadow-sm" alt="Avatar">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-0.5">
                            <h4 class="font-bold text-slate-900 truncate group-hover:text-rdc-blue transition-colors">Support ServiceRDC</h4>
                            <span class="text-[10px] text-slate-400 font-medium">12 Janv</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">Votre compte a été vérifié.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Window -->
    <div class="flex-1 flex flex-col bg-slate-50/30">
        <!-- Chat Header -->
        <div class="p-6 bg-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=Vodacom+Recrutement&background=EE0000&color=fff" class="w-10 h-10 rounded-xl object-cover shadow-sm" alt="Avatar">
                <div>
                    <h4 class="font-bold text-slate-900">Vodacom RDC</h4>
                    <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> En ligne
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue hover:bg-blue-50 rounded-xl transition-all"><i class="fas fa-phone"></i></button>
                <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue hover:bg-blue-50 rounded-xl transition-all"><i class="fas fa-video"></i></button>
                <button class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue hover:bg-blue-50 rounded-xl transition-all"><i class="fas fa-info-circle"></i></button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar">
            <!-- Received Message -->
            <div class="flex gap-4 max-w-[80%]">
                <img src="https://ui-avatars.com/api/?name=Vodacom+Recrutement&background=EE0000&color=fff" class="w-8 h-8 rounded-lg mt-auto shrink-0" alt="Avatar">
                <div class="space-y-1">
                    <div class="bg-white p-4 rounded-2xl rounded-bl-none shadow-sm border border-slate-100 text-sm text-slate-700 leading-relaxed">
                        Bonjour ! Nous avons bien reçu votre candidature pour le poste de Senior Designer. Êtes-vous disponible demain pour un entretien téléphonique ?
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium ml-2">14:15</span>
                </div>
            </div>

            <!-- Sent Message -->
            <div class="flex gap-4 max-w-[80%] ml-auto flex-row-reverse">
                <div class="space-y-1 text-right">
                    <div class="bg-rdc-blue p-4 rounded-2xl rounded-br-none shadow-lg shadow-blue-500/20 text-sm text-white leading-relaxed">
                        Bonjour. Oui, je suis disponible demain à partir de 14h00.
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium mr-2">14:18</span>
                </div>
            </div>

            <!-- Received Message -->
            <div class="flex gap-4 max-w-[80%]">
                <img src="https://ui-avatars.com/api/?name=Vodacom+Recrutement&background=EE0000&color=fff" class="w-8 h-8 rounded-lg mt-auto shrink-0" alt="Avatar">
                <div class="space-y-1">
                    <div class="bg-white p-4 rounded-2xl rounded-bl-none shadow-sm border border-slate-100 text-sm text-slate-700 leading-relaxed">
                        D'accord, je serai présent. Merci pour cette opportunité.
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium ml-2">14:20</span>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 bg-white border-t border-slate-100">
            <form class="flex items-center gap-4">
                <button type="button" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rdc-blue transition-colors">
                    <i class="fas fa-paperclip text-lg"></i>
                </button>
                <div class="flex-1 relative">
                    <input type="text" placeholder="Écrivez votre message..." class="w-full px-6 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-inner outline-none">
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rdc-yellow transition-colors">
                        <i class="far fa-smile text-lg"></i>
                    </button>
                </div>
                <button type="submit" class="w-12 h-12 bg-rdc-blue text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 hover:bg-rdc-blue-dark hover:scale-105 transition-all">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
