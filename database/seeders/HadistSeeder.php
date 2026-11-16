<?php

namespace Database\Seeders;

use App\Models\Hadist;
use Illuminate\Database\Seeder;

class HadistSeeder extends Seeder
{
    public function run(): void
    {
        $hadists = [
            [
                'nama_hadist' => 'Hadits Kebersihan Sebagian dari Iman',
                'baca_latin' => 'Ath-thuhuuru syathrul iimaan.',
                'artinya' => 'Kebersihan itu sebagian dari (tanda) keimanan. (HR. Muslim)',
                'ket' => 'HR. Muslim no. 223',
            ],
            [
                'nama_hadist' => 'Hadits Kewajiban Menuntut Ilmu',
                'baca_latin' => 'Thalabul \'ilmi fariidhatun \'alaa kulli muslim.',
                'artinya' => 'Menuntut ilmu itu wajib atas setiap muslim. (HR. Ibnu Majah)',
                'ket' => 'HR. Ibnu Majah no. 224',
            ],
            [
                'nama_hadist' => 'Hadits Senyum Adalah Sedekah',
                'baca_latin' => 'Tabassumuka fii wajhi akhiika laka shadaqah.',
                'artinya' => 'Senyummu di hadapan saudaramu adalah sedekah bagimu. (HR. Tirmidzi)',
                'ket' => 'HR. Tirmidzi no. 1956',
            ],
            [
                'nama_hadist' => 'Hadits Larangan Marah',
                'baca_latin' => 'Laa taghdhab wa lakal jannah.',
                'artinya' => 'Janganlah kamu marah, maka bagimu surga. (HR. Thabrani)',
                'ket' => 'HR. Thabrani dalam Al-Ausath',
            ],
            [
                'nama_hadist' => 'Hadits Kasih Sayang',
                'baca_latin' => 'Man laa yarham laa yurham.',
                'artinya' => 'Barangsiapa yang tidak menyayangi, niscaya tidak akan disayangi. (HR. Bukhari & Muslim)',
                'ket' => 'HR. Bukhari no. 5997 dan Muslim no. 2318',
            ],
            [
                'nama_hadist' => 'Hadits Keutamaan Belajar Al-Qur\'an',
                'baca_latin' => 'Khairukum man ta\'allamal qur\'aana wa \'allamah.',
                'artinya' => 'Sebaik-baik kalian adalah orang yang belajar Al-Qur\'an dan mengajarkannya. (HR. Bukhari)',
                'ket' => 'HR. Bukhari no. 5027',
            ],
            [
                'nama_hadist' => 'Hadits Berkata Baik atau Diam',
                'baca_latin' => 'Man kaana yu\'minu billaahi wal yaumil aakhiri falyaqul khairan au liyashmut.',
                'artinya' => 'Barangsiapa beriman kepada Allah dan hari akhir, hendaklah ia berkata yang baik atau diam. (HR. Bukhari & Muslim)',
                'ket' => 'HR. Bukhari no. 6018 dan Muslim no. 47',
            ],
            [
                'nama_hadist' => 'Hadits Niat dalam Beramal',
                'baca_latin' => 'Innamal a\'maalu bin niyyaat.',
                'artinya' => 'Sesungguhnya setiap amal perbuatan itu tergantung pada niatnya. (HR. Bukhari & Muslim)',
                'ket' => 'HR. Bukhari no. 1 dan Muslim no. 1907',
            ],
        ];

        foreach ($hadists as $item) {
            Hadist::updateOrCreate(
                ['nama_hadist' => $item['nama_hadist']],
                [
                    'baca_latin' => $item['baca_latin'],
                    'artinya' => $item['artinya'],
                    'ket' => $item['ket'],
                ]
            );
        }
    }
}
