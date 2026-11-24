<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('setoran.quran.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Catatan Setoran Nama Surat
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('setoran.quran.update', $setoranQuran) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Santri -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Santri <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id" name="siswa_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('siswa_id') border-rose-500 @enderror" required>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $setoranQuran->siswa_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} &bull; NIS {{ $s->nis }} &bull; {{ $s->kelas?->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Surat -->
                    <div>
                        <label for="surat_id" class="block text-sm font-semibold text-slate-700 mb-1">
                            Surat <span class="text-rose-500">*</span>
                        </label>
                        <select id="surat_id" name="surat_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('surat_id') border-rose-500 @enderror" required>
                            @foreach($surats as $su)
                                <option value="{{ $su->id }}" {{ old('surat_id', $setoranQuran->surat_id) == $su->id ? 'selected' : '' }}>
                                    {{ $su->nomor }}. {{ $su->nama_surat }} {{ $su->nama_arab ? '- '.$su->nama_arab : '' }} ({{ $su->jumlah_ayat }} ayat, Juz {{ $su->juz }})
                                </option>
                            @endforeach
                        </select>
                        @error('surat_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Juz -->
                    <div>
                        <label for="juz" class="block text-sm font-semibold text-slate-700 mb-1">
                            Juz <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="juz" name="juz" min="1" max="30" value="{{ old('juz', $setoranQuran->juz) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('juz') border-rose-500 @enderror" required>
                        @error('juz') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Ayat Mulai -->
                    <div>
                        <label for="ayat_mulai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Ayat Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="ayat_mulai" name="ayat_mulai" min="1" value="{{ old('ayat_mulai', $setoranQuran->ayat_mulai) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ayat_mulai') border-rose-500 @enderror" required>
                        @error('ayat_mulai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Ayat Selesai -->
                    <div>
                        <label for="ayat_selesai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Ayat Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="ayat_selesai" name="ayat_selesai" min="1" value="{{ old('ayat_selesai', $setoranQuran->ayat_selesai) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ayat_selesai') border-rose-500 @enderror" required>
                        @error('ayat_selesai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Tanggal -->
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Setoran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" max="{{ now()->toDateString() }}" value="{{ old('tanggal', $setoranQuran->tanggal->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tanggal') border-rose-500 @enderror" required>
                        @error('tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nilai -->
                    <div>
                        <label for="nilai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select id="nilai" name="nilai" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nilai') border-rose-500 @enderror" required>
                            @foreach($penilaianList as $p)
                                <option value="{{ $p->value }}" {{ old('nilai', $setoranQuran->nilai->value) == $p->value ? 'selected' : '' }}>
                                    {{ $p->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('nilai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="ket" class="block text-sm font-semibold text-slate-700 mb-1">
                        Catatan / Keterangan
                    </label>
                    <textarea id="ket" name="ket" rows="3" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">{{ old('ket', $setoranQuran->ket) }}</textarea>
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('setoran.quran.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
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
