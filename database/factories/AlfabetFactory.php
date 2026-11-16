<?php

namespace Database\Factories;

use App\Models\Alfabet;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlfabetFactory extends Factory
{
    protected $model = Alfabet::class;

    public function definition(): array
    {
        return [
            'nama_alfabet' => fake()->unique()->word(),
            'ket' => fake()->sentence(),
        ];
    }
}
