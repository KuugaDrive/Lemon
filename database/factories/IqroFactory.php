<?php

namespace Database\Factories;

use App\Models\Iqro;
use Illuminate\Database\Eloquent\Factories\Factory;

class IqroFactory extends Factory
{
    protected $model = Iqro::class;

    public function definition(): array
    {
        return [
            'nama_iqro' => 'Iqro ' . fake()->unique()->numberBetween(1, 100),
            'jumlah_halaman' => 31,
            'ket' => fake()->sentence(),
        ];
    }
}
