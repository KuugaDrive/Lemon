<?php

namespace App\Models;

use App\Enums\StatusPresensi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'tanggal',
        'status',
        'ket',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status' => StatusPresensi::class,
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
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

        return $query->whereHas('siswa', function (Builder $q) use ($user) {
            $q->visibleTo($user);
        });
    }

    public function scopeTanggal(Builder $query, $date): Builder
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeRentang(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('tanggal', [$from, $to]);
    }
}
