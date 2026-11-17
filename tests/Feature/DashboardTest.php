<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected Kelas $kelasA;
    protected Kelas $kelasB;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Permission::firstOrCreate(['name' => 'kelola-spp']);

        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo('kelola-spp');

        $this->admin = User::factory()->create([
            'nip' => '198501012010011001',
            'name' => 'Admin Utama',
        ]);
        $this->admin->assignRole('admin');

        $this->guru = User::factory()->create([
            'nip' => '199002022015022002',
            'name' => 'Ustadzah Fatimah',
        ]);
        $this->guru->assignRole('guru');

        $this->kelasA = Kelas::create([
            'nama_kelas' => 'Kelas Madinah',
            'keterangan' => 'Kelompok Belajar Madinah',
        ]);

        $this->kelasB = Kelas::create([
            'nama_kelas' => 'Kelas Makkah',
            'keterangan' => 'Kelompok Belajar Makkah',
        ]);

        $this->guru->kelas()->attach($this->kelasA->id);

        Siswa::create([
            'nis' => '1001',
            'nama_siswa' => 'Ahmad Santri',
            'nama_ayah_kandung' => 'Fulan',
            'nama_ibu_kandung' => 'Fulanah',
            'alamat' => 'Yogyakarta',
            'kelas_id' => $this->kelasA->id,
        ]);

        Siswa::create([
            'nis' => '1002',
            'nama_siswa' => 'Budi Santri',
            'nama_ayah_kandung' => 'Bambang',
            'nama_ibu_kandung' => 'Siti',
            'alamat' => 'Sleman',
            'kelas_id' => $this->kelasB->id,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_and_see_all_stats(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Utama');
        $response->assertSee('Administrator');
        $response->assertSee('Santri Aktif');
        $response->assertSee('Guru Pengajar');
        $response->assertSee('Rombel Kelas');
        $response->assertSee('SPP Bulan Ini');
        $response->assertSee('Kelas Madinah');
        $response->assertSee('Kelas Makkah');
        $response->assertSee('Pintasan Transaksi dan Operasional');
    }

    public function test_guru_can_access_dashboard_and_see_only_assigned_class(): void
    {
        $response = $this->actingAs($this->guru)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ustadzah Fatimah');
        $response->assertSee('Guru Pengajar');
        $response->assertSee('Santri Bimbingan');
        $response->assertSee('Kelas Diampu');
        $response->assertSee('Kelas Madinah');
        $response->assertDontSee('Kelas Makkah');
        $response->assertDontSee('SPP Bulan Ini');
    }
}
