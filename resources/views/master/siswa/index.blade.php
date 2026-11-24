<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Data Santri
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('master.siswa.index') }}" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIS atau nama..." 
                           class="w-full text-sm pl-9 pr-4 py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="w-full sm:w-56">
                    <select name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                    @if(request('q') || request('kelas_id'))
                        <a href="{{ route('master.siswa.index') }}" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            @role('admin')
                <a href="{{ route('master.siswa.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Santri</span>
                </a>
            @endrole
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">NIS</th>
                            <th class="px-6 py-4">Nama Santri</th>
                            <th class="px-3 py-4 text-center">L/P</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Ayah Kandung</th>
                            <th class="px-6 py-4">Ibu Kandung</th>
                            <th class="px-6 py-4">Wali</th>
                            <th class="px-6 py-4">No HP Ortu</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($siswas as $siswa)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900 whitespace-nowrap">
                                    {{ $siswa->nis }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <a href="{{ route('master.siswa.show', $siswa) }}" class="text-slate-900 hover:text-emerald-700 hover:underline">
                                        {{ $siswa->nama_siswa }}
                                    </a>
                                </td>
                                <td class="px-3 py-4 text-center whitespace-nowrap">
                                    @if($siswa->jenis_kelamin === 'P')
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-pink-100 text-pink-700 border border-pink-200" title="Perempuan">
                                            P
                                        </span>
                                    @elseif($siswa->jenis_kelamin === 'L')
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200" title="Laki-laki">
                                            L
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-mono text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $siswa->nama_ayah_kandung ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $siswa->nama_ibu_kandung ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $siswa->nama_wali ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $siswa->no_hp_ortu ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('master.siswa.show', $siswa) }}" class="p-1.5 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        @role('admin')
                                            <a href="{{ route('master.siswa.edit', $siswa) }}" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <form method="POST" action="{{ route('master.siswa.destroy', $siswa) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data santri {{ $siswa->nama_siswa }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Data">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="font-medium text-slate-600">Tidak ada data santri ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswas->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $siswas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
