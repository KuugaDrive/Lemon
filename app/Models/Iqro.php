<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Iqro extends Model
{
    use HasFactory;

    protected $table = 'iqros';

    protected $fillable = [
        'nama_iqro',
        'jumlah_halaman',
        'ket',
    ];

    protected $casts = [
        'jumlah_halaman' => 'integer',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where('nama_iqro', 'like', "%{$term}%")
                     ->orWhere('ket', 'like', "%{$term}%");
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('nama_iqro');
    }

    public function setoranIqros(): HasMany
    {
        return $this->hasMany(SetoranIqro::class, 'iqro_id');
    }

    /**
     * Cek apakah materi ini sudah pernah dipakai di transaksi setoran.
     */
    public function isDipakai(): bool
    {
        return class_exists(SetoranIqro::class) && $this->setoranIqros()->exists();
    }
}
