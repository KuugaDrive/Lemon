<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('spp.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Pencatatan Pembayaran SPP Satuan
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('spp.store') }}" class="space-y-6">
                @csrf

                <!-- Santri -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Pilih Santri <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id" name="siswa_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('siswa_id') border-rose-500 @enderror" required>
                        <option value="">-- Pilih Santri --</option>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $selectedSiswaId) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} &bull; NIS {{ $s->nis }} &bull; {{ $s->kelas?->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Bulan -->
                    <div>
                        <label for="bulan" class="block text-sm font-semibold text-slate-700 mb-1">
                            Bulan Periode <span class="text-rose-500">*</span>
                        </label>
                        <select id="bulan" name="bulan" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('bulan') border-rose-500 @enderror" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('bulan', $selectedBulan) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        @error('bulan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label for="tahun" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tahun Periode <span class="text-rose-500">*</span>
                        </label>
                        <select id="tahun" name="tahun" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tahun') border-rose-500 @enderror" required>
                            @for($y = now()->year + 1; $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ old('tahun', $selectedTahun) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        @error('tahun') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Info Status Tagihan Dinamis -->
                <div id="info-tagihan-container" class="rounded-lg p-4 border transition-all duration-200 {{ $tagihanInfo ? ($tagihanInfo['is_lunas'] ? 'bg-amber-50 border-amber-200 text-amber-800' : ($tagihanInfo['total_terbayar'] > 0 ? 'bg-sky-50 border-sky-200 text-sky-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800')) : 'hidden' }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 text-xs sm:text-sm">
                            <div id="info-tagihan-title" class="font-bold">
                                @if($tagihanInfo)
                                    @if($tagihanInfo['is_lunas'])
                                        Status: LUNAS
                                    @elseif($tagihanInfo['total_terbayar'] > 0)
                                        Status: KURANG BAYAR
                                    @else
                                        Status: BELUM BAYAR
                                    @endif
                                @endif
                            </div>
                            <div id="info-tagihan-desc" class="mt-0.5">
                                @if($tagihanInfo)
                                    @if($tagihanInfo['is_lunas'])
                                        SPP santri ini untuk periode yang dipilih sudah Lunas (Total terbayar: {{ $tagihanInfo['rupiah_terbayar'] }}).
                                    @elseif($tagihanInfo['total_terbayar'] > 0)
                                        Santri telah membayar {{ $tagihanInfo['rupiah_terbayar'] }}. <strong>Sisa kekurangan yang harus dibayar: {{ $tagihanInfo['rupiah_sisa'] }}</strong> (Tarif standar: {{ $tagihanInfo['rupiah_default'] }}).
                                    @else
                                        Santri belum melakukan pembayaran untuk periode ini. Tarif SPP standar: {{ $tagihanInfo['rupiah_default'] }}.
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Nominal -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="nominal" class="block text-sm font-semibold text-slate-700">
                                Nominal Rupiah <span class="text-rose-500">*</span>
                            </label>
                            <span id="info-max-nominal" class="text-xs text-emerald-700 font-semibold">
                                Maksimal: Rp {{ number_format($nominalToPay ?? $defaultNominal, 0, ',', '.') }}
                            </span>
                        </div>
                        <x-input-rupiah name="nominal" id="nominal" :value="old('nominal', $nominalToPay ?? $defaultNominal)" required class="@error('nominal') border-rose-500 @enderror" />
                        <p id="nominal-exceed-warning" class="text-xs text-rose-600 font-semibold mt-1 hidden"></p>
                        @error('nominal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror

                        <!-- Action helper buttons -->
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <button type="button" onclick="setNominalValue({{ $defaultNominal }})" class="inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded transition-colors">
                                Tarif Penuh: Rp {{ number_format($defaultNominal, 0, ',', '.') }}
                            </button>
                            <button type="button" id="btn-set-sisa" onclick="setNominalSisa()" style="display: {{ ($tagihanInfo && $tagihanInfo['sisa_tagihan'] > 0 && $tagihanInfo['sisa_tagihan'] < $defaultNominal) ? 'inline-flex' : 'none' }};" class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-2 py-1 rounded transition-colors">
                                Bayar Sisa: <span id="label-sisa" class="ml-1">{{ $tagihanInfo ? $tagihanInfo['rupiah_sisa'] : '' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tanggal Bayar -->
                    <div>
                        <label for="tgl_bayar" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" max="{{ now()->toDateString() }}" value="{{ old('tgl_bayar', now()->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tgl_bayar') border-rose-500 @enderror" required>
                        @error('tgl_bayar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="ket" class="block text-sm font-semibold text-slate-700 mb-1">
                        Keterangan
                    </label>
                    <input type="text" id="ket" name="ket" value="{{ old('ket') }}" placeholder="Misal: Tunai / Transfer Bank BCA / Cicilan ke-1" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('spp.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="btn-submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-medium text-white shadow-sm transition-colors">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentSisaTagihan = {{ $tagihanInfo ? $tagihanInfo['sisa_tagihan'] : $defaultNominal }};
        const defaultNominalValue = {{ $defaultNominal }};

        function setNominalValue(val) {
            const el = document.getElementById('nominal');
            if (el) {
                el.value = new Intl.NumberFormat('id-ID').format(val);
                el.dispatchEvent(new Event('input'));
            }
        }

        function setNominalSisa() {
            if (currentSisaTagihan > 0) {
                setNominalValue(currentSisaTagihan);
            }
        }

        function validateNominalLimit() {
            const el = document.getElementById('nominal');
            const warningEl = document.getElementById('nominal-exceed-warning');
            const submitBtn = document.getElementById('btn-submit');
            if (!el) return;

            const val = parseInt(el.value.replace(/[^0-9]/g, ''), 10) || 0;
            if (currentSisaTagihan > 0 && val > currentSisaTagihan) {
                warningEl.classList.remove('hidden');
                warningEl.innerText = '⚠️ Nominal melebihi sisa tagihan! Maksimal pembayaran adalah Rp ' + new Intl.NumberFormat('id-ID').format(currentSisaTagihan);
                el.classList.add('border-rose-500');
                if (submitBtn) submitBtn.disabled = true;
            } else {
                warningEl.classList.add('hidden');
                el.classList.remove('border-rose-500');
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        function checkTagihanLive() {
            const siswaId = document.getElementById('siswa_id').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;
            const container = document.getElementById('info-tagihan-container');
            const titleEl = document.getElementById('info-tagihan-title');
            const descEl = document.getElementById('info-tagihan-desc');
            const btnSisa = document.getElementById('btn-set-sisa');
            const labelSisa = document.getElementById('label-sisa');
            const maxEl = document.getElementById('info-max-nominal');

            if (!siswaId) {
                container.className = 'hidden';
                btnSisa.style.display = 'none';
                if (maxEl) maxEl.innerText = 'Standar: Rp ' + new Intl.NumberFormat('id-ID').format(defaultNominalValue);
                currentSisaTagihan = defaultNominalValue;
                validateNominalLimit();
                return;
            }

            const url = `{{ route('spp.cek-tagihan') }}?siswa_id=${encodeURIComponent(siswaId)}&bulan=${encodeURIComponent(bulan)}&tahun=${encodeURIComponent(tahun)}`;

            fetch(url, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    currentSisaTagihan = data.sisa_tagihan;

                    if (maxEl) {
                        maxEl.innerText = data.is_lunas ? 'Tagihan Lunas (Maksimal: Rp 0)' : 'Maksimal: ' + data.rupiah_sisa;
                    }

                    container.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-800',
                                               'bg-sky-50', 'border-sky-200', 'text-sky-800',
                                               'bg-amber-50', 'border-amber-200', 'text-amber-800');

                    if (data.is_lunas) {
                        container.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-800');
                        titleEl.innerText = 'Status: SUDAH LUNAS';
                        descEl.innerHTML = `Santri ini sudah melunasi SPP untuk periode yang dipilih (Total terbayar: <strong>${data.rupiah_terbayar}</strong>).`;
                        btnSisa.style.display = 'none';
                    } else if (data.total_terbayar > 0) {
                        container.classList.add('bg-sky-50', 'border-sky-200', 'text-sky-800');
                        titleEl.innerText = 'Status: KURANG BAYAR';
                        descEl.innerHTML = `Santri telah membayar ${data.rupiah_terbayar}. <strong>Sisa kekurangan yang harus dibayar: ${data.rupiah_sisa}</strong> (Tarif standar: ${data.rupiah_default}).`;
                        
                        btnSisa.style.display = 'inline-flex';
                        labelSisa.innerText = data.rupiah_sisa;
                        setNominalValue(data.sisa_tagihan);
                    } else {
                        container.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
                        titleEl.innerText = 'Status: BELUM BAYAR';
                        descEl.innerHTML = `Santri belum melakukan pembayaran untuk periode ini. Tarif SPP standar: <strong>${data.rupiah_default}</strong>.`;
                        
                        btnSisa.style.display = 'none';
                        setNominalValue(data.default_nominal);
                    }
                    validateNominalLimit();
                })
                .catch(err => {
                    console.error('Gagal mengecek tagihan SPP:', err);
                });
        }

        document.getElementById('siswa_id').addEventListener('change', checkTagihanLive);
        document.getElementById('bulan').addEventListener('change', checkTagihanLive);
        document.getElementById('tahun').addEventListener('change', checkTagihanLive);
        document.getElementById('nominal').addEventListener('input', validateNominalLimit);
    </script>
</x-app-layout>
