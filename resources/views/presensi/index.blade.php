<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Input Presensi Harian Santri
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Bar: Kelas & Tanggal -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('presensi.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-64">
                    <label for="kelas_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Pilih Kelas
                    </label>
                    <select id="kelas_id" name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @forelse($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @empty
                            <option value="">Tidak ada kelas yang diampu</option>
                        @endforelse
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <label for="tanggal" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Tanggal Presensi
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" max="{{ now()->toDateString() }}" 
                           class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Tampilkan
                    </button>
                    <a href="{{ route('presensi.rekap', ['kelas_id' => $selectedKelasId]) }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-lg transition-colors">
                        Lihat Rekap
                    </a>
                </div>
            </form>
        </div>

        @if(!$selectedKelas)
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 text-center text-slate-500">
                <p>Silakan pilih kelas terlebih dahulu untuk memulai pengisian presensi.</p>
            </div>
        @elseif($siswas->isEmpty())
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 text-center text-slate-500">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="font-semibold text-slate-800 text-base">Belum Ada Santri di Kelas Ini</h3>
                <p class="text-sm mt-1">Kelas {{ $selectedKelas->nama_kelas }} saat ini belum memiliki santri terdaftar.</p>
            </div>
        @else
            <!-- Form Input Presensi Massal -->
            <form method="POST" action="{{ route('presensi.store') }}">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Table Header Actions -->
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $selectedKelas->nama_kelas }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Tanggal: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span> &bull; Total: {{ $siswas->count() }} Santri
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="setSemuaHadir()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Set Semua Hadir</span>
                            </button>
                        </div>
                    </div>

                    <!-- Presensi Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3.5 text-center w-12">No</th>
                                    <th class="px-6 py-3.5">Nama Santri</th>
                                    <th class="px-6 py-3.5 text-center">Status Kehadiran</th>
                                    <th class="px-6 py-3.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($siswas as $index => $siswa)
                                    @php
                                        $saved = $existingPresensi->get($siswa->id);
                                        $currentStatus = $saved ? $saved->status->value : 'hadir';
                                        $currentKet = $saved ? $saved->ket : '';
                                    @endphp
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">
                                            {{ $index + 1 }}
                                            <input type="hidden" name="presensi[{{ $index }}][siswa_id]" value="{{ $siswa->id }}">
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="font-semibold text-slate-900">{{ $siswa->nama_siswa }}</div>
                                            <div class="text-xs text-slate-400 font-mono">NIS: {{ $siswa->nis }}</div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-center gap-2 sm:gap-4 flex-wrap">
                                                @foreach($statuses as $st)
                                                    <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-semibold cursor-pointer transition-colors {{ $currentStatus === $st->value ? $st->badgeClass() : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                                        <input type="radio" 
                                                               name="presensi[{{ $index }}][status]" 
                                                               value="{{ $st->value }}" 
                                                               class="status-radio-{{ $st->value }} text-emerald-600 focus:ring-emerald-500"
                                                               {{ $currentStatus === $st->value ? 'checked' : '' }}>
                                                        <span>{{ $st->label() }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <input type="text" 
                                                   name="presensi[{{ $index }}][ket]" 
                                                   value="{{ old("presensi.{$index}.ket", $currentKet) }}" 
                                                   placeholder="Contoh: Demam, Acara keluarga..."
                                                   class="w-full text-xs px-3 py-1.5 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Submit Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-xs text-slate-500">
                            * Data presensi yang telah tersimpan dapat diperbarui kapan saja pada tanggal yang sama.
                        </span>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Presensi Kelas</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <!-- Script Set Semua Hadir -->
    <script>
        function setSemuaHadir() {
            const hadirRadios = document.querySelectorAll('.status-radio-hadir');
            hadirRadios.forEach(radio => {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        }
    </script>
</x-app-layout>
