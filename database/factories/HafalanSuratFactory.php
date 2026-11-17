<?php

namespace Database\Factories;

use App\Enums\Penilaian;
use App\Models\HafalanSurat;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HafalanSuratFactory extends Factory
{
    protected $model = HafalanSurat::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'guru_id' => User::factory(),
            'surat_id' => Surat::factory(),
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => fake()->randomElement(Penilaian::cases()),
            'ket' => fake()->sentence(),
        ];
    }
}
