@extends('layouts.admin')

@section('title', 'Gestion Actualités')
@section('header_title', 'Centre de Presse HQ')
@section('page_title', 'Édition de Contenu')
@section('page_subtitle', 'Publiez des articles, des mises à jour système et des annonces officielles pour la communauté.')

@section('content')
<div class="space-y-8 pb-20" x-data="{
    showModal: false,
    newArticle: { title: '', category: 'GÉNÉRAL', content: '' },
    loading: false,
    publishArticle() {
        this.loading = true;
        fetch('{{ route('admin.content.news.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.newArticle)
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            this.showModal = false;
            window.location.reload();
        });
    },
    deleteArticle(id) {
        if(confirm('Supprimer cet article ?')) {
            fetch(`/admin/content/news/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    }
}">
    <!-- HUD Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Annonces Publiées -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-blue-50 text-rdc-blue rounded-lg sm:rounded-xl">
                    <i class="fas fa-bullhorn text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Annonces Publiées</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $articles->count() }}</h3>
            </div>
        </div>

        <!-- Vues Totales -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-eye text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Vues Totales</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ number_format($articles->sum('views')) }}</h3>
            </div>
        </div>

        <!-- Moyenne Vues/Article -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-start justify-between mb-3 sm:mb-4 relative z-10">
                <div class="p-2 sm:p-3 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl">
                    <i class="fas fa-chart-simple text-sm sm:text-xl"></i>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] sm:text-sm font-medium text-slate-500 truncate">Vues par Article</p>
                <h3 class="text-lg sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $articles->count() > 0 ? number_format($articles->sum('views') / $articles->count(), 1) : 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- News List -->
    <div class="bg-white rounded-[2.5rem] sm:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative min-h-[500px]">
        <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/20">
            <h3 class="text-sm sm:text-lg font-black text-slate-900 uppercase tracking-tight">Registre des Annonces</h3>
            <button @click="showModal = true" class="px-4 sm:px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase rounded-xl tracking-widest hover:bg-rdc-blue transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                <i class="fas fa-plus"></i> Publier une actualité
            </button>
        </div>
        
        <div class="overflow-x-hidden">
            <table class="w-full text-left table-fixed lg:table-auto border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="w-[50%] sm:w-auto pl-4 pr-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-nowrap">Article / Date</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie</th>
                        <th class="w-[20%] sm:w-auto px-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center text-nowrap">Vues</th>
                        <th class="w-[30%] sm:w-auto pr-4 pl-2 sm:px-8 py-6 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($articles as $article)
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="pl-4 pr-2 sm:px-8 py-4 sm:py-6">
                                <div class="flex items-center gap-3">
                                    <div class="hidden sm:flex w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 shadow-sm">
                                        <img src="{{ $article->image_url ?? 'https://images.unsplash.com/photo-1573164713714-d95e4309a66d?q=80&w=100&auto=format&fit=crop' }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] sm:text-sm font-black text-slate-900 block truncate group-hover:text-rdc-blue transition-colors">{{ $article->title }}</span>
                                        <span class="text-[7px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5 sm:mt-1 block">Publié le {{ $article->published_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-6">
                                <span class="px-3 py-1 bg-blue-50 text-rdc-blue rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-100">{{ $article->category }}</span>
                            </td>
                            <td class="px-2 sm:px-8 py-4 sm:py-6 text-center">
                                <span class="text-[11px] sm:text-base font-black text-slate-900 tracking-tighter">{{ number_format($article->views) }}</span>
                            </td>
                            <td class="pr-4 pl-2 sm:px-8 py-4 sm:py-6 text-right">
                                <div class="flex items-center justify-end gap-2 sm:gap-3">
                                    <button class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-white text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-100">
                                        <i class="fas fa-eye text-[9px] sm:text-sm"></i>
                                    </button>
                                    <button @click="deleteArticle({{ $article->id }})" class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl bg-white text-slate-400 hover:bg-rdc-red hover:text-white transition-all shadow-sm border border-slate-100">
                                        <i class="fas fa-trash text-[9px] sm:text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-24">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-5xl mb-8 shadow-inner ring-8 ring-slate-50/50">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                    <h4 class="text-base sm:text-xl font-black text-slate-400 uppercase tracking-widest">Aucune Actualité</h4>
                                    <p class="text-[10px] sm:text-xs text-slate-300 font-bold uppercase tracking-tight mt-3 max-w-[300px] mx-auto leading-relaxed">
                                        Commencez par publier votre première annonce officielle pour informer votre communauté.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Publish -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="showModal = false" class="bg-white w-full max-w-xl rounded-[3rem] shadow-3xl overflow-hidden p-10">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-8">Nouvelle Actualité</h3>
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Titre</label>
                    <input type="text" x-model="newArticle.title" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Catégorie</label>
                    <select x-model="newArticle.category" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-black outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10">
                        <option>GÉNÉRAL</option>
                        <option>SYSTÈME</option>
                        <option>PROMO</option>
                        <option>COMMUNAUTÉ</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Contenu</label>
                    <textarea x-model="newArticle.content" rows="4" class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl text-sm font-medium outline-none ring-1 ring-slate-100 focus:ring-4 focus:ring-rdc-blue/10"></textarea>
                </div>
                <div class="pt-4 flex gap-4">
                    <button @click="showModal = false" class="flex-1 py-5 bg-slate-100 text-slate-400 font-black rounded-2xl text-xs uppercase tracking-widest">Annuler</button>
                    <button @click="publishArticle()" :disabled="loading" class="flex-1 py-5 bg-slate-900 text-white font-black rounded-2xl text-xs uppercase tracking-widest hover:bg-rdc-blue transition-all disabled:opacity-50">
                        <span x-text="loading ? 'Publication...' : 'Publier Maintenant'">Publier Maintenant</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
