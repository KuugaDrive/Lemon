<?php

namespace Database\Seeders;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\BacaBuku;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Iqro;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SetoranHafalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswas = Siswa::with('kelas.gurus')->get();
        if ($siswas->isEmpty()) {
            $this->command?->warn('Belum ada siswa di database. Jalankan SiswaSeeder terlebih dahulu.');
            return;
        }

        $allIqros = Iqro::orderBy('id')->get();
        $allSurats = Surat::orderBy('nomor')->get();
        $allDoas = Doa::orderBy('id')->get();
        $allHadists = Hadist::orderBy('id')->get();
        $allAlfabets = Alfabet::orderBy('id')->get();

        $defaultGuru = User::role('guru')->first() ?? User::role('admin')->first();

        // Bersihkan data setoran dan hafalan lama agar seeder bersifat idempotent
        SetoranIqro::query()->delete();
        SetoranQuran::query()->delete();
        HafalanDoa::query()->delete();
        HafalanHadist::query()->delete();
        HafalanSurat::query()->delete();
        BacaBuku::query()->delete();

        $today = Carbon::today();

        $evaluations = [
            Penilaian::BSB->value => [
                'Sangat lancar, makhroj dan tajwid sangat baik.',
                'Mampu melafalkan secara mandiri dengan tartil.',
                'Hafalan sangat mutqin, intonasi dan tajwid tepat.',
                'Luar biasa, bacaan fasih tanpa keraguan.',
                'Alhamdulillah tuntas dengan predikat sempurna.',
                'Pelafalan sangat jelas, tenang dan percaya diri.',
            ],
            Penilaian::BSH->value => [
                'Lancar dan bacaan sudah sesuai kaidah standar.',
                'Alhamdulillah baik, makhroj huruf sudah tepat.',
                'Sudah hafal dengan baik, sedikit bimbingan di akhir ayat.',
                'Mampu membaca dengan tertib dan tenang.',
                'Bacaan fasih, siap melanjutkan ke materi berikutnya.',
                'Bagus, penguasaan materi berkembang sesuai harapan.',
                'Cukup lancar, tajwid dasar sudah diterapkan.',
            ],
            Penilaian::MB->value => [
                'Mulai berkembang, perlu latihan kelancaran di rumah.',
                'Perlu muroja\'ah ulang terutama di bagian pertengahan.',
                'Makhroj huruf masih agak tertukar pada beberapa lafadz.',
                'Masih membutuhkan sedikit bimbingan dari ustadzah.',
                'Sudah bisa mengingat, disarankan latihan tempo membaca.',
            ],
            Penilaian::BB->value => [
                'Masih terbata-bata, perlu pendampingan intensif.',
                'Belum menguasai waqaf dan makhroj dasar dengan baik.',
                'Perlu mengulang kembali materi ini pada pertemuan berikutnya.',
            ],
        ];

        $getKet = function (Penilaian $nilai) use ($evaluations): ?string {
            // 85% berikan catatan, 15% tanpa catatan (opsional di sistem)
            if (rand(1, 100) > 85) {
                return null;
            }
            $list = $evaluations[$nilai->value];
            return $list[array_rand($list)];
        };

        $getRandomNilai = function (int $progressIndex, int $totalSteps): Penilaian {
            // Simulasi perkembangan santri: di awal ada MB/BB, makin ke belakang condong BSH/BSB
            $ratio = $progressIndex / max($totalSteps, 1);
            $dice = rand(1, 100);

            if ($ratio < 0.3) {
                if ($dice <= 15) return Penilaian::BB;
                if ($dice <= 45) return Penilaian::MB;
                if ($dice <= 85) return Penilaian::BSH;
                return Penilaian::BSB;
            }

            if ($dice <= 5) return Penilaian::BB;
            if ($dice <= 20) return Penilaian::MB;
            if ($dice <= 65) return Penilaian::BSH;
            return Penilaian::BSB;
        };

        // Surah-surah pendek populer untuk anak PAUD / TPQ
        $juz30Surats = $allSurats->filter(fn ($s) => $s->nomor >= 93 || $s->nomor === 1)->values();

        // Rentang tanggal pengujian historis (dari 50 hari lalu hingga hari ini)
        $dateOffsets = [50, 44, 38, 32, 26, 21, 16, 11, 7, 4, 2, 1, 0];

        $totalIqroCount = 0;
        $totalQuranCount = 0;
        $totalDoaCount = 0;
        $totalHadistCount = 0;
        $totalSuratCount = 0;
        $totalBacaBukuCount = 0;

        foreach ($siswas as $siswaIndex => $siswa) {
            // Tentukan guru penilai berdasarkan kelas yang diampu
            $guru = $siswa->kelas?->gurus?->first() ?? $defaultGuru;
            if (! $guru) {
                continue;
            }

            // Tentukan level awal siswa berdasarkan rombel/indeks siswa
            $isTpq = str_contains(strtolower($siswa->kelas?->nama_kelas ?? ''), 'tpq');
            $isPaudB = str_contains(strtolower($siswa->kelas?->nama_kelas ?? ''), 'paud b');

            // 1. SETORAN IQRO (7 - 10 setoran per siswa)
            $iqroStartLevel = $isTpq ? rand(3, 5) : ($isPaudB ? rand(2, 3) : rand(1, 2));
            $currentIqro = $allIqros->firstWhere('nama_iqro', "Iqro {$iqroStartLevel}") ?? $allIqros->first();
            $currentHalaman = rand(1, 5);

            $iqroSteps = rand(7, 10);
            for ($step = 0; $step < $iqroSteps; $step++) {
                $daysAgo = $dateOffsets[$step % count($dateOffsets)];
                // Santri ganjil mendapat setoran hari ini (offset 0)
                if ($step === $iqroSteps - 1 && ($siswaIndex % 2 === 0)) {
                    $daysAgo = 0;
                }
                $tgl = $today->copy()->subDays($daysAgo)->toDateString();

                $currentHalaman = min($currentHalaman + rand(2, 4), $currentIqro->jumlah_halaman);
                $nilai = $getRandomNilai($step, $iqroSteps);

                SetoranIqro::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'iqro_id' => $currentIqro->id,
                    'halaman' => $currentHalaman,
                    'tanggal' => $tgl,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalIqroCount++;

                // Jika sudah mendekati halaman akhir jilid, naik ke jilid berikutnya
                if ($currentHalaman >= $currentIqro->jumlah_halaman - 2 && $iqroStartLevel < 6) {
                    $iqroStartLevel++;
                    $nextIqro = $allIqros->firstWhere('nama_iqro', "Iqro {$iqroStartLevel}");
                    if ($nextIqro) {
                        $currentIqro = $nextIqro;
                        $currentHalaman = 1;
                    }
                }
            }

            // 2. SETORAN AL-QUR'AN (5 - 8 setoran per siswa)
            $quranSteps = rand(5, 8);
            $selectedSurats = $juz30Surats->shuffle()->take($quranSteps);
            foreach ($selectedSurats as $qIndex => $surat) {
                $daysAgo = $dateOffsets[($qIndex * 2) % count($dateOffsets)];
                if ($qIndex === 0 && ($siswaIndex % 3 === 0)) {
                    $daysAgo = 0;
                }
                $tgl = $today->copy()->subDays($daysAgo)->toDateString();

                $maxAyat = max((int) $surat->jumlah_ayat, 1);
                $ayatMulai = 1;
                $ayatSelesai = $maxAyat > 7 ? rand(3, min(7, $maxAyat)) : $maxAyat;

                $nilai = $getRandomNilai($qIndex, $quranSteps);

                SetoranQuran::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'surat_id' => $surat->id,
                    'juz' => $surat->juz ?: 30,
                    'ayat_mulai' => $ayatMulai,
                    'ayat_selesai' => $ayatSelesai,
                    'tanggal' => $tgl,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalQuranCount++;
            }

            // 3. HAFALAN DOA (5 - 8 doa per siswa)
            $doaSteps = rand(5, min(8, $allDoas->count()));
            $chosenDoas = $allDoas->shuffle()->take($doaSteps);
            foreach ($chosenDoas as $dIndex => $doa) {
                $daysAgo = $dateOffsets[($dIndex * 2 + 1) % count($dateOffsets)];
                if ($dIndex === 0 && ($siswaIndex % 2 === 1)) {
                    $daysAgo = 0;
                }
                $tglPengujian = $today->copy()->subDays($daysAgo)->toDateString();
                $tglHafalan = $today->copy()->subDays($daysAgo + rand(1, 4))->toDateString();

                $nilai = $getRandomNilai($dIndex, $doaSteps);

                HafalanDoa::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'doa_id' => $doa->id,
                    'tgl_hafalan' => $tglHafalan,
                    'tgl_pengujian' => $tglPengujian,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalDoaCount++;
            }

            // 4. HAFALAN HADIST (4 - 7 hadits per siswa)
            $hadistSteps = rand(4, min(7, $allHadists->count()));
            $chosenHadists = $allHadists->shuffle()->take($hadistSteps);
            foreach ($chosenHadists as $hIndex => $hadist) {
                $daysAgo = $dateOffsets[($hIndex * 3) % count($dateOffsets)];
                if ($hIndex === 0 && ($siswaIndex % 3 === 1)) {
                    $daysAgo = 0;
                }
                $tglPengujian = $today->copy()->subDays($daysAgo)->toDateString();
                $tglHafalan = $today->copy()->subDays($daysAgo + rand(2, 5))->toDateString();

                $nilai = $getRandomNilai($hIndex, $hadistSteps);

                HafalanHadist::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'hadist_id' => $hadist->id,
                    'tgl_hafalan' => $tglHafalan,
                    'tgl_pengujian' => $tglPengujian,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalHadistCount++;
            }

            // 5. HAFALAN SURAT (5 - 8 surat per siswa)
            $suratHafalanSteps = rand(5, 8);
            $chosenSurats = $juz30Surats->shuffle()->take($suratHafalanSteps);
            foreach ($chosenSurats as $sIndex => $surat) {
                $daysAgo = $dateOffsets[($sIndex * 2 + 2) % count($dateOffsets)];
                if ($sIndex === 0 && ($siswaIndex % 4 === 0)) {
                    $daysAgo = 0;
                }
                $tglPengujian = $today->copy()->subDays($daysAgo)->toDateString();
                $tglHafalan = $today->copy()->subDays($daysAgo + rand(2, 6))->toDateString();

                $nilai = $getRandomNilai($sIndex, $suratHafalanSteps);

                HafalanSurat::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'surat_id' => $surat->id,
                    'tgl_hafalan' => $tglHafalan,
                    'tgl_pengujian' => $tglPengujian,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalSuratCount++;
            }

            // 6. BACA BUKU ALFABET (5 - 8 catatan per siswa)
            $bacaSteps = rand(5, 8);
            $chosenAlfabets = $allAlfabets->shuffle()->take($bacaSteps);
            foreach ($chosenAlfabets as $bIndex => $alfabet) {
                $daysAgo = $dateOffsets[($bIndex * 2 + 3) % count($dateOffsets)];
                if ($bIndex === 0 && ($siswaIndex % 2 === 0)) {
                    $daysAgo = 0;
                }
                $tglPengujian = $today->copy()->subDays($daysAgo)->toDateString();
                $tglHafalan = $today->copy()->subDays($daysAgo + rand(1, 3))->toDateString();

                $nilai = $getRandomNilai($bIndex, $bacaSteps);

                BacaBuku::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $guru->id,
                    'alfabet_id' => $alfabet->id,
                    'jilid' => (string) rand(1, 3),
                    'halaman' => rand(1, 20),
                    'tgl_hafalan' => $tglHafalan,
                    'tgl_pengujian' => $tglPengujian,
                    'nilai' => $nilai,
                    'ket' => $getKet($nilai),
                ]);
                $totalBacaBukuCount++;
            }
        }

        $totalAll = $totalIqroCount + $totalQuranCount + $totalDoaCount + $totalHadistCount + $totalSuratCount + $totalBacaBukuCount;

        $this->command?->info("Setoran & Hafalan Seeder selesai!");
        $this->command?->info("- Setoran Iqro: {$totalIqroCount}");
        $this->command?->info("- Setoran Qur'an: {$totalQuranCount}");
        $this->command?->info("- Hafalan Doa: {$totalDoaCount}");
        $this->command?->info("- Hafalan Hadits: {$totalHadistCount}");
        $this->command?->info("- Hafalan Nama Surat: {$totalSuratCount}");
        $this->command?->info("- Baca Buku Alfabet: {$totalBacaBukuCount}");
        $this->command?->info("Total data terbuat: {$totalAll} records.");
    }
}
