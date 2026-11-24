<?php

namespace App\Models;

use App\Enums\Penilaian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HafalanSurat extends Model
{
    use HasFactory;

    protected $table = 'hafalan_surats';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'surat_id',
        'ayat_mulai',
        'ayat_selesai',
        'tgl_hafalan',
        'tgl_pengujian',
        'nilai',
        'ket',
    ];

    protected $casts = [
        'ayat_mulai' => 'integer',
        'ayat_selesai' => 'integer',
        'tgl_hafalan' => 'date',
        'tgl_pengujian' => 'date',
        'nilai' => Penilaian::class,
    ];

    public function getRentangAyatAttribute(): string
    {
        if ($this->ayat_mulai && $this->ayat_selesai) {
            return $this->ayat_mulai == $this->ayat_selesai
                ? "Ayat {$this->ayat_mulai}"
                : "Ayat {$this->ayat_mulai}-{$this->ayat_selesai}";
        }

        return 'Seluruh Surat';
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->whereIn('siswa_id', Siswa::visibleTo($user)->select('siswas.id'));
    }
}
