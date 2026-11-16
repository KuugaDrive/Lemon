<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'nis',
        'nama_siswa',
        'alamat',
        'nama_orang_tua',
        'no_hp_ortu',
        'kelas_id',
    ];

    /**
     * Kelas tempat siswa belajar.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Scope untuk membatasi siswa berdasarkan role pengguna:
     * - Admin melihat semua siswa
     * - Guru hanya melihat siswa di kelas yang diampunya
     */
    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('guru')) {
            return $query->whereHas('kelas.gurus', function (Builder $q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
