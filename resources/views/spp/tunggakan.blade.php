<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('spp.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Monitoring Tunggakan SPP
            </h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('spp.tunggakan') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-60">
                    <label for="kelas_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Pilih Kelas</label>
                    <select id="kelas_id" name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-44">
                    <label for="bulan" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Bulan Periode</label>
                    <select id="bulan" name="bulan" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-full sm:w-32">
                    <label for="tahun" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Tahun Periode</label>
                    <select id="tahun" name="tahun" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                    Tampilkan
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 bg-white rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Periode Acuan</span>
                <div class="text-xl font-bold text-slate-900 mt-1">{{ $namaBulan }} {{ $tahun }}</div>
                <div class="text-xs text-slate-400 mt-1">Jatuh Tempo: Setiap tgl {{ $tunggakanData['jatuh_tempo_hari'] }}</div>
            </div>

            <div class="p-5 bg-amber-50 rounded-xl shadow-sm border border-amber-200">
                <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Santri Belum Bayar</span>
                <div class="text-2xl font-bold text-amber-700 mt-1">{{ $tunggakanData['total_santri'] }} Anak</div>
                <div class="text-xs text-amber-600 mt-1">Perlu konfirmasi / penagihan</div>
            </div>

            <div class="p-5 bg-rose-50 rounded-xl shadow-sm border border-rose-200">
                <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider">Potensi SPP Tertunggak</span>
                <div class="text-2xl font-bold text-rose-700 mt-1">Rp {{ number_format($tunggakanData['total_nominal'], 0, ',', '.') }}</div>
                <div class="text-xs text-rose-600 mt-1">Asumsi default Rp {{ number_format($tunggakanData['default_nominal'], 0, ',', '.') }}/bulan</div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-6 py-3.5">Santri</th>
                            <th class="px-6 py-3.5">Kelas</th>
                            <th class="px-6 py-3.5">Nama Orang Tua</th>
                            <th class="px-6 py-3.5">Wali Santri</th>
                            <th class="px-6 py-3.5">Kontak WhatsApp</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($tunggakanData['siswa_list'] as $index => $siswa)
                            @php
                                $hp = preg_replace('/[^0-9]/', '', $siswa->no_hp_ortu);
                                if (str_starts_with($hp, '0')) {
                                    $hp = '62' . substr($hp, 1);
                                }
                                $pesanWa = rawurlencode("Assalamu'alaikum Warahmatullahi Wabarakatuh, Bapak/Ibu wali dari {$siswa->nama_siswa}. Mengingatkan untuk iuran SPP periode {$namaBulan} {$tahun}. Terima kasih.");
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('spp.riwayat', $siswa) }}" class="font-semibold text-slate-900 hover:text-emerald-700 hover:underline">
                                        {{ $siswa->nama_siswa }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">NIS: {{ $siswa->nis }}</div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-600">
                                    {{ $siswa->kelas?->nama_kelas }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $siswa->nama_orang_tua ?: ($siswa->nama_ayah_kandung ?: '-') }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $siswa->nama_wali ?: '-' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if($siswa->no_hp_ortu)
                                        <a href="https://wa.me/{{ $hp }}?text={{ $pesanWa }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ $siswa->no_hp_ortu }}</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Tidak ada nomor</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        Belum Lunas
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('spp.surat-tunggakan', ['siswa' => $siswa->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 text-xs font-semibold rounded-lg shadow-sm transition-colors gap-1.5" title="Cetak Surat Tunggakan PDF">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Surat Tunggakan</span>
                                        </a>
                                        <a href="{{ route('spp.create', ['siswa_id' => $siswa->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors gap-1">
                                            <span>Bayar Sekarang</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-emerald-600 font-medium">
                                    Alhamdulillah, semua santri sudah melunasi SPP untuk periode {{ $namaBulan }} {{ $tahun }}!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
