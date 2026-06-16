<x-app-layout title="Review Organizer – GreenMile" :user="auth()->user()">
    <div class="px-4 py-6 max-w-5xl mx-auto space-y-8 animate-fade-in">
        {{-- Header Section --}}
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Review Organizer</h1>
            <p class="text-sm text-gray-500 mt-1">Verifikasi dan kelola pendaftaran Event Organizer untuk platform GreenRun.</p>
        </div>
        @if(session('success'))
            <div class="p-4 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-250 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Loading Skeleton Placeholder (Requirement: Loading State: Skeleton Cards, Skeleton Table) --}}
        <div id="admin-skeleton" class="hidden space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-white rounded-2xl p-5 shadow-md animate-pulse flex flex-col justify-between animate-delay-100" style="height: 140px;">
                        <div>
                            <div class="w-8 h-8 rounded-xl bg-gray-200 mb-3"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                            <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                        </div>
                        <div class="h-3 bg-gray-200 rounded w-2/3 mt-2"></div>
                    </div>
                @endfor
            </div>
            <div class="bg-white rounded-2xl p-6 space-y-4 animate-pulse shadow-md">
                <div class="h-6 bg-gray-200 rounded w-1/4"></div>
                <div class="space-y-3">
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards (Requirement: Summary Cards) --}}
        <div id="statistics-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Organizer -->
            <div class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 bg-forest/5 text-forest" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Organizer</p>
                    <p class="font-bold text-2xl mt-1 text-gray-900">{{ number_format($totalOrganizers) }} Organizer</p>
                </div>
                <span class="text-xs text-gray-400 mt-2">Terdaftar di sistem</span>
            </div>

            <!-- Pending Approval -->
            <div class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 bg-amber-50 text-amber-600">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Pending Approval</p>
                    <p class="font-bold text-2xl mt-1 text-amber-650" style="color: #D97706;">{{ number_format($pendingCount) }} Pending</p>
                </div>
                <span class="text-xs text-amber-500 mt-2 font-medium">Menunggu review</span>
            </div>

            <!-- Approved Organizer -->
            <div class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 bg-emerald-50 text-emerald-600">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Approved Organizer</p>
                    <p class="font-bold text-2xl mt-1 text-emerald-600">{{ number_format($approvedCount) }} Approved</p>
                </div>
                <span class="text-xs text-emerald-500 mt-2 font-medium">Aktif beroperasi</span>
            </div>

            <!-- Rejected Organizer -->
            <div class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 bg-rose-50 text-rose-600">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Rejected Organizer</p>
                    <p class="font-bold text-2xl mt-1 text-rose-600">{{ number_format($rejectedCount) }} Rejected</p>
                </div>
                <span class="text-xs text-gray-400 mt-2">Ditolak sistem</span>
            </div>
        </div>

        {{-- Pending Organizer Section (Requirement: Pending Organizer Table) --}}
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
            <div class="px-6 pt-6 pb-4">
                <h2 class="text-lg font-bold text-gray-800">Menunggu Persetujuan (Pending)</h2>
                <p class="text-xs text-gray-400 mt-0.5">Daftar pendaftaran organizer baru yang perlu direview.</p>
            </div>

            @if($pendingOrganizers->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <p class="text-sm font-semibold">Tidak ada pendaftaran pending</p>
                    <p class="text-xs text-gray-400 mt-1">Semua pendaftaran Event Organizer telah selesai diproses.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3.5">Nama Organisasi</th>
                                <th class="px-6 py-3.5">Nama Penanggung Jawab</th>
                                <th class="px-6 py-3.5">Email</th>
                                <th class="px-6 py-3.5">Nomor Telepon</th>
                                <th class="px-6 py-3.5">Tanggal Registrasi</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-750">
                            @foreach($pendingOrganizers as $organizer)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $organizer->organizerProfile->organization_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">{{ $organizer->organizerProfile->contact_person ?? $organizer->name }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">{{ $organizer->email }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-600">{{ $organizer->organizerProfile->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $organizer->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('admin.organizers.show', $organizer->id) }}" class="text-xs font-bold text-gray-500 hover:text-emerald-600 transition-colors">
                                                Detail
                                            </a>
                                            <button type="button"
                                                    onclick="openModal('approve', '{{ route('admin.organizers.approve', $organizer->id) }}', '{{ $organizer->organizerProfile->organization_name ?? $organizer->name }}')"
                                                    class="text-xs font-bold text-emerald-600 hover:text-emerald-800 transition-colors">
                                                Approve
                                            </button>
                                            <button type="button"
                                                    onclick="openModal('reject', '{{ route('admin.organizers.reject', $organizer->id) }}', '{{ $organizer->organizerProfile->organization_name ?? $organizer->name }}')"
                                                    class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Approval History Section (Requirement: Approval History) --}}
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
            <div class="px-6 pt-6 pb-4">
                <h2 class="text-lg font-bold text-gray-800">Riwayat Approval</h2>
                <p class="text-xs text-gray-400 mt-0.5">Log riwayat peninjauan pendaftaran organizer.</p>
            </div>

            @if($historyOrganizers->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <p class="text-sm">Belum ada riwayat approval</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3.5">Nama Organisasi</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Approved/Rejected By</th>
                                <th class="px-6 py-3.5">Processed At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($historyOrganizers as $organizer)
                                @php
                                    $isApproved = $organizer->status === 'approved';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        {{ $organizer->organizerProfile->organization_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($isApproved)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800">
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-800">
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">{{ $organizer->approver->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $organizer->approved_at ? $organizer->approved_at->format('d M Y, H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Confirmation Modal Overlay (Requirement: Confirmation Modals) --}}
    <div id="approval-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
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
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
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
