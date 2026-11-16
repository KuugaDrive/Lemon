<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHadistRequest;
use App\Http\Requests\UpdateHadistRequest;
use App\Models\Hadist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HadistController extends Controller
{
    public function index(Request $request): View
    {
        $hadists = Hadist::search($request->input('q'))
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('katalog.hadist.index', compact('hadists'));
    }

    public function create(): View
    {
        return view('katalog.hadist.create');
    }

    public function store(StoreHadistRequest $request): RedirectResponse
    {
        $hadist = Hadist::create($request->validated());

        return redirect()->route('katalog.hadist.index')
            ->with('success', "Hadits \"{$hadist->nama_hadist}\" berhasil ditambahkan ke katalog.");
    }

    public function edit(Hadist $hadist): View
    {
        return view('katalog.hadist.edit', compact('hadist'));
    }

    public function update(UpdateHadistRequest $request, Hadist $hadist): RedirectResponse
    {
        $hadist->update($request->validated());

        return redirect()->route('katalog.hadist.index')
            ->with('success', "Data hadits \"{$hadist->nama_hadist}\" berhasil diperbarui.");
    }

    public function destroy(Hadist $hadist): RedirectResponse
    {
        if ($hadist->isDipakai()) {
            return redirect()->route('katalog.hadist.index')
                ->with('error', "Hadits \"{$hadist->nama_hadist}\" tidak dapat dihapus karena sudah memiliki riwayat hafalan santri.");
        }

        $nama = $hadist->nama_hadist;
        $hadist->delete();

        return redirect()->route('katalog.hadist.index')
            ->with('success', "Hadits \"{$nama}\" berhasil dihapus dari katalog.");
    }
}
