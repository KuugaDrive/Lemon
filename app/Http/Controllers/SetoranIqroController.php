<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\SetoranIqroRequest;
use App\Models\Iqro;
use App\Models\Kelas;
use App\Models\SetoranIqro;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetoranIqroController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = SetoranIqro::visibleTo($user)->with(['siswa.kelas', 'guru', 'iqro']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->input('kelas_id'));
            });
        }

        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->input('siswa_id'));
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->input('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->input('sampai_tanggal'));
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
                })->orWhereHas('iqro', function ($iq) use ($term) {
                    $iq->where('nama_iqro', 'like', "%{$term}%");
                });
            });
        }

        $setorans = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $iqros = Iqro::orderBy('nama_iqro')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.iqro.index', compact('setorans', 'kelasList', 'siswas', 'iqros', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $iqros = Iqro::orderBy('nama_iqro')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.iqro.create', compact('siswas', 'iqros', 'penilaianList'));
    }

    public function store(SetoranIqroRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        SetoranIqro::create($data);

        return redirect()->route('setoran.iqro.index')->with('success', 'Catatan setoran Iqro berhasil ditambahkan.');
    }

    public function edit(SetoranIqro $setoranIqro): View
    {
        if (! $setoranIqro->exists) {
            $id = request()->route('setoran_iqro') ?? request()->route('iqro');
            $setoranIqro = SetoranIqro::findOrFail($id);
        }

        $user = auth()->user();
        if (! $user->canAccessSiswa($setoranIqro->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data setoran santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $iqros = Iqro::orderBy('nama_iqro')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.iqro.edit', compact('setoranIqro', 'siswas', 'iqros', 'penilaianList'));
    }

    public function update(SetoranIqroRequest $request, SetoranIqro $setoranIqro): RedirectResponse
    {
        if (! $setoranIqro->exists) {
            $id = request()->route('setoran_iqro') ?? request()->route('iqro');
            $setoranIqro = SetoranIqro::findOrFail($id);
        }

        $setoranIqro->update($request->validated());

        return redirect()->route('setoran.iqro.index')->with('success', 'Catatan setoran Iqro berhasil diperbarui.');
    }

    public function destroy(SetoranIqro $setoranIqro): RedirectResponse
    {
        if (! $setoranIqro->exists) {
            $id = request()->route('setoran_iqro') ?? request()->route('iqro');
            $setoranIqro = SetoranIqro::findOrFail($id);
        }

        if (! auth()->user()->canAccessSiswa($setoranIqro->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data setoran santri ini.');
        }

        $setoranIqro->delete();

        return redirect()->route('setoran.iqro.index')->with('success', 'Catatan setoran Iqro berhasil dihapus.');
    }
}
