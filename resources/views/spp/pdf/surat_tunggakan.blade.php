<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pemberitahuan Tunggakan SPP - {{ $siswa->nama_siswa }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 20mm 18mm 20mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo {
            width: 75px;
            text-align: left;
        }
        .kop-logo img {
            height: 60px;
            width: auto;
        }
        .kop-text {
            text-align: center;
            padding-right: 75px; /* balance logo width */
        }
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .kop-sub {
            font-size: 9pt;
            color: #475569;
            margin-top: 3px;
        }
        .kop-border {
            border-top: 2.5px solid #0f172a;
            border-bottom: 1px solid #0f172a;
            height: 3px;
            margin-top: 8px;
            margin-bottom: 18px;
        }

        /* Surat Info */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            vertical-align: top;
            padding: 1.5px 0;
        }

        /* Address */
        .recipient-box {
            margin-top: 10px;
            margin-bottom: 18px;
            font-size: 10.5pt;
        }
        .recipient-box .title {
            font-weight: bold;
        }

        /* Content Paragraphs */
        p {
            margin: 0 0 10px 0;
            text-align: justify;
        }
        .greeting {
            margin-bottom: 12px;
        }

        /* Data Santri */
        .data-santri-table {
            width: 100%;
            margin: 10px 0 16px 15px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .data-santri-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .data-santri-table .label {
            width: 150px;
            color: #334155;
        }
        .data-santri-table .sep {
            width: 15px;
            text-align: center;
        }
        .data-santri-table .value {
            font-weight: bold;
            color: #0f172a;
        }

        /* Table Tunggakan */
        .tabel-tunggakan {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 14px 0;
            font-size: 9.5pt;
        }
        .tabel-tunggakan th {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .tabel-tunggakan td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .tabel-tunggakan .total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #94a3b8;
        }

        .terbilang-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 6px 12px;
            margin-bottom: 14px;
            font-size: 9pt;
            font-style: italic;
            color: #334155;
            text-align: center;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 65px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .signature-title {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <table class="kop-table">
        <tr>
            @if($logoPath)
                <td class="kop-logo">
                    <img src="{{ $logoPath }}" alt="Logo">
                </td>
            @endif
            <td class="kop-text" style="{{ $logoPath ? '' : 'padding-right: 0;' }}">
                <div class="kop-title">{{ $profil['nama_lembaga'] }}</div>
                <div class="kop-sub">{{ $profil['alamat_lembaga'] }} &bull; Telp: {{ $profil['no_telp_lembaga'] }}</div>
            </td>
        </tr>
    </table>
    <div class="kop-border"></div>

    <!-- Nomor & Tanggal Surat -->
    <table class="meta-table">
        <tr>
            <td style="width: 90px;">Nomor</td>
            <td style="width: 15px;">:</td>
            <td>{{ $nomorSurat }}</td>
            <td style="text-align: right;">{{ $tglSurat }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><strong>Pemberitahuan Tunggakan Iuran SPP</strong></td>
            <td></td>
        </tr>
    </table>

    <!-- Kepada Yth. Nama Orang Tua -->
    <div class="recipient-box">
        Kepada Yth.<br>
        <span class="title">Bapak / Ibu {{ $namaOrtu }}</span><br>
        Orang Tua / Wali dari ananda: <strong>{{ $siswa->nama_siswa }}</strong><br>
        di Tempat
    </div>

    <p class="greeting"><em>Assalamu'alaikum Warahmatullahi Wabarakatuh,</em></p>

    <p>
        Segala puji hanya bagi Allah Subhanahu wa Ta'ala atas limpahan rahmat, taufik, dan hidayah-Nya. Shalawat dan salam semoga senantiasa tercurah kepada junjungan kita Nabi Besar Muhammad Shallallahu 'Alaihi Wasallam, keluarga, sahabat, dan para pengikutnya hingga akhir zaman.
    </p>

    <p>
        Sehubungan dengan kelancaran kegiatan operasional belajar mengajar serta pemeliharaan sarana prasarana di <strong>{{ $profil['nama_lembaga'] }}</strong>, bersama surat ini kami menginformasikan catatan administrasi Iuran Bulanan / SPP ananda:
    </p>

    <!-- Data Santri -->
    <table class="data-santri-table">
        <tr>
            <td class="label">Nama Santri</td>
            <td class="sep">:</td>
            <td class="value">{{ $siswa->nama_siswa }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Induk Santri (NIS)</td>
            <td class="sep">:</td>
            <td class="value" style="font-family: monospace;">{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td class="value">{{ $siswa->kelas?->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Guru Pengampu</td>
            <td class="sep">:</td>
            <td class="value">{{ $guruPengampu }}</td>
        </tr>
    </table>

    <p>
        Hingga diterbitkannya surat ini, rincian bulan yang belum tercatat pelunasannya adalah sebagai berikut:
    </p>

    <!-- Tabel Rincian Tunggakan -->
    <table class="tabel-tunggakan">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Bulan yang Ditunggak</th>
                <th style="width: 120px;">Batas Jatuh Tempo</th>
                <th style="width: 130px; text-align: center;">Status</th>
                <th style="width: 130px; text-align: right;">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unpaidList as $idx => $item)
                <tr>
                    <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                    <td style="font-weight: 600;">{{ $item['periode_label'] }}</td>
                    <td style="text-align: center; color: #475569;">{{ $item['jatuh_tempo'] }}</td>
                    <td style="text-align: center; font-size: 8.5pt; color: {{ ($item['terbayar'] ?? 0) > 0 ? '#b45309' : '#b91c1c' }};">
                        {{ $item['status_info'] ?? 'Belum Bayar' }}
                    </td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ $item['nominal_rupiah'] }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right; text-transform: uppercase;">Total Tunggakan :</td>
                <td style="text-align: right; font-family: monospace; color: #b91c1c; font-size: 10.5pt;">{{ $totalKeseluruhanRupiah }}</td>
            </tr>
        </tbody>
    </table>

    <div class="terbilang-box">
        Terbilang: <strong># {{ $terbilang }} #</strong> <br><br><br>
        <p> Pembayaran dapat dilakukan melalui rekening di bawah ini: </p>
        <strong>Bank: BSI - A/N  Binar Sunnah School - No. Rekening: 7326863453 </strong>
    </div>

    <p>
        Mengingat pentingnya kontribusi SPP untuk kelangsungan proses pendidikan ananda, kami mengimbau kepada Bapak/Ibu agar dapat segera menyelesaikan kewajiban tersebut melalui rekening sekolah atau langsung ke bagian Tata Usaha / Keuangan.
    </p>

    <p>
        Apabila Bapak/Ibu telah melakukan pembayaran sebelum surat ini diterima, mohon konfirmasikan bukti transfer kepada kami untuk dilakukan penyesuaian pencatatan.
    </p>

    <p>
        Demikian surat pemberitahuan ini kami sampaikan. Atas perhatian, pengertian, dan kerja sama yang baik dari Bapak/Ibu, kami haturkan <em>jazakumullahu khairan katsiran</em>.
    </p>

    <p style="margin-top: 10px;"><em>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</em></p>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Kepala Sekolah</strong>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $profil['nama_kepala'] }}</div>
                @if(!empty($profil['nip_kepala']))
                    <div class="signature-title">NIP. {{ $profil['nip_kepala'] }}</div>
                @else
                    <div class="signature-title">{{ $profil['nama_lembaga'] }}</div>
                @endif
            </td>
            <td>
                Jakarta, {{ $tglSurat }}<br>
                <strong>Bagian Keuangan &amp; Administrasi</strong>
                <div class="signature-space"></div>
                <div class="signature-name">Bendahara Sekolah</div>
                <div class="signature-title">Tata Usaha SPP</div>
            </td>
        </tr>
    </table>

</body>
</html>
