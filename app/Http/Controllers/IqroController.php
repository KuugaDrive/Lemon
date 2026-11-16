<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIqroRequest;
use App\Http\Requests\UpdateIqroRequest;
use App\Models\Iqro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IqroController extends Controller
{
    public function index(Request $request): View
    {
        $iqros = Iqro::search($request->input('q'))
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('katalog.iqro.index', compact('iqros'));
    }

    public function create(): View
    {
        return view('katalog.iqro.create');
    }

    public function store(StoreIqroRequest $request): RedirectResponse
    {
        $iqro = Iqro::create($request->validated());

        return redirect()->route('katalog.iqro.index')
            ->with('success', "Materi {$iqro->nama_iqro} berhasil ditambahkan ke katalog.");
    }

    public function edit(Iqro $iqro): View
    {
        return view('katalog.iqro.edit', compact('iqro'));
    }

    public function update(UpdateIqroRequest $request, Iqro $iqro): RedirectResponse
    {
        $iqro->update($request->validated());

        return redirect()->route('katalog.iqro.index')
            ->with('success', "Data {$iqro->nama_iqro} berhasil diperbarui.");
    }

    public function destroy(Iqro $iqro): RedirectResponse
    {
        if ($iqro->isDipakai()) {
            return redirect()->route('katalog.iqro.index')
                ->with('error', "Materi {$iqro->nama_iqro} tidak dapat dihapus karena sudah memiliki data setoran santri.");
        }

        $nama = $iqro->nama_iqro;
        $iqro->delete();

        return redirect()->route('katalog.iqro.index')
            ->with('success', "Materi {$nama} berhasil dihapus dari katalog.");
    }
}
