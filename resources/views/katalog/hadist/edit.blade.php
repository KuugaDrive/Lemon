<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('katalog.hadist.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Ubah Hadits: {{ $hadist->nama_hadist }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('katalog.hadist.update', $hadist) }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200 space-y-6">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-200 pb-4">
                <h3 class="text-base font-semibold text-slate-900">Perbarui Data Hadits</h3>
                <p class="text-sm text-slate-500">Sesuaikan nama hadits, bacaan latin, dan artinya.</p>
            </div>

            <div>
                <label for="nama_hadist" class="block text-sm font-medium text-slate-700">Nama Hadits <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_hadist" name="nama_hadist" value="{{ old('nama_hadist', $hadist->nama_hadist) }}" required autofocus
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('nama_hadist')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="baca_latin" class="block text-sm font-medium text-slate-700">Bacaan Latin <span class="text-rose-500">*</span></label>
                <textarea id="baca_latin" name="baca_latin" rows="3" required
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('baca_latin', $hadist->baca_latin) }}</textarea>
                @error('baca_latin')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="artinya" class="block text-sm font-medium text-slate-700">Arti / Terjemahan <span class="text-rose-500">*</span></label>
                <textarea id="artinya" name="artinya" rows="3" required
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('artinya', $hadist->artinya) }}</textarea>
                @error('artinya')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ket" class="block text-sm font-medium text-slate-700">Keterangan / Perawi (Opsional)</label>
                <input type="text" id="ket" name="ket" value="{{ old('ket', $hadist->ket) }}"
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('ket')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('katalog.hadist.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
