<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('master.guru.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Tambah Guru / Pengguna Baru
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('master.guru.store') }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Informasi Pribadi &amp; Akun</h3>
                <p class="text-sm text-slate-500">Lengkapi data akun dan guru baru di bawah ini.</p>
            </div>

            <!-- NIP & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nip" class="block text-sm font-medium text-slate-700">Nomor Induk Pegawai NIP <span class="text-rose-500">*</span></label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
                           placeholder="Contoh: 19850101..." 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nip')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap dan Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Ustadzah Fatimah, S.Pd." 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-6 mt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_kelamin" value="L" class="text-emerald-600 focus:ring-emerald-500" {{ old('jenis_kelamin', 'L') === 'L' ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Laki-laki</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_kelamin" value="P" class="text-emerald-600 focus:ring-emerald-500" {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Perempuan</span>
                    </label>
                </div>
                @error('jenis_kelamin')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & No. HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@contoh.com" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">Nomor HP / WhatsApp</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                           placeholder="Contoh: 081234567890" 
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('no_hp')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-slate-700">Alamat Tempat Tinggal</label>
                <textarea id="alamat" name="alamat" rows="2" 
                          placeholder="Masukkan alamat lengkap..." 
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kata Sandi -->
            <div x-data="{ showPass: false }">
                <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi Akun <span class="text-rose-500">*</span></label>
                <div class="relative mt-1 rounded-lg shadow-sm">
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password" 
                           name="password" 
                           required
                           value=""
                           autocomplete="new-password"
                           placeholder="Minimal 6 karakter" 
                           class="block w-full pr-10 rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <button type="button" 
                            @click="showPass = !showPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                            tabindex="-1"
                            :title="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                        <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPass" x-cloak class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Peran (Role) -->
            <div class="border-t border-slate-200 pt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Peran / Role Pengguna <span class="text-rose-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-3">Satu pengguna boleh memiliki beberapa peran, misal Guru merangkap Admin atau Kepsek.</p>
                <div class="flex flex-wrap gap-4">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors {{ in_array($role->name, old('roles', ['guru'])) ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                   {{ in_array($role->name, old('roles', ['guru'])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-semibold text-slate-800">{{ $role->name === 'kepsek' ? 'Kepala Sekolah (Kepsek)' : ucfirst($role->name) }}</span>
                                <span class="text-xs block text-slate-500">
                                    @if($role->name === 'admin')
                                        Akses penuh ke semua master &amp; data
                                    @elseif($role->name === 'kepsek')
                                        Kepala Sekolah (penandatangan surat &amp; laporan)
                                    @else
                                        Akses mengajar siswa di kelasnya
                                    @endif
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kelas yang Diampu -->
            <div class="border-t border-slate-200 pt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Kelas yang Diampu
                </label>
                <p class="text-xs text-slate-500 mb-3">Pilih satu atau lebih kelas yang diajar oleh guru ini.</p>
                @if($kelas->isEmpty())
                    <p class="text-sm text-slate-500 italic">Belum ada kelas yang dibuat.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto p-2 border border-slate-200 rounded-lg bg-slate-50">
                        @foreach($kelas as $k)
                            <label class="flex items-center gap-2 text-sm text-slate-700 p-1.5 hover:bg-white rounded cursor-pointer">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" 
                                       {{ in_array($k->id, old('kelas_ids', [])) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $k->nama_kelas }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('kelas_ids')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('master.guru.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                    Simpan Guru
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
