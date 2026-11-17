<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('spp.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Pengaturan Default SPP
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <div class="mb-6 pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Konfigurasi Nilai & Jatuh Tempo</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Nilai default ini digunakan sebagai rekomendasi awal pada form input SPP satuan dan massal, serta acuan kalkulasi potensi tunggakan.
                </p>
            </div>

            <form method="POST" action="{{ route('spp.pengaturan.simpan') }}" class="space-y-6">
                @csrf

                <!-- Nominal Default -->
                <div>
                    <label for="spp_nominal_default" class="block text-sm font-semibold text-slate-700 mb-1">
                        Nominal SPP Default Per Bulan Rupiah <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="spp_nominal_default" name="spp_nominal_default" min="1000" step="1000" value="{{ old('spp_nominal_default', $nominalDefault) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('spp_nominal_default') border-rose-500 @enderror" required>
                    <p class="text-xs text-slate-400 mt-1">Contoh: 150000 untuk Rp 150.000,-</p>
                    @error('spp_nominal_default') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jatuh Tempo -->
                <div>
                    <label for="spp_jatuh_tempo_hari" class="block text-sm font-semibold text-slate-700 mb-1">
                        Tanggal Jatuh Tempo Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="spp_jatuh_tempo_hari" name="spp_jatuh_tempo_hari" min="1" max="31" value="{{ old('spp_jatuh_tempo_hari', $jatuhTempo) }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 @error('spp_jatuh_tempo_hari') border-rose-500 @enderror" required>
                    <p class="text-xs text-slate-400 mt-1">Tanggal dalam bulan (1 - 31), default tanggal 10 setiap bulan.</p>
                    @error('spp_jatuh_tempo_hari') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('spp.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-sm font-medium text-white shadow-sm transition-colors">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
