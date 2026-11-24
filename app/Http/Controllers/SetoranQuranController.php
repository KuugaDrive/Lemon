<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\SetoranQuranRequest;
use App\Models\Kelas;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetoranQuranController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = SetoranQuran::visibleTo($user)->with(['siswa.kelas', 'guru', 'surat']);

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
                })->orWhereHas('surat', function ($sq) use ($term) {
                    $sq->where('nama_surat', 'like', "%{$term}%");
                });
            });
        }

        $setorans = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.quran.index', compact('setorans', 'kelasList', 'siswas', 'surats', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.quran.create', compact('siswas', 'surats', 'penilaianList'));
    }

    public function store(SetoranQuranRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        SetoranQuran::create($data);

        return redirect()->route('setoran.quran.index')->with('success', 'Catatan setoran Al-Qur\'an berhasil ditambahkan.');
    }

    public function edit(SetoranQuran $setoranQuran): View
    {
        if (! $setoranQuran->exists) {
            $id = request()->route('setoran_quran') ?? request()->route('quran');
            $setoranQuran = SetoranQuran::findOrFail($id);
        }

        $user = auth()->user();
        if (! $user->canAccessSiswa($setoranQuran->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data setoran santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('setoran.quran.edit', compact('setoranQuran', 'siswas', 'surats', 'penilaianList'));
    }

    public function update(SetoranQuranRequest $request, SetoranQuran $setoranQuran): RedirectResponse
    {
        if (! $setoranQuran->exists) {
            $id = request()->route('setoran_quran') ?? request()->route('quran');
            $setoranQuran = SetoranQuran::findOrFail($id);
        }

        $setoranQuran->update($request->validated());

        return redirect()->route('setoran.quran.index')->with('success', 'Catatan setoran Al-Qur\'an berhasil diperbarui.');
    }

    public function destroy(SetoranQuran $setoranQuran): RedirectResponse
    {
        if (! $setoranQuran->exists) {
            $id = request()->route('setoran_quran') ?? request()->route('quran');
            $setoranQuran = SetoranQuran::findOrFail($id);
        }

        if (! auth()->user()->canAccessSiswa($setoranQuran->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data setoran santri ini.');
        }

        $setoranQuran->delete();

        return redirect()->route('setoran.quran.index')->with('success', 'Catatan setoran Al-Qur\'an berhasil dihapus.');
    }
}
