<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Input SPP Massal Per Kelas
                </h2>
                <p class="text-xs text-slate-500 mt-1">Catat pembayaran seluruh santri dalam satu kelas sekaligus</p>
            </div>
            <a href="{{ route('spp.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('spp.massal') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-64">
                    <label for="kelas_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Pilih Kelas</label>
                    <select id="kelas_id" name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
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

        @if($selectedKelas)
            <form method="POST" action="{{ route('spp.massal.store') }}">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <!-- Shared inputs -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-6">
                    <h3 class="font-bold text-slate-900 mb-4">Pengaturan Pembayaran Bersama</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nominal Per Santri Rupiah</label>
                            <input type="number" name="nominal" min="1" step="1000" value="{{ old('nominal', $defaultNominal) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="tgl_bayar" max="{{ now()->toDateString() }}" value="{{ old('tgl_bayar', now()->toDateString()) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                            <input type="text" name="ket" value="{{ old('ket', 'Pembayaran massal ' . $selectedKelas->nama_kelas) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Santri Selection Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-900">Daftar Santri — {{ $selectedKelas->nama_kelas }}</h4>
                            <p class="text-xs text-slate-500">Centang santri yang akan dibayarkan</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleAll(true)" class="text-xs font-medium text-emerald-700 hover:underline">Centang Semua</button>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="toggleAll(false)" class="text-xs font-medium text-slate-500 hover:underline">Hapus Centang</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3.5 text-center w-12">Pilih</th>
                                    <th class="px-6 py-3.5">NIS</th>
                                    <th class="px-6 py-3.5">Nama Santri</th>
                                    <th class="px-6 py-3.5">Wali Santri</th>
                                    <th class="px-6 py-3.5 text-center">Status Periode Ini</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($siswas as $siswa)
                                    @php
                                        $sudahBayar = $sudahBayarSiswaIds->contains($siswa->id);
                                    @endphp
                                    <tr class="hover:bg-slate-50/75 transition-colors {{ $sudahBayar ? 'bg-slate-50/50 opacity-70' : '' }}">
                                        <td class="px-4 py-3 text-center">
                                            @if($sudahBayar)
                                                <input type="checkbox" disabled class="rounded border-slate-300 text-slate-400">
                                            @else
                                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" checked class="santri-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 font-mono text-xs text-slate-500">{{ $siswa->nis }}</td>
                                        <td class="px-6 py-3 font-semibold text-slate-900">{{ $siswa->nama_siswa }}</td>
                                        <td class="px-6 py-3 text-slate-600">{{ $siswa->nama_orang_tua }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @if($sudahBayar)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    Sudah Lunas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                    Belum Bayar
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                            Belum ada santri terdaftar di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
                        <a href="{{ route('spp.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-medium text-white shadow-sm transition-colors">
                            Simpan Pembayaran Massal
                        </button>
                    </div>
                </div>
            </form>

            <script>
                function toggleAll(checked) {
                    document.querySelectorAll('.santri-checkbox').forEach(cb => {
                        cb.checked = checked;
                    });
                }
            </script>
        @endif
    </div>
</x-app-layout>
