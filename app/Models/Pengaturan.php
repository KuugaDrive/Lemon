<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturans';

    protected $fillable = [
        'kunci',
        'nilai',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('kunci', $key)->first();
        if ($setting !== null) {
            return $setting->nilai;
        }

        $defaults = [
            'spp_nominal_default' => '150000',
            'spp_jatuh_tempo_hari' => '10',
            'nama_lembaga' => 'PAUD / TPQ Lemon',
            'alamat_lembaga' => 'Jl. Pendidikan Karakter Anak No. 7, Jakarta',
            'no_telp_lembaga' => '0812-3456-7890',
            'nama_kepala' => 'H. Muhammad Yusuf, M.Pd.',
        ];

        return $defaults[$key] ?? $default;
    }

    public static function set(string $key, $value): static
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            ['nilai' => (string) $value]
        );
    }
}
