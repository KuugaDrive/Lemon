<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Catatan Setoran Iqro
            </h2>
            <a href="{{ route('setoran.iqro.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('setoran.iqro.update', $setoranIqro) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Santri -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Santri <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id" name="siswa_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('siswa_id') border-rose-500 @enderror" required>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id', $setoranIqro->siswa_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} &bull; NIS {{ $s->nis }} &bull; {{ $s->kelas?->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Iqro -->
                    <div>
                        <label for="iqro_id" class="block text-sm font-semibold text-slate-700 mb-1">
                            Materi Iqro <span class="text-rose-500">*</span>
                        </label>
                        <select id="iqro_id" name="iqro_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('iqro_id') border-rose-500 @enderror" required>
                            @foreach($iqros as $i)
                                <option value="{{ $i->id }}" {{ old('iqro_id', $setoranIqro->iqro_id) == $i->id ? 'selected' : '' }}>
                                    {{ $i->nama_iqro }} &bull; {{ $i->jumlah_halaman }} Halaman
                                </option>
                            @endforeach
                        </select>
                        @error('iqro_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Halaman -->
                    <div>
                        <label for="halaman" class="block text-sm font-semibold text-slate-700 mb-1">
                            Halaman <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="halaman" name="halaman" min="1" value="{{ old('halaman', $setoranIqro->halaman) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('halaman') border-rose-500 @enderror" required>
                        @error('halaman') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Tanggal -->
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Setoran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" max="{{ now()->toDateString() }}" value="{{ old('tanggal', $setoranIqro->tanggal->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('tanggal') border-rose-500 @enderror" required>
                        @error('tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nilai -->
                    <div>
                        <label for="nilai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select id="nilai" name="nilai" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('nilai') border-rose-500 @enderror" required>
                            @foreach($penilaianList as $p)
                                <option value="{{ $p->value }}" {{ old('nilai', $setoranIqro->nilai->value) == $p->value ? 'selected' : '' }}>
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
                    <textarea id="ket" name="ket" rows="3" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('ket') border-rose-500 @enderror">{{ old('ket', $setoranIqro->ket) }}</textarea>
                    @error('ket') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('setoran.iqro.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
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
