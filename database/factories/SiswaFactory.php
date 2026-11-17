<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        $ayah = fake()->name('male');
        $ibu = fake()->name('female');

        return [
            'nis' => fake()->unique()->numerify('2026####'),
            'nama_siswa' => fake()->name(),
            'alamat' => fake()->address(),
            'nama_ayah_kandung' => $ayah,
            'nama_ibu_kandung' => $ibu,
            'nama_orang_tua' => $ayah,
            'no_hp_ortu' => fake()->phoneNumber(),
            'kelas_id' => Kelas::factory(),
        ];
    }
}
