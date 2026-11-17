<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Riwayat SPP — {{ $siswa->nama_siswa }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('spp.create', ['siswa_id' => $siswa->id]) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    + Tambah Pembayaran
                </a>
                <a href="{{ route('spp.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ $siswa->nama_siswa }}</h3>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500 mt-1">
                    <span>NIS: <strong class="font-mono text-slate-700">{{ $siswa->nis }}</strong></span>
                    <span>Kelas: <strong class="text-slate-700">{{ $siswa->kelas?->nama_kelas }}</strong></span>
                    <span>Wali: <strong class="text-slate-700">{{ $siswa->nama_orang_tua }}</strong> ({{ $siswa->no_hp_ortu ?: '-' }})</span>
                </div>
            </div>
            <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 text-right">
                <div class="text-xs font-semibold text-emerald-800 uppercase">Total Terbayar</div>
                <div class="text-xl font-bold text-emerald-700 mt-0.5">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h4 class="font-bold text-slate-900">Riwayat Seluruh Transaksi</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Tanggal Bayar</th>
                            <th class="px-6 py-3.5">Periode Bulan & Tahun</th>
                            <th class="px-6 py-3.5 text-right">Nominal</th>
                            <th class="px-6 py-3.5">Keterangan</th>
                            <th class="px-6 py-3.5">Pencatat</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($riwayats as $item)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->tgl_bayar->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $item->periode_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $item->nominal_rupiah }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $item->ket ?: '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $item->pencatat?->name }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('spp.kuitansi', $item) }}" target="_blank" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Kuitansi</a>
                                    <a href="{{ route('spp.edit', $item) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800">Ubah</a>
                                    <form method="POST" action="{{ route('spp.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus catatan pembayaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">
                                    Santri ini belum memiliki riwayat pembayaran SPP.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
