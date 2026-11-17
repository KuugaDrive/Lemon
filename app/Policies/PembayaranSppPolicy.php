<?php

namespace App\Policies;

use App\Models\PembayaranSpp;
use App\Models\User;

class PembayaranSppPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kelola-spp') || $user->hasRole('admin');
    }

    public function view(User $user, PembayaranSpp $pembayaranSpp): bool
    {
        return $user->hasPermissionTo('kelola-spp') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola-spp') || $user->hasRole('admin');
    }

    public function update(User $user, PembayaranSpp $pembayaranSpp): bool
    {
        return $user->hasPermissionTo('kelola-spp') || $user->hasRole('admin');
    }

    public function delete(User $user, PembayaranSpp $pembayaranSpp): bool
    {
        return $user->hasPermissionTo('kelola-spp') || $user->hasRole('admin');
    }
}
