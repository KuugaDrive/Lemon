<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasList = [
            [
                'nama_kelas' => 'Kelas Usman bin Affan (PAUD A)',
                'keterangan' => 'Kelas anak usia 3-4 tahun',
            ],
            [
                'nama_kelas' => 'Kelas Ali bin Abi Thalib (PAUD B)',
                'keterangan' => 'Kelas anak usia 4-5 tahun',
            ],
            [
                'nama_kelas' => 'Kelas Abu Bakar Ash-Shiddiq (TPQ 1)',
                'keterangan' => 'Kelas pembinaan Al-Qur\'an dan Iqro pemula',
            ],
        ];

        foreach ($kelasList as $kelas) {
            Kelas::firstOrCreate(
                ['nama_kelas' => $kelas['nama_kelas']],
                $kelas
            );
        }
    }
}
