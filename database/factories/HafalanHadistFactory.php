<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\Hadist;
use App\Models\HafalanHadist;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HafalanHadistFactory extends Factory
{
    protected $model = HafalanHadist::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'hadist_id' => Hadist::factory(),
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
