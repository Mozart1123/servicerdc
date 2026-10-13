{{-- Sélection des communes d'intervention de l'artisan (point 10, base du matching).
     Le champ caché garantit que "aucune case cochée" vide bien la liste. --}}
@php
    $communes = \App\Models\Commune::active()->get(['id', 'name']);
    $selectedCommuneIds = array_map('intval', old('commune_ids', $selected ?? []));
@endphp
<input type="hidden" name="commune_ids_submitted" value="1">
<div class="flex flex-wrap gap-2">
    @foreach($communes as $commune)
        <label class="cursor-pointer select-none">
            <input type="checkbox" name="commune_ids[]" value="{{ $commune->id }}" class="peer sr-only"
                   {{ in_array($commune->id, $selectedCommuneIds, true) ? 'checked' : '' }}>
            <span class="inline-flex items-center px-3 h-9 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-600 peer-checked:bg-rdc-blue peer-checked:border-rdc-blue peer-checked:text-white transition-all">
                {{ $commune->name }}
            </span>
        </label>
    @endforeach
</div>
@error('commune_ids') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
@error('commune_ids.*') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
