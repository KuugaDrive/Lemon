<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuratRequest;
use App\Http\Requests\UpdateSuratRequest;
use App\Models\Surat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratController extends Controller
{
    public function index(Request $request): View
    {
        $surats = Surat::search($request->input('q'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('katalog.surat.index', compact('surats'));
    }

    public function create(): View
    {
        return view('katalog.surat.create');
    }

    public function store(StoreSuratRequest $request): RedirectResponse
    {
        $surat = Surat::create($request->validated());

        return redirect()->route('katalog.surat.index')
            ->with('success', "Surat {$surat->nama_surat} (No. {$surat->nomor}) berhasil ditambahkan ke katalog.");
    }

    public function edit(Surat $surat): View
    {
        return view('katalog.surat.edit', compact('surat'));
    }

    public function update(UpdateSuratRequest $request, Surat $surat): RedirectResponse
    {
        $surat->update($request->validated());

        return redirect()->route('katalog.surat.index')
            ->with('success', "Data surat {$surat->nama_surat} berhasil diperbarui.");
    }

    public function destroy(Surat $surat): RedirectResponse
    {
        if ($surat->isDipakai()) {
            return redirect()->route('katalog.surat.index')
                ->with('error', "Surat {$surat->nama_surat} tidak dapat dihapus karena sudah memiliki riwayat setoran atau hafalan santri.");
        }

        $nama = $surat->nama_surat;
        $surat->delete();

        return redirect()->route('katalog.surat.index')
            ->with('success', "Surat {$nama} berhasil dihapus dari katalog.");
    }
}
