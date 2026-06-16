<x-app-layout title="Edit Event – GreenMile" :user="$user">
    <div class="px-4 py-6 max-w-3xl mx-auto space-y-6">
        {{-- Header / Back link --}}
        <div class="flex items-center gap-2 border-b border-gray-150 pb-5">
            <a href="{{ route('organizer.events.show', $event->id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Event</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah detail dan status event GreenRun Anda.</p>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-event-form" method="POST" action="{{ route('organizer.events.update', $event->id) }}" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6" novalidate>
            @csrf
            @method('PUT')

            {{-- Event Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Event <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" placeholder="Contoh: GreenRun Surabaya Eco-Sprint" required
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
                <label for="location" class="block text-sm font-semibold text-gray-700">Lokasi <span class="text-red-500">*</span></label>
                <input type="text" id="location" name="location" value="{{ old('location', $event->location) }}" placeholder="Contoh: Taman Bungkul, Surabaya" required
                       class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('location') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                @error('location')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Dates Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Start Date --}}
                <div class="space-y-1.5">
                    <label for="start_date" class="block text-sm font-semibold text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d') : '') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('start_date') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('start_date')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- End Date --}}
                <div class="space-y-1.5">
                    <label for="end_date" class="block text-sm font-semibold text-gray-700">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('end_date') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('end_date')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Gambar Banner --}}
            <div class="space-y-1.5">
                <label for="banner" class="block text-sm font-semibold text-gray-700">Gambar Banner</label>
                @if($event->banner)
                    <div class="mb-3">
                        <img src="{{ $event->banner }}" alt="{{ $event->name }}" class="w-full max-h-48 object-cover rounded-xl border border-gray-200 shadow-sm bg-gray-50">
                    </div>
                @endif
                <input type="file" id="banner" name="banner" accept="image/png, image/jpeg, image/jpg, image/webp"
                       class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-emerald focus:ring-emerald/20 focus:outline-none focus:ring-4 transition-all text-sm text-gray-800 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald/10 file:text-emerald hover:file:bg-emerald/20 file:cursor-pointer cursor-pointer">
                @error('banner')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Event</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan detail aktivitas lari peduli lingkungan ini..."
                          class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Info Fields Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Total Rewards --}}
                <div class="space-y-1.5">
                    <label for="total_rewards" class="block text-sm font-semibold text-gray-700">Total Hadiah</label>
                    <input type="text" id="total_rewards" name="total_rewards" value="{{ old('total_rewards', $event->total_rewards) }}" placeholder="Contoh: Rp 15.000.000"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('total_rewards') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('total_rewards')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Max Participants --}}
                <div class="space-y-1.5">
                    <label for="max_participants" class="block text-sm font-semibold text-gray-700">Maks. Peserta</label>
                    <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1" placeholder="Kosongkan jika tak terbatas"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('max_participants') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">
                    @error('max_participants')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Point Pool --}}
                <div class="space-y-1.5">
                    <label for="point_pool" class="block text-sm font-semibold text-gray-700">Total Point Pool <span class="text-red-500">*</span></label>
                    <input type="number" id="point_pool" name="point_pool" value="{{ old('point_pool', $event->point_pool) }}" min="1" required
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('point_pool') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800"
                           placeholder="Contoh: 50000">
                    @error('point_pool')
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Status Option --}}
            @php
                $currentRawStatus = strtolower($event->getRawOriginal('status') ?? 'draft');
            @endphp
            <div class="space-y-1.5">
                <label for="status" class="block text-sm font-semibold text-gray-700">Status Event <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('status') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800 bg-white">
                    <option value="draft" {{ old('status', $currentRawStatus) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $currentRawStatus) === 'published' ? 'selected' : '' }}>Published (Upcoming)</option>
                    <option value="ongoing" {{ old('status', $currentRawStatus) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="finished" {{ old('status', $currentRawStatus) === 'finished' ? 'selected' : '' }}>Finished</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            @php
                $isLocked = in_array(strtolower($event->getRawOriginal('status') ?? 'draft'), ['ongoing', 'finished']);
            @endphp

            <!-- Point Pool Configuration Section -->
            <div class="bg-gray-50 border border-gray-150 rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #2ECF89;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.904-4.473L21 9l-3.486-3.486L9.813 15.904z" />
                    </svg>
                    Konfigurasi Poin Event
                </h3>
                @if($isLocked)
                    <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Konfigurasi poin tidak dapat diubah karena status event saat ini sudah berlangsung (Ongoing) atau selesai (Finished).</span>
                    </div>
                @endif

                <div class="space-y-1.5 max-w-md">
                    <label for="total_point_pool" class="block text-sm font-semibold text-gray-700">Total Point Pool <span class="text-red-500">*</span></label>
                    <input type="number" id="total_point_pool" name="total_point_pool" value="{{ old('total_point_pool', $event->total_point_pool) }}" min="1" required {{ $isLocked ? 'disabled' : '' }}
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('total_point_pool') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-4 focus:ring-emerald/20 text-sm text-gray-800 disabled:bg-gray-100 disabled:cursor-not-allowed">
                    <p class="text-[11px] text-gray-400">Total poin yang dapat didistribusikan ke seluruh checkpoint dalam event ini.</p>
                    @error('total_point_pool')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit / Cancel buttons --}}
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <a href="{{ route('organizer.events.show', $event->id) }}" class="px-5 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-submit-event" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
