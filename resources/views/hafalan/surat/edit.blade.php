<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Catatan Hafalan Surat
            </h2>
            <a href="{{ route('hafalan.surat.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('hafalan.surat.update', $hafalanSurat) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Santri -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Santri <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id" name="siswa_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('siswa_id') border-rose-500 @enderror" required>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $hafalanSurat->siswa_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} &bull; NIS {{ $s->nis }} &bull; {{ $s->kelas?->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Surat -->
                <div>
                    <label for="surat_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Surat Al-Qur'an <span class="text-rose-500">*</span>
                    </label>
                    <select id="surat_id" name="surat_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('surat_id') border-rose-500 @enderror" required>
                        @foreach($surats as $su)
                            <option value="{{ $su->id }}" {{ old('surat_id', $hafalanSurat->surat_id) == $su->id ? 'selected' : '' }}>
                                {{ $su->nomor }}. {{ $su->nama_surat }} ({{ $su->jumlah_ayat }} ayat, Juz {{ $su->juz }})
                            </option>
                        @endforeach
                    </select>
                    @error('surat_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Tanggal Mulai Hafalan -->
                    <div>
                        <label for="tgl_hafalan" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Mulai Menghafal
                        </label>
                        <input type="date" id="tgl_hafalan" name="tgl_hafalan" max="{{ now()->toDateString() }}" value="{{ old('tgl_hafalan', $hafalanSurat->tgl_hafalan?->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tgl_hafalan') border-rose-500 @enderror">
                        @error('tgl_hafalan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Pengujian -->
                    <div>
                        <label for="tgl_pengujian" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Pengujian <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tgl_pengujian" name="tgl_pengujian" max="{{ now()->toDateString() }}" value="{{ old('tgl_pengujian', $hafalanSurat->tgl_pengujian->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tgl_pengujian') border-rose-500 @enderror" required>
                        @error('tgl_pengujian') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Nilai -->
                <div>
                    <label for="nilai" class="block text-sm font-semibold text-slate-700 mb-1">
                        Penilaian <span class="text-rose-500">*</span>
                    </label>
                    <select id="nilai" name="nilai" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nilai') border-rose-500 @enderror" required>
                        @foreach($penilaianList as $p)
                            <option value="{{ $p->value }}" {{ old('nilai', $hafalanSurat->nilai->value) == $p->value ? 'selected' : '' }}>
                                {{ $p->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('nilai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="ket" class="block text-sm font-semibold text-slate-700 mb-1">
                        Catatan / Keterangan
                    </label>
                    <textarea id="ket" name="ket" rows="3" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">{{ old('ket', $hafalanSurat->ket) }}</textarea>
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('hafalan.surat.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
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
