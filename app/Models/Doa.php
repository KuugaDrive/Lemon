<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doa extends Model
{
    use HasFactory;

    protected $table = 'doas';

    protected $fillable = [
        'nama_doa',
        'baca_latin',
        'artinya',
        'ket',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where('nama_doa', 'like', "%{$term}%")
                     ->orWhere('baca_latin', 'like', "%{$term}%")
                     ->orWhere('artinya', 'like', "%{$term}%")
                     ->orWhere('ket', 'like', "%{$term}%");
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('nama_doa');
    }

    public function hafalanDoas(): HasMany
    {
        return $this->hasMany(HafalanDoa::class, 'doa_id');
    }

    public function isDipakai(): bool
    {
        return class_exists(HafalanDoa::class) && $this->hafalanDoas()->exists();
    }
}
