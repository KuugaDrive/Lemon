<?php

namespace Tests\Feature;

use App\Enums\Penilaian;
use App\Enums\StatusPresensi;
use App\Models\Alfabet;
use App\Models\BacaBuku;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Iqro;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Presensi;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru1;
    protected User $guru2;
    protected User $guruAdmin;
    protected Kelas $kelasA;
    protected Kelas $kelasB;
    protected Siswa $siswaA;
    protected Siswa $siswaB;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'guru']);

        $permissionSpp = Permission::create(['name' => 'kelola-spp']);
        $adminRole->givePermissionTo($permissionSpp);

        $this->admin = User::factory()->create(['nip' => 'ADM001', 'name' => 'Admin Utama']);
        $this->admin->assignRole('admin');

        $this->guru1 = User::factory()->create(['nip' => 'GUR001', 'name' => 'Guru Kelas A']);
        $this->guru1->assignRole('guru');

        $this->guru2 = User::factory()->create(['nip' => 'GUR002', 'name' => 'Guru Kelas B']);
        $this->guru2->assignRole('guru');

        $this->guruAdmin = User::factory()->create(['nip' => 'GUR003', 'name' => 'Guru Merangkap Admin']);
        $this->guruAdmin->assignRole(['admin', 'guru']);

        $this->kelasA = Kelas::create(['nama_kelas' => 'Kelas A (Abu Bakar)']);
        $this->kelasB = Kelas::create(['nama_kelas' => 'Kelas B (Umar bin Khattab)']);

        $this->guru1->kelas()->attach($this->kelasA->id);
        $this->guru2->kelas()->attach($this->kelasB->id);
        $this->guruAdmin->kelas()->attach($this->kelasA->id);

        $this->siswaA = Siswa::create([
            'nis' => '1001',
            'nama_siswa' => 'Ahmad Santri',
            'alamat' => 'Jl. Mawar 1',
            'nama_orang_tua' => 'Bapak Ahmad',
            'no_hp_ortu' => '08123456781',
            'kelas_id' => $this->kelasA->id,
        ]);

        $this->siswaB = Siswa::create([
            'nis' => '2001',
            'nama_siswa' => 'Budi Santri',
            'alamat' => 'Jl. Mawar 2',
            'nama_orang_tua' => 'Bapak Budi',
            'no_hp_ortu' => '08123456782',
            'kelas_id' => $this->kelasB->id,
        ]);

        // Seed sample records for reports
        Presensi::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'tanggal' => now()->toDateString(),
            'status' => StatusPresensi::Hadir,
        ]);

        $iqro = Iqro::create(['nama_iqro' => 'Iqro 1', 'jumlah_halaman' => 30]);
        SetoranIqro::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'iqro_id' => $iqro->id,
            'halaman' => 5,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        $surat = Surat::create(['nomor' => 1, 'nama_surat' => 'Al-Fatihah', 'juz' => 1, 'jumlah_ayat' => 7]);
        SetoranQuran::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'surat_id' => $surat->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 7,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSB,
        ]);

        $doa = Doa::create(['nama_doa' => 'Doa Mau Makan', 'baca_latin' => 'Allahumma barik...', 'artinya' => 'Ya Allah...']);
        HafalanDoa::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'doa_id' => $doa->id,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        $hadist = Hadist::create(['nama_hadist' => 'Hadits Niat', 'baca_latin' => 'Innamal a\'malu binniyat', 'artinya' => 'Sesungguhnya amal...']);
        HafalanHadist::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'hadist_id' => $hadist->id,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSB,
        ]);

        HafalanSurat::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'surat_id' => $surat->id,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        $alfabet = Alfabet::create(['nama_alfabet' => 'Alif', 'ket' => 'ا']);
        BacaBuku::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'alfabet_id' => $alfabet->id,
            'jilid' => 'Jilid 1',
            'halaman' => 3,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        PembayaranSpp::create([
            'siswa_id' => $this->siswaA->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);
    }

    public function test_halaman_index_laporan_dapat_diakses(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('laporan.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Keuangan SPP');

        $responseGuru = $this->actingAs($this->guru1)->get(route('laporan.index'));
        $responseGuru->assertStatus(200);
        $responseGuru->assertDontSee('Keuangan SPP');
    }

    public function test_cetak_laporan_pdf_semua_modul_mode_siswa_berhasil(): void
    {
        $modules = ['presensi', 'iqro', 'quran', 'doa', 'hadist', 'surat', 'baca_buku', 'spp'];
        $dari = now()->startOfMonth()->toDateString();
        $sampai = now()->toDateString();

        foreach ($modules as $mod) {
            $response = $this->actingAs($this->admin)->post(route('laporan.cetak'), [
                'jenis' => $mod,
                'mode' => 'siswa',
                'siswa_id' => $this->siswaA->id,
                'dari_tanggal' => $dari,
                'sampai_tanggal' => $sampai,
            ]);

            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');
            $this->assertNotEmpty($response->getContent());
        }
    }

    public function test_cetak_laporan_pdf_semua_modul_mode_kelas_berhasil(): void
    {
        $modules = ['presensi', 'iqro', 'quran', 'doa', 'hadist', 'surat', 'baca_buku', 'spp'];
        $dari = now()->startOfMonth()->toDateString();
        $sampai = now()->toDateString();

        foreach ($modules as $mod) {
            $response = $this->actingAs($this->admin)->post(route('laporan.cetak'), [
                'jenis' => $mod,
                'mode' => 'kelas',
                'kelas_id' => $this->kelasA->id,
                'dari_tanggal' => $dari,
                'sampai_tanggal' => $sampai,
            ]);

            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');
            $this->assertNotEmpty($response->getContent());
        }
    }

    public function test_guru_non_admin_ditolak_cetak_laporan_spp(): void
    {
        $payload = [
            'jenis' => 'spp',
            'mode' => 'siswa',
            'siswa_id' => $this->siswaA->id,
            'dari_tanggal' => now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->guru1)->post(route('laporan.cetak'), $payload);
        $response->assertStatus(403);
    }

    public function test_guru_ditolak_cetak_laporan_siswa_atau_kelas_lain(): void
    {
        // Guru 1 coba cetak siswa kelas B -> 403
        $responseSiswa = $this->actingAs($this->guru1)->post(route('laporan.cetak'), [
            'jenis' => 'presensi',
            'mode' => 'siswa',
            'siswa_id' => $this->siswaB->id,
            'dari_tanggal' => now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => now()->toDateString(),
        ]);
        $responseSiswa->assertStatus(403);

        // Guru 1 coba cetak kelas B -> 403
        $responseKelas = $this->actingAs($this->guru1)->post(route('laporan.cetak'), [
            'jenis' => 'presensi',
            'mode' => 'kelas',
            'kelas_id' => $this->kelasB->id,
            'dari_tanggal' => now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => now()->toDateString(),
        ]);
        $responseKelas->assertStatus(403);
    }

    public function test_rentang_tanggal_melebihi_366_hari_ditolak(): void
    {
        $payload = [
            'jenis' => 'presensi',
            'mode' => 'siswa',
            'siswa_id' => $this->siswaA->id,
            'dari_tanggal' => '2025-01-01',
            'sampai_tanggal' => '2026-05-01', // > 1 tahun
        ];

        $response = $this->actingAs($this->admin)->post(route('laporan.cetak'), $payload);
        $response->assertSessionHasErrors('sampai_tanggal');
    }

    public function test_guru_merangkap_admin_dapat_mencetak_semua_laporan(): void
    {
        $response = $this->actingAs($this->guruAdmin)->post(route('laporan.cetak'), [
            'jenis' => 'spp',
            'mode' => 'siswa',
            'siswa_id' => $this->siswaB->id,
            'dari_tanggal' => now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => now()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
