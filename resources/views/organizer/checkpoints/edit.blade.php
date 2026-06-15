<x-app-layout title="Edit Checkpoint – Jejak Hijau" :user="auth()->user()">
    <div class="px-4 py-6 max-w-3xl mx-auto space-y-6">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Checkpoint</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah detail untuk checkpoint <span class="font-semibold text-gray-700">{{ $checkpoint->name }}</span>.</p>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-checkpoint-form" method="POST" action="{{ route('organizer.checkpoints.update', $checkpoint->id) }}" class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6" novalidate>
            @csrf
            @method('PUT')

            {{-- Checkpoint Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Checkpoint <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $checkpoint->name) }}" placeholder="Contoh: Checkpoint 1 - Registrasi" required
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
                <input type="text" id="location" name="location" value="{{ old('location', $checkpoint->location) }}" placeholder="Contoh: Dekat Pintu Masuk Utama atau -7.289, 112.748"
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
                          class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-800">{{ old('description', $checkpoint->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>            
            
            {{-- Checkpoint Points Configuration Section --}}
            <div class="bg-gray-50 border border-gray-150 rounded-2xl p-6 space-y-6">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #2ECF89;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.904-4.473L21 9l-3.486-3.486L9.813 15.904z" />
                    </svg>
                    Konfigurasi Poin Checkpoint
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Sequence --}}
                    <div class="space-y-1.5">
                        <label for="sequence" class="block text-sm font-semibold text-gray-700">Urutan Checkpoint <span class="text-red-500">*</span></label>
                        <input type="number" id="sequence" name="sequence" value="{{ old('sequence', $checkpoint->sequence) }}" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('sequence') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-4 focus:ring-emerald/20 text-sm text-gray-800">
                        @error('sequence')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="space-y-1.5">
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-4 focus:ring-emerald/20 text-sm text-gray-800 bg-white">
                            <option value="active" {{ strtolower(old('status', $checkpoint->status)) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ strtolower(old('status', $checkpoint->status)) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Custom Points Checkbox & Points Input --}}
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-sm font-semibold text-gray-700">
                            <input type="checkbox" id="is_custom_point" name="is_custom_point" value="1" {{ old('is_custom_point', $checkpoint->is_custom_point) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-emerald focus:ring-emerald/20">
                            <span>Kustomisasi Poin Manual</span>
                        </label>
                        
                        <div class="space-y-1.5">
                            <input type="number" id="points" name="points" value="{{ old('points', $checkpoint->point) }}" min="1"
                                   class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('points') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-4 focus:ring-emerald/20 text-sm text-gray-800 disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <p id="points-help-text" class="text-[11px] text-gray-400 font-semibold"></p>
                            @error('points')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tiers Builder --}}
                <div class="space-y-4 pt-4 border-t border-gray-150">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Tier Poin Peserta</h4>
                            <p class="text-xs text-gray-400">Atur berapa persen dari pool checkpoint yang diterima peserta berdasarkan urutan/rank scan. Peserta yang tidak masuk tier manapun mendapat 0 poin dari checkpoint ini.</p>
                        </div>
                        <button type="button" id="btn-add-tier" class="px-4 py-2 bg-forest hover:bg-forest/90 text-white rounded-xl text-xs font-bold transition-all" style="background-color: #003F2F;">
                            + Tambah Tier
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-150">
                            <thead>
                                <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3 px-4">Rank Mulai</th>
                                    <th class="py-3 px-4">Rank Selesai</th>
                                    <th class="py-3 px-4">Persentase Poin (%)</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tiers-container" class="divide-y divide-gray-150 bg-white rounded-xl">
                                {{-- Will be filled by JS --}}
                            </tbody>
                        </table>
                    </div>
                    @error('bonus_tiers')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Live Simulation Widget --}}
                <div class="bg-white border border-gray-150 rounded-xl p-4 space-y-3 pt-4">
                    <h4 class="text-xs font-bold text-gray-650 uppercase tracking-wider">Simulasi Penerimaan Poin (Checkpoint ini)</h4>
                    <div id="simulation-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        {{-- Will be generated --}}
                    </div>
                </div>
            </div>

            {{-- Submit / Cancel buttons --}}
            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="px-5 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-submit-checkpoint" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isCustomCheckbox = document.getElementById('is_custom_point');
            const pointsInput = document.getElementById('points');
            const pointsHelpText = document.getElementById('points-help-text');
            const btnSubmit = document.getElementById('btn-submit-checkpoint');
            
            const totalPointPool = {{ (int) $checkpoint->event->total_point_pool }};
            // Exclude the current checkpoint from existing checkpoints list in JS
            const existingCheckpoints = {!! json_encode($checkpoint->event->checkpoints->where('id', '!=', $checkpoint->id)->map(fn($cp) => ['id' => $cp->id, 'point' => $cp->point, 'is_custom' => (bool)$cp->is_custom_point])->values()) !!};
            
            const tiersContainer = document.getElementById('tiers-container');
            const btnAddTier = document.getElementById('btn-add-tier');

            let tiers = [];
            @if(old('bonus_tiers'))
                tiers = {!! json_encode(old('bonus_tiers')) !!};
            @else
                tiers = {!! json_encode($checkpoint->bonusTiers->map(fn($t) => ['rank_start' => $t->rank_start, 'rank_end' => $t->rank_end, 'bonus_percentage' => $t->bonus_percentage])->values()) !!};
                if (tiers.length === 0) {
                    tiers = [
                        { rank_start: 1, rank_end: 10, bonus_percentage: 20 },
                        { rank_start: 11, rank_end: 20, bonus_percentage: 10 },
                        { rank_start: 21, rank_end: null, bonus_percentage: 5 }
                    ];
                }
            @endif

            function getPointsAllocation() {
                let customSum = 0;
                let nonCustomCount = 1; // including current one
                
                existingCheckpoints.forEach(cp => {
                    if (cp.is_custom) {
                        customSum += cp.point;
                    } else {
                        nonCustomCount++;
                    }
                });

                const remainingPool = Math.max(0, totalPointPool - customSum);
                const autoPoint = nonCustomCount > 0 ? Math.floor(remainingPool / nonCustomCount) : 0;
                
                return {
                    customSum,
                    remainingPool,
                    autoPoint
                };
            }

            function updatePointsField() {
                const alloc = getPointsAllocation();
                const isCustom = isCustomCheckbox.checked;

                if (isCustom) {
                    pointsInput.removeAttribute('disabled');
                    pointsInput.setAttribute('required', 'required');
                    if (pointsInput.value === '' || pointsInput.value == alloc.autoPoint) {
                        pointsInput.value = {{ (int) $checkpoint->point }} || (alloc.autoPoint > 0 ? alloc.autoPoint : 1);
                    }
                    pointsHelpText.textContent = `Sisa Poin Tersedia: ${alloc.remainingPool.toLocaleString('id-ID')} Pts`;
                    pointsHelpText.className = "text-[11px] text-emerald font-semibold";
                } else {
                    pointsInput.setAttribute('disabled', 'disabled');
                    pointsInput.removeAttribute('required');
                    pointsInput.value = alloc.autoPoint;
                    pointsHelpText.textContent = `* Poin dihitung otomatis berdasarkan skema distribusi pool event.`;
                    pointsHelpText.className = "text-[11px] text-gray-400 font-semibold";
                }
                validatePointsLimit();
                updateSimulation();
            }

            function validatePointsLimit() {
                const alloc = getPointsAllocation();
                const isCustom = isCustomCheckbox.checked;

                if (isCustom) {
                    const value = parseInt(pointsInput.value) || 0;
                    if (value > alloc.remainingPool) {
                        pointsHelpText.textContent = `Poin checkpoint melebihi total point pool event (${alloc.remainingPool.toLocaleString('id-ID')} Pts sisa).`;
                        pointsHelpText.className = "text-[11px] text-red-500 font-semibold";
                        btnSubmit.disabled = true;
                        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        btnSubmit.disabled = false;
                        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            isCustomCheckbox.addEventListener('change', updatePointsField);
            pointsInput.addEventListener('input', validatePointsLimit);

            function renderTiers() {
                tiersContainer.innerHTML = '';
                tiers.forEach((tier, index) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50/50 transition-colors';

                    const isUnlimited = tier.rank_end === null || tier.rank_end === '';

                    tr.innerHTML = `
                        <td class="py-3 px-4">
                            <input type="number" name="bonus_tiers[${index}][rank_start]" value="${tier.rank_start}" readonly
                                   class="w-20 px-3 py-1.5 rounded-lg border border-gray-250 bg-gray-50 text-sm text-gray-500 text-center font-semibold">
                        </td>
                        <td class="py-3 px-4 flex items-center gap-2">
                            <input type="number" name="bonus_tiers[${index}][rank_end]" value="${isUnlimited ? '' : tier.rank_end}" ${isUnlimited ? 'disabled' : ''}
                                   placeholder="∞" min="${tier.rank_start}"
                                   class="w-20 px-3 py-1.5 rounded-lg border border-gray-250 focus:border-emerald focus:ring-4 focus:ring-emerald/20 text-sm text-center text-gray-800 rank-end-input">
                            <label class="flex items-center gap-1 cursor-pointer select-none">
                                <input type="checkbox" ${isUnlimited ? 'checked' : ''} class="unlimited-checkbox rounded border-gray-300 text-emerald focus:ring-emerald/20">
                                <span class="text-xs text-gray-500">Dan seterusnya</span>
                            </label>
                        </td>
                        <td class="py-3 px-4">
                            <div class="relative flex items-center w-32">
                                <input type="number" name="bonus_tiers[${index}][bonus_percentage]" value="${tier.bonus_percentage}" min="0" max="1000" step="any" required
                                       class="w-full pr-8 pl-3 py-1.5 rounded-lg border border-gray-250 focus:border-emerald focus:ring-4 focus:ring-emerald/20 text-sm text-gray-800 percentage-input">
                                <span class="absolute right-3 text-sm text-gray-400 font-semibold">%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <button type="button" class="btn-remove-tier text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                Hapus
                            </button>
                        </td>
                    `;

                    const checkbox = tr.querySelector('.unlimited-checkbox');
                    const rankEndInput = tr.querySelector('.rank-end-input');
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            rankEndInput.disabled = true;
                            rankEndInput.value = '';
                            tiers[index].rank_end = null;
                        } else {
                            rankEndInput.disabled = false;
                            rankEndInput.value = parseInt(rankEndInput.min) || (parseInt(tier.rank_start) + 9);
                            tiers[index].rank_end = parseInt(rankEndInput.value);
                        }
                        updateTiersRanks();
                        updateSimulation();
                    });

                    rankEndInput.addEventListener('input', function() {
                        tiers[index].rank_end = this.value === '' ? null : parseInt(this.value);
                        updateTiersRanks();
                        updateSimulation();
                    });

                    const percentageInput = tr.querySelector('.percentage-input');
                    percentageInput.addEventListener('input', function() {
                        tiers[index].bonus_percentage = parseFloat(this.value) || 0;
                        updateSimulation();
                    });

                    const btnRemove = tr.querySelector('.btn-remove-tier');
                    btnRemove.addEventListener('click', function() {
                        tiers.splice(index, 1);
                        updateTiersRanks();
                        renderTiers();
                        updateSimulation();
                    });

                    tiersContainer.appendChild(tr);
                });
            }

            function updateTiersRanks() {
                let currentRank = 1;
                tiers.forEach((tier, index) => {
                    tier.rank_start = currentRank;
                    if (tier.rank_end !== null && tier.rank_end !== '') {
                        if (tier.rank_end < tier.rank_start) {
                            tier.rank_end = tier.rank_start;
                        }
                        currentRank = parseInt(tier.rank_end) + 1;
                    } else {
                        if (index < tiers.length - 1) {
                            tier.rank_end = tier.rank_start + 9;
                            currentRank = parseInt(tier.rank_end) + 1;
                        }
                    }
                });
            }

            btnAddTier.addEventListener('click', function() {
                updateTiersRanks();
                const lastTier = tiers[tiers.length - 1];
                let nextMin = 1;
                if (lastTier) {
                    if (lastTier.rank_end === null || lastTier.rank_end === '') {
                        lastTier.rank_end = lastTier.rank_start + 9;
                    }
                    nextMin = lastTier.rank_end + 1;
                }
                tiers.push({
                    rank_start: nextMin,
                    rank_end: null,
                    bonus_percentage: 10
                });
                renderTiers();
                updateSimulation();
            });

            function updateSimulation() {
                const basePoint = parseInt(pointsInput.value) || 0;
                const simulationContainer = document.getElementById('simulation-container');
                simulationContainer.innerHTML = '';

                const simulatedRanks = [1, 5, 11, 25];
                
                simulatedRanks.forEach(rank => {
                    let matchingPercentage = null;
                    for (let tier of tiers) {
                        const min = parseInt(tier.rank_start) || 1;
                        const max = tier.rank_end === null || tier.rank_end === '' ? null : parseInt(tier.rank_end);
                        if (rank >= min && (max === null || rank <= max)) {
                            matchingPercentage = parseFloat(tier.bonus_percentage) || 0;
                            break;
                        }
                    }

                    const hasTiers = tiers.length > 0;
                    let totalPoint;
                    let subText;

                    if (hasTiers) {
                        if (matchingPercentage !== null) {
                            totalPoint = Math.floor(basePoint * (matchingPercentage / 100));
                            subText = `${matchingPercentage}% dari pool ${basePoint.toLocaleString('id-ID')} Pts`;
                        } else {
                            totalPoint = 0;
                            subText = `Tidak ada tier yang cocok`;
                        }
                    } else {
                        totalPoint = basePoint;
                        subText = `Base point (tanpa tier)`;
                    }

                    const div = document.createElement('div');
                    div.className = 'p-3 bg-gray-50 border border-gray-150 rounded-xl';
                    div.innerHTML = `
                        <span class="block text-xs text-gray-500 font-semibold">Scan Urutan #${rank}</span>
                        <span class="text-sm font-bold text-gray-800 block mt-0.5">${totalPoint.toLocaleString('id-ID')} Pts</span>
                        <span class="text-[10px] text-gray-400 block">${subText}</span>
                    `;
                    simulationContainer.appendChild(div);
                });
            }

            pointsInput.addEventListener('input', updateSimulation);

            // Initialize
            updatePointsField();
            updateTiersRanks();
            renderTiers();
            updateSimulation();
        });
    </script>
</x-app-layout>
