<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\HafalanDoaRequest;
use App\Models\Doa;
use App\Models\HafalanDoa;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HafalanDoaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = HafalanDoa::visibleTo($user)->with(['siswa.kelas', 'guru', 'doa']);

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
                })->orWhereHas('doa', function ($dq) use ($term) {
                    $dq->where('nama_doa', 'like', "%{$term}%");
                });
            });
        }

        $hafalans = $query->latest('tgl_pengujian')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $doas = Doa::orderBy('nama_doa')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.doa.index', compact('hafalans', 'kelasList', 'siswas', 'doas', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $doas = Doa::orderBy('nama_doa')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.doa.create', compact('siswas', 'doas', 'penilaianList'));
    }

    public function store(HafalanDoaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        HafalanDoa::create($data);

        return redirect()->route('hafalan.doa.index')->with('success', 'Catatan hafalan Doa berhasil ditambahkan.');
    }

    public function edit(HafalanDoa $hafalanDoa): View
    {
        if (! $hafalanDoa->exists) {
            $id = request()->route('hafalan_doa') ?? request()->route('doa');
            $hafalanDoa = HafalanDoa::findOrFail($id);
        }

        $user = auth()->user();
        if (! $user->canAccessSiswa($hafalanDoa->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data hafalan santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $doas = Doa::orderBy('nama_doa')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.doa.edit', compact('hafalanDoa', 'siswas', 'doas', 'penilaianList'));
    }

    public function update(HafalanDoaRequest $request, HafalanDoa $hafalanDoa): RedirectResponse
    {
        if (! $hafalanDoa->exists) {
            $id = request()->route('hafalan_doa') ?? request()->route('doa');
            $hafalanDoa = HafalanDoa::findOrFail($id);
        }

        $hafalanDoa->update($request->validated());

        return redirect()->route('hafalan.doa.index')->with('success', 'Catatan hafalan Doa berhasil diperbarui.');
    }

    public function destroy(HafalanDoa $hafalanDoa): RedirectResponse
    {
        if (! $hafalanDoa->exists) {
            $id = request()->route('hafalan_doa') ?? request()->route('doa');
            $hafalanDoa = HafalanDoa::findOrFail($id);
        }

        if (! auth()->user()->canAccessSiswa($hafalanDoa->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data hafalan santri ini.');
        }

        $hafalanDoa->delete();

        return redirect()->route('hafalan.doa.index')->with('success', 'Catatan hafalan Doa berhasil dihapus.');
    }
}
