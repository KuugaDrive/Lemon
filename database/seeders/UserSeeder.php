<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allKelas = Kelas::all();
        $kelas1 = $allKelas->get(0);
        $kelas2 = $allKelas->get(1);
        $kelas3 = $allKelas->get(2);

        // 1. Admin utama (NIP: admin, Password: password, Role: admin)
        $admin = User::firstOrCreate(
            ['nip' => 'admin'],
            [
                'name' => 'Administrator Lembaga',
                'email' => 'admin@lemon.test',
                'alamat' => 'Jl. Pendidikan Al-Qur\'an No. 1, Jakarta',
                'no_hp' => '081234567890',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Guru murni (NIP: GURU01, Role: guru, mengampu kelas 1)
        $guru1 = User::firstOrCreate(
            ['nip' => 'GURU01'],
            [
                'name' => 'Ustadzah Fatimah, S.Pd.',
                'email' => 'fatimah@lemon.test',
                'alamat' => 'Jl. Melati No. 12, Jakarta',
                'no_hp' => '081234567891',
                'password' => Hash::make('password'),
            ]
        );
        $guru1->syncRoles(['guru']);
        if ($kelas1) {
            $guru1->kelas()->syncWithoutDetaching([$kelas1->id]);
        }

        // 3. Guru yang merangkap admin (NIP: GURU02, Role: guru & admin, mengampu kelas 2 & 3)
        $guru2 = User::firstOrCreate(
            ['nip' => 'GURU02'],
            [
                'name' => 'Ustadz Ahmad Fauzi, S.Pd.I',
                'email' => 'ahmad@lemon.test',
                'alamat' => 'Jl. Mawar Indah No. 5, Jakarta',
                'no_hp' => '081234567892',
                'password' => Hash::make('password'),
            ]
        );
        $guru2->syncRoles(['guru', 'admin']);
        $kelasToSync = [];
        if ($kelas2) $kelasToSync[] = $kelas2->id;
        if ($kelas3) $kelasToSync[] = $kelas3->id;
        if (!empty($kelasToSync)) {
            $guru2->kelas()->syncWithoutDetaching($kelasToSync);
        }
    }
}
