@extends('layouts.admin')

@section('title', 'Gestion des Rapports')
@section('header_title', 'Rapports & Archives')
@section('page_title', 'Centre de Rapports')
@section('page_subtitle', 'Consultez, gérez et générez des rapports détaillés sur l\'activité du système.')

@section('content')
<div class="space-y-8">
    <!-- Reports Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Rapports</span>
                <i class="fas fa-file-alt text-rdc-blue"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900">{{ $reports->total() ?? 0 }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Générés ce mois</span>
                <i class="fas fa-calendar-check text-emerald-500"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900">12</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Type dominant</span>
                <i class="fas fa-chart-pie text-amber-500"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900">Financier</h3>
        </div>
    </div>

    <!-- Master Generator & Section Links -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Générateur de Rapports Maître</h4>
            <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase">Type de Données</label>
                        <select name="type" class="w-full text-xs font-bold border-slate-200 rounded-xl px-4 py-3 focus:ring-rdc-blue/20">
                            <option value="full">Système Complet</option>
                            <option value="financial">États Financiers</option>
                            <option value="users">Utilisateurs & Roles</option>
                            <option value="services">Services & Jobs</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase">Période d'Audit</label>
                        <select name="period" class="w-full text-xs font-bold border-slate-200 rounded-xl px-4 py-3 focus:ring-rdc-blue/20">
                            <option value="day">Aujourd'hui</option>
                            <option value="week">7 derniers jours</option>
                            <option value="month">Ce mois-ci</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:bg-rdc-blue transition-all active:scale-[0.98]">
                    Compiler & Générer le Rapport
                </button>
            </form>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent"></div>
            <h4 class="text-xs font-black text-white/30 uppercase tracking-[0.2em] mb-6 relative z-10">Analytique Avancée</h4>
            <div class="grid grid-cols-2 gap-4 relative z-10">
                <a href="{{ route('admin.reports-hq.analytics') }}" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl transition-all group/link">
                    <i class="fas fa-chart-line text-rdc-blue mb-3"></i>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">Performances <i class="fas fa-arrow-right ml-1 opacity-0 group-hover/link:opacity-100 transition-opacity"></i></p>
                </a>
                <a href="{{ route('admin.reports-hq.financial') }}" class="p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl transition-all group/link">
                    <i class="fas fa-vault text-amber-500 mb-3"></i>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">Finances <i class="fas fa-arrow-right ml-1 opacity-0 group-hover/link:opacity-100 transition-opacity"></i></p>
                </a>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" x-data="{
        confirmDelete(id) {
            if(confirm('Supprimer ce rapport définitivement ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/reports/${id}`;
                form.innerHTML = `
                    <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                    <input type='hidden' name='_method' value='DELETE'>
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }">
        <div class="px-8 py-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Archives des Rapports</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Historique de génération</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type / Période</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Généré le</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Par</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reports as $report)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-4 text-xs font-black text-slate-400">#{{ $report->id }}</td>
                        <td class="px-8 py-4">
                            <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-600 uppercase">{{ $report->type }}</span>
                        </td>
                        <td class="px-8 py-4 text-xs font-bold text-slate-600">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-8 py-4 text-xs font-bold text-slate-900">{{ $report->generator->name ?? 'Système' }}</td>
                        <td class="px-8 py-4 text-right flex items-center justify-end gap-2">
                            <a href="{{ route('admin.reports.download', $report->id) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-rdc-blue rounded-lg hover:bg-rdc-blue hover:text-white transition-all">
                                <i class="fas fa-download text-[10px]"></i>
                            </a>
                            <button @click="confirmDelete({{ $report->id }})" class="w-8 h-8 flex items-center justify-center bg-red-50 text-rdc-red rounded-lg hover:bg-rdc-red hover:text-white transition-all">
                                <i class="fas fa-trash-can text-[10px]"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <i class="fas fa-file-invoice text-4xl text-slate-200"></i>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Aucun rapport disponible</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
        <div class="px-8 py-6 border-t border-slate-50">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
