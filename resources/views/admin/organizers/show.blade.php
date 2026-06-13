<x-app-layout title="Detail Organizer – Jejak Hijau" :user="auth()->user()">
    <div class="px-4 py-6 max-w-4xl mx-auto space-y-6 animate-fade-in">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('admin.organizers.index') }}" class="hover:text-emerald-600">Review Organizer</a>
            <span>/</span>
            <span class="text-gray-500 font-semibold">Detail Organizer</span>
        </div>

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-150 pb-5">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Detail Pengajuan Organizer</h1>
                <p class="text-sm text-gray-500 mt-1">Review informasi profil lengkap sebelum melakukan verifikasi.</p>
            </div>
            <div>
                <a href="{{ route('admin.organizers.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Kembali ke List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Organization Profile details --}}
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-5">
                    <h2 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-forest" style="color: #003F2F;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Informasi Organisasi
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs uppercase font-bold tracking-wider text-gray-400">Nama Organisasi</span>
                            <span class="font-bold text-gray-850 mt-0.5 block">{{ $organizer->organizerProfile->organization_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase font-bold tracking-wider text-gray-400">Nama Penanggung Jawab</span>
                            <span class="font-medium text-gray-800 mt-0.5 block">{{ $organizer->organizerProfile->contact_person ?? $organizer->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase font-bold tracking-wider text-gray-400">Nomor Telepon</span>
                            <span class="font-semibold text-gray-800 mt-0.5 block">{{ $organizer->organizerProfile->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase font-bold tracking-wider text-gray-400">Website</span>
                            @if($organizer->organizerProfile && $organizer->organizerProfile->website)
                                <a href="{{ $organizer->organizerProfile->website }}" target="_blank" class="text-emerald-600 font-semibold hover:underline mt-0.5 block">
                                    {{ $organizer->organizerProfile->website }}
                                </a>
                            @else
                                <span class="text-gray-400 mt-0.5 block">Tidak ada website</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2">
                        <span class="block text-xs uppercase font-bold tracking-wider text-gray-400 mb-1">Deskripsi Organisasi</span>
                        <div class="p-4 rounded-xl bg-gray-50 text-sm text-gray-700 border border-gray-150">
                            {{ $organizer->organizerProfile->description ?? 'Tidak ada deskripsi organisasi yang ditambahkan.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account settings & Metadata --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-forest" style="color: #003F2F;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Akun
                    </h2>

                    <div class="space-y-3">
                        <div>
                            <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Username</span>
                            <span class="font-semibold text-gray-800 text-sm">{{ $organizer->username }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Email</span>
                            <span class="font-mono text-gray-700 text-xs">{{ $organizer->email }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Tanggal Registrasi</span>
                            <span class="text-gray-650 text-xs">{{ $organizer->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Status Saat Ini</span>
                            <div class="mt-1">
                                @if($organizer->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Pending
                                    </span>
                                @elseif($organizer->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Review audit data if reviewed --}}
                        @if($organizer->approved_by)
                            <div class="pt-3 border-t border-gray-100 space-y-2">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Direview Oleh</span>
                                    <span class="text-gray-700 text-xs font-medium">{{ $organizer->approver->name ?? 'System' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Direview Pada</span>
                                    <span class="text-gray-500 text-xs">{{ $organizer->approved_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Controls for review --}}
                @if($organizer->status === 'pending')
                    <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-3">
                        <span class="block text-xs uppercase font-bold tracking-wider text-gray-400 text-center mb-1">Tindakan</span>
                        <div class="flex flex-col gap-2.5">
                            <button type="button"
                                    onclick="openModal('approve', '{{ route('admin.organizers.approve', $organizer->id) }}', '{{ $organizer->organizerProfile->organization_name ?? $organizer->name }}')"
                                    class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                                Setujui Pendaftaran
                            </button>
                            <button type="button"
                                    onclick="openModal('reject', '{{ route('admin.organizers.reject', $organizer->id) }}', '{{ $organizer->organizerProfile->organization_name ?? $organizer->name }}')"
                                    class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak Pendaftaran
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Confirmation Modal Overlay --}}
    <div id="approval-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl border border-gray-150 p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
            <div id="modal-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center">
                {{-- Dynamic Icon --}}
                <svg id="modal-icon-approve" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg id="modal-icon-reject" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 id="modal-title" class="text-lg font-bold text-gray-800"></h3>
                <p id="modal-description" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <form id="modal-form" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="modal-submit-btn" class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all shadow-sm">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, url, name) {
            const modal = document.getElementById('approval-modal');
            const form = document.getElementById('modal-form');
            const title = document.getElementById('modal-title');
            const desc = document.getElementById('modal-description');
            const submitBtn = document.getElementById('modal-submit-btn');
            const iconContainer = document.getElementById('modal-icon-container');
            const iconApprove = document.getElementById('modal-icon-approve');
            const iconReject = document.getElementById('modal-icon-reject');

            form.action = url;
            modal.classList.remove('hidden');

            if (action === 'approve') {
                title.textContent = 'Setujui Organizer?';
                desc.innerHTML = `Setujui pendaftaran organizer <strong>${name}</strong>?<br><br>Organizer akan dapat mengakses dashboard organizer dan membuat event.`;
                submitBtn.textContent = 'Setujui';
                submitBtn.className = 'w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm';
                iconContainer.className = 'w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-250 flex items-center justify-center text-emerald-600';
                iconApprove.classList.remove('hidden');
                iconReject.classList.add('hidden');
            } else {
                title.textContent = 'Tolak Organizer?';
                desc.innerHTML = `Tolak pendaftaran organizer <strong>${name}</strong>?<br><br>Organizer tidak akan dapat mengakses dashboard organizer.`;
                submitBtn.textContent = 'Tolak';
                submitBtn.className = 'w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-sm';
                iconContainer.className = 'w-12 h-12 rounded-xl bg-rose-50 border border-rose-250 flex items-center justify-center text-rose-600';
                iconApprove.classList.add('hidden');
                iconReject.classList.remove('hidden');
            }
        }

        function closeModal() {
            document.getElementById('approval-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
