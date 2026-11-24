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
        'jenis_kelamin',
        'email',
        'alamat',
        'no_hp',
        'password',
    ];

    /**
     * Label jenis kelamin: Laki-laki / Perempuan.
     */
    public function getJenisKelaminLabelAttribute(): string
    {
        return match ($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

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
     * Dapatkan user yang memiliki peran Kepala Sekolah (Kepsek).
     */
    public static function getKepsek(): ?self
    {
        try {
            if (!\Spatie\Permission\Models\Role::where('name', 'kepsek')->exists()) {
                return null;
            }

            return static::role('kepsek')->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Kelas yang diampu oleh guru ini.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'user_id', 'kelas_id')->withTimestamps();
    }

    public function setoranIqros(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetoranIqro::class, 'guru_id');
    }

    public function setoranQurans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetoranQuran::class, 'guru_id');
    }

    public function hafalanDoas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanDoa::class, 'guru_id');
    }

    public function hafalanHadists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanHadist::class, 'guru_id');
    }

    public function hafalanSurats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanSurat::class, 'guru_id');
    }

    public function bacaBukus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BacaBuku::class, 'guru_id');
    }

    public function pembayaranSpps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PembayaranSpp::class, 'dicatat_oleh');
    }

    /**
     * Cek apakah user memiliki akses ke data siswa tertentu.
     */
    public function canAccessSiswa(?Siswa $siswa): bool
    {
        if (! $siswa) {
            return false;
        }

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
    public function canAccessKelas(?Kelas $kelas): bool
    {
        if (! $kelas) {
            return false;
        }

        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->hasRole('guru')) {
            return $this->kelas()->where('kelas.id', $kelas->id)->exists();
        }

        return false;
    }
}
