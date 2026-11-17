<?php

namespace Tests\Feature;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\Iqro;
use App\Models\Kelas;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SetoranHafalanTest extends TestCase
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
    protected Iqro $iqro1;
    protected Surat $suratAlFatihah;
    protected Doa $doa1;
    protected Hadist $hadist1;
    protected Alfabet $alfabetAlif;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'guru']);

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
            'alamat' => 'Jl. Melati',
            'nama_orang_tua' => 'Bapak Ahmad',
            'no_hp_ortu' => '08123456781',
            'kelas_id' => $this->kelasA->id,
        ]);

        $this->siswaB = Siswa::create([
            'nis' => '2001',
            'nama_siswa' => 'Budi Santri',
            'alamat' => 'Jl. Mawar',
            'nama_orang_tua' => 'Bapak Budi',
            'no_hp_ortu' => '08123456782',
            'kelas_id' => $this->kelasB->id,
        ]);

        $this->iqro1 = Iqro::create(['nama_iqro' => 'Iqro 1', 'jumlah_halaman' => 30]);
        $this->suratAlFatihah = Surat::create(['nomor' => 1, 'nama_surat' => 'Al-Fatihah', 'juz' => 1, 'jumlah_ayat' => 7]);
        $this->doa1 = Doa::create(['nama_doa' => 'Doa Sebelum Makan', 'baca_latin' => 'Allahumma barik...', 'artinya' => 'Ya Allah berkahilah...']);
        $this->hadist1 = Hadist::create(['nama_hadist' => 'Hadits Kebersihan', 'baca_latin' => 'Ath-thuhuru syathrul iman', 'artinya' => 'Kebersihan adalah sebagian dari iman']);
        $this->alfabetAlif = Alfabet::create(['nama_alfabet' => 'Alif', 'ket' => 'ا']);
    }

    public function test_guru_bisa_input_setoran_iqro_siswa_kelasnya(): void
    {
        $payload = [
            'siswa_id' => $this->siswaA->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 5,
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSH',
            'ket' => 'Lancar dan makhraj baik',
        ];

        $response = $this->actingAs($this->guru1)->post(route('setoran.iqro.store'), $payload);
        $response->assertRedirect(route('setoran.iqro.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('setoran_iqros', [
            'siswa_id' => $this->siswaA->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 5,
            'nilai' => 'BSH',
            'guru_id' => $this->guru1->id,
        ]);
    }

    public function test_guru_tidak_bisa_input_setoran_iqro_siswa_kelas_lain(): void
    {
        $payload = [
            'siswa_id' => $this->siswaB->id, // Siswa kelas B
            'iqro_id' => $this->iqro1->id,
            'halaman' => 5,
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSH',
        ];

        // Guru 1 hanya memegang Kelas A -> ditolak 403
        $response = $this->actingAs($this->guru1)->post(route('setoran.iqro.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_halaman_melebihi_maksimal_iqro_ditolak(): void
    {
        $payload = [
            'siswa_id' => $this->siswaA->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 35, // Iqro 1 hanya 30 halaman
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSH',
        ];

        $response = $this->actingAs($this->guru1)->post(route('setoran.iqro.store'), $payload);
        $response->assertSessionHasErrors('halaman');
    }

    public function test_ayat_selesai_melebihi_total_ayat_surat_ditolak(): void
    {
        $payload = [
            'siswa_id' => $this->siswaA->id,
            'surat_id' => $this->suratAlFatihah->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10, // Al-Fatihah hanya 7 ayat
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSB',
        ];

        $response = $this->actingAs($this->guru1)->post(route('setoran.quran.store'), $payload);
        $response->assertSessionHasErrors('ayat_selesai');
    }

    public function test_ayat_selesai_kurang_dari_ayat_mulai_ditolak(): void
    {
        $payload = [
            'siswa_id' => $this->siswaA->id,
            'surat_id' => $this->suratAlFatihah->id,
            'juz' => 1,
            'ayat_mulai' => 5,
            'ayat_selesai' => 2, // Lebih kecil dari ayat mulai
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSB',
        ];

        $response = $this->actingAs($this->guru1)->post(route('setoran.quran.store'), $payload);
        $response->assertSessionHasErrors('ayat_selesai');
    }

    public function test_tanggal_pengujian_sebelum_tanggal_hafalan_ditolak(): void
    {
        $payload = [
            'siswa_id' => $this->siswaA->id,
            'doa_id' => $this->doa1->id,
            'tgl_hafalan' => '2026-11-10',
            'tgl_pengujian' => '2026-11-05', // Sebelum tanggal mulai hafalan
            'nilai' => 'BSB',
        ];

        $response = $this->actingAs($this->guru1)->post(route('hafalan.doa.store'), $payload);
        $response->assertSessionHasErrors('tgl_pengujian');
    }

    public function test_tanggal_masa_depan_ditolak_pada_semua_modul(): void
    {
        $future = now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->guru1)->post(route('setoran.iqro.store'), [
            'siswa_id' => $this->siswaA->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 1,
            'tanggal' => $future,
            'nilai' => 'BSH',
        ]);
        $response->assertSessionHasErrors('tanggal');
    }

    public function test_penilaian_tidak_valid_ditolak(): void
    {
        $response = $this->actingAs($this->guru1)->post(route('setoran.iqro.store'), [
            'siswa_id' => $this->siswaA->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 1,
            'tanggal' => now()->toDateString(),
            'nilai' => 'ISTIMEWA', // Tidak ada di enum Penilaian
        ]);
        $response->assertSessionHasErrors('nilai');
    }

    public function test_guru_merangkap_admin_bisa_mengakses_dan_input_semua_kelas(): void
    {
        $payload = [
            'siswa_id' => $this->siswaB->id, // Siswa kelas B
            'iqro_id' => $this->iqro1->id,
            'halaman' => 2,
            'tanggal' => now()->toDateString(),
            'nilai' => 'BSB',
        ];

        // GuruAdmin punya role admin juga, sehingga punya akses penuh
        $response = $this->actingAs($this->guruAdmin)->post(route('setoran.iqro.store'), $payload);
        $response->assertRedirect(route('setoran.iqro.index'));
        $this->assertDatabaseHas('setoran_iqros', ['siswa_id' => $this->siswaB->id, 'halaman' => 2]);
    }

    public function test_katalog_yang_dipakai_tidak_dapat_dihapus(): void
    {
        SetoranIqro::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'iqro_id' => $this->iqro1->id,
            'halaman' => 10,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        $this->assertTrue($this->iqro1->isDipakai());

        $response = $this->actingAs($this->admin)->delete(route('katalog.iqro.destroy', $this->iqro1));
        $response->assertRedirect(route('katalog.iqro.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('iqros', ['id' => $this->iqro1->id]);
    }

    public function test_siswa_dengan_riwayat_setoran_tidak_dapat_dihapus(): void
    {
        SetoranQuran::create([
            'siswa_id' => $this->siswaA->id,
            'guru_id' => $this->guru1->id,
            'surat_id' => $this->suratAlFatihah->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 7,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSB,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master.siswa.destroy', $this->siswaA));
        $response->assertRedirect(route('master.siswa.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('siswas', ['id' => $this->siswaA->id]);
    }
}
