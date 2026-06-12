<x-app-layout title="Tambah Checkpoint Baru – Jejak Hijau" :user="auth()->user()">
    <div class="px-4 py-6 max-w-3xl mx-auto space-y-6">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 border-b border-gray-150 pb-5">
            <a href="{{ route('organizer.events.checkpoints.index', $event->id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Checkpoint</h1>
                <p class="text-sm text-gray-500 mt-1">Buat checkpoint baru untuk event <span class="font-semibold text-gray-700">{{ $event->name }}</span>.</p>
            </div>
        </div>

        {{-- Form --}}
        <form id="create-checkpoint-form" method="POST" action="{{ route('organizer.events.checkpoints.store', $event->id) }}" class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-6" novalidate>
            @csrf

            {{-- Checkpoint Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Checkpoint <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Checkpoint 1 - Registrasi" required
                       class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                @error('name')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Location --}}
            <div class="space-y-1.5">
                <label for="location" class="block text-sm font-semibold text-gray-700">Detail Lokasi (Koordinat / Deskripsi Titik)</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="Contoh: Dekat Pintu Masuk Utama atau -7.289, 112.748"
                       class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('location') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                @error('location')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Checkpoint</label>
                <textarea id="description" name="description" rows="3" placeholder="Deskripsikan checkpoint ini atau petunjuk bagi peserta..."
                          class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Sequence & Points & Status Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Sequence --}}
                <div class="space-y-1.5">
                    <label for="sequence" class="block text-sm font-semibold text-gray-700">Urutan Checkpoint <span class="text-red-500">*</span></label>
                    <input type="number" id="sequence" name="sequence" value="{{ old('sequence', $nextSequence) }}" min="1" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('sequence') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('sequence')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Points --}}
                <div class="space-y-1.5">
                    <label for="points" class="block text-sm font-semibold text-gray-700">Poin Reward <span class="text-red-500">*</span></label>
                    <input type="number" id="points" name="points" value="{{ old('points', 50) }}" min="1" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('points') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('points')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="space-y-1.5">
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('status') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800 bg-white">
                        <option value="active" {{ old('status') === 'inactive' ? '' : 'selected' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Submit / Cancel buttons --}}
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <a href="{{ route('organizer.events.checkpoints.index', $event->id) }}" class="px-5 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-submit-checkpoint" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Simpan Checkpoint
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
