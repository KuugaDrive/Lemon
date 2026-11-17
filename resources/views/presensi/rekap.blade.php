<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Rekapitulasi Presensi Bulanan
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('presensi.rekap') }}" class="flex flex-wrap items-end gap-4">
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
                            <option value="">Tidak ada kelas</option>
                        @endforelse
                    </select>
                </div>

                <div class="w-full sm:w-40">
                    <label for="bulan" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Bulan
                    </label>
                    <select id="bulan" name="bulan" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-full sm:w-32">
                    <label for="tahun" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Tahun
                    </label>
                    <select id="tahun" name="tahun" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Tampilkan
                    </button>
                    <a href="{{ route('presensi.index', ['kelas_id' => $selectedKelasId]) }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-lg transition-colors">
                        Input Hari Ini
                    </a>
                </div>
            </form>
        </div>

        <!-- Rekap Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">{{ $selectedKelas ? $selectedKelas->nama_kelas : 'Pilih Kelas' }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Periode: <span class="font-semibold text-slate-700">{{ $namaBulan }} {{ $tahun }}</span></p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-6 py-3.5">Nama Santri</th>
                            <th class="px-4 py-3.5 text-center text-emerald-700">Hadir</th>
                            <th class="px-4 py-3.5 text-center text-blue-700">Izin</th>
                            <th class="px-4 py-3.5 text-center text-amber-700">Sakit</th>
                            <th class="px-4 py-3.5 text-center text-rose-700">Alfa</th>
                            <th class="px-6 py-3.5 text-center">Total Hari</th>
                            <th class="px-6 py-3.5 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($rekapData as $index => $row)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('presensi.siswa', ['siswa' => $row->siswa->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="font-semibold text-slate-900 hover:text-emerald-700 hover:underline">
                                        {{ $row->siswa->nama_siswa }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">NIS: {{ $row->siswa->nis }}</div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-emerald-700">{{ $row->hadir }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-700">{{ $row->izin }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-amber-700">{{ $row->sakit }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-rose-700">{{ $row->alfa }}</td>
                                <td class="px-6 py-3 text-center font-mono text-slate-700">{{ $row->total_hari }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $row->persentase >= 80 ? 'bg-emerald-100 text-emerald-800' : ($row->persentase >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $row->persentase }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-400 italic">
                                    Tidak ada data santri pada kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
