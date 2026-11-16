<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('katalog.surat.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Surat: {{ $surat->nama_surat }} (No. {{ $surat->nomor }})
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('katalog.surat.update', $surat) }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Perbarui Data Surat</h3>
                <p class="text-sm text-slate-500">Sesuaikan nomor, nama, juz, dan jumlah ayat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nomor" class="block text-sm font-medium text-slate-700">Nomor Surat (1-114) <span class="text-rose-500">*</span></label>
                    <input type="number" id="nomor" name="nomor" value="{{ old('nomor', $surat->nomor) }}" min="1" max="114" required autofocus
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nomor')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_surat" class="block text-sm font-medium text-slate-700">Nama Surat <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_surat" name="nama_surat" value="{{ old('nama_surat', $surat->nama_surat) }}" required
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('nama_surat')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="juz" class="block text-sm font-medium text-slate-700">Juz Utama (1-30) <span class="text-rose-500">*</span></label>
                    <input type="number" id="juz" name="juz" value="{{ old('juz', $surat->juz) }}" min="1" max="30" required
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('juz')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah_ayat" class="block text-sm font-medium text-slate-700">Jumlah Ayat <span class="text-rose-500">*</span></label>
                    <input type="number" id="jumlah_ayat" name="jumlah_ayat" value="{{ old('jumlah_ayat', $surat->jumlah_ayat) }}" min="1" max="1000" required
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('jumlah_ayat')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="ket" class="block text-sm font-medium text-slate-700">Keterangan (Opsional)</label>
                <textarea id="ket" name="ket" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('ket', $surat->ket) }}</textarea>
                @error('ket')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('katalog.surat.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
