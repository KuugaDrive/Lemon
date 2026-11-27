<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\Siswa;
use Illuminate\Support\Collection;

class SppService
{
    /**
     * Mengambil data tunggakan SPP dinamis per periode (bulan & tahun).
     * Santri dianggap menunggak jika total bayar pada periode tersebut < default nominal SPP.
     */
    public static function tunggakan(int $bulan, int $tahun, ?int $kelasId = null): array
    {
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = (int) Pengaturan::get('spp_jatuh_tempo_hari', 10);

        $query = Siswa::with(['kelas', 'pembayaranSpps' => function ($q) use ($bulan, $tahun) {
            $q->where('bulan', $bulan)->where('tahun', $tahun);
        }]);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $allSiswa = $query->orderBy('kelas_id')->orderBy('nama_siswa')->get();

        $siswaMenunggak = $allSiswa->filter(function ($s) use ($defaultNominal) {
            $totalBayar = (int) $s->pembayaranSpps->sum('nominal');
            return $totalBayar < $defaultNominal;
        })->values();

        $totalNominal = 0;
        foreach ($siswaMenunggak as $s) {
            $totalBayar = (int) $s->pembayaranSpps->sum('nominal');
            $sisa = max(0, $defaultNominal - $totalBayar);
            $s->total_dibayar = $totalBayar;
            $s->sisa_tunggakan = $sisa;
            $s->status_tunggakan = $totalBayar > 0 ? 'kurang_bayar' : 'belum_bayar';
            $totalNominal += $sisa;
        }

        $totalSantri = $siswaMenunggak->count();

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jatuh_tempo_hari' => $jatuhTempo,
            'default_nominal' => $defaultNominal,
            'total_santri' => $totalSantri,
            'total_nominal' => $totalNominal,
            'siswa_list' => $siswaMenunggak,
        ];
    }

    /**
     * Mengambil rincian status tagihan seorang santri untuk periode tertentu secara dinamis.
     */
    public static function getTagihanSiswa(int $siswaId, int $bulan, int $tahun): array
    {
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
        $totalTerbayar = (int) PembayaranSpp::where('siswa_id', $siswaId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('nominal');

        $sisa = max(0, $defaultNominal - $totalTerbayar);
        $isLunas = $totalTerbayar >= $defaultNominal;

        $statusLabel = 'Belum Bayar';
        if ($isLunas) {
            $statusLabel = 'Lunas';
        } elseif ($totalTerbayar > 0) {
            $statusLabel = 'Kurang Bayar';
        }

        return [
            'default_nominal' => $defaultNominal,
            'total_terbayar' => $totalTerbayar,
            'sisa_tagihan' => $sisa,
            'is_lunas' => $isLunas,
            'status_label' => $statusLabel,
            'rupiah_default' => 'Rp ' . number_format($defaultNominal, 0, ',', '.'),
            'rupiah_terbayar' => 'Rp ' . number_format($totalTerbayar, 0, ',', '.'),
            'rupiah_sisa' => 'Rp ' . number_format($sisa, 0, ',', '.'),
        ];
    }

    public static function riwayat(Siswa $siswa): Collection
    {
        return $siswa->pembayaranSpps()
            ->with('pencatat')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            $hasil = $baca[$angka];
        } elseif ($angka < 20) {
            $hasil = self::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $hasil = self::terbilang((int) ($angka / 10)) . ' Puluh ' . self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = 'Seratus ' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = self::terbilang((int) ($angka / 100)) . ' Ratus ' . self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = 'Seribu ' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = self::terbilang((int) ($angka / 1000)) . ' Ribu ' . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = self::terbilang((int) ($angka / 1000000)) . ' Juta ' . self::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $hasil = self::terbilang((int) ($angka / 1000000000)) . ' Miliar ' . self::terbilang($angka % 1000000000);
        } else {
            $hasil = '';
        }

        return trim(preg_replace('/\s+/', ' ', $hasil));
    }

    public static function terbilangRupiah(int $angka): string
    {
        if ($angka === 0) {
            return 'Nol Rupiah';
        }

        return self::terbilang($angka) . ' Rupiah';
    }
}
