<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';

    protected $fillable = [
        'nomor',
        'nama_surat',
        'juz',
        'jumlah_ayat',
        'ket',
    ];

    protected $casts = [
        'nomor' => 'integer',
        'juz' => 'integer',
        'jumlah_ayat' => 'integer',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where('nama_surat', 'like', "%{$term}%")
                     ->orWhere('nomor', $term)
                     ->orWhere('juz', $term)
                     ->orWhere('ket', 'like', "%{$term}%");
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('nomor');
    }

    public function setoranQurans(): HasMany
    {
        return $this->hasMany(SetoranQuran::class, 'surat_id');
    }

    public function hafalanSurats(): HasMany
    {
        return $this->hasMany(HafalanSurat::class, 'surat_id');
    }

    /**
     * Cek apakah surat sudah dipakai di setoran Quran atau hafalan surat.
     */
    public function isDipakai(): bool
    {
        $usedInSetoran = class_exists(SetoranQuran::class) && $this->setoranQurans()->exists();
        $usedInHafalan = class_exists(HafalanSurat::class) && $this->hafalanSurats()->exists();

        return $usedInSetoran || $usedInHafalan;
    }
}
