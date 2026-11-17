<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('katalog.doa.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Tambah Doa ke Katalog
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('katalog.doa.store') }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Data Doa Harian</h3>
                <p class="text-sm text-slate-500">Lengkapi nama doa, teks bacaan latin, dan arti terjemahannya.</p>
            </div>

            <div>
                <label for="nama_doa" class="block text-sm font-medium text-slate-700">Nama Doa <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_doa" name="nama_doa" value="{{ old('nama_doa') }}" required autofocus
                       placeholder="Contoh: Doa Sebelum Makan" 
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('nama_doa')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="baca_latin" class="block text-sm font-medium text-slate-700">Bacaan Latin <span class="text-rose-500">*</span></label>
                <textarea id="baca_latin" name="baca_latin" rows="3" required
                          placeholder="Tuliskan bacaan latin doa..." 
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('baca_latin') }}</textarea>
                @error('baca_latin')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="artinya" class="block text-sm font-medium text-slate-700">Arti / Terjemahan <span class="text-rose-500">*</span></label>
                <textarea id="artinya" name="artinya" rows="3" required
                          placeholder="Tuliskan arti dalam bahasa Indonesia..." 
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('artinya') }}</textarea>
                @error('artinya')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ket" class="block text-sm font-medium text-slate-700">Keterangan atau Adab</label>
                <input type="text" id="ket" name="ket" value="{{ old('ket') }}"
                       placeholder="Contoh: Dibaca sebelum menyantap makanan" 
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('ket')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('katalog.doa.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">Simpan Doa</button>
            </div>
        </form>
    </div>
</x-app-layout>
