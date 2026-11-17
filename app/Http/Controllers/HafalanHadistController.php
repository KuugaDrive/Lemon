<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\HafalanHadistRequest;
use App\Models\Hadist;
use App\Models\HafalanHadist;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HafalanHadistController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = HafalanHadist::visibleTo($user)->with(['siswa.kelas', 'guru', 'hadist']);

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
                })->orWhereHas('hadist', function ($hq) use ($term) {
                    $hq->where('nama_hadist', 'like', "%{$term}%");
                });
            });
        }

        $hafalans = $query->latest('tgl_pengujian')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $hadists = Hadist::orderBy('nama_hadist')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.hadist.index', compact('hafalans', 'kelasList', 'siswas', 'hadists', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $hadists = Hadist::orderBy('nama_hadist')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.hadist.create', compact('siswas', 'hadists', 'penilaianList'));
    }

    public function store(HafalanHadistRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        HafalanHadist::create($data);

        return redirect()->route('hafalan.hadist.index')->with('success', 'Catatan hafalan Hadits berhasil ditambahkan.');
    }

    public function edit(HafalanHadist $hafalanHadist): View
    {
        $user = auth()->user();
        if (! $user->canAccessSiswa($hafalanHadist->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data hafalan santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $hadists = Hadist::orderBy('nama_hadist')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.hadist.edit', compact('hafalanHadist', 'siswas', 'hadists', 'penilaianList'));
    }

    public function update(HafalanHadistRequest $request, HafalanHadist $hafalanHadist): RedirectResponse
    {
        $hafalanHadist->update($request->validated());

        return redirect()->route('hafalan.hadist.index')->with('success', 'Catatan hafalan Hadits berhasil diperbarui.');
    }

    public function destroy(HafalanHadist $hafalanHadist): RedirectResponse
    {
        if (! auth()->user()->canAccessSiswa($hafalanHadist->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data hafalan santri ini.');
        }

        $hafalanHadist->delete();

        return redirect()->route('hafalan.hadist.index')->with('success', 'Catatan hafalan Hadits berhasil dihapus.');
    }
}
