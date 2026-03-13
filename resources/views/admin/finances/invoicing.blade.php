@extends('layouts.admin')

@section('title', 'Facturation')
@section('header_title', 'Gestion Comptable')
@section('page_title', 'Centre de Facturation')
@section('page_subtitle', 'Gérez les factures émises pour les services premium et les frais de plateforme.')

@section('content')
<div class="space-y-8 pb-20">
    <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-5 sm:p-8 py-5 border-b border-slate-50 flex items-center justify-between gap-4">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Factures</h3>
            <button class="px-4 sm:px-6 py-2 sm:py-2.5 bg-slate-50 text-slate-400 text-[8px] sm:text-[10px] font-black uppercase rounded-lg sm:rounded-xl tracking-tighter sm:tracking-widest border border-slate-100 hover:bg-slate-900 hover:text-white transition-all shrink-0">Tout .PDF</button>
        </div>
        
        <div class="overflow-x-auto sm:overflow-x-visible">
            <table class="w-full text-left table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-1/3 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest">N° Facture</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="w-1/3 sm:w-auto px-2 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest">Montant</th>
                        <th class="w-1/4 sm:w-auto px-4 sm:px-8 py-4 sm:py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-right">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 sm:px-8 py-4 sm:py-6 font-mono text-[9px] sm:text-xs font-black text-slate-900 truncate">#INV-2026-001</td>
                        <td class="hidden sm:table-cell px-8 py-6">
                            <p class="text-sm font-black text-slate-900">Jean-Charles Kabila</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Pack Premium Artisans</p>
                        </td>
                        <td class="px-2 sm:px-8 py-4 sm:py-6 font-black text-[9px] sm:text-sm text-slate-900">45.00 $</td>
                        <td class="px-4 sm:px-8 py-4 sm:py-6 text-right">
                            <button class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-50 text-slate-400 hover:bg-rdc-blue hover:text-white transition-all shadow-sm">
                                <i class="fas fa-file-pdf text-xs"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
