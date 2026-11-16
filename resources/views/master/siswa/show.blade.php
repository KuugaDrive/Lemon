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
                <div class="flex items-center gap-2">
                    <a href="{{ route('master.siswa.edit', $siswa) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Ubah Data</span>
                    </a>
                </div>
            @endrole
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Main Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-6 text-white flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-bold shadow-inner">
                    {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ $siswa->nama_siswa }}</h3>
                    <p class="text-emerald-100 text-sm mt-0.5">NIS: <span class="font-mono font-semibold">{{ $siswa->nis }}</span> &bull; Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                </div>
            </div>

            <!-- Details Section -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Pribadi Santri -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-slate-900 border-b border-slate-100 pb-2 text-sm uppercase tracking-wider text-emerald-800">
                            Data Santri
                        </h4>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-slate-500">Nomor Induk Santri (NIS)</dt>
                                <dd class="font-semibold font-mono text-slate-900 mt-0.5">{{ $siswa->nis }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Nama Lengkap</dt>
                                <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->nama_siswa }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kelas / Rombel</dt>
                                <dd class="mt-0.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $siswa->kelas->nama_kelas ?? '-' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Tanggal Terdaftar</dt>
                                <dd class="text-slate-700 mt-0.5">{{ $siswa->created_at ? $siswa->created_at->translatedFormat('d F Y') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Data Orang Tua & Kontak -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-slate-900 border-b border-slate-100 pb-2 text-sm uppercase tracking-wider text-emerald-800">
                            Data Orang Tua &amp; Domisili
                        </h4>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-slate-500">Nama Orang Tua / Wali</dt>
                                <dd class="font-medium text-slate-900 mt-0.5">{{ $siswa->nama_orang_tua }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Nomor HP / WhatsApp</dt>
                                <dd class="font-mono text-slate-900 mt-0.5">
                                    @if($siswa->no_hp_ortu)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siswa->no_hp_ortu) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 hover:underline">
                                            {{ $siswa->no_hp_ortu }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Tidak ada nomor</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Alamat Tempat Tinggal</dt>
                                <dd class="text-slate-800 mt-0.5 leading-relaxed">{{ $siswa->alamat ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="mt-8 pt-4 border-t border-slate-200 flex justify-between items-center">
                    <a href="{{ route('master.siswa.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">
                        &larr; Kembali ke Daftar Santri
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
    </div>
</x-app-layout>
