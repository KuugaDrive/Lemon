<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Kumpulan Nama Surat Al-Qur'an
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('katalog.surat.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau nomor surat..." 
                           class="w-full text-sm pl-9 pr-4 py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                    Cari
                </button>
                @if(request('q'))
                    <a href="{{ route('katalog.surat.index') }}" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg">Reset</a>
                @endif
            </form>

            <a href="{{ route('katalog.surat.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Surat</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-center">No.</th>
                            <th class="px-6 py-4">Nama Surat</th>
                            <th class="px-6 py-4 text-center">Juz Utama</th>
                            <th class="px-6 py-4 text-center">Jumlah Ayat</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($surats as $surat)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-4 text-center font-mono font-bold text-slate-900">{{ $surat->nomor }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $surat->nama_surat }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-800 border border-slate-200">
                                        Juz {{ $surat->juz }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $surat->jumlah_ayat }} Ayat
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $surat->ket ?? '-' }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('katalog.surat.edit', $surat) }}" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('katalog.surat.destroy', $surat) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat {{ $surat->nama_surat }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data surat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($surats->hasPages())
                <div class="p-4 border-t border-slate-200">{{ $surats->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
