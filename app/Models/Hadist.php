<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hadist extends Model
{
    use HasFactory;

    protected $table = 'hadists';

    protected $fillable = [
        'nama_hadist',
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
        return $query->where('nama_hadist', 'like', "%{$term}%")
                     ->orWhere('baca_latin', 'like', "%{$term}%")
                     ->orWhere('artinya', 'like', "%{$term}%")
                     ->orWhere('ket', 'like', "%{$term}%");
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('nama_hadist');
    }

    public function hafalanHadists(): HasMany
    {
        return $this->hasMany(HafalanHadist::class, 'hadist_id');
    }

    public function isDipakai(): bool
    {
        return class_exists(HafalanHadist::class) && $this->hafalanHadists()->exists();
    }
}
