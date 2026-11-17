<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('master.guru.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Data Guru / Pengguna: {{ $guru->name }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('master.guru.update', $guru) }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Perbarui Data Guru &amp; Akun</h3>
                <p class="text-sm text-slate-500">Perbarui data informasi dan hak akses guru di bawah ini.</p>
            </div>

            <!-- NIP & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nip" class="block text-sm font-medium text-slate-700">Nomor Induk Pegawai NIP <span class="text-rose-500">*</span></label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}" required
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nip')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap dan Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $guru->name) }}" required
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email & No. HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $guru->email) }}"
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">Nomor HP / WhatsApp</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}"
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
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat', $guru->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kata Sandi -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi Baru</label>
                <input type="password" id="password" name="password"
                       placeholder="Kosongkan jika tidak ingin mengubah kata sandi" 
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                <p class="mt-1 text-xs text-slate-500">Biarkan kosong jika kata sandi tidak diubah.</p>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Peran (Role) -->
            <div class="border-t border-slate-200 pt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Peran / Role Pengguna <span class="text-rose-500">*</span>
                </label>
                @php
                    $userRoleNames = old('roles', $guru->roles->pluck('name')->toArray());
                @endphp
                <div class="flex flex-wrap gap-4">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors {{ in_array($role->name, $userRoleNames) ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                   {{ in_array($role->name, $userRoleNames) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-semibold text-slate-800">{{ ucfirst($role->name) }}</span>
                                <span class="text-xs block text-slate-500">{{ $role->name === 'admin' ? 'Akses penuh ke semua master & data' : 'Akses mengajar siswa di kelasnya' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @if($guru->id === auth()->id())
                    <p class="mt-1 text-xs text-amber-600">Catatan: Anda sedang mengubah akun Anda sendiri. Role admin tidak boleh dicabut.</p>
                @endif
                @error('roles')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kelas yang Diampu -->
            <div class="border-t border-slate-200 pt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Kelas yang Diampu
                </label>
                @php
                    $assignedKelas = old('kelas_ids', $guru->kelas->pluck('id')->toArray());
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto p-2 border border-slate-200 rounded-lg bg-slate-50">
                    @foreach($kelas as $k)
                        <label class="flex items-center gap-2 text-sm text-slate-700 p-1.5 hover:bg-white rounded cursor-pointer">
                            <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" 
                                   {{ in_array($k->id, $assignedKelas) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>{{ $k->nama_kelas }}</span>
                        </label>
                    @endforeach
                </div>
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
