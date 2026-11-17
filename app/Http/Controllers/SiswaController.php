<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar santri / siswa (sesuai scope visibleTo).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Query dibatasi oleh scope visibleTo
        $query = Siswa::visibleTo($user)->with(['kelas'])->orderBy('nama_siswa');

        // Filter kelas jika dipilih
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->input('kelas_id'));
        }

        // Pencarian NIS atau Nama Siswa
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_siswa', 'like', "%{$keyword}%")
                  ->orWhere('nis', 'like', "%{$keyword}%")
                  ->orWhere('nama_orang_tua', 'like', "%{$keyword}%");
            });
        }

        $siswas = $query->paginate(10)->withQueryString();

        // Dropdown kelas dibatasi oleh scope visibleTo user
        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();

        return view('master.siswa.index', compact('siswas', 'kelasList'));
    }

    /**
     * Tampilkan form tambah santri baru.
     */
    public function create(): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('master.siswa.create', compact('kelasList'));
    }

    /**
     * Simpan data santri baru.
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        $siswa = Siswa::create($request->validated());

        return redirect()->route('master.siswa.index')
            ->with('success', "Santri {$siswa->nama_siswa} (NIS: {$siswa->nis}) berhasil ditambahkan.");
    }

    /**
     * Tampilkan detail santri.
     */
    public function show(Siswa $siswa): View
    {
        // Pastikan siswa berada dalam scope user
        if (!auth()->user()->canAccessSiswa($siswa)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data santri di kelas ini.');
        }

        $siswa->load('kelas');

        return view('master.siswa.show', compact('siswa'));
    }

    /**
     * Tampilkan form edit santri.
     */
    public function edit(Siswa $siswa): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('master.siswa.edit', compact('siswa', 'kelasList'));
    }

    /**
     * Perbarui data santri.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->route('master.siswa.index')
            ->with('success', "Data santri {$siswa->nama_siswa} berhasil diperbarui.");
    }

    /**
     * Hapus data santri.
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        if (
            $siswa->presensis()->exists() ||
            $siswa->setoranIqros()->exists() ||
            $siswa->setoranQurans()->exists() ||
            $siswa->hafalanDoas()->exists() ||
            $siswa->hafalanHadists()->exists() ||
            $siswa->hafalanSurats()->exists() ||
            $siswa->bacaBukus()->exists() ||
            $siswa->pembayaranSpps()->exists()
        ) {
            return redirect()->route('master.siswa.index')
                ->with('error', "Santri {$siswa->nama_siswa} tidak dapat dihapus karena telah memiliki data riwayat setoran/presensi/SPP.");
        }

        try {
            $nama = $siswa->nama_siswa;
            $siswa->delete();

            return redirect()->route('master.siswa.index')
                ->with('success', "Data santri {$nama} berhasil dihapus.");
        } catch (QueryException $e) {
            return redirect()->route('master.siswa.index')
                ->with('error', 'Santri tidak dapat dihapus karena masih memiliki relasi data terkait.');
        }
    }
}
