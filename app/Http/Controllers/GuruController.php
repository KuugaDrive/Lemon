<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class GuruController extends Controller
{
    /**
     * Tampilkan daftar guru & pengguna.
     */
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'kelas'])->orderBy('name');

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nip', 'like', "%{$keyword}%")
                  ->orWhere('no_hp', 'like', "%{$keyword}%");
            });
        }

        $gurus = $query->paginate(10)->withQueryString();

        return view('master.guru.index', compact('gurus'));
    }

    /**
     * Tampilkan form tambah guru baru.
     */
    public function create(): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $roles = Role::orderBy('name')->get();

        return view('master.guru.create', compact('kelas', 'roles'));
    }

    /**
     * Simpan data guru baru.
     */
    public function store(StoreGuruRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        if (!empty($validated['kelas_ids'])) {
            $user->kelas()->sync($validated['kelas_ids']);
        }

        return redirect()->route('master.guru.index')
            ->with('success', "Guru / Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Tampilkan form edit guru.
     */
    public function edit(User $guru): View
    {
        $guru->load(['roles', 'kelas']);
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $roles = Role::orderBy('name')->get();

        return view('master.guru.edit', compact('guru', 'kelas', 'roles'));
    }

    /**
     * Perbarui data guru.
     */
    public function update(UpdateGuruRequest $request, User $guru): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $guru->update($data);
        $guru->syncRoles($validated['roles'] ?? []);
        $guru->kelas()->sync($validated['kelas_ids'] ?? []);

        return redirect()->route('master.guru.index')
            ->with('success', "Data {$guru->name} berhasil diperbarui.");
    }

    /**
     * Hapus data guru.
     */
    public function destroy(User $guru): RedirectResponse
    {
        // Pencegahan: admin tidak boleh menghapus dirinya sendiri
        if ($guru->id === auth()->id()) {
            return redirect()->route('master.guru.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Pencegahan: sistem tidak boleh sampai tanpa admin sama sekali
        if ($guru->hasRole('admin')) {
            $totalAdmin = User::role('admin')->count();
            if ($totalAdmin <= 1) {
                return redirect()->route('master.guru.index')
                    ->with('error', 'Tidak dapat menghapus admin terakhir pada sistem.');
            }
        }

        try {
            $nama = $guru->name;
            $guru->kelas()->detach();
            $guru->delete();

            return redirect()->route('master.guru.index')
                ->with('success', "Data {$nama} berhasil dihapus.");
        } catch (QueryException $e) {
            return redirect()->route('master.guru.index')
                ->with('error', 'Data pengguna tidak dapat dihapus karena masih terhubung dengan data lain.');
        }
    }
}
