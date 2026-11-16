<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('master.kelas.index') }}" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Detail Kelas: {{ $kelas->nama_kelas }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('master.kelas.edit', $kelas) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Ubah Kelas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kelas Info -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 md:col-span-2">
                <h3 class="text-base font-semibold text-slate-900 mb-3">Informasi Kelas</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500 font-medium">Nama Kelas</dt>
                        <dd class="text-slate-900 font-semibold text-base mt-0.5">{{ $kelas->nama_kelas }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 font-medium">Total Santri</dt>
                        <dd class="mt-0.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                {{ $kelas->siswas->count() }} Santri
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500 font-medium">Keterangan / Catatan</dt>
                        <dd class="text-slate-800 mt-0.5">{{ $kelas->keterangan ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Guru Pengampu -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h3 class="text-base font-semibold text-slate-900 mb-3">Guru Pengampu</h3>
                @if($kelas->gurus->isEmpty())
                    <p class="text-sm text-slate-400 italic">Belum ada guru yang ditugaskan ke kelas ini.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($kelas->gurus as $guru)
                            <li class="py-2.5 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $guru->name }}</p>
                                    <p class="text-xs text-slate-500">NIP: {{ $guru->nip }}</p>
                                </div>
                                <span class="text-xs text-slate-500">{{ $guru->no_hp ?? '-' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Daftar Santri di Kelas Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-900">Daftar Santri / Siswa Terdaftar</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Seluruh santri yang aktif di kelas {{ $kelas->nama_kelas }}</p>
                </div>
                @role('admin')
                    <a href="{{ route('master.siswa.create') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                        + Tambah Santri
                    </a>
                @endrole
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">NIS</th>
                            <th class="px-6 py-3.5">Nama Santri</th>
                            <th class="px-6 py-3.5">Nama Orang Tua</th>
                            <th class="px-6 py-3.5">No. HP Orang Tua</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($kelas->siswas as $siswa)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-6 py-3.5 font-mono font-medium text-slate-900">
                                    {{ $siswa->nis }}
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-900">
                                    {{ $siswa->nama_siswa }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $siswa->nama_orang_tua }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $siswa->no_hp_ortu ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <a href="{{ route('master.siswa.show', $siswa) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:underline">
                                        Lihat Profil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                    Belum ada santri di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
