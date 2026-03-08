@extends('layouts.admin')

@section('title', 'Gestion des Catégories')
@section('header_title', 'Catégories')
@section('page_title', 'Architecture des Services')
@section('page_subtitle', 'Gérez les domaines d\'expertise et les catégories de services disponibles sur la plateforme.')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Left: Category List -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Icône & Nom</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Slug</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Description</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center text-lg shadow-sm border border-blue-100 group-hover:bg-rdc-blue group-hover:text-white transition-all">
                                        <i class="{{ $category->icon ?? 'fas fa-tags' }}"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-900">{{ $category->name }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wider">
                                    {{ $category->slug }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs text-slate-500 line-clamp-1 max-w-[200px]" title="{{ $category->description }}">
                                    {{ $category->description ?: 'Aucune description' }}
                                </p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-rdc-blue transition-all" 
                                            onclick="editCategory({{ json_encode($category) }})" title="Modifier">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-rdc-red hover:bg-red-50 transition-all" title="Supprimer">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 text-2xl mb-4">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <p class="text-slate-400 text-sm font-medium">Aucune catégorie disponible.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-50">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right: Form -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 sticky top-8">
            <h3 id="formTitle" class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-3">
                <i class="fas fa-plus-circle text-rdc-blue"></i> Ajouter un Domaine
            </h3>
            
            <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nom du domaine</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                           placeholder="ex: Plomberie, Design, etc.">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Icône (FontAwesome)</label>
                    <div class="relative">
                        <i class="fas fa-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="icon" id="icon"
                               class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: fas fa-tools">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Slug (Lien URL)</label>
                    <input type="text" name="slug" id="slug"
                           class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                           placeholder="ex: plomberie-batiment">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 ml-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                              placeholder="Brève description du domaine..."></textarea>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-rdc-blue hover:shadow-lg hover:shadow-blue-500/20 transition-all">
                        Enregistrer
                    </button>
                    <button type="button" id="cancelBtn" class="hidden px-6 py-4 bg-slate-100 text-slate-500 rounded-2xl text-sm font-bold hover:bg-slate-200 transition-all">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(category) {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit text-rdc-blue"></i> Modifier : ' + category.name;
        document.getElementById('categoryForm').action = "{{ url('admin/categories') }}/" + category.id;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('name').value = category.name;
        document.getElementById('icon').value = category.icon;
        document.getElementById('slug').value = category.slug;
        document.getElementById('description').value = category.description;
        
        document.getElementById('cancelBtn').classList.remove('hidden');
    }

    document.getElementById('cancelBtn').onclick = function() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle text-rdc-blue"></i> Ajouter un Domaine';
        document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('categoryForm').reset();
        this.classList.add('hidden');
    }
</script>
@endsection
