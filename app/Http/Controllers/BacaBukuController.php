<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\BacaBukuRequest;
use App\Models\Alfabet;
use App\Models\BacaBuku;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BacaBukuController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = BacaBuku::visibleTo($user)->with(['siswa.kelas', 'guru', 'alfabet']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->input('kelas_id'));
            });
        }

        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->input('siswa_id'));
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tgl_pengujian', '>=', $request->input('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tgl_pengujian', '<=', $request->input('sampai_tanggal'));
        }

        if ($request->filled('nilai')) {
            $query->where('nilai', $request->input('nilai'));
        }

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->whereHas('siswa', function ($sq) use ($term) {
                    $sq->where('nama_siswa', 'like', "%{$term}%")
                       ->orWhere('nis', 'like', "%{$term}%");
                })->orWhereHas('alfabet', function ($aq) use ($term) {
                    $aq->where('nama_alfabet', 'like', "%{$term}%");
                });
            });
        }

        $bacaBukus = $query->latest('tgl_pengujian')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $alfabets = Alfabet::orderBy('nama_alfabet')->get();
        $penilaianList = Penilaian::cases();

        return view('baca-buku.index', compact('bacaBukus', 'kelasList', 'siswas', 'alfabets', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $alfabets = Alfabet::orderBy('nama_alfabet')->get();
        $penilaianList = Penilaian::cases();

        return view('baca-buku.create', compact('siswas', 'alfabets', 'penilaianList'));
    }

    public function store(BacaBukuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        BacaBuku::create($data);

        return redirect()->route('baca-buku.index')->with('success', 'Catatan Baca Buku / Huruf Hijaiyah berhasil ditambahkan.');
    }

    public function edit(BacaBuku $bacaBuku): View
    {
        $user = auth()->user();
        if (! $user->canAccessSiswa($bacaBuku->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $alfabets = Alfabet::orderBy('nama_alfabet')->get();
        $penilaianList = Penilaian::cases();

        return view('baca-buku.edit', compact('bacaBuku', 'siswas', 'alfabets', 'penilaianList'));
    }

    public function update(BacaBukuRequest $request, BacaBuku $bacaBuku): RedirectResponse
    {
        $bacaBuku->update($request->validated());

        return redirect()->route('baca-buku.index')->with('success', 'Catatan Baca Buku / Huruf Hijaiyah berhasil diperbarui.');
    }

    public function destroy(BacaBuku $bacaBuku): RedirectResponse
    {
        if (! auth()->user()->canAccessSiswa($bacaBuku->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data santri ini.');
        }

        $bacaBuku->delete();

        return redirect()->route('baca-buku.index')->with('success', 'Catatan Baca Buku / Huruf Hijaiyah berhasil dihapus.');
    }
}
