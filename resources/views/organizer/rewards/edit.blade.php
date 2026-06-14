<x-app-layout title="Edit Reward – Jejak Hijau" :user="auth()->user()">
    <div class="px-4 py-6 max-w-3xl mx-auto space-y-6">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 border-b border-gray-150 pb-5">
            <a href="{{ route('organizer.rewards.show', $reward->id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Reward</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah detail reward <span class="font-semibold text-gray-700">{{ $reward->name }}</span>.</p>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-reward-form" method="POST" action="{{ route('organizer.rewards.update', $reward->id) }}" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6" novalidate>
            @csrf
            @method('PUT')

            {{-- Reward Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Reward <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $reward->name) }}" placeholder="Contoh: Tumbler Eksklusif GreenRun" required
                       class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                @error('name')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Reward <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="4" placeholder="Deskripsikan reward ini..." required
                          class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">{{ old('description', $reward->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Image File Input --}}
            <div class="space-y-1.5">
                <label for="image" class="block text-sm font-semibold text-gray-700">Foto / Gambar Reward</label>
                @if($reward->image)
                    <div class="mb-3">
                        <span class="block text-xs text-gray-400 mb-1">Gambar saat ini:</span>
                        <img src="{{ $reward->image }}" alt="{{ $reward->name }}" class="w-32 h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                    </div>
                @endif
                <div class="mt-1 flex items-center gap-4">
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/jpg, image/webp"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald/10 file:text-emerald hover:file:bg-emerald/20 file:cursor-pointer cursor-pointer">
                </div>
                <p class="text-xs text-gray-400">Format yang didukung: JPG, JPEG, PNG, WEBP. Ukuran maksimal 2 MB. Kosongkan jika tidak ingin mengubah gambar.</p>
                @error('image')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Required Points, Stock, and Status Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Required Points --}}
                <div class="space-y-1.5">
                    <label for="required_points" class="block text-sm font-semibold text-gray-700">Poin Dibutuhkan <span class="text-red-500">*</span></label>
                    <input type="number" id="required_points" name="required_points" value="{{ old('required_points', $reward->required_points) }}" min="1" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('required_points') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('required_points')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Stock --}}
                <div class="space-y-1.5">
                    <label for="stock" class="block text-sm font-semibold text-gray-700">Stok <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $reward->stock) }}" min="0" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('stock') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('stock')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="space-y-1.5">
                    <label for="is_active" class="block text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select id="is_active" name="is_active" required
                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('is_active') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800 bg-white">
                        <option value="1" {{ old('is_active', $reward->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $reward->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Submit / Cancel buttons --}}
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <a href="{{ route('organizer.rewards.show', $reward->id) }}" class="px-5 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-submit-reward" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
