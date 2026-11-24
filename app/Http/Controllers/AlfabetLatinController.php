<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlfabetLatinRequest;
use App\Http\Requests\UpdateAlfabetLatinRequest;
use App\Models\AlfabetLatin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlfabetLatinController extends Controller
{
    public function index(Request $request): View
    {
        $alfabetLatins = AlfabetLatin::search($request->input('q'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('katalog.alfabet-latin.index', compact('alfabetLatins'));
    }

    public function create(): View
    {
        return view('katalog.alfabet-latin.create');
    }

    public function store(StoreAlfabetLatinRequest $request): RedirectResponse
    {
        $alfabetLatin = AlfabetLatin::create($request->validated());

        return redirect()->route('katalog.alfabet-latin.index')
            ->with('success', "Huruf {$alfabetLatin->huruf} berhasil ditambahkan ke kumpulan alfabet.");
    }

    public function edit(AlfabetLatin $alfabetLatin): View
    {
        if (! $alfabetLatin->exists) {
            $id = request()->route('alfabet_latin') ?? request()->route('alfabet-latin');
            $alfabetLatin = AlfabetLatin::findOrFail($id);
        }

        return view('katalog.alfabet-latin.edit', compact('alfabetLatin'));
    }

    public function update(UpdateAlfabetLatinRequest $request, AlfabetLatin $alfabetLatin): RedirectResponse
    {
        if (! $alfabetLatin->exists) {
            $id = request()->route('alfabet_latin') ?? request()->route('alfabet-latin');
            $alfabetLatin = AlfabetLatin::findOrFail($id);
        }

        $alfabetLatin->update($request->validated());

        return redirect()->route('katalog.alfabet-latin.index')
            ->with('success', "Data huruf {$alfabetLatin->huruf} berhasil diperbarui.");
    }

    public function destroy(AlfabetLatin $alfabetLatin): RedirectResponse
    {
        if (! $alfabetLatin->exists) {
            $id = request()->route('alfabet_latin') ?? request()->route('alfabet-latin');
            $alfabetLatin = AlfabetLatin::findOrFail($id);
        }

        if ($alfabetLatin->isDipakai()) {
            return redirect()->route('katalog.alfabet-latin.index')
                ->with('error', "Huruf {$alfabetLatin->huruf} tidak dapat dihapus karena sudah memiliki riwayat baca buku santri.");
        }

        $huruf = $alfabetLatin->huruf;
        $alfabetLatin->delete();

        return redirect()->route('katalog.alfabet-latin.index')
            ->with('success', "Huruf {$huruf} berhasil dihapus dari kumpulan alfabet.");
    }
}
