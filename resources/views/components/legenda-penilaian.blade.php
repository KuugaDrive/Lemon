<div {{ $attributes->merge(['class' => 'bg-slate-50 border border-slate-200 rounded-xl p-3 sm:p-4 text-xs text-slate-600']) }}>
    <div class="font-semibold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Keterangan Skala Penilaian:</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">BB</span>
            <span>Belum Berkembang</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">MB</span>
            <span>Mulai Berkembang</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">BSH</span>
            <span>Berkembang Sesuai Harapan</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">BSB</span>
            <span>Berkembang Sangat Baik</span>
        </div>
    </div>
</div>
