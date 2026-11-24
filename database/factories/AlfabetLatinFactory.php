<?php

namespace Database\Factories;

use App\Models\AlfabetLatin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlfabetLatinFactory extends Factory
{
    protected $model = AlfabetLatin::class;

    public function definition(): array
    {
        return [
            'huruf' => fake()->unique()->randomLetter(),
            'ket' => fake()->sentence(),
        ];
    }
}
