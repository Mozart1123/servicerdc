@extends($layout)

@section('header_title', 'Mon Profil')
@section('header_subtitle', 'Mettez à jour vos informations personnelles.')

@section($contentSection)
<div class="space-y-10">

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-medium text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        @method('PUT')

        {{-- Photo Section --}}
        <section>
            <h2 class="text-lg font-bold text-slate-900 mb-6">Photo de profil</h2>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="relative group">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ Storage::url(auth()->user()->profile_photo) }}"
                             id="photo-preview"
                             class="w-24 h-24 rounded-full border border-slate-200 object-cover" alt="Profile">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=16a3b0&color=fff&size=200"
                             id="photo-preview"
                             class="w-24 h-24 rounded-full border border-slate-200 object-cover" alt="Profile">
                    @endif
                    <label for="profile_photo_input" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*" class="hidden"
                           onchange="previewPhoto(this)">
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">{{ auth()->user()->name ?? 'Utilisateur' }}</h3>
                    <p class="text-sm text-slate-500">{{ ucfirst(auth()->user()->user_type) }}</p>
                    <p class="text-xs text-slate-400 mt-2">Cliquez sur la photo pour la changer (max 2Mo).</p>
                    @error('profile_photo') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <hr class="border-slate-100">

        {{-- Personal Information --}}
        <section>
            <h2 class="text-lg font-bold text-slate-900 mb-6">Informations personnelles</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                    @error('name') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm text-slate-500 cursor-not-allowed" readonly>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+243 ..."
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                    @error('phone') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Ville</label>
                    <input type="text" name="city" value="{{ old('city', auth()->user()->city) }}" placeholder="Kinshasa, Gombe..."
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm">
                </div>
                
                <div class="sm:col-span-2 space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Bio / Résumé</label>
                    <textarea name="bio" rows="4"
                              class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#16a3b0] focus:border-[#16a3b0] outline-none transition-all text-sm resize-none"
                              placeholder="Parlez-nous de vous...">{{ old('bio', auth()->user()->bio) }}</textarea>
                    @error('bio') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="flex flex-col sm:flex-row items-center justify-start gap-4 pt-4">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#16a3b0] text-white font-medium text-sm rounded-lg hover:bg-[#138b96] transition-colors text-center">
                Enregistrer les modifications
            </button>
            <a href="{{ url()->previous() }}" class="w-full sm:w-auto px-6 py-2.5 text-slate-500 font-medium text-sm hover:text-slate-900 transition-colors text-center">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('photo-preview').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
