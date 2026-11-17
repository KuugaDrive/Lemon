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
     * Ambil daftar santri yang menunggak / belum membayar SPP untuk periode bulan & tahun tertentu.
     */
    public static function tunggakan(int $bulan, int $tahun, ?int $kelasId = null): array
    {
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = (int) Pengaturan::get('spp_jatuh_tempo_hari', 10);

        $query = Siswa::with('kelas');

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        // Cari siswa yang BELUM memiliki catatan pembayaran di (bulan, tahun) terpilih
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

    /**
     * Ambil riwayat pembayaran SPP seorang santri.
     */
    public static function riwayat(Siswa $siswa): Collection
    {
        return $siswa->pembayaranSpps()
            ->with('pencatat')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }
}
