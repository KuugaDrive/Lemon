<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Pembayaran SPP
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('spp.pengaturan') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Pengaturan</span>
                </a>
                <a href="{{ route('spp.tunggakan') }}" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Cek Tunggakan</span>
                </a>
                <a href="{{ route('spp.massal') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Input Massal</span>
                </a>
                <a href="{{ route('spp.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Satuan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('kuitansi_id'))
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-emerald-900 font-medium">
                    Pembayaran berhasil dicatat. Kuitansi resmi dapat langsung dicetak.
                </div>
                <a href="{{ route('spp.kuitansi', session('kuitansi_id')) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-emerald-700 rounded-lg hover:bg-emerald-800 transition-colors w-fit shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Kuitansi Transaksi Ini
                </a>
            </div>
        @endif
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('spp.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Santri</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIS atau nama santri..." class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['kelas_id', 'bulan', 'tahun', 'q']))
                        <a href="{{ route('spp.index') }}" class="px-3 py-2 border border-slate-300 hover:bg-slate-50 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-slate-900 text-white rounded-xl shadow-sm p-4 sm:p-5 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Total Penerimaan</span>
                <div class="text-2xl font-bold mt-0.5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>
            <div class="text-right text-xs text-slate-300">
                <span>{{ $pembayarans->total() }} Transaksi</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Tgl Bayar</th>
                            <th class="px-6 py-3.5">Santri</th>
                            <th class="px-6 py-3.5">Wali Santri</th>
                            <th class="px-6 py-3.5">Kelas</th>
                            <th class="px-6 py-3.5">Periode</th>
                            <th class="px-6 py-3.5 text-right">Nominal</th>
                            <th class="px-6 py-3.5">Keterangan</th>
                            <th class="px-6 py-3.5">Petugas</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($pembayarans as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->tgl_bayar->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('spp.riwayat', $item->siswa) }}" class="font-semibold text-slate-900 hover:text-emerald-700 hover:underline">
                                        {{ $item->siswa->nama_siswa }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">{{ $item->siswa->nis }}</div>
                                </td>
                                <td class="px-6 py-3.5 text-slate-700">
                                    {{ $item->siswa->nama_wali }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-600">
                                    {{ $item->siswa->kelas?->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap font-medium text-slate-800">
                                    {{ $item->periode_label }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $item->nominal_rupiah }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $item->ket ?: '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $item->pencatat?->name }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('spp.kuitansi', $item) }}" target="_blank" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Kuitansi</a>
                                    <a href="{{ route('spp.edit', $item) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800">Ubah</a>
                                    <form method="POST" action="{{ route('spp.destroy', $item) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus catatan pembayaran SPP ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada data pembayaran SPP.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pembayarans->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
