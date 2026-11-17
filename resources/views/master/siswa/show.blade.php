<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('master.siswa.index') }}" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Profil Santri: {{ $siswa->nama_siswa }}
                </h2>
            </div>

            @role('admin')
                <a href="{{ route('master.siswa.edit', $siswa) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Ubah Data</span>
                </a>
            @endrole
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <h3 class="font-semibold text-slate-900 border-b border-slate-200 pb-2 text-sm uppercase tracking-wider">
                        Data Santri
                    </h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">NIS</dt>
                            <dd class="font-semibold font-mono text-slate-900 mt-0.5">{{ $siswa->nis }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Nama Lengkap</dt>
                            <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->nama_siswa }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kelas</dt>
                            <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Tanggal Terdaftar</dt>
                            <dd class="text-slate-700 mt-0.5">{{ $siswa->created_at ? $siswa->created_at->translatedFormat('d F Y') : '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-4">
                    <h3 class="font-semibold text-slate-900 border-b border-slate-200 pb-2 text-sm uppercase tracking-wider">
                        Data Orang Tua
                    </h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Nama Ayah Kandung</dt>
                            <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->nama_ayah_kandung ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Nama Ibu Kandung</dt>
                            <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->nama_ibu_kandung ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Nomor HP Ortu</dt>
                            <dd class="font-mono text-slate-900 mt-0.5">
                                @if($siswa->no_hp_ortu)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siswa->no_hp_ortu) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 hover:underline">
                                        {{ $siswa->no_hp_ortu }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Tidak ada nomor</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Alamat</dt>
                            <dd class="text-slate-800 mt-0.5 leading-relaxed">{{ $siswa->alamat ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ route('master.siswa.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">
                    Kembali
                </a>

                @role('admin')
                    <form method="POST" action="{{ route('master.siswa.destroy', $siswa) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data santri {{ $siswa->nama_siswa }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-sm font-medium transition-colors">
                            Hapus Santri
                        </button>
                    </form>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
