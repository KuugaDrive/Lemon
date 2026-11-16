<?php

namespace Database\Seeders;

use App\Models\Iqro;
use Illuminate\Database\Seeder;

class IqroSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            Iqro::updateOrCreate(
                ['nama_iqro' => "Iqro {$i}"],
                [
                    'jumlah_halaman' => 31, // Asumsi standar awal, dapat diubah oleh admin
                    'ket' => "Buku bimbingan membaca Al-Qur'an jilid {$i}",
                ]
            );
        }
    }
}
