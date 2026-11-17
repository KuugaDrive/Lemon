<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PresensiRekap
{
    /**
     * Hitung rekap presensi per siswa untuk suatu kelas dalam rentang tanggal tertentu.
     * Menggunakan query agregat tunggal tanpa N+1.
     */
    public static function hitung(int $kelasId, string $startDate, string $endDate, ?User $user = null): Collection
    {
        $user = $user ?? auth()->user();

        // Ambil siswa pada kelas tersebut yang lolos visibleTo
        $siswas = Siswa::visibleTo($user)
            ->where('kelas_id', $kelasId)
            ->with('kelas')
            ->orderBy('nama_siswa')
            ->get();

        if ($siswas->isEmpty()) {
            return collect();
        }

        // Ambil data agregat presensi dalam satu query tunggal
        $aggregates = DB::table('presensis')
            ->select('siswa_id')
            ->selectRaw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin")
            ->selectRaw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit")
            ->selectRaw("SUM(CASE WHEN status = 'alfa' THEN 1 ELSE 0 END) as alfa")
            ->selectRaw("COUNT(*) as total_hari")
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        return $siswas->map(function ($siswa) use ($aggregates) {
            $agg = $aggregates->get($siswa->id);
            $hadir = (int) ($agg->hadir ?? 0);
            $izin = (int) ($agg->izin ?? 0);
            $sakit = (int) ($agg->sakit ?? 0);
            $alfa = (int) ($agg->alfa ?? 0);
            $totalHari = (int) ($agg->total_hari ?? 0);
            $persentase = $totalHari > 0 ? round(($hadir / $totalHari) * 100, 1) : 0;

            return (object) [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alfa' => $alfa,
                'total_hari' => $totalHari,
                'persentase' => $persentase,
            ];
        });
    }
}
