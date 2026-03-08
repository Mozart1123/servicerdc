@extends('layouts.admin')

@section('title', 'Facturation')
@section('header_title', 'Gestion Comptable')
@section('page_title', 'Centre de Facturation')
@section('page_subtitle', 'Gérez les factures émises pour les services premium et les frais de plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Dernières Factures</h3>
            <button class="px-6 py-2.5 bg-slate-50 text-slate-400 text-[10px] font-black uppercase rounded-xl tracking-widest border border-slate-100 hover:bg-slate-900 hover:text-white transition-all">Télécharger Tout</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">N° Facture</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Montant HT</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6 font-mono text-xs font-black text-slate-900">#INV-2026-001</td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-900">Jean-Charles Kabila</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Pack Premium Artisans</p>
                        </td>
                        <td class="px-8 py-6 font-black text-slate-900">45.00 $</td>
                        <td class="px-8 py-6 text-right">
                            <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all shadow-sm">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
