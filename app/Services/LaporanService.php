<?php

namespace App\Services;

use App\Models\BacaBuku;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LaporanService
{
    public static function prepare(array $params): array
    {
        $jenis = $params['jenis'];
        $mode = $params['mode'];
        $dariTanggal = $params['dari_tanggal'];
        $sampaiTanggal = $params['sampai_tanggal'];

        $profil = [
            'nama_lembaga' => Pengaturan::get('nama_lembaga', 'PAUD / TPQ Lemon'),
            'alamat_lembaga' => Pengaturan::get('alamat_lembaga', 'Jl. Pendidikan Karakter Anak No. 7, Jakarta'),
            'no_telp_lembaga' => Pengaturan::get('no_telp_lembaga', '0812-3456-7890'),
            'nama_kepala' => Pengaturan::get('nama_kepala', 'H. Muhammad Yusuf, M.Pd.'),
        ];

        $periodeStr = Carbon::parse($dariTanggal)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d M Y');

        $siswa = null;
        $kelas = null;
        $items = collect();
        $rekapData = null;
        $isScored = in_array($jenis, ['iqro', 'quran', 'doa', 'hadist', 'surat', 'baca_buku']);
        $orientation = $mode === 'kelas' ? 'landscape' : 'portrait';

        if ($mode === 'siswa') {
            $siswa = Siswa::with('kelas')->findOrFail($params['siswa_id']);
            $kelas = $siswa->kelas;
            $entitySlug = Str::slug($siswa->nama_siswa);

            switch ($jenis) {
                case 'presensi':
                    $items = Presensi::where('siswa_id', $siswa->id)
                        ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                        ->with('guru')
                        ->orderBy('tanggal')
                        ->get();
                    break;
                case 'iqro':
                    $items = SetoranIqro::where('siswa_id', $siswa->id)
                        ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                        ->with(['iqro', 'guru'])
                        ->orderBy('tanggal')
                        ->get();
                    break;
                case 'quran':
                    $items = SetoranQuran::where('siswa_id', $siswa->id)
                        ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                        ->with(['surat', 'guru'])
                        ->orderBy('tanggal')
                        ->get();
                    break;
                case 'doa':
                    $items = HafalanDoa::where('siswa_id', $siswa->id)
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['doa', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'hadist':
                    $items = HafalanHadist::where('siswa_id', $siswa->id)
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['hadist', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'surat':
                    $items = HafalanSurat::where('siswa_id', $siswa->id)
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['surat', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'baca_buku':
                    $items = BacaBuku::where('siswa_id', $siswa->id)
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['alfabet', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'spp':
                    $items = PembayaranSpp::where('siswa_id', $siswa->id)
                        ->whereBetween('tgl_bayar', [$dariTanggal, $sampaiTanggal])
                        ->with('pencatat')
                        ->orderBy('tahun')
                        ->orderBy('bulan')
                        ->get();
                    break;
            }
        } else {
            // Mode Kelas
            $kelas = Kelas::with(['siswas', 'gurus'])->findOrFail($params['kelas_id']);
            $entitySlug = Str::slug($kelas->nama_kelas);

            switch ($jenis) {
                case 'presensi':
                    $rekapData = PresensiRekap::hitung($kelas->id, $dariTanggal, $sampaiTanggal);
                    break;
                case 'iqro':
                    $items = SetoranIqro::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'iqro', 'guru'])
                        ->orderBy('tanggal')
                        ->get();
                    break;
                case 'quran':
                    $items = SetoranQuran::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'surat', 'guru'])
                        ->orderBy('tanggal')
                        ->get();
                    break;
                case 'doa':
                    $items = HafalanDoa::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'doa', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'hadist':
                    $items = HafalanHadist::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'hadist', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'surat':
                    $items = HafalanSurat::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'surat', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'baca_buku':
                    $items = BacaBuku::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tgl_pengujian', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'alfabet', 'guru'])
                        ->orderBy('tgl_pengujian')
                        ->get();
                    break;
                case 'spp':
                    $items = PembayaranSpp::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelas->id))
                        ->whereBetween('tgl_bayar', [$dariTanggal, $sampaiTanggal])
                        ->with(['siswa', 'pencatat'])
                        ->orderBy('tgl_bayar')
                        ->get();
                    break;
            }
        }

        $cleanStart = Carbon::parse($dariTanggal)->format('Ymd');
        $cleanEnd = Carbon::parse($sampaiTanggal)->format('Ymd');
        $filename = "laporan-{$jenis}-{$mode}-{$entitySlug}-{$cleanStart}-{$cleanEnd}.pdf";

        $viewName = "laporan.pdf.{$jenis}_{$mode}";

        $judulLaporan = self::judul($jenis, $mode);

        return [
            'view' => $viewName,
            'filename' => $filename,
            'orientation' => $orientation,
            'data' => compact(
                'profil',
                'jenis',
                'mode',
                'dariTanggal',
                'sampaiTanggal',
                'periodeStr',
                'siswa',
                'kelas',
                'items',
                'rekapData',
                'isScored',
                'judulLaporan'
            ),
        ];
    }

    public static function judul(string $jenis, string $mode): string
    {
        $labelJenis = match ($jenis) {
            'presensi' => 'Presensi dan Kehadiran Santri',
            'iqro' => 'Perkembangan Setoran Iqro',
            'quran' => 'Perkembangan Setoran Al-Qur\'an',
            'doa' => 'Hafalan Doa Harian',
            'hadist' => 'Hafalan Hadits Pendek',
            'surat' => 'Hafalan Nama Surat',
            'baca_buku' => 'Baca Buku Alfabet',
            'spp' => 'Pembayaran Iuran SPP',
            default => 'Laporan Akademik',
        };

        $labelMode = $mode === 'siswa' ? 'Individual Santri' : 'Rekapitulasi Kelas';

        return "LAPORAN " . strtoupper($labelJenis) . " — " . strtoupper($labelMode);
    }
}
