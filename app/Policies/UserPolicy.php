<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya admin yang dapat melihat daftar guru/user.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat melihat detail guru/user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat menambah guru/user.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat mengubah guru/user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang dapat menghapus guru/user, dan tidak boleh menghapus dirinya sendiri.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $user->id !== $model->id;
    }
}
