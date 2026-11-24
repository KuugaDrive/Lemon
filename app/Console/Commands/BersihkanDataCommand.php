<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class BersihkanDataCommand extends Command
{
    protected $signature = 'lemon:bersihkan-data {--force : Bypass konfirmasi keamanan} {--keep-master : Pertahankan data kelas dan santri, hanya hapus transaksi}';
    protected $description = 'Membersihkan data transaksi dan sampel sistem Lemon, menyisakan katalog materi dan akun admin utama.';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                'PERINGATAN: Perintah ini akan menghapus seluruh data transaksi dan sampel lembaga secara permanen. Apakah Anda yakin ingin melanjutkan?',
                false
            );

            if (! $confirmed) {
                $this->warn('Operasi pembersihan data dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $this->info('Memulai pembersihan data...');

        $ringkasan = [];

        Schema::disableForeignKeyConstraints();

        // 1. Data Transaksi
        $tabelTransaksi = [
            'presensis' => 'Presensi Harian',
            'setoran_iqros' => 'Setoran Iqro',
            'setoran_qurans' => 'Setoran Al-Qur\'an',
            'hafalan_doas' => 'Hafalan Doa',
            'hafalan_hadists' => 'Hafalan Hadits',
            'hafalan_surats' => 'Setoran Surat',
            'baca_bukus' => 'Baca Buku (Hijaiyah/Latin)',
            'pembayaran_spps' => 'Pembayaran SPP',
        ];

        foreach ($tabelTransaksi as $tabel => $label) {
            $count = DB::table($tabel)->count();
            DB::table($tabel)->truncate();
            $ringkasan[] = [$label, $tabel, $count, 'Dibersihkan (Truncate)'];
        }

        // 2. Relasi Guru-Kelas
        $countGuruKelas = DB::table('guru_kelas')->count();
        DB::table('guru_kelas')->truncate();
        $ringkasan[] = ['Relasi Guru-Kelas', 'guru_kelas', $countGuruKelas, 'Dibersihkan (Truncate)'];

        // 3. Siswa & Kelas Sampel
        if (! $this->option('keep-master')) {
            $countSiswa = DB::table('siswas')->count();
            DB::table('siswas')->truncate();
            $ringkasan[] = ['Data Santri / Siswa', 'siswas', $countSiswa, 'Dibersihkan (Truncate)'];

            $countKelas = DB::table('kelas')->count();
            DB::table('kelas')->truncate();
            $ringkasan[] = ['Data Kelas', 'kelas', $countKelas, 'Dibersihkan (Truncate)'];

            // 4. Pengguna Guru (Non-Admin)
            Role::firstOrCreate(['name' => 'admin']);
            Role::firstOrCreate(['name' => 'guru']);

            $adminUser = User::where('nip', 'admin')->first();
            if (! $adminUser) {
                $adminUser = User::create([
                    'nip' => 'admin',
                    'name' => 'Administrator Lemon',
                    'password' => Hash::make('password'),
                ]);
                $adminUser->assignRole('admin');
            } else {
                $adminUser->assignRole('admin');
            }

            $countUsersDeleted = User::where('nip', '!=', 'admin')->count();
            User::where('nip', '!=', 'admin')->delete();
            $ringkasan[] = ['User Guru & Sampel (Kecuali Admin)', 'users', $countUsersDeleted, 'Dihapus (Menyisakan NIP: admin)'];
        }

        Schema::enableForeignKeyConstraints();

        $this->newLine();
        $this->table(['Entitas Data', 'Nama Tabel', 'Baris Terhapus', 'Status'], $ringkasan);
        $this->newLine();

        $this->info('Verifikasi Status Master Katalog:');
        $this->line('- Iqro: ' . DB::table('iqros')->count() . ' baris (UTUH)');
        $this->line('- Surat Al-Qur\'an: ' . DB::table('surats')->count() . ' baris (UTUH)');
        $this->line('- Doa Harian: ' . DB::table('doas')->count() . ' baris (UTUH)');
        $this->line('- Hadits Pendek: ' . DB::table('hadists')->count() . ' baris (UTUH)');
        $this->line('- Huruf Hijaiyah: ' . DB::table('alfabets')->count() . ' baris (UTUH)');
        $this->line('- Huruf Latin: ' . DB::table('alfabet_latins')->count() . ' baris (UTUH)');
        $this->line('- Pengaturan SPP: ' . DB::table('pengaturans')->count() . ' baris (UTUH)');
        if (isset($adminUser)) {
            $this->line('- Akun Admin Tersisa: NIP ' . $adminUser->nip . ' (' . $adminUser->name . ')');
        }

        $this->newLine();
        $this->info('Pembersihan data selesai dengan aman dan sukses!');

        return Command::SUCCESS;
    }
}
