<?php

namespace Database\Factories;

use App\Models\Doa;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoaFactory extends Factory
{
    protected $model = Doa::class;

    public function definition(): array
    {
        return [
            'nama_doa' => 'Doa ' . fake()->sentence(3),
            'baca_latin' => fake()->sentence(6),
            'artinya' => fake()->sentence(6),
            'ket' => fake()->sentence(),
        ];
    }
}
