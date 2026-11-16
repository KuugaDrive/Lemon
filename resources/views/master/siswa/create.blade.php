<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('master.siswa.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Tambah Santri / Siswa Baru
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('master.siswa.store') }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Data Santri / Siswa</h3>
                <p class="text-sm text-slate-500">Lengkapi formulir pendaftaran santri baru di bawah ini.</p>
            </div>

            <!-- NIS & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nis" class="block text-sm font-medium text-slate-700">NIS (Nomor Induk Santri) <span class="text-rose-500">*</span></label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required autofocus
                           placeholder="Contoh: 202601001" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nis')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_siswa" class="block text-sm font-medium text-slate-700">Nama Lengkap Santri <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_siswa" name="nama_siswa" value="{{ old('nama_siswa') }}" required
                           placeholder="Contoh: Muhammad Al-Fatih" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nama_siswa')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kelas -->
            <div>
                <label for="kelas_id" class="block text-sm font-medium text-slate-700">Kelas / Rombongan Belajar <span class="text-rose-500">*</span></label>
                <select id="kelas_id" name="kelas_id" required
                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-slate-200 pt-4">
                <h3 class="text-base font-semibold text-slate-900">Data Orang Tua / Wali</h3>
                <p class="text-sm text-slate-500">Informasi orang tua atau wali santri untuk keperluan kontak.</p>
            </div>

            <!-- Nama Orang Tua & No HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama_orang_tua" class="block text-sm font-medium text-slate-700">Nama Orang Tua / Wali <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_orang_tua" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" required
                           placeholder="Contoh: Bambang Hartono" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nama_orang_tua')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp_ortu" class="block text-sm font-medium text-slate-700">Nomor HP / WhatsApp Orang Tua</label>
                    <input type="text" id="no_hp_ortu" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}"
                           placeholder="Contoh: 081211110001" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('no_hp_ortu')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-slate-700">Alamat Tempat Tinggal</label>
                <textarea id="alamat" name="alamat" rows="3" 
                          placeholder="Masukkan alamat lengkap santri..." 
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('master.siswa.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                    Simpan Santri
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
