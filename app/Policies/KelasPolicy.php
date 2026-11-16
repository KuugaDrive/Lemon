<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;

class KelasPolicy
{
    /**
     * Hanya admin yang dapat mengakses master kelas.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Kelas $kelas): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('guru') && $user->canAccessKelas($kelas));
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Kelas $kelas): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Kelas $kelas): bool
    {
        return $user->hasRole('admin');
    }
}
