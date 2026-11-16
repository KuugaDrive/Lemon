<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('2026####'),
            'nama_siswa' => fake()->name(),
            'alamat' => fake()->address(),
            'nama_orang_tua' => fake()->name(),
            'no_hp_ortu' => fake()->phoneNumber(),
            'kelas_id' => Kelas::factory(),
        ];
    }
}
