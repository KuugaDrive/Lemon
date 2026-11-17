<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $allKelas = Kelas::all();
        if ($allKelas->isEmpty()) {
            return;
        }

        $kelas1 = $allKelas->get(0);
        $kelas2 = $allKelas->get(1) ?? $kelas1;
        $kelas3 = $allKelas->get(2) ?? $kelas1;

        $daftarSiswa = [
            [
                'nis' => '202601001',
                'nama_siswa' => 'Muhammad Al-Fatih',
                'alamat' => 'Jl. Tebet Barat Dalam No. 10, Jakarta Selatan',
                'nama_ayah_kandung' => 'Bambang Hartono',
                'nama_ibu_kandung' => 'Siti Aminah',
                'nama_orang_tua' => 'Bambang Hartono',
                'no_hp_ortu' => '081211110001',
                'kelas_id' => $kelas1->id,
            ],
            [
                'nis' => '202601002',
                'nama_siswa' => 'Aisyah Humaira',
                'alamat' => 'Jl. Pancoran Barat No. 5, Jakarta Selatan',
                'nama_ayah_kandung' => 'Rahmat Hidayat',
                'nama_ibu_kandung' => 'Nurul Aini',
                'nama_orang_tua' => 'Rahmat Hidayat',
                'no_hp_ortu' => '081211110002',
                'kelas_id' => $kelas1->id,
            ],
            [
                'nis' => '202601003',
                'nama_siswa' => 'Umar Abdullah',
                'alamat' => 'Jl. Kalibata Tengah No. 12, Jakarta Selatan',
                'nama_ayah_kandung' => 'Dedi Iskandar',
                'nama_ibu_kandung' => 'Fatimah Zahra',
                'nama_orang_tua' => 'Dedi Iskandar',
                'no_hp_ortu' => '081211110003',
                'kelas_id' => $kelas1->id,
            ],
            [
                'nis' => '202601004',
                'nama_siswa' => 'Maryam Salsabila',
                'alamat' => 'Jl. Duren Tiga No. 4, Jakarta Selatan',
                'nama_ayah_kandung' => 'Hendra Gunawan',
                'nama_ibu_kandung' => 'Dewi Lestari',
                'nama_orang_tua' => 'Hendra Gunawan',
                'no_hp_ortu' => '081211110004',
                'kelas_id' => $kelas1->id,
            ],
            [
                'nis' => '202601005',
                'nama_siswa' => 'Ibrahim Khalil',
                'alamat' => 'Jl. Pengadegan Timur No. 18, Jakarta Selatan',
                'nama_ayah_kandung' => 'Agus Priyono',
                'nama_ibu_kandung' => 'Sri Wahyuni',
                'nama_orang_tua' => 'Agus Priyono',
                'no_hp_ortu' => '081211110005',
                'kelas_id' => $kelas1->id,
            ],
            [
                'nis' => '202602001',
                'nama_siswa' => 'Hasan Al-Bashri',
                'alamat' => 'Jl. Pejaten Barat No. 22, Jakarta Selatan',
                'nama_ayah_kandung' => 'Eko Prasetyo',
                'nama_ibu_kandung' => 'Indah Permata',
                'nama_orang_tua' => 'Eko Prasetyo',
                'no_hp_ortu' => '081222220001',
                'kelas_id' => $kelas2->id,
            ],
            [
                'nis' => '202602002',
                'nama_siswa' => 'Fathimah Az-Zahra',
                'alamat' => 'Jl. Warung Jati Barat No. 8, Jakarta Selatan',
                'nama_ayah_kandung' => 'Suryanto Wibowo',
                'nama_ibu_kandung' => 'Rina Marlina',
                'nama_orang_tua' => 'Suryanto Wibowo',
                'no_hp_ortu' => '081222220002',
                'kelas_id' => $kelas2->id,
            ],
            [
                'nis' => '202602003',
                'nama_siswa' => 'Ali Zainal Abidin',
                'alamat' => 'Jl. Bangka Raya No. 15, Jakarta Selatan',
                'nama_ayah_kandung' => 'M. Ridwan',
                'nama_ibu_kandung' => 'Khadijah',
                'nama_orang_tua' => 'M. Ridwan',
                'no_hp_ortu' => '081222220003',
                'kelas_id' => $kelas2->id,
            ],
            [
                'nis' => '202602004',
                'nama_siswa' => 'Khadijah Al-Kubro',
                'alamat' => 'Jl. Kemang Timur No. 3, Jakarta Selatan',
                'nama_ayah_kandung' => 'Faisal Tanjung',
                'nama_ibu_kandung' => 'Halimah',
                'nama_orang_tua' => 'Faisal Tanjung',
                'no_hp_ortu' => '081222220004',
                'kelas_id' => $kelas2->id,
            ],
            [
                'nis' => '202602005',
                'nama_siswa' => 'Hamzah Asadullah',
                'alamat' => 'Jl. Ampera Raya No. 7, Jakarta Selatan',
                'nama_ayah_kandung' => 'Lukman Hakim',
                'nama_ibu_kandung' => 'Aisyah',
                'nama_orang_tua' => 'Lukman Hakim',
                'no_hp_ortu' => '081222220005',
                'kelas_id' => $kelas2->id,
            ],
            [
                'nis' => '202603001',
                'nama_siswa' => 'Bilal bin Rabah',
                'alamat' => 'Jl. Pasar Minggu Raya No. 30, Jakarta Selatan',
                'nama_ayah_kandung' => 'Ahmad Suhendar',
                'nama_ibu_kandung' => 'Maryam',
                'nama_orang_tua' => 'Ahmad Suhendar',
                'no_hp_ortu' => '081233330001',
                'kelas_id' => $kelas3->id,
            ],
            [
                'nis' => '202603002',
                'nama_siswa' => 'Zaid bin Tsabit',
                'alamat' => 'Jl. Ragunan No. 14, Jakarta Selatan',
                'nama_ayah_kandung' => 'Wahyudi Santoso',
                'nama_ibu_kandung' => 'Salma',
                'nama_orang_tua' => 'Wahyudi Santoso',
                'no_hp_ortu' => '081233330002',
                'kelas_id' => $kelas3->id,
            ],
            [
                'nis' => '202603003',
                'nama_siswa' => 'Ruqayyah binti Muhammad',
                'alamat' => 'Jl. Cilandak Timur No. 11, Jakarta Selatan',
                'nama_ayah_kandung' => 'Taufik Hidayat',
                'nama_ibu_kandung' => 'Zubaidah',
                'nama_orang_tua' => 'Taufik Hidayat',
                'no_hp_ortu' => '081233330003',
                'kelas_id' => $kelas3->id,
            ],
            [
                'nis' => '202603004',
                'nama_siswa' => 'Salman Al-Farisi',
                'alamat' => 'Jl. Kebagusan Raya No. 9, Jakarta Selatan',
                'nama_ayah_kandung' => 'Budi Santoso',
                'nama_ibu_kandung' => 'Rukmini',
                'nama_orang_tua' => 'Budi Santoso',
                'no_hp_ortu' => '081233330004',
                'kelas_id' => $kelas3->id,
            ],
            [
                'nis' => '202603005',
                'nama_siswa' => 'Zainab binti Ali',
                'alamat' => 'Jl. Lenteng Agung No. 21, Jakarta Selatan',
                'nama_ayah_kandung' => 'Arif Rahman',
                'nama_ibu_kandung' => 'Safira',
                'nama_orang_tua' => 'Arif Rahman',
                'no_hp_ortu' => '081233330005',
                'kelas_id' => $kelas3->id,
            ],
        ];

        foreach ($daftarSiswa as $siswa) {
            Siswa::updateOrCreate(
                ['nis' => $siswa['nis']],
                $siswa
            );
        }
    }
}
