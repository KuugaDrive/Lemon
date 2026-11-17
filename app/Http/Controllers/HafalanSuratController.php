<?php

namespace App\Http\Controllers;

use App\Enums\Penilaian;
use App\Http\Requests\HafalanSuratRequest;
use App\Models\HafalanSurat;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Surat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HafalanSuratController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = HafalanSurat::visibleTo($user)->with(['siswa.kelas', 'guru', 'surat']);

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
                })->orWhereHas('surat', function ($sq) use ($term) {
                    $sq->where('nama_surat', 'like', "%{$term}%");
                });
            });
        }

        $hafalans = $query->latest('tgl_pengujian')->latest('id')->paginate(15)->withQueryString();

        $kelasList = Kelas::visibleTo($user)->orderBy('nama_kelas')->get();
        $siswas = Siswa::visibleTo($user)->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.surat.index', compact('hafalans', 'kelasList', 'siswas', 'surats', 'penilaianList'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.surat.create', compact('siswas', 'surats', 'penilaianList'));
    }

    public function store(HafalanSuratRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['guru_id'] = auth()->id();

        HafalanSurat::create($data);

        return redirect()->route('hafalan.surat.index')->with('success', 'Catatan hafalan Surat berhasil ditambahkan.');
    }

    public function edit(HafalanSurat $hafalanSurat): View
    {
        $user = auth()->user();
        if (! $user->canAccessSiswa($hafalanSurat->siswa)) {
            abort(403, 'Anda tidak memiliki akses ke data hafalan santri ini.');
        }

        $siswas = Siswa::visibleTo($user)->with('kelas')->orderBy('nama_siswa')->get();
        $surats = Surat::orderBy('nomor')->get();
        $penilaianList = Penilaian::cases();

        return view('hafalan.surat.edit', compact('hafalanSurat', 'siswas', 'surats', 'penilaianList'));
    }

    public function update(HafalanSuratRequest $request, HafalanSurat $hafalanSurat): RedirectResponse
    {
        $hafalanSurat->update($request->validated());

        return redirect()->route('hafalan.surat.index')->with('success', 'Catatan hafalan Surat berhasil diperbarui.');
    }

    public function destroy(HafalanSurat $hafalanSurat): RedirectResponse
    {
        if (! auth()->user()->canAccessSiswa($hafalanSurat->siswa)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data hafalan santri ini.');
        }

        $hafalanSurat->delete();

        return redirect()->route('hafalan.surat.index')->with('success', 'Catatan hafalan Surat berhasil dihapus.');
    }
}
