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
        'jenis_kelamin',
        'alamat',
        'nama_ayah_kandung',
        'nama_ibu_kandung',
        'nama_orang_tua',
        'nama_wali',
        'no_hp_ortu',
        'kelas_id',
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

    protected static function booted(): void
    {
        static::saving(function (Siswa $siswa) {
            if (empty($siswa->nama_orang_tua)) {
                $parts = array_filter([$siswa->nama_ayah_kandung, $siswa->nama_ibu_kandung]);
                $siswa->nama_orang_tua = implode(' / ', $parts);
            }
            if (empty($siswa->nama_ayah_kandung) && !empty($siswa->nama_orang_tua)) {
                $siswa->nama_ayah_kandung = $siswa->nama_orang_tua;
            }
        });
    }

    /**
     * Nama wali yang tampil: jika nama_wali terisi maka tampilkan, jika kosong tampilkan nama_orang_tua.
     */
    public function getWaliTampilAttribute(): string
    {
        if (! empty($this->nama_wali)) {
            return $this->nama_wali;
        }

        if (! empty($this->nama_orang_tua)) {
            return $this->nama_orang_tua;
        }

        return '-';
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function presensis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }

    public function setoranIqros(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetoranIqro::class, 'siswa_id');
    }

    public function setoranQurans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetoranQuran::class, 'siswa_id');
    }

    public function hafalanDoas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanDoa::class, 'siswa_id');
    }

    public function hafalanHadists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanHadist::class, 'siswa_id');
    }

    public function hafalanSurats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HafalanSurat::class, 'siswa_id');
    }

    public function bacaBukus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BacaBuku::class, 'siswa_id');
    }

    public function pembayaranSpps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PembayaranSpp::class, 'siswa_id');
    }

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
            return $query->whereHas('kelas.gurus', function (Builder $q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
