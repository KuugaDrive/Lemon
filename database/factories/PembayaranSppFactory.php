<?php

namespace Database\Factories;

use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembayaranSppFactory extends Factory
{
    protected $model = PembayaranSpp::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'bulan' => fake()->numberBetween(1, 12),
            'tahun' => (int) now()->year,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'ket' => fake()->optional()->sentence(),
            'dicatat_oleh' => User::factory(),
        ];
    }
}
