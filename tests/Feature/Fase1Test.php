<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Fase1Test extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_successfully_and_populates_expected_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Roles exist
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'guru')->exists());

        // Admin user exists with NIP 'admin'
        $admin = User::where('nip', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));

        // Guru exists with assigned class
        $guru1 = User::where('nip', 'GURU01')->first();
        $this->assertNotNull($guru1);
        $this->assertTrue($guru1->hasRole('guru'));
        $this->assertFalse($guru1->hasRole('admin'));
        $this->assertGreaterThan(0, $guru1->kelas()->count());

        // Guru merangkap admin exists
        $guru2 = User::where('nip', 'GURU02')->first();
        $this->assertNotNull($guru2);
        $this->assertTrue($guru2->hasRole('guru'));
        $this->assertTrue($guru2->hasRole('admin'));
        $this->assertGreaterThan(0, $guru2->kelas()->count());

        // Classes and students exist
        $this->assertEquals(3, Kelas::count());
        $this->assertEquals(15, Siswa::count());
    }

    public function test_login_uses_nip_instead_of_email(): void
    {
        Role::create(['name' => 'guru']);
        $user = User::factory()->create([
            'nip' => '19900101',
            'password' => bcrypt('rahasia123'),
        ]);
        $user->assignRole('guru');

        // Login sukses dengan NIP
        $response = $this->post('/login', [
            'nip' => '19900101',
            'password' => 'rahasia123',
        ]);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // Login gagal dengan password salah
        $failResponse = $this->post('/login', [
            'nip' => '19900101',
            'password' => 'passwordsalah',
        ]);
        $failResponse->assertSessionHasErrors('nip');
        $this->assertGuest();
    }

    public function test_siswa_visible_to_scope_enforces_teacher_and_admin_boundaries(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'guru']);

        $kelasA = Kelas::factory()->create(['nama_kelas' => 'Kelas A']);
        $kelasB = Kelas::factory()->create(['nama_kelas' => 'Kelas B']);

        // Siswa 3 di kelas A, 2 di kelas B
        $siswaA = Siswa::factory()->count(3)->create(['kelas_id' => $kelasA->id]);
        $siswaB = Siswa::factory()->count(2)->create(['kelas_id' => $kelasB->id]);

        // Guru A hanya pegang kelas A
        $guruA = User::factory()->create(['nip' => 'GURUA']);
        $guruA->assignRole('guru');
        $guruA->kelas()->attach($kelasA->id);

        // Admin murni
        $admin = User::factory()->create(['nip' => 'ADMIN01']);
        $admin->assignRole('admin');

        // Guru yang juga merangkap admin (hanya pegang kelas B secara pivot, tapi admin)
        $guruAdmin = User::factory()->create(['nip' => 'GURUADMIN']);
        $guruAdmin->assignRole(['guru', 'admin']);
        $guruAdmin->kelas()->attach($kelasB->id);

        // Scope verification
        // 1. Admin melihat semua (5 siswa)
        $this->assertEquals(5, Siswa::visibleTo($admin)->count());

        // 2. Guru A hanya melihat 3 siswa di Kelas A
        $visibleGuruA = Siswa::visibleTo($guruA)->get();
        $this->assertEquals(3, $visibleGuruA->count());
        $this->assertTrue($visibleGuruA->pluck('id')->contains($siswaA->first()->id));
        $this->assertFalse($visibleGuruA->pluck('id')->contains($siswaB->first()->id));

        // 3. Guru yang merangkap admin melihat semua (5 siswa)
        $this->assertEquals(5, Siswa::visibleTo($guruAdmin)->count());

        // 4. canAccessSiswa helper check
        $this->assertTrue($guruA->canAccessSiswa($siswaA->first()));
        $this->assertFalse($guruA->canAccessSiswa($siswaB->first()));
        $this->assertTrue($admin->canAccessSiswa($siswaB->first()));
        $this->assertTrue($guruAdmin->canAccessSiswa($siswaA->first()));
    }
}
