<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\Iqro;
use App\Models\SetoranIqro;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SetoranIqroFactory extends Factory
{
    protected $model = SetoranIqro::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'iqro_id' => Iqro::factory(),
            'halaman' => fake()->numberBetween(1, 30),
            'tanggal' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
