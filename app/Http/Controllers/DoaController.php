<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoaRequest;
use App\Http\Requests\UpdateDoaRequest;
use App\Models\Doa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoaController extends Controller
{
    public function index(Request $request): View
    {
        $doas = Doa::search($request->input('q'))
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('katalog.doa.index', compact('doas'));
    }

    public function create(): View
    {
        return view('katalog.doa.create');
    }

    public function store(StoreDoaRequest $request): RedirectResponse
    {
        $doa = Doa::create($request->validated());

        return redirect()->route('katalog.doa.index')
            ->with('success', "Doa \"{$doa->nama_doa}\" berhasil ditambahkan ke katalog.");
    }

    public function edit(Doa $doa): View
    {
        return view('katalog.doa.edit', compact('doa'));
    }

    public function update(UpdateDoaRequest $request, Doa $doa): RedirectResponse
    {
        $doa->update($request->validated());

        return redirect()->route('katalog.doa.index')
            ->with('success', "Data doa \"{$doa->nama_doa}\" berhasil diperbarui.");
    }

    public function destroy(Doa $doa): RedirectResponse
    {
        if ($doa->isDipakai()) {
            return redirect()->route('katalog.doa.index')
                ->with('error', "Doa \"{$doa->nama_doa}\" tidak dapat dihapus karena sudah memiliki riwayat hafalan santri.");
        }

        $nama = $doa->nama_doa;
        $doa->delete();

        return redirect()->route('katalog.doa.index')
            ->with('success', "Doa \"{$nama}\" berhasil dihapus dari katalog.");
    }
}
