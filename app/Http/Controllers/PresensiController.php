<?php

namespace App\Http\Controllers;

use App\Enums\StatusPresensi;
use App\Http\Requests\SimpanPresensiRequest;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Services\PresensiRekap;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PresensiController extends Controller
{
    /**
     * Halaman input presensi harian massal.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();

        $selectedKelasId = $request->input('kelas_id') ?: $kelasList->first()?->id;
        $tanggal = $request->input('tanggal') ?: now()->toDateString();

        $selectedKelas = null;
        $siswas = collect();
        $existingPresensi = collect();

        if ($selectedKelasId) {
            $selectedKelas = Kelas::visibleTo($user)->find($selectedKelasId);
            if (!$selectedKelas) {
                abort(403, 'Anda tidak memiliki hak akses terhadap kelas ini.');
            }

            $siswas = Siswa::visibleTo($user)
                ->where('kelas_id', $selectedKelasId)
                ->orderBy('nama_siswa')
                ->get();

            if ($siswas->isNotEmpty()) {
                $existingPresensi = Presensi::whereIn('siswa_id', $siswas->pluck('id'))
                    ->whereDate('tanggal', $tanggal)
                    ->get()
                    ->keyBy('siswa_id');
            }
        }

        $statuses = StatusPresensi::cases();

        return view('presensi.index', compact(
            'kelasList',
            'selectedKelasId',
            'selectedKelas',
            'tanggal',
            'siswas',
            'existingPresensi',
            'statuses'
        ));
    }

    /**
     * Simpan presensi massal dalam satu transaksi.
     */
    public function store(SimpanPresensiRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $guruId = auth()->id();
        $tanggal = $validated['tanggal'];
        $count = count($validated['presensi']);

        DB::transaction(function () use ($validated, $guruId, $tanggal) {
            foreach ($validated['presensi'] as $item) {
                $presensi = Presensi::where('siswa_id', $item['siswa_id'])
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                if ($presensi) {
                    $presensi->update([
                        'guru_id' => $guruId,
                        'status' => $item['status'],
                        'ket' => $item['ket'] ?? null,
                    ]);
                } else {
                    Presensi::create([
                        'siswa_id' => $item['siswa_id'],
                        'tanggal' => $tanggal,
                        'guru_id' => $guruId,
                        'status' => $item['status'],
                        'ket' => $item['ket'] ?? null,
                    ]);
                }
            }
        });

        $formattedTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');

        return redirect()->route('presensi.index', [
            'kelas_id' => $validated['kelas_id'],
            'tanggal' => $tanggal,
        ])->with('success', "Presensi {$count} santri tanggal {$formattedTanggal} berhasil disimpan.");
    }

    /**
     * Update cepat status & catatan presensi santri dari pop-up rekap.
     */
    public function quickUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'presensi_id' => ['required', 'exists:presensis,id'],
            'status' => ['required', 'in:hadir,izin,sakit,alfa'],
            'ket' => ['nullable', 'string', 'max:255'],
        ], [
            'presensi_id.required' => 'ID Presensi tidak valid.',
            'status.required' => 'Status kehadiran wajib dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'ket.max' => 'Catatan maksimal 255 karakter.',
        ]);

        $presensi = Presensi::with('siswa')->findOrFail($validated['presensi_id']);

        if (! auth()->user()->canAccessSiswa($presensi->siswa)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah presensi santri ini.');
        }

        $presensi->update([
            'status' => $validated['status'],
            'ket' => $validated['ket'],
            'guru_id' => auth()->id(),
        ]);

        $formattedTanggal = Carbon::parse($presensi->tanggal)->translatedFormat('d F Y');
        $namaSiswa = $presensi->siswa->nama_siswa;

        return back()->with('success', "Presensi {$namaSiswa} tanggal {$formattedTanggal} berhasil diperbarui.");
    }

    /**
     * Halaman rekapitulasi kehadiran per kelas.
     */
    public function rekap(Request $request): View
    {
        $user = auth()->user();
        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();

        $selectedKelasId = $request->input('kelas_id') ?: $kelasList->first()?->id;
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        $selectedKelas = null;
        $rekapData = collect();

        if ($selectedKelasId) {
            $selectedKelas = Kelas::visibleTo($user)->find($selectedKelasId);
            if (!$selectedKelas) {
                abort(403, 'Anda tidak memiliki hak akses terhadap kelas ini.');
            }

            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

            $rekapData = PresensiRekap::hitung($selectedKelasId, $startDate, $endDate, $user);
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');

        return view('presensi.rekap', compact(
            'kelasList',
            'selectedKelasId',
            'selectedKelas',
            'bulan',
            'tahun',
            'namaBulan',
            'rekapData'
        ));
    }

    /**
     * Detail catatan harian presensi per siswa untuk bulan terpilih.
     */
    public function showSiswa(Request $request, Siswa $siswa): View
    {
        if (!auth()->user()->canAccessSiswa($siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data presensi santri ini.');
        }

        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $presensis = Presensi::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('guru')
            ->orderBy('tanggal')
            ->get();

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');

        return view('presensi.siswa_detail', compact(
            'siswa',
            'bulan',
            'tahun',
            'namaBulan',
            'presensis'
        ));
    }
}
