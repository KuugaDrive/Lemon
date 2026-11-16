<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlfabetRequest;
use App\Http\Requests\UpdateAlfabetRequest;
use App\Models\Alfabet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlfabetController extends Controller
{
    public function index(Request $request): View
    {
        $alfabets = Alfabet::search($request->input('q'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('katalog.alfabet.index', compact('alfabets'));
    }

    public function create(): View
    {
        return view('katalog.alfabet.create');
    }

    public function store(StoreAlfabetRequest $request): RedirectResponse
    {
        $alfabet = Alfabet::create($request->validated());

        return redirect()->route('katalog.alfabet.index')
            ->with('success', "Huruf {$alfabet->nama_alfabet} berhasil ditambahkan ke katalog.");
    }

    public function edit(Alfabet $alfabet): View
    {
        return view('katalog.alfabet.edit', compact('alfabet'));
    }

    public function update(UpdateAlfabetRequest $request, Alfabet $alfabet): RedirectResponse
    {
        $alfabet->update($request->validated());

        return redirect()->route('katalog.alfabet.index')
            ->with('success', "Data huruf {$alfabet->nama_alfabet} berhasil diperbarui.");
    }

    public function destroy(Alfabet $alfabet): RedirectResponse
    {
        if ($alfabet->isDipakai()) {
            return redirect()->route('katalog.alfabet.index')
                ->with('error', "Huruf {$alfabet->nama_alfabet} tidak dapat dihapus karena sudah memiliki data baca buku santri.");
        }

        $nama = $alfabet->nama_alfabet;
        $alfabet->delete();

        return redirect()->route('katalog.alfabet.index')
            ->with('success', "Huruf {$nama} berhasil dihapus dari katalog.");
    }
}
