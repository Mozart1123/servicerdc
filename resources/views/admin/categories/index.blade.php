@extends('layouts.admin')

@section('title', 'Gestion des Catégories')
@section('header_title', 'Catégories')
@section('page_title', 'Architecture des Services')
@section('page_subtitle', 'Gérez les domaines d\'expertise et les catégories de services disponibles sur la plateforme.')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 sm:gap-8">
    <!-- Left: Category List -->
    <div class="xl:col-span-2 space-y-6 order-2 xl:order-1">
        <div class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto sm:overflow-x-visible">
                <table class="w-full text-left border-collapse table-fixed sm:table-auto">
                    <thead>
                        <tr class="bg-slate-50/50 text-nowrap">
                            <th class="w-2/3 sm:w-auto px-4 sm:px-8 py-4 sm:py-5 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest">Domaine</th>
                            <th class="hidden sm:table-cell px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Slug</th>
                            <th class="hidden lg:table-cell px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Description</th>
                            <th class="w-1/3 sm:w-auto px-4 sm:px-8 py-4 sm:py-5 text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-tighter sm:tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-4 sm:px-8 py-4 sm:py-5">
                                <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-50 text-rdc-blue flex items-center justify-center text-sm sm:text-lg shadow-sm border border-blue-100 group-hover:bg-rdc-blue group-hover:text-white transition-all shrink-0">
                                        <i class="{{ $category->icon ?? 'fas fa-tags' }}"></i>
                                    </div>
                                    <p class="text-xs sm:text-sm font-black text-slate-900 truncate">{{ $category->name }}</p>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-5">
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wider truncate inline-block max-w-[120px]">
                                    {{ $category->slug }}
                                </span>
                            </td>
                            <td class="hidden lg:table-cell px-8 py-5">
                                <p class="text-xs text-slate-500 line-clamp-1 max-w-[200px]" title="{{ $category->description }}">
                                    {{ $category->description ?: 'N/A' }}
                                </p>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-right">
                                <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                                    <button class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-rdc-blue transition-all" 
                                            onclick="editCategory({{ json_encode($category) }})" title="Modifier">
                                        <i class="fas fa-edit text-[10px] sm:text-xs"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-50 text-rdc-red hover:bg-red-50 transition-all">
                                            <i class="fas fa-trash-alt text-[10px] sm:text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 sm:py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 text-xl sm:text-2xl mb-4">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <p class="text-slate-400 text-xs sm:text-sm font-medium">Aucune catégorie.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(optional($categories)->hasPages())
            <div class="px-6 sm:px-8 py-4 bg-slate-50/50 border-t border-slate-50">
                {{ optional($categories)->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right: Form -->
    <div class="order-1 xl:order-2 space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 sticky top-8">
            <h3 id="formTitle" class="text-base sm:text-lg font-black text-slate-900 mb-6 flex items-center gap-3">
                <i class="fas fa-plus-circle text-rdc-blue"></i> Nouveau
            </h3>
            
            <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 sm:space-y-5">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div>
                    <label class="block text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nom</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                           placeholder="ex: Plomberie">
                </div>

                <div>
                    <label class="block text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Icône</label>
                    <div class="relative">
                        <i class="fas fa-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="text" name="icon" id="icon"
                               class="w-full pl-10 sm:pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                               placeholder="ex: fas fa-tools">
                    </div>
                </div>

                <div class="hidden sm:block">
                    <label class="block text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Slug</label>
                    <input type="text" name="slug" id="slug"
                           class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none"
                           placeholder="ex: plomberie">
                </div>

                <div>
                    <label class="block text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl sm:rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-rdc-blue transition-all outline-none resize-none"
                              placeholder="Détails..."></textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3.5 sm:py-4 bg-slate-900 text-white rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-black uppercase tracking-widest hover:bg-rdc-blue hover:shadow-lg hover:shadow-blue-500/20 transition-all">
                        OK
                    </button>
                    <button type="button" id="cancelBtn" class="hidden px-5 py-3.5 sm:py-4 bg-slate-100 text-slate-500 rounded-xl sm:rounded-2xl text-[10px] sm:text-sm font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">
                        X
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
