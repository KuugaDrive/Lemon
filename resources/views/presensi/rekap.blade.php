<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Rekapitulasi Presensi Bulanan
        </h2>
    </x-slot>

    <div class="space-y-6" x-data="{
        openModal: false,
        siswaNama: '',
        statusKey: '',
        statusLabel: '',
        records: [],
        editingId: null,
        editStatus: '',
        editKet: '',
        openDetail(nama, key, label, items) {
            this.siswaNama = nama;
            this.statusKey = key;
            this.statusLabel = label;
            this.records = items || [];
            this.editingId = null;
            this.openModal = true;
        },
        startEdit(rec) {
            this.editingId = rec.id;
            this.editStatus = rec.status;
            this.editKet = rec.ket || '';
        },
        cancelEdit() {
            this.editingId = null;
        }
    }">
        <!-- Filter Bar -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
            <form method="GET" action="{{ route('presensi.rekap') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-64">
                    <label for="kelas_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Pilih Kelas
                    </label>
                    <select id="kelas_id" name="kelas_id" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @forelse($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @empty
                            <option value="">Tidak ada kelas</option>
                        @endforelse
                    </select>
                </div>

                <div class="w-full sm:w-40">
                    <label for="bulan" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Bulan
                    </label>
                    <select id="bulan" name="bulan" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-full sm:w-32">
                    <label for="tahun" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Tahun
                    </label>
                    <select id="tahun" name="tahun" class="w-full text-sm py-2 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Tampilkan
                    </button>
                    <a href="{{ route('presensi.index', ['kelas_id' => $selectedKelasId]) }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-lg transition-colors">
                        Input Hari Ini
                    </a>
                </div>
            </form>
        </div>

        <!-- Rekap Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-slate-900">{{ $selectedKelas ? $selectedKelas->nama_kelas : 'Pilih Kelas' }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Periode: <span class="font-semibold text-slate-700">{{ $namaBulan }} {{ $tahun }}</span></p>
                </div>
                <div class="text-xs text-slate-500 italic">
                    Tip: Klik angka kehadiran untuk melihat rincian tanggal &amp; mengubah status/catatan.
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-100/75 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-6 py-3.5">Nama Santri</th>
                            <th class="px-4 py-3.5 text-center text-emerald-700">Hadir</th>
                            <th class="px-4 py-3.5 text-center text-blue-700">Izin</th>
                            <th class="px-4 py-3.5 text-center text-amber-700">Sakit</th>
                            <th class="px-4 py-3.5 text-center text-rose-700">Alfa</th>
                            <th class="px-6 py-3.5 text-center">Total Hari</th>
                            <th class="px-6 py-3.5 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($rekapData as $index => $row)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('presensi.siswa', ['siswa' => $row->siswa->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="font-semibold text-slate-900 hover:text-emerald-700 hover:underline">
                                        {{ $row->siswa->nama_siswa }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">NIS: {{ $row->siswa->nis }}</div>
                                </td>
                                
                                <!-- Hadir -->
                                <td class="px-4 py-3 text-center">
                                    @if($row->hadir > 0)
                                        <button type="button" 
                                                @click="openDetail('{{ addslashes($row->siswa->nama_siswa) }}', 'hadir', 'Hadir', {{ json_encode($row->details['hadir']) }})" 
                                                class="font-bold text-emerald-700 hover:text-emerald-900 hover:underline px-2 py-0.5 rounded hover:bg-emerald-50 transition-colors cursor-pointer" 
                                                title="Klik untuk melihat tanggal hadir">
                                            {{ $row->hadir }}
                                        </button>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Izin -->
                                <td class="px-4 py-3 text-center">
                                    @if($row->izin > 0)
                                        <button type="button" 
                                                @click="openDetail('{{ addslashes($row->siswa->nama_siswa) }}', 'izin', 'Izin', {{ json_encode($row->details['izin']) }})" 
                                                class="font-bold text-blue-700 hover:text-blue-900 hover:underline px-2 py-0.5 rounded hover:bg-blue-50 transition-colors cursor-pointer" 
                                                title="Klik untuk melihat tanggal izin & ubah status">
                                            {{ $row->izin }}
                                        </button>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Sakit -->
                                <td class="px-4 py-3 text-center">
                                    @if($row->sakit > 0)
                                        <button type="button" 
                                                @click="openDetail('{{ addslashes($row->siswa->nama_siswa) }}', 'sakit', 'Sakit', {{ json_encode($row->details['sakit']) }})" 
                                                class="font-bold text-amber-700 hover:text-amber-900 hover:underline px-2 py-0.5 rounded hover:bg-amber-50 transition-colors cursor-pointer" 
                                                title="Klik untuk melihat tanggal sakit & ubah status">
                                            {{ $row->sakit }}
                                        </button>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <!-- Alfa -->
                                <td class="px-4 py-3 text-center">
                                    @if($row->alfa > 0)
                                        <button type="button" 
                                                @click="openDetail('{{ addslashes($row->siswa->nama_siswa) }}', 'alfa', 'Alfa', {{ json_encode($row->details['alfa']) }})" 
                                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 hover:bg-rose-200 border border-rose-200 transition-colors cursor-pointer" 
                                                title="Klik untuk melihat tanggal alfa & ubah status">
                                            {{ $row->alfa }}
                                        </button>
                                    @else
                                        <span class="text-slate-400 font-normal">0</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 text-center font-mono text-slate-700">{{ $row->total_hari }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $row->persentase >= 80 ? 'bg-emerald-100 text-emerald-800' : ($row->persentase >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $row->persentase }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-400 italic">
                                    Tidak ada data santri pada kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail & Edit Modal Pop-up -->
        <div x-show="openModal" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" 
                 class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            Rincian Presensi: <span x-text="statusLabel" class="text-emerald-700"></span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Santri: <span x-text="siswaNama" class="font-semibold text-slate-800"></span> &bull; Periode {{ $namaBulan }} {{ $tahun }}
                        </p>
                    </div>
                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-200/50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[70vh] overflow-y-auto space-y-3">
                    <template x-if="records.length === 0">
                        <p class="text-slate-400 text-sm text-center py-6 italic">Tidak ada catatan untuk kategori ini.</p>
                    </template>

                    <template x-for="(item, idx) in records" :key="item.id">
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/75 space-y-2.5 transition-all">
                            <!-- View Mode -->
                            <div x-show="editingId !== item.id" class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900" x-text="item.tanggal_format"></div>
                                    <div class="text-xs text-slate-500 mt-0.5" x-text="item.ket ? ('Catatan: ' + item.ket) : 'Tidak ada catatan tambahan'"></div>
                                </div>
                                <button type="button" @click="startEdit(item)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Ubah</span>
                                </button>
                            </div>

                            <!-- Edit Form Mode -->
                            <div x-show="editingId === item.id">
                                <form method="POST" action="{{ route('presensi.quick-update') }}" class="space-y-3 bg-white p-3.5 rounded-lg border border-slate-300 shadow-sm">
                                    @csrf
                                    <input type="hidden" name="presensi_id" :value="item.id">
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">
                                            Ubah Presensi Tanggal <span x-text="item.tanggal_format" class="text-emerald-700"></span>:
                                        </span>
                                    </div>

                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Kehadiran</label>
                                            <select name="status" x-model="editStatus" class="w-full text-xs py-1.5 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                <option value="hadir">Hadir</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                                <option value="alfa">Alfa</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Catatan / Keterangan</label>
                                            <input type="text" name="ket" x-model="editKet" placeholder="Alasan izin / sakit / keterangan..." class="w-full text-xs py-1.5 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                        <button type="button" @click="cancelEdit()" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800 hover:bg-slate-100 border border-slate-300 rounded-lg transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition-colors">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
