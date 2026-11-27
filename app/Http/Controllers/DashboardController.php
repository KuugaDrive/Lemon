<?php

namespace App\Http\Controllers;

use App\Enums\StatusPresensi;
use App\Models\BacaBuku;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Presensi;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = Carbon::today();
        $currentMonth = (int) $today->format('n');
        $currentYear = (int) $today->year;

        $isAdmin = $user->hasRole('admin') || $user->hasRole('kepsek');

        if ($isAdmin) {
            $totalSiswa = Siswa::count();
            $totalGuru = User::role('guru')->count();
            $totalKelas = Kelas::count();
            $sppBulanIni = PembayaranSpp::where('bulan', $currentMonth)
                ->where('tahun', $currentYear)
                ->sum('nominal');

            $presensiHariIni = Presensi::whereDate('tanggal', $today)->get();
            $presensiHadirCount = $presensiHariIni->filter(fn ($p) => $p->status === StatusPresensi::Hadir)->count();
            $totalPresensiHariIni = $presensiHariIni->count();
            $presensiBySiswa = $presensiHariIni->groupBy('siswa_id');

            $setoranHariIniCount = SetoranIqro::whereDate('tanggal', $today)->count()
                + SetoranQuran::whereDate('tanggal', $today)->count()
                + HafalanDoa::whereDate('tgl_pengujian', $today)->count()
                + HafalanHadist::whereDate('tgl_pengujian', $today)->count()
                + BacaBuku::whereDate('tgl_pengujian', $today)->count();

            $kelasList = Kelas::with(['gurus', 'siswas'])->get()->map(function ($kelas) use ($presensiBySiswa) {
                $presensi = $kelas->siswas->flatMap(fn ($s) => $presensiBySiswa->get($s->id, collect()));

                return [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'guru_names' => $kelas->gurus->pluck('name')->implode(', ') ?: '-',
                    'total_siswa' => $kelas->siswas->count(),
                    'terisi' => $presensi->isNotEmpty(),
                    'hadir' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Hadir)->count(),
                    'izin' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Izin)->count(),
                    'sakit' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Sakit)->count(),
                    'alfa' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Alfa)->count(),
                ];
            });

            return view('dashboard', [
                'user' => $user,
                'isAdmin' => true,
                'today' => $today,
                'totalSiswa' => $totalSiswa,
                'totalGuru' => $totalGuru,
                'totalKelas' => $totalKelas,
                'sppBulanIni' => $sppBulanIni,
                'presensiHadirCount' => $presensiHadirCount,
                'totalPresensiHariIni' => $totalPresensiHariIni,
                'setoranHariIniCount' => $setoranHariIniCount,
                'kelasList' => $kelasList,
            ]);
        }

        $kelasUser = $user->kelas()->with('siswas')->get();
        $siswaIds = $kelasUser->flatMap->siswas->pluck('id')->unique();

        $totalSiswa = $siswaIds->count();
        $totalKelas = $kelasUser->count();

        $presensiHariIni = Presensi::whereDate('tanggal', $today)
            ->whereIn('siswa_id', $siswaIds)
            ->get();
        $presensiHadirCount = $presensiHariIni->filter(fn ($p) => $p->status === StatusPresensi::Hadir)->count();
        $totalPresensiHariIni = $presensiHariIni->count();
        $presensiBySiswa = $presensiHariIni->groupBy('siswa_id');

        $setoranHariIniCount = SetoranIqro::whereDate('tanggal', $today)->whereIn('siswa_id', $siswaIds)->count()
            + SetoranQuran::whereDate('tanggal', $today)->whereIn('siswa_id', $siswaIds)->count()
            + HafalanDoa::whereDate('tgl_pengujian', $today)->whereIn('siswa_id', $siswaIds)->count()
            + HafalanHadist::whereDate('tgl_pengujian', $today)->whereIn('siswa_id', $siswaIds)->count()
            + BacaBuku::whereDate('tgl_pengujian', $today)->whereIn('siswa_id', $siswaIds)->count();

        $kelasList = $kelasUser->map(function ($kelas) use ($presensiBySiswa) {
            $presensi = $kelas->siswas->flatMap(fn ($s) => $presensiBySiswa->get($s->id, collect()));

            return [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'total_siswa' => $kelas->siswas->count(),
                'terisi' => $presensi->isNotEmpty(),
                'hadir' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Hadir)->count(),
                'izin' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Izin)->count(),
                'sakit' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Sakit)->count(),
                'alfa' => $presensi->filter(fn ($p) => $p->status === StatusPresensi::Alfa)->count(),
            ];
        });

        return view('dashboard', [
            'user' => $user,
            'isAdmin' => false,
            'today' => $today,
            'totalSiswa' => $totalSiswa,
            'totalKelas' => $totalKelas,
            'presensiHadirCount' => $presensiHadirCount,
            'totalPresensiHariIni' => $totalPresensiHariIni,
            'setoranHariIniCount' => $setoranHariIniCount,
            'kelasList' => $kelasList,
        ]);
    }
}
