<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alfabet extends Model
{
    use HasFactory;

    protected $table = 'alfabets';

    protected $fillable = [
        'nama_alfabet',
        'ket',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where('nama_alfabet', 'like', "%{$term}%")
                     ->orWhere('ket', 'like', "%{$term}%");
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }

    public function bacaBukus(): HasMany
    {
        return $this->hasMany(BacaBuku::class, 'alfabet_id');
    }

    public function isDipakai(): bool
    {
        return class_exists(BacaBuku::class) && $this->bacaBukus()->exists();
    }
}
