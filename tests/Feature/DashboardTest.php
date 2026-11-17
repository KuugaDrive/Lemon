<?php

namespace Tests\Feature;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\BacaBuku;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Iqro;
use App\Models\Kelas;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
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
        $response->assertSee('Status Presensi Kelas Hari Ini');
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

    public function test_dashboard_counts_all_setoran_and_hafalan_modules_today(): void
    {
        $today = now()->toDateString();
        $siswaA = Siswa::where('nis', '1001')->first();
        $siswaB = Siswa::where('nis', '1002')->first();

        $iqro = Iqro::create(['nama_iqro' => 'Jilid 1', 'jumlah_halaman' => 30]);
        $surat = Surat::create(['nomor' => 1, 'nama_surat' => 'Al-Fatihah', 'juz' => 1, 'jumlah_ayat' => 7]);
        $doa = Doa::create(['nama_doa' => 'Doa Mau Tidur', 'baca_latin' => 'Bismika', 'artinya' => 'Arti']);
        $hadist = Hadist::create(['nama_hadist' => 'Kebersihan', 'baca_latin' => 'At-thuhur', 'artinya' => 'Arti']);
        $alfabet = Alfabet::create(['nama_alfabet' => 'Buku Baca A']);

        SetoranIqro::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $this->guru->id,
            'iqro_id' => $iqro->id,
            'halaman' => 5,
            'tanggal' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        SetoranQuran::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $this->guru->id,
            'surat_id' => $surat->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 5,
            'tanggal' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        HafalanDoa::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $this->guru->id,
            'doa_id' => $doa->id,
            'tgl_hafalan' => $today,
            'tgl_pengujian' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        HafalanHadist::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $this->guru->id,
            'hadist_id' => $hadist->id,
            'tgl_hafalan' => $today,
            'tgl_pengujian' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        HafalanSurat::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $this->guru->id,
            'surat_id' => $surat->id,
            'tgl_hafalan' => $today,
            'tgl_pengujian' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        BacaBuku::create([
            'siswa_id' => $siswaB->id,
            'guru_id' => $this->admin->id,
            'alfabet_id' => $alfabet->id,
            'jilid' => '1',
            'halaman' => 10,
            'tgl_hafalan' => $today,
            'tgl_pengujian' => $today,
            'nilai' => Penilaian::BSH,
        ]);

        // Admin sees all 6 records
        $responseAdmin = $this->actingAs($this->admin)->get('/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewHas('setoranHariIniCount', 6);

        // Guru (assigned to kelasA where siswaA is) sees only the 5 records for siswaA
        $responseGuru = $this->actingAs($this->guru)->get('/dashboard');
        $responseGuru->assertStatus(200);
        $responseGuru->assertViewHas('setoranHariIniCount', 5);
    }
}
