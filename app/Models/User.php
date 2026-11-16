<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'name',
        'email',
        'alamat',
        'no_hp',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kelas yang diampu oleh guru ini.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'user_id', 'kelas_id')->withTimestamps();
    }

    /**
     * Cek apakah user memiliki akses ke data siswa tertentu.
     */
    public function canAccessSiswa(Siswa $siswa): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->hasRole('guru')) {
            return $this->kelas()->where('kelas.id', $siswa->kelas_id)->exists();
        }

        return false;
    }

    /**
     * Cek apakah user memiliki akses ke data kelas tertentu.
     */
    public function canAccessKelas(Kelas $kelas): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->hasRole('guru')) {
            return $this->kelas()->where('kelas.id', $kelas->id)->exists();
        }

        return false;
    }
}
