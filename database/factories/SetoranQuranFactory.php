<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SetoranQuranFactory extends Factory
{
    protected $model = SetoranQuran::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'surat_id' => Surat::factory(),
            'juz' => 30,
            'ayat_mulai' => 1,
            'ayat_selesai' => 5,
            'tanggal' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
