<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('presensi.rekap', ['kelas_id' => $siswa->kelas_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Riwayat Presensi Santri: {{ $siswa->nama_siswa }}
            </h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Student Info Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $siswa->nama_siswa }}</h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500 mt-1">
                        <span>NIS: <strong class="font-mono text-slate-700">{{ $siswa->nis }}</strong></span>
                        <span>Kelas: <strong class="text-slate-700">{{ $siswa->kelas->nama_kelas }}</strong></span>
                        <span>Orang Tua: <strong class="text-slate-700">{{ $siswa->nama_orang_tua }}</strong></span>
                    </div>
                </div>

                <!-- Periode Selector -->
                <form method="GET" action="{{ route('presensi.siswa', $siswa) }}" class="flex items-center gap-2">
                    <select name="bulan" class="text-sm py-1.5 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="tahun" class="text-sm py-1.5 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>

            <!-- Mini Summary Cards -->
            @php
                $countHadir = $presensis->where('status.value', 'hadir')->count();
                $countIzin = $presensis->where('status.value', 'izin')->count();
                $countSakit = $presensis->where('status.value', 'sakit')->count();
                $countAlfa = $presensis->where('status.value', 'alfa')->count();
                $totalHari = $presensis->count();
                $persen = $totalHari > 0 ? round(($countHadir / $totalHari) * 100) : 0;
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-100">
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 text-center">
                    <div class="text-xs font-semibold text-emerald-800 uppercase">Hadir</div>
                    <div class="text-xl font-bold text-emerald-700 mt-1">{{ $countHadir }}</div>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 text-center">
                    <div class="text-xs font-semibold text-blue-800 uppercase">Izin</div>
                    <div class="text-xl font-bold text-blue-700 mt-1">{{ $countIzin }}</div>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-100 text-center">
                    <div class="text-xs font-semibold text-amber-800 uppercase">Sakit</div>
                    <div class="text-xl font-bold text-amber-700 mt-1">{{ $countSakit }}</div>
                </div>
                <div class="p-3 bg-rose-50 rounded-lg border border-rose-100 text-center">
                    <div class="text-xs font-semibold text-rose-800 uppercase">Alfa</div>
                    <div class="text-xl font-bold text-rose-700 mt-1">{{ $countAlfa }}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-center col-span-2 sm:col-span-1">
                    <div class="text-xs font-semibold text-slate-700 uppercase">% Kehadiran</div>
                    <div class="text-xl font-bold text-slate-900 mt-1">{{ $persen }}%</div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h4 class="font-bold text-slate-800">Catatan Harian — {{ $namaBulan }} {{ $tahun }}</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Hari</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5">Keterangan</th>
                            <th class="px-6 py-3.5">Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($presensis as $item)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-3 font-medium text-slate-900">
                                    {{ $item->tanggal->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-3 text-slate-600">
                                    {{ $item->tanggal->translatedFormat('l') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->status->badgeClass() }}">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600">
                                    {{ $item->ket ?: '-' }}
                                </td>
                                <td class="px-6 py-3 text-slate-500 text-xs">
                                    {{ $item->guru?->name ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                    Belum ada catatan presensi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
