<x-app-layout title="Edit Profil – Jejak Hijau" :user="$user">
    <div class="px-4 py-6 max-w-xl mx-auto space-y-6 animate-fade-in">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 border-b border-gray-150 pb-5">
            <a href="{{ route('profile.show') }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Profil</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah informasi dasar akun GreenRun Anda.</p>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6" novalidate>
            @csrf
            @method('PUT')

            {{-- Full Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Contoh: Fatahillah Firzalay" required
                       class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                @error('name')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Username --}}
            <div class="space-y-1.5">
                <label for="username" class="block text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                <div class="relative rounded-xl shadow-sm">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-semibold">@</span>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="username" required
                           class="w-full pl-8 pr-4 py-2.5 rounded-xl border {{ $errors->has('username') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                </div>
                <p class="text-xs text-gray-400">Username hanya boleh berisi huruf dan angka (minimal 4 karakter).</p>
                @error('username')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Profile Picture --}}
            <div class="space-y-1.5">
                <label for="avatar" class="block text-sm font-semibold text-gray-700">Foto Profil</label>
                @if($user->avatar)
                    <div class="mb-3">
                        <span class="block text-xs text-gray-400 mb-1">Foto saat ini:</span>
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 object-cover rounded-full border border-gray-200 shadow-sm bg-gray-50">
                    </div>
                @endif
                <div class="mt-1 flex items-center gap-4">
                    <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald/10 file:text-emerald hover:file:bg-emerald/20 file:cursor-pointer cursor-pointer">
                </div>
                <p class="text-xs text-gray-400">Format yang didukung: JPG, JPEG, PNG, WEBP. Ukuran maksimal 2 MB.</p>
                @error('avatar')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Submit / Cancel --}}
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <a href="{{ route('profile.show') }}" class="px-5 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-save-profile" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
