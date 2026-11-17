<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi {{ $spp->nomor_kuitansi }} - {{ $spp->siswa->nama_siswa }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page {
                size: A5 landscape;
                margin: 8mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .print-container {
                border: 2px solid #000000 !important;
                box-shadow: none !important;
                padding: 16px !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-8 px-4 print-wrapper">
    <div class="max-w-3xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('spp.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar SPP
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-700 rounded-lg hover:bg-emerald-800 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Kuitansi
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white p-8 border-2 border-slate-800 shadow-sm rounded-lg print-container">
        <div class="border-b-2 border-slate-800 pb-4 mb-4 flex items-start justify-between">
            <div>
                <div class="text-xl font-bold tracking-tight text-slate-900 uppercase">
                    {{ $profil['nama_lembaga'] }}
                </div>
                <div class="text-xs text-slate-600 mt-1">
                    {{ $profil['alamat_lembaga'] }}
                </div>
                <div class="text-xs text-slate-600">
                    Telepon: {{ $profil['no_telp_lembaga'] }}
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase font-semibold text-slate-500">
                    Kuitansi Pembayaran
                </div>
                <div class="text-sm font-mono font-bold text-slate-900 mt-0.5">
                    {{ $spp->nomor_kuitansi }}
                </div>
                <div class="text-xs text-slate-600 mt-1">
                    {{ $spp->tgl_bayar->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <div class="text-center my-4">
            <span class="text-base font-bold uppercase tracking-wider text-slate-900 border-b border-slate-800 pb-0.5 px-3">
                Bukti Pembayaran SPP
            </span>
        </div>

        <div class="space-y-2.5 text-sm my-5">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Telah Terima Dari</div>
                <div class="col-span-8 font-semibold text-slate-900">: {{ $spp->siswa->nama_wali }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Nama Santri</div>
                <div class="col-span-8 font-semibold text-slate-900">: {{ $spp->siswa->nama_siswa }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Nomor Induk Santri</div>
                <div class="col-span-8 font-mono text-slate-900">: {{ $spp->siswa->nis }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Kelas</div>
                <div class="col-span-8 text-slate-900">: {{ $spp->siswa->kelas?->nama_kelas ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Untuk Pembayaran</div>
                <div class="col-span-8 font-semibold text-slate-900">: SPP Periode {{ $spp->periode_label }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 text-slate-600 font-medium">Keterangan Transaksi</div>
                <div class="col-span-8 text-slate-900">: {{ $spp->ket ?: 'Lunas' }}</div>
            </div>
        </div>

        <div class="my-5 p-3.5 bg-slate-50 border border-slate-300 rounded">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs uppercase font-semibold text-slate-600">Jumlah Pembayaran</span>
                <span class="text-lg font-mono font-bold text-slate-900">{{ $spp->nominal_rupiah }}</span>
            </div>
            <div class="text-xs text-slate-700 italic border-t border-slate-200 pt-1.5">
                Terbilang: {{ $spp->terbilang }}
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 text-center pt-4 text-sm">
            <div>
                <div class="text-xs text-slate-600 mb-16">Penyetor</div>
                <div class="font-semibold text-slate-900 underline">{{ $spp->siswa->nama_wali }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Orang Tua / Wali Santri</div>
            </div>
            <div>
                <div class="text-xs text-slate-600 mb-16">Penerima</div>
                <div class="font-semibold text-slate-900 underline">{{ $spp->pencatat?->name ?? 'Petugas Tata Usaha' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Petugas Tata Usaha / Bendahara</div>
            </div>
        </div>

        <div class="mt-8 pt-3 border-t border-slate-200 text-center text-xs text-slate-400">
            Kuitansi ini dicetak otomatis melalui sistem informasi lembaga dan sah sebagai bukti pembayaran.
        </div>
    </div>
</body>
</html>
