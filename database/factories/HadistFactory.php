<?php

namespace Database\Factories;

use App\Models\Hadist;
use Illuminate\Database\Eloquent\Factories\Factory;

class HadistFactory extends Factory
{
    protected $model = Hadist::class;

    public function definition(): array
    {
        return [
            'nama_hadist' => 'Hadits ' . fake()->sentence(3),
            'baca_latin' => fake()->sentence(6),
            'artinya' => fake()->sentence(6),
            'ket' => fake()->sentence(),
        ];
    }
}
