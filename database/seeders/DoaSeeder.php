<?php

namespace Database\Seeders;

use App\Models\Doa;
use Illuminate\Database\Seeder;

class DoaSeeder extends Seeder
{
    public function run(): void
    {
        $doas = [
            [
                'nama_doa' => 'Doa Sebelum Makan',
                'baca_latin' => 'Allahumma baarik lanaa fiimaa razaqtanaa wa qinaa \'adzaaban naar.',
                'artinya' => 'Ya Allah, berkahilah rezeki yang telah Engkau berikan kepada kami dan peliharalah kami dari siksa api neraka.',
                'ket' => 'Dibaca sebelum menyantap makanan atau minuman',
            ],
            [
                'nama_doa' => 'Doa Sesudah Makan',
                'baca_latin' => 'Alhamdu lillaahil ladzii ath\'amanaa wa saqaanaa wa ja\'alanaa muslimiin.',
                'artinya' => 'Segala puji bagi Allah yang telah memberi kami makan dan minum serta menjadikan kami orang-orang muslim.',
                'ket' => 'Dibaca setelah selesai makan dan minum',
            ],
            [
                'nama_doa' => 'Doa Sebelum Tidur',
                'baca_latin' => 'Bismika allaahumma ahyaa wa bismika amuut.',
                'artinya' => 'Dengan nama-Mu ya Allah, aku hidup dan dengan nama-Mu aku mati.',
                'ket' => 'Dibaca saat hendak tidur',
            ],
            [
                'nama_doa' => 'Doa Bangun Tidur',
                'baca_latin' => 'Alhamdu lillaahil ladzii ahyaanaa ba\'da maa amaatanaa wa ilaihin nusyuur.',
                'artinya' => 'Segala puji bagi Allah yang menghidupkan kami kembali setelah mematikan kami dan kepada-Nya kami dibangkitkan.',
                'ket' => 'Dibaca sesaat setelah terbangun dari tidur',
            ],
            [
                'nama_doa' => 'Doa Masuk Kamar Mandi',
                'baca_latin' => 'Allahumma innii a\'uudzu bika minal khubutsi wal khabaa-its.',
                'artinya' => 'Ya Allah, sesungguhnya aku berlindung kepada-Mu dari godaan setan laki-laki dan setan perempuan.',
                'ket' => 'Mendahulukan kaki kiri ketika masuk',
            ],
            [
                'nama_doa' => 'Doa Keluar Kamar Mandi',
                'baca_latin' => 'Ghufraanaka. Alhamdu lillaahil ladzii adzhaba \'annil adzaa wa \'aafaanii.',
                'artinya' => 'Aku memohon ampunan-Mu. Segala puji bagi Allah yang telah menghilangkan kotoran dari tubuhku dan memberiku kesehatan.',
                'ket' => 'Mendahulukan kaki kanan ketika keluar',
            ],
            [
                'nama_doa' => 'Doa Untuk Kedua Orang Tua',
                'baca_latin' => 'Rabbighfir lii wa liwaalidayya warhamhumaa kamaa rabbayaanii shaghiiraa.',
                'artinya' => 'Wahai Tuhanku, ampunilah aku dan kedua orang tuaku, dan sayangilah mereka sebagaimana mereka menyayangiku di waktu kecil.',
                'ket' => 'Dibaca setiap selesai salat fardhu maupun sunnah',
            ],
            [
                'nama_doa' => 'Doa Naik Kendaraan',
                'baca_latin' => 'Subhaanal ladzii sakhkhara lanaa haadzaa wa maa kunnaa lahuu muqriniin, wa innaa ilaa rabbinaa lamunqalibuun.',
                'artinya' => 'Maha Suci Allah yang telah menundukkan semua ini bagi kami padahal sebelumnya kami tidak mampu menguasainya, dan sesungguhnya kami akan kembali kepada Tuhan kami.',
                'ket' => 'Dibaca saat menaiki kendaraan darat, laut, atau udara',
            ],
            [
                'nama_doa' => 'Doa Masuk Masjid',
                'baca_latin' => 'Allahummaftah lii abwaaba rahmatik.',
                'artinya' => 'Ya Allah, bukakanlah untukku pintu-pintu rahmat-Mu.',
                'ket' => 'Mendahulukan kaki kanan saat memasuki masjid',
            ],
            [
                'nama_doa' => 'Doa Keluar Masjid',
                'baca_latin' => 'Allahumma innii as-aluka min fadhlik.',
                'artinya' => 'Ya Allah, sesungguhnya aku memohon keutamaan dari-Mu.',
                'ket' => 'Mendahulukan kaki kiri saat keluar dari masjid',
            ],
        ];

        foreach ($doas as $item) {
            Doa::updateOrCreate(
                ['nama_doa' => $item['nama_doa']],
                [
                    'baca_latin' => $item['baca_latin'],
                    'artinya' => $item['artinya'],
                    'ket' => $item['ket'],
                ]
            );
        }
    }
}
