<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\Doa;
use App\Models\HafalanDoa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HafalanDoaFactory extends Factory
{
    protected $model = HafalanDoa::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'doa_id' => Doa::factory(),
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
