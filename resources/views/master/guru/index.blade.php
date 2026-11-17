<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Data Guru &amp; Pengguna
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Header Actions & Search -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <!-- Search Form -->
            <form method="GET" action="{{ route('master.guru.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIP atau Nama..." 
                           class="w-full text-sm pl-9 pr-4 py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                    Cari
                </button>
                @if(request('q'))
                    <a href="{{ route('master.guru.index') }}" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Add Button -->
            <a href="{{ route('master.guru.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Guru</span>
            </a>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">NIP</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">No. HP</th>
                            <th class="px-6 py-4">Peran</th>
                            <th class="px-6 py-4">Kelas Diampu</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($gurus as $guru)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900 whitespace-nowrap">
                                    {{ $guru->nip }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $guru->name }}
                                    @if($guru->id === auth()->id())
                                        <span class="ml-1 text-[11px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-normal">Anda</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $guru->no_hp ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($guru->roles as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($guru->kelas->isEmpty())
                                        <span class="text-xs text-slate-400 italic">Tidak ada</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($guru->kelas as $k)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $k->nama_kelas }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit Button -->
                                        <a href="{{ route('master.guru.edit', $guru) }}" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Delete Button -->
                                        @if($guru->id !== auth()->id())
                                            <form method="POST" action="{{ route('master.guru.destroy', $guru) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru {{ $guru->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Data">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="font-medium text-slate-600">Tidak ada data guru / pengguna ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($gurus->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $gurus->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
