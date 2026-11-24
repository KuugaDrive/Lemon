<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Dashboard Utama
        </h2>
    </x-slot>

    <div class="space-y-6">
 <div class="bg-purple-100 rounded-xl border border-purple-200 p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">
                        Assalamu'alaikum, {{ $user->name }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Sistem Informasi Binar Sunnah School &bull; {{ $today->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <div class="text-left md:text-right">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peran Pengguna</div>
                    <div class="text-sm font-semibold text-slate-800 mt-0.5">
                        NIP {{ $user->nip }} &bull; {{ $isAdmin ? 'Administrator' : 'Guru Pengajar' }}
                    </div>
                </div>
            </div>
        </div>

        @if($isAdmin)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-blue-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Santri Aktif</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalSiswa) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Seluruh kelas</div>
                </div>

                <div class="bg-blue-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Guru Pengajar</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalGuru) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Staf pendidik</div>
                </div>

                <div class="bg-blue-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rombel Kelas</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalKelas) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Kelas aktif</div>
                </div>

                <div class="bg-emerald-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Presensi Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $presensiHadirCount }} / {{ $totalPresensiHariIni }}</div>
                    <div class="text-xs text-slate-400 mt-1">Santri hadir</div>
                </div>

                <div class="bg-emerald-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Setoran Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($setoranHariIniCount) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Catatan hafalan</div>
                </div>

                <div class="bg-green-100 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">SPP Bulan Ini</div>
                    <div class="text-lg font-bold text-emerald-700 mt-2">Rp {{ number_format($sppBulanIni, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400 mt-1">{{ $today->translatedFormat('F Y') }}</div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Santri Bimbingan</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalSiswa) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Kelas yang diampu</div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kelas Diampu</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalKelas) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Rombel aktif</div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Presensi Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $presensiHadirCount }} / {{ $totalPresensiHariIni }}</div>
                    <div class="text-xs text-slate-400 mt-1">Santri hadir</div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Setoran Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($setoranHariIniCount) }}</div>
                    <div class="text-xs text-slate-400 mt-1">Catatan hafalan</div>
                </div>
            </div>
        @endif

        <div class="bg-white-100 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-slate-800 text-base">Status Presensi Kelas Hari Ini</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pantauan pengisian presensi per kelas tanggal {{ $today->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5 w-12 text-center">No</th>
                            <th class="px-6 py-3.5">Nama Kelas</th>
                            @if($isAdmin)
                                <th class="px-6 py-3.5">Guru Pengampu</th>
                            @endif
                            <th class="px-6 py-3.5 text-center">Jumlah Santri</th>
                            <th class="px-6 py-3.5">Status Presensi</th>
                            <th class="px-6 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($kelasList as $idx => $k)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-4 text-center font-mono text-xs text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $k['nama_kelas'] }}</td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 text-slate-600 text-xs">{{ $k['guru_names'] }}</td>
                                @endif
                                <td class="px-6 py-4 text-center font-semibold text-slate-800">{{ $k['total_siswa'] }}</td>
                                <td class="px-6 py-4">
                                    @if($k['terisi'])
                                        <span class="text-emerald-700 font-semibold text-xs">
                                            Tercatat: {{ $k['hadir'] }} Hadir, {{ $k['izin'] }} Izin, {{ $k['sakit'] }} Sakit, {{ $k['alfa'] }} Alfa
                                        </span>
                                    @else
                                        <span class="text-amber-700 font-semibold text-xs">
                                            Belum diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('presensi.index', ['kelas_id' => $k['id'], 'tanggal' => $today->toDateString()]) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        Buka Presensi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-6 py-8 text-center text-slate-400 text-xs italic">
                                    Belum ada data kelas terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
