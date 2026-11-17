<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Data Pembayaran SPP
            </h2>
            <a href="{{ route('spp.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <!-- Info Santri (Readonly) -->
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 mb-6 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">{{ $spp->siswa->nama_siswa }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">NIS: {{ $spp->siswa->nis }} | Kelas: {{ $spp->siswa->kelas?->nama_kelas }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-slate-200 text-slate-700">Santri Tetap</span>
            </div>

            <form method="POST" action="{{ route('spp.update', $spp) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Bulan -->
                    <div>
                        <label for="bulan" class="block text-sm font-semibold text-slate-700 mb-1">
                            Bulan Periode <span class="text-rose-500">*</span>
                        </label>
                        <select id="bulan" name="bulan" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('bulan') border-rose-500 @enderror" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('bulan', $spp->bulan) == $m ? 'selected' : '' }}>
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
                                <option value="{{ $y }}" {{ old('tahun', $spp->tahun) == $y ? 'selected' : '' }}>
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
                            Nominal (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="nominal" name="nominal" min="1" step="1000" value="{{ old('nominal', $spp->nominal) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nominal') border-rose-500 @enderror" required>
                        @error('nominal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Bayar -->
                    <div>
                        <label for="tgl_bayar" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" max="{{ now()->toDateString() }}" value="{{ old('tgl_bayar', $spp->tgl_bayar->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tgl_bayar') border-rose-500 @enderror" required>
                        @error('tgl_bayar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="ket" class="block text-sm font-semibold text-slate-700 mb-1">
                        Keterangan (Opsional)
                    </label>
                    <input type="text" id="ket" name="ket" value="{{ old('ket', $spp->ket) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('spp.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-medium text-white shadow-sm transition-colors">
                        Perbarui Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
