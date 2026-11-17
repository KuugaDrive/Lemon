<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Hafalan Hadits Pendek
            </h2>
            <a href="{{ route('hafalan.hadist.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Hafalan</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('hafalan.hadist.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
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
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Santri</label>
                    <select name="siswa_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Semua Santri</option>
                        @foreach($siswas as $s)
                            <option value="{{ $s->id }}" {{ request('siswa_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_siswa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Dari Tgl Uji</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sampai Tgl Uji</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nilai</label>
                    <select name="nilai" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Semua Nilai</option>
                        @foreach($penilaianList as $p)
                            <option value="{{ $p->value }}" {{ request('nilai') == $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <x-legenda-penilaian />

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Tgl Pengujian</th>
                            <th class="px-6 py-3.5">Santri</th>
                            <th class="px-6 py-3.5">Wali Santri</th>
                            <th class="px-6 py-3.5">Kelas</th>
                            <th class="px-6 py-3.5">Hadits</th>
                            <th class="px-6 py-3.5">Tgl Mulai</th>
                            <th class="px-6 py-3.5 text-center">Nilai</th>
                            <th class="px-6 py-3.5">Keterangan</th>
                            <th class="px-6 py-3.5">Penguji</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($hafalans as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->tgl_pengujian->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->siswa->nama_siswa }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $item->siswa->nis }}</div>
                                </td>
                                <td class="px-6 py-3.5 text-slate-700">
                                    {{ $item->siswa->nama_wali }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-600">
                                    {{ $item->siswa->kelas?->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-800">
                                    {{ $item->hadist->nama_hadist }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-500 text-xs">
                                    {{ $item->tgl_hafalan ? $item->tgl_hafalan->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                    <x-badge-nilai :nilai="$item->nilai" />
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $item->ket ?: '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $item->guru?->name }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('hafalan.hadist.edit', $item) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800">Ubah</a>
                                    <form method="POST" action="{{ route('hafalan.hadist.destroy', $item) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus catatan hafalan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada data hafalan hadits pendek.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hafalans->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $hafalans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
