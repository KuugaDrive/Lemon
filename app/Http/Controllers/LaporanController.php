<?php

namespace App\Http\Controllers;

use App\Http\Requests\CetakLaporanRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Halaman Pusat Cetak Laporan.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();

        $canAccessSpp = $user->hasRole('admin') || $user->hasPermissionTo('kelola-spp');

        $jenisOptions = [
            'presensi' => 'Presensi Harian',
            'iqro' => 'Setoran Iqro',
            'quran' => 'Setoran Nama Surat',
            'doa' => 'Setoran Doa Harian',
            'hadist' => 'Setoran Hadist',
            'baca_buku' => 'Setoran Alfabet',
        ];

        if ($canAccessSpp) {
            $jenisOptions['spp'] = 'Keuangan SPP';
        }

        $defaultDariTanggal = now()->startOfMonth()->toDateString();
        $defaultSampaiTanggal = now()->toDateString();

        return view('laporan.index', compact(
            'kelasList',
            'siswas',
            'canAccessSpp',
            'jenisOptions',
            'defaultDariTanggal',
            'defaultSampaiTanggal'
        ));
    }

    /**
     * Generate & stream/download PDF laporan.
     */
    public function cetak(CetakLaporanRequest $request): Response
    {
        $prepared = LaporanService::prepare($request->validated());

        $pdf = Pdf::loadView($prepared['view'], $prepared['data'])
            ->setPaper('a4', $prepared['orientation']);

        if ($request->boolean('download')) {
            return $pdf->download($prepared['filename']);
        }

        return $pdf->stream($prepared['filename']);
    }
}
