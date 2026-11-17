<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #1e293b;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .lembaga-name {
            font-size: 14pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .lembaga-meta {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 8px 0 2px 0;
            color: #0f172a;
        }
        .doc-period {
            text-align: center;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-table .meta-label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: bold; }

        .badge-bb { background-color: #ffe4e6; color: #9f1239; font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; }
        .badge-mb { background-color: #fef3c7; color: #92400e; font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; }
        .badge-bsh { background-color: #dbeafe; color: #1e40af; font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; }
        .badge-bsb { background-color: #d1fae5; color: #065f46; font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; }

        .legend-box {
            border: 1px dashed #cbd5e1;
            padding: 6px 10px;
            background-color: #fcfcfc;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 8pt;
            color: #475569;
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .signature-table {
            width: 100%;
            margin-top: 16px;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .signature-space {
            height: 55px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .footer-note {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <!-- KOP LEMBAGA -->
    <table class="header-table">
        <tr>
            <td style="text-align: center;">
                <div class="lembaga-name">{{ $profil['nama_lembaga'] }}</div>
                <div class="lembaga-meta">{{ $profil['alamat_lembaga'] }} | Kontak/WA: {{ $profil['no_telp_lembaga'] }}</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">{{ $judulLaporan }}</div>
    <div class="doc-period">Periode: {{ $periodeStr }}</div>

    <!-- METADATA SUBJEK -->
    <table class="meta-table">
        @if($mode === 'siswa')
            <tr>
                <td class="meta-label">Nama Santri</td>
                <td style="width: 32%;">: <strong>{{ $siswa->nama_siswa }}</strong></td>
                <td class="meta-label">Kelas</td>
                <td>: {{ $siswa->kelas?->nama_kelas }}</td>
            </tr>
            <tr>
                <td class="meta-label">NIS</td>
                <td>: <span class="font-mono">{{ $siswa->nis }}</span></td>
                <td class="meta-label">Nama Wali</td>
                <td>: {{ $siswa->nama_orang_tua }} ({{ $siswa->no_hp_ortu ?: '-' }})</td>
            </tr>
        @else
            <tr>
                <td class="meta-label">Nama Kelas</td>
                <td style="width: 32%;">: <strong>{{ $kelas->nama_kelas }}</strong></td>
                <td class="meta-label">Jumlah Santri</td>
                <td>: {{ $kelas->siswas->count() }} Santri</td>
            </tr>
            <tr>
                <td class="meta-label">Guru Pengampu</td>
                <td colspan="3">: {{ $kelas->gurus->pluck('name')->join(', ') ?: '-' }}</td>
            </tr>
        @endif
    </table>

    <!-- KONTEN UTAMA -->
    @yield('content')

    <!-- LEGENDA PENILAIAN -->
    @if($isScored)
        <div class="legend-box">
            <div class="legend-title">Keterangan Skala Penilaian:</div>
            <strong>BB</strong> = Belum Berkembang &nbsp;|&nbsp;
            <strong>MB</strong> = Mulai Berkembang &nbsp;|&nbsp;
            <strong>BSH</strong> = Berkembang Sesuai Harapan &nbsp;|&nbsp;
            <strong>BSB</strong> = Berkembang Sangat Baik
        </div>
    @endif

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Guru Pengampu Kelas<br>
                <div class="signature-space"></div>
                <span class="signature-name">{{ auth()->user()->name }}</span><br>
                <span>NIP. {{ auth()->user()->nip }}</span>
            </td>
            <td>
                Jakarta, {{ now()->translatedFormat('d F Y') }}<br>
                Kepala Lembaga<br>
                <div class="signature-space"></div>
                <span class="signature-name">{{ $profil['nama_kepala'] }}</span><br>
                <span>Pimpinan Lembaga</span>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dicetak secara otomatis melalui Sistem Informasi PAUD/TPQ Lemon pada {{ now()->translatedFormat('d F Y H:i:s') }}
    </div>
</body>
</html>
