<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranSpp extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_spps';

    protected $fillable = [
        'siswa_id',
        'bulan',
        'tahun',
        'nominal',
        'tgl_bayar',
        'ket',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tgl_bayar' => 'date',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'nominal' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /**
     * Label periode dalam Bahasa Indonesia, misal "November 2026".
     */
    protected function periodeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $namaBulan = Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F');
                return "{$namaBulan} {$this->tahun}";
            }
        );
    }

    /**
     * Format nominal dalam Rupiah, misal "Rp 150.000".
     */
    protected function nominalRupiah(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->nominal, 0, ',', '.')
        );
    }

    public function scopePeriode(Builder $query, int $bulan, int $tahun): Builder
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    public function scopeKelas(Builder $query, int $kelasId): Builder
    {
        return $query->whereHas('siswa', function ($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    }

    public function scopeRentang(Builder $query, int $dariTahun, int $dariBulan, int $sampaiTahun, int $sampaiBulan): Builder
    {
        $startKey = $dariTahun * 100 + $dariBulan;
        $endKey = $sampaiTahun * 100 + $sampaiBulan;

        return $query->whereRaw('(tahun * 100 + bulan) >= ?', [$startKey])
                     ->whereRaw('(tahun * 100 + bulan) <= ?', [$endKey]);
    }
}
