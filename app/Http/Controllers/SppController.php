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

        $totalNominal = (clone $query)->sum('nominal');

        $pembayarans = $query->latest('tgl_bayar')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);

        return view('spp.index', compact('pembayarans', 'kelasList', 'totalNominal', 'defaultNominal'));
    }

    public function create(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $siswas = Siswa::with('kelas')->orderBy('nama_siswa')->get();

        $selectedSiswaId = $request->input('siswa_id');
        $selectedBulan = (int) ($request->input('bulan') ?: now()->month);
        $selectedTahun = (int) ($request->input('tahun') ?: now()->year);
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);

        $nominalToPay = $defaultNominal;
        $tagihanInfo = null;

        if ($selectedSiswaId) {
            $tagihanInfo = SppService::getTagihanSiswa((int) $selectedSiswaId, $selectedBulan, $selectedTahun);
            if ($request->filled('nominal')) {
                $nominalToPay = (int) $request->input('nominal');
            } elseif ($tagihanInfo['sisa_tagihan'] > 0) {
                $nominalToPay = $tagihanInfo['sisa_tagihan'];
            }
        } elseif ($request->filled('nominal')) {
            $nominalToPay = (int) $request->input('nominal');
        }

        return view('spp.create', compact(
            'kelasList',
            'siswas',
            'selectedSiswaId',
            'selectedBulan',
            'selectedTahun',
            'defaultNominal',
            'nominalToPay',
            'tagihanInfo'
        ));
    }

    /**
     * Endpoint JSON untuk cek tagihan dan sisa tunggakan santri secara real-time.
     */
    public function cekTagihan(Request $request): \Illuminate\Http\JsonResponse
    {
        $siswaId = (int) $request->input('siswa_id');
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        if (! $siswaId) {
            $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
            return response()->json([
                'default_nominal' => $defaultNominal,
                'total_terbayar' => 0,
                'sisa_tagihan' => $defaultNominal,
                'is_lunas' => false,
                'status_label' => 'Belum Bayar',
                'rupiah_default' => 'Rp ' . number_format($defaultNominal, 0, ',', '.'),
                'rupiah_terbayar' => 'Rp 0',
                'rupiah_sisa' => 'Rp ' . number_format($defaultNominal, 0, ',', '.'),
            ]);
        }

        $tagihan = SppService::getTagihanSiswa($siswaId, $bulan, $tahun);
        return response()->json($tagihan);
    }

    public function store(StoreSppRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['dicatat_oleh'] = auth()->id();

        $pembayaran = PembayaranSpp::create($data);

        return redirect()->route('spp.index')
            ->with('success', "Pembayaran SPP {$pembayaran->siswa->nama_siswa} periode {$pembayaran->periode_label} berhasil dicatat.")
            ->with('kuitansi_id', $pembayaran->id);
    }

    public function kuitansi(PembayaranSpp $spp): View
    {
        $spp->load(['siswa.kelas', 'pencatat']);

        $profil = [
            'nama_lembaga' => Pengaturan::get('nama_lembaga', 'Binar Sunnah School'),
            'alamat_lembaga' => Pengaturan::getAlamatLembaga(),
            'no_telp_lembaga' => Pengaturan::getNoTelpLembaga(),
            'nama_kepala' => Pengaturan::getNamaKepala(),
            'nip_kepala' => Pengaturan::getNipKepala(),
        ];

        return view('spp.kuitansi', compact('spp', 'profil'));
    }

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
                    ->groupBy('siswa_id')
                    ->havingRaw('SUM(nominal) >= ?', [$defaultNominal])
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
            $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
            foreach ($validated['siswa_ids'] as $siswaId) {
                $totalBayar = (int) PembayaranSpp::where('siswa_id', $siswaId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->sum('nominal');

                if ($totalBayar < $defaultNominal) {
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

    public function edit(PembayaranSpp $spp): View
    {
        $spp->load('siswa.kelas');
        return view('spp.edit', compact('spp'));
    }

    public function update(UpdateSppRequest $request, PembayaranSpp $spp): RedirectResponse
    {
        $spp->update($request->validated());

        return redirect()->route('spp.index')
            ->with('success', "Catatan SPP {$spp->siswa->nama_siswa} berhasil diperbarui.");
    }

    public function destroy(PembayaranSpp $spp): RedirectResponse
    {
        $nama = $spp->siswa->nama_siswa;
        $periode = $spp->periode_label;

        $spp->delete();

        return redirect()->route('spp.index')
            ->with('success', "Catatan pembayaran SPP {$nama} periode {$periode} berhasil dihapus.");
    }

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

    public function riwayat(Siswa $siswa): View
    {
        $riwayats = SppService::riwayat($siswa);
        $totalTerbayar = $riwayats->sum('nominal');
        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);

        $terbayarPerPeriode = $riwayats->groupBy(fn ($r) => $r->tahun . '-' . $r->bulan)
            ->map(fn ($group) => $group->sum('nominal'));

        return view('spp.riwayat', compact('siswa', 'riwayats', 'totalTerbayar', 'defaultNominal', 'terbayarPerPeriode'));
    }

    /**
     * Cetak Surat Pemberitahuan Tunggakan SPP dalam bentuk PDF.
     */
    public function suratTunggakan(Request $request, Siswa $siswa): \Illuminate\Http\Response
    {
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        $siswa->load(['kelas.gurus']);

        $defaultNominal = (int) Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = (int) Pengaturan::get('spp_jatuh_tempo_hari', 10);

        $paymentsPerMonth = PembayaranSpp::where('siswa_id', $siswa->id)
            ->where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->get()
            ->groupBy('bulan');

        $unpaidList = [];
        for ($m = 1; $m <= $bulan; $m++) {
            $paidInMonth = (int) ($paymentsPerMonth->get($m)?->sum('nominal') ?? 0);
            if ($paidInMonth < $defaultNominal) {
                $sisa = $defaultNominal - $paidInMonth;
                $namaBulanM = Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F');
                $statusInfo = $paidInMonth > 0
                    ? "Kurang Bayar (Terbayar: Rp " . number_format($paidInMonth, 0, ',', '.') . ")"
                    : "Belum Bayar";

                $unpaidList[] = [
                    'bulan' => $m,
                    'tahun' => $tahun,
                    'periode_label' => "{$namaBulanM} {$tahun}",
                    'jatuh_tempo' => "{$jatuhTempo} {$namaBulanM} {$tahun}",
                    'terbayar' => $paidInMonth,
                    'nominal' => $sisa,
                    'nominal_rupiah' => 'Rp ' . number_format($sisa, 0, ',', '.'),
                    'status_info' => $statusInfo,
                ];
            }
        }

        if (empty($unpaidList)) {
            $namaBulanM = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
            $unpaidList[] = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'periode_label' => "{$namaBulanM} {$tahun}",
                'jatuh_tempo' => "{$jatuhTempo} {$namaBulanM} {$tahun}",
                'terbayar' => 0,
                'nominal' => $defaultNominal,
                'nominal_rupiah' => 'Rp ' . number_format($defaultNominal, 0, ',', '.'),
                'status_info' => 'Belum Bayar',
            ];
        }

        $totalKeseluruhan = array_sum(array_column($unpaidList, 'nominal'));
        $totalKeseluruhanRupiah = 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.');
        $terbilang = SppService::terbilangRupiah($totalKeseluruhan);

        $guruPengampu = $siswa->kelas?->gurus->pluck('name')->implode(', ') ?: '-';
        $namaOrtu = $siswa->nama_orang_tua ?: ($siswa->nama_ayah_kandung ?: ($siswa->nama_wali ?: 'Bapak / Ibu Wali Santri'));

        $profil = [
            'nama_lembaga' => Pengaturan::get('nama_lembaga', 'Binar Sunnah School'),
            'alamat_lembaga' => Pengaturan::getAlamatLembaga(),
            'no_telp_lembaga' => Pengaturan::getNoTelpLembaga(),
            'nama_kepala' => Pengaturan::getNamaKepala(),
            'nip_kepala' => Pengaturan::getNipKepala(),
        ];

        $logoPath = null;
        if (extension_loaded('gd')) {
            $candidate = storage_path('app/public/logo-binar-693.png');
            if (file_exists($candidate)) {
                $logoPath = $candidate;
            }
        }

        $nomorSurat = sprintf('%03d/SPP-BSS/%s/%d', $siswa->id, Carbon::createFromDate($tahun, $bulan, 1)->format('m'), $tahun);
        $tglSurat = Carbon::now()->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('spp.pdf.surat_tunggakan', compact(
            'siswa',
            'bulan',
            'tahun',
            'unpaidList',
            'totalKeseluruhan',
            'totalKeseluruhanRupiah',
            'terbilang',
            'guruPengampu',
            'namaOrtu',
            'profil',
            'logoPath',
            'nomorSurat',
            'tglSurat'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream("surat-tunggakan-{$siswa->nis}-{$bulan}-{$tahun}.pdf");
    }

    public function pengaturan(): View
    {
        $nominalDefault = Pengaturan::get('spp_nominal_default', 150000);
        $jatuhTempo = Pengaturan::get('spp_jatuh_tempo_hari', 10);

        return view('spp.pengaturan', compact('nominalDefault', 'jatuhTempo'));
    }

    public function simpanPengaturan(PengaturanSppRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Pengaturan::set('spp_nominal_default', $validated['spp_nominal_default']);
        Pengaturan::set('spp_jatuh_tempo_hari', $validated['spp_jatuh_tempo_hari']);

        return redirect()->route('spp.pengaturan')
            ->with('success', 'Pengaturan default SPP berhasil disimpan.');
    }
}
