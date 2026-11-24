<?php

namespace Database\Factories;

use App\Models\Surat;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuratFactory extends Factory
{
    protected $model = Surat::class;

    public function definition(): array
    {
        return [
            'nomor' => fake()->unique()->numberBetween(1, 114),
            'nama_surat' => 'Surat ' . fake()->word(),
            'nama_arab' => 'الفاتحة',
            'juz' => fake()->numberBetween(1, 30),
            'jumlah_ayat' => fake()->numberBetween(3, 286),
            'ket' => fake()->sentence(),
        ];
    }
}
