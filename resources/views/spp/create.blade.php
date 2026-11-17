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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Nominal -->
                    <div>
                        <label for="nominal" class="block text-sm font-semibold text-slate-700 mb-1">
                            Nominal Rupiah <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="nominal" name="nominal" min="1" step="1000" value="{{ old('nominal', $defaultNominal) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nominal') border-rose-500 @enderror" required>
                        @error('nominal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
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
                    <input type="text" id="ket" name="ket" value="{{ old('ket') }}" placeholder="Misal: Tunai / Transfer Bank BCA a.n..." class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('spp.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-medium text-white shadow-sm transition-colors">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
