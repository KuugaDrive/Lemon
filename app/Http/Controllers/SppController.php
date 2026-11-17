<?php

namespace App\Http\Controllers;

use App\Http\Requests\MassalSppRequest;
use App\Http\Requests\PengaturanSppRequest;
use App\Http\Requests\StoreSppRequest;
use App\Http\Requests\UpdateSppRequest;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Services\SppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SppController extends Controller
{
    /**
     * Daftar seluruh transaksi SPP.
     */
    public function index(Request $request): View
    {
        $query = PembayaranSpp::with(['siswa.kelas', 'pencatat']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->input('kelas_id'));
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->input('bulan'));
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->input('tahun'));
        }

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->whereHas('siswa', function ($sq) use ($term) {
                $sq->where('nama_siswa', 'like', "%{$term}%")
                   ->orWhere('nis', 'like', "%{$term}%");
            });
        }

        // Hitung total nominal pada filter aktif sebelum paginasi
        $totalNominal = (clone $query)->sum('nominal');

        $pembayarans = $query->latest('tgl_bayar')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('spp.index', compact('pembayarans', 'kelasList', 'totalNominal'));
    }

    /**
     * Form pembayaran SPP satuan.
     */
    public function create(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $siswas = Siswa::with('kelas')->orderBy('nama_siswa')->get();

        $selectedSiswaId = $request->input('siswa_id');
        $selectedBulan = (int) ($request->input('bulan') ?: now()->month);
        $selectedTahun = (int) ($request->input('tahun') ?: now()->year);
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);

        return view('spp.create', compact(
            'kelasList',
            'siswas',
            'selectedSiswaId',
            'selectedBulan',
            'selectedTahun',
            'defaultNominal'
        ));
    }

    /**
     * Simpan pembayaran SPP satuan.
     */
    public function store(StoreSppRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['dicatat_oleh'] = auth()->id();

        $pembayaran = PembayaranSpp::create($data);

        return redirect()->route('spp.index')
            ->with('success', "Pembayaran SPP {$pembayaran->siswa->nama_siswa} periode {$pembayaran->periode_label} berhasil dicatat.");
    }

    /**
     * Form input SPP massal satu kelas.
     */
    public function massal(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $selectedKelasId = $request->input('kelas_id') ?: $kelasList->first()?->id;
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);

        $selectedKelas = null;
        $siswas = collect();
        $sudahBayarSiswaIds = collect();

        if ($selectedKelasId) {
            $selectedKelas = Kelas::find($selectedKelasId);
            $siswas = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama_siswa')->get();

            if ($siswas->isNotEmpty()) {
                $sudahBayarSiswaIds = PembayaranSpp::whereIn('siswa_id', $siswas->pluck('id'))
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->pluck('siswa_id');
            }
        }

        return view('spp.massal', compact(
            'kelasList',
            'selectedKelasId',
            'selectedKelas',
            'bulan',
            'tahun',
            'defaultNominal',
            'siswas',
            'sudahBayarSiswaIds'
        ));
    }

    /**
     * Proses simpan pembayaran massal.
     */
    public function storeMassal(MassalSppRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $dicatatOleh = auth()->id();
        $bulan = $validated['bulan'];
        $tahun = $validated['tahun'];
        $nominal = $validated['nominal'];
        $tglBayar = $validated['tgl_bayar'];
        $ket = $validated['ket'] ?? null;

        $count = 0;

        DB::transaction(function () use ($validated, $bulan, $tahun, $nominal, $tglBayar, $ket, $dicatatOleh, &$count) {
            foreach ($validated['siswa_ids'] as $siswaId) {
                // Abaikan jika sudah pernah bayar periode ini
                $exists = PembayaranSpp::where('siswa_id', $siswaId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->exists();

                if (! $exists) {
                    PembayaranSpp::create([
                        'siswa_id' => $siswaId,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'nominal' => $nominal,
                        'tgl_bayar' => $tglBayar,
                        'ket' => $ket,
                        'dicatat_oleh' => $dicatatOleh,
                    ]);
                    $count++;
                }
            }
        });

        $namaBulan = Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F');

        return redirect()->route('spp.index')
            ->with('success', "Berhasil mencatat pembayaran SPP massal untuk {$count} santri pada periode {$namaBulan} {$tahun}.");
    }

    /**
     * Form edit pembayaran.
     */
    public function edit(PembayaranSpp $spp): View
    {
        $spp->load('siswa.kelas');
        return view('spp.edit', compact('spp'));
    }

    /**
     * Perbarui data pembayaran.
     */
    public function update(UpdateSppRequest $request, PembayaranSpp $spp): RedirectResponse
    {
        $spp->update($request->validated());

        return redirect()->route('spp.index')
            ->with('success', "Catatan SPP {$spp->siswa->nama_siswa} berhasil diperbarui.");
    }

    /**
     * Hapus pembayaran.
     */
    public function destroy(PembayaranSpp $spp): RedirectResponse
    {
        $nama = $spp->siswa->nama_siswa;
        $periode = $spp->periode_label;

        $spp->delete();

        return redirect()->route('spp.index')
            ->with('success', "Catatan pembayaran SPP {$nama} periode {$periode} berhasil dihapus.");
    }

    /**
     * Rekapitulasi tunggakan SPP.
     */
    public function tunggakan(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $selectedKelasId = $request->filled('kelas_id') ? (int) $request->input('kelas_id') : null;
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        $tunggakanData = SppService::tunggakan($bulan, $tahun, $selectedKelasId);

        $namaBulan = Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F');

        return view('spp.tunggakan', compact(
            'kelasList',
            'selectedKelasId',
            'bulan',
            'tahun',
            'namaBulan',
            'tunggakanData'
        ));
    }

    /**
     * Riwayat pembayaran per santri.
     */
    public function riwayat(Siswa $siswa): View
    {
        $riwayats = SppService::riwayat($siswa);
        $totalTerbayar = $riwayats->sum('nominal');

        return view('spp.riwayat', compact('siswa', 'riwayats', 'totalTerbayar'));
    }

    /**
     * Halaman pengaturan SPP.
     */
    public function pengaturan(): View
    {
        $nominalDefault = Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = Pengaturan::get('spp_jatuh_tempo_hari', 10);

        return view('spp.pengaturan', compact('nominalDefault', 'jatuhTempo'));
    }

    /**
     * Simpan pengaturan default SPP.
     */
    public function simpanPengaturan(PengaturanSppRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Pengaturan::set('spp_nominal_default', $validated['spp_nominal_default']);
        Pengaturan::set('spp_jatuh_tempo_hari', $validated['spp_jatuh_tempo_hari']);

        return redirect()->route('spp.pengaturan')
            ->with('success', 'Pengaturan default SPP berhasil disimpan.');
    }
}
