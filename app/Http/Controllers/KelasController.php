<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Kelas;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    /**
     * Tampilkan daftar kelas.
     */
    public function index(Request $request): View
    {
        $query = Kelas::with(['gurus'])->withCount('siswas')->orderBy('nama_kelas');

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where('nama_kelas', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
        }

        $kelasList = $query->paginate(10)->withQueryString();

        return view('master.kelas.index', compact('kelasList'));
    }

    /**
     * Tampilkan form tambah kelas.
     */
    public function create(): View
    {
        return view('master.kelas.create');
    }

    /**
     * Simpan kelas baru.
     */
    public function store(StoreKelasRequest $request): RedirectResponse
    {
        $kelas = Kelas::create($request->validated());

        return redirect()->route('master.kelas.index')
            ->with('success', "Kelas {$kelas->nama_kelas} berhasil ditambahkan.");
    }

    /**
     * Tampilkan detail kelas.
     */
    public function show(Kelas $kelas): View
    {
        $kelas->load(['gurus', 'siswas']);

        return view('master.kelas.show', compact('kelas'));
    }

    /**
     * Tampilkan form edit kelas.
     */
    public function edit(Kelas $kelas): View
    {
        return view('master.kelas.edit', compact('kelas'));
    }

    /**
     * Perbarui data kelas.
     */
    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()->route('master.kelas.index')
            ->with('success', "Data kelas {$kelas->nama_kelas} berhasil diperbarui.");
    }

    /**
     * Hapus data kelas.
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        // Tolak jika kelas masih memiliki siswa
        if ($kelas->siswas()->exists()) {
            return redirect()->route('master.kelas.index')
                ->with('error', "Kelas {$kelas->nama_kelas} tidak dapat dihapus karena masih memiliki data santri/siswa.");
        }

        try {
            $nama = $kelas->nama_kelas;
            $kelas->gurus()->detach();
            $kelas->delete();

            return redirect()->route('master.kelas.index')
                ->with('success', "Kelas {$nama} berhasil dihapus.");
        } catch (QueryException $e) {
            return redirect()->route('master.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki keterkaitan data.');
        }
    }
}
