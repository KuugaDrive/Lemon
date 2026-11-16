<?php

namespace App\Policies;

use App\Models\Siswa;
use App\Models\User;

class SiswaPolicy
{
    /**
     * Admin dan guru dapat melihat daftar siswa (guru dibatasi scope kelasnya).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('guru');
    }

    /**
     * Admin dapat melihat semua siswa, guru hanya siswa di kelas yang ia ampu.
     */
    public function view(User $user, Siswa $siswa): bool
    {
        return $user->canAccessSiswa($siswa);
    }

    /**
     * Hanya admin yang dapat menambah siswa.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat mengubah data siswa.
     */
    public function update(User $user, Siswa $siswa): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat menghapus siswa.
     */
    public function delete(User $user, Siswa $siswa): bool
    {
        return $user->hasRole('admin');
    }
}
