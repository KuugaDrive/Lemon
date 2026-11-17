<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\Siswa;
use Illuminate\Support\Collection;

class SppService
{
    public static function tunggakan(int $bulan, int $tahun, ?int $kelasId = null): array
    {
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = (int) Pengaturan::get('spp_jatuh_tempo_hari', 10);

        $query = Siswa::with('kelas');

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $query->whereDoesntHave('pembayaranSpps', function ($q) use ($bulan, $tahun) {
            $q->where('bulan', $bulan)->where('tahun', $tahun);
        });

        $siswaMenunggak = $query->orderBy('kelas_id')->orderBy('nama_siswa')->get();

        $totalSantri = $siswaMenunggak->count();
        $totalNominal = $totalSantri * $defaultNominal;

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

    public static function riwayat(Siswa $siswa): Collection
    {
        return $siswa->pembayaranSpps()
            ->with('pencatat')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
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
