<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'keterangan',
    ];

    /**
     * Guru yang mengampu kelas ini.
     */
    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guru_kelas', 'kelas_id', 'user_id')->withTimestamps();
    }

    /**
     * Siswa di dalam kelas ini.
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Scope untuk membatasi kelas berdasarkan role pengguna:
     * - Admin melihat semua kelas
     * - Guru non-admin hanya melihat kelas yang ia pegang
     * - User tanpa kelas tidak melihat kelas apa pun
     */
    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('admin') || $user->hasRole('kepsek')) {
            return $query;
        }

        if ($user->hasRole('guru')) {
            return $query->whereHas('gurus', function (Builder $q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
