<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\BacaBuku;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BacaBukuFactory extends Factory
{
    protected $model = BacaBuku::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'alfabet_id' => Alfabet::factory(),
            'jilid' => 'Jilid 1',
            'halaman' => fake()->numberBetween(1, 20),
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
