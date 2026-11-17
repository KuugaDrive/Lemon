<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Pusat Cetak Laporan PDF
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <div class="mb-6 pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Pilih Parameter Laporan</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Pilih jenis modul, target laporan per santri atau rekap kelas, serta periode tanggal untuk mencetak dokumen PDF resmi.
                </p>
            </div>

            <form method="POST" action="{{ route('laporan.cetak') }}" target="_blank" class="space-y-6" id="laporanForm">
                @csrf

                <!-- Jenis Laporan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Pilih Modul / Jenis Laporan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($jenisOptions as $key => $label)
                            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 hover:border-emerald-500 cursor-pointer transition-colors bg-slate-50/50 has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:text-emerald-900">
                                <input type="radio" name="jenis" value="{{ $key }}" {{ old('jenis', 'presensi') === $key ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-semibold">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mode Laporan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Target / Mode Laporan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-colors has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500">
                            <input type="radio" name="mode" value="siswa" id="mode_siswa" onchange="toggleMode()" checked class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">Laporan Individual Santri</span>
                                <span class="text-xs text-slate-500">Rekam jejak detail per tanggal untuk satu santri terpilih.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-colors has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500">
                            <input type="radio" name="mode" value="kelas" id="mode_kelas" onchange="toggleMode()" class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">Rekapitulasi Kelas</span>
                                <span class="text-xs text-slate-500">Ringkasan kolektif seluruh santri dalam satu kelas.</span>
                            </div>
                        </label>
                    </div>
                    @error('mode') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pemilih Santri (Mode Siswa) -->
                <div id="container_siswa">
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Pilih Santri <span class="text-rose-500">*</span>
                    </label>
                    <select id="siswa_id" name="siswa_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('siswa_id') border-rose-500 @enderror">
                        <option value="">-- Pilih Santri --</option>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} &bull; NIS {{ $s->nis }} &bull; {{ $s->kelas?->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pemilih Kelas (Mode Kelas) -->
                <div id="container_kelas" class="hidden">
                    <label for="kelas_id" class="block text-sm font-semibold text-slate-700 mb-1">
                        Pilih Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select id="kelas_id" name="kelas_id" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('kelas_id') border-rose-500 @enderror">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Rentang Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label for="dari_tanggal" class="block text-sm font-semibold text-slate-700 mb-1">
                            Dari Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ old('dari_tanggal', $defaultDariTanggal) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('dari_tanggal') border-rose-500 @enderror" required>
                        @error('dari_tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sampai_tanggal" class="block text-sm font-semibold text-slate-700 mb-1">
                            Sampai Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ old('sampai_tanggal', $defaultSampaiTanggal) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('sampai_tanggal') border-rose-500 @enderror" required>
                        @error('sampai_tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="submit" name="download" value="0" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-semibold text-white shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Buka PDF</span>
                    </button>
                    <button type="submit" name="download" value="1" class="px-5 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-sm font-semibold text-slate-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Berkas PDF</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMode() {
            const isSiswa = document.getElementById('mode_siswa').checked;
            const containerSiswa = document.getElementById('container_siswa');
            const containerKelas = document.getElementById('container_kelas');

            if (isSiswa) {
                containerSiswa.classList.remove('hidden');
                containerKelas.classList.add('hidden');
            } else {
                containerSiswa.classList.add('hidden');
                containerKelas.classList.remove('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', toggleMode);
    </script>
</x-app-layout>
