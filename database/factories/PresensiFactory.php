<?php

namespace Database\Factories;

use App\Enums\StatusPresensi;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'tanggal' => now()->toDateString(),
            'status' => fake()->randomElement(StatusPresensi::cases()),
            'ket' => fake()->optional()->sentence(),
        ];
    }
}
