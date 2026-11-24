<?php

namespace Tests\Feature;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\AlfabetLatin;
use App\Models\BacaBuku;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\HafalanDoa;
use App\Models\HafalanHadist;
use App\Models\HafalanSurat;
use App\Models\Iqro;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\SetoranIqro;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditHapusSemuaFiturTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kelas $kelas;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Permission::firstOrCreate(['name' => 'kelola-spp']);

        $this->admin = User::factory()->create(['nip' => 'admin_test']);
        $this->admin->assignRole('admin');
        $this->admin->givePermissionTo('kelola-spp');

        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas Mawar']);
        $this->siswa = Siswa::create([
            'nis' => '1001',
            'nama_siswa' => 'Ahmad Santri',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-01-01',
            'nama_orang_tua' => 'Bapak Ahmad',
        ]);
    }

    public function test_edit_update_hapus_setoran_quran_berhasil(): void
    {
        $surat = Surat::create([
            'nomor' => 1,
            'nama_surat' => 'Al-Fatihah',
            'nama_arab' => 'الفاتحة',
            'juz' => 1,
            'jumlah_ayat' => 7,
        ]);

        $setoran = SetoranQuran::create([
            'siswa_id' => $this->siswa->id,
            'guru_id' => $this->admin->id,
            'surat_id' => $surat->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 5,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        // 1. Edit page
        $resEdit = $this->actingAs($this->admin)->get(route('setoran.quran.edit', $setoran));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Catatan Setoran', false);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('setoran.quran.update', $setoran), [
            'siswa_id' => $this->siswa->id,
            'surat_id' => $surat->id,
            'juz' => 1,
            'ayat_mulai' => 1,
            'ayat_selesai' => 7,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSB->value,
        ]);
        $resUpdate->assertRedirect(route('setoran.quran.index'));
        $this->assertEquals(7, $setoran->fresh()->ayat_selesai);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('setoran.quran.destroy', $setoran));
        $resDelete->assertRedirect(route('setoran.quran.index'));
        $this->assertDatabaseMissing('setoran_qurans', ['id' => $setoran->id]);
    }

    public function test_edit_update_hapus_hafalan_doa_berhasil(): void
    {
        $doa = Doa::create([
            'nama_doa' => 'Doa Mau Tidur',
            'baca_latin' => 'Bismika allahumma ahya wa amut',
            'artinya' => 'Dengan nama-Mu ya Allah aku hidup dan aku mati',
        ]);

        $hafalan = HafalanDoa::create([
            'siswa_id' => $this->siswa->id,
            'guru_id' => $this->admin->id,
            'doa_id' => $doa->id,
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        // 1. Edit page
        $resEdit = $this->actingAs($this->admin)->get(route('hafalan.doa.edit', $hafalan));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Catatan Setoran Doa Harian', false);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('hafalan.doa.update', $hafalan), [
            'siswa_id' => $this->siswa->id,
            'doa_id' => $doa->id,
            'tgl_hafalan' => now()->subDays(2)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSB->value,
            'ket' => 'Lancar sekali',
        ]);
        $resUpdate->assertRedirect(route('hafalan.doa.index'));
        $this->assertEquals(Penilaian::BSB, $hafalan->fresh()->nilai);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('hafalan.doa.destroy', $hafalan));
        $resDelete->assertRedirect(route('hafalan.doa.index'));
        $this->assertDatabaseMissing('hafalan_doas', ['id' => $hafalan->id]);
    }

    public function test_edit_update_hapus_hafalan_hadist_berhasil(): void
    {
        $hadist = Hadist::create([
            'nama_hadist' => 'Hadits Kebersihan',
            'baca_latin' => 'At-thuhuru syathrul iman',
            'artinya' => 'Kebersihan itu sebagian dari iman',
        ]);

        $hafalan = HafalanHadist::create([
            'siswa_id' => $this->siswa->id,
            'guru_id' => $this->admin->id,
            'hadist_id' => $hadist->id,
            'tgl_hafalan' => now()->subDays(3)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::MB,
        ]);

        // 1. Edit page
        $resEdit = $this->actingAs($this->admin)->get(route('hafalan.hadist.edit', $hafalan));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Catatan Setoran Hadist', false);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('hafalan.hadist.update', $hafalan), [
            'siswa_id' => $this->siswa->id,
            'hadist_id' => $hadist->id,
            'tgl_hafalan' => now()->subDays(3)->toDateString(),
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH->value,
        ]);
        $resUpdate->assertRedirect(route('hafalan.hadist.index'));
        $this->assertEquals(Penilaian::BSH, $hafalan->fresh()->nilai);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('hafalan.hadist.destroy', $hafalan));
        $resDelete->assertRedirect(route('hafalan.hadist.index'));
        $this->assertDatabaseMissing('hafalan_hadists', ['id' => $hafalan->id]);
    }

    public function test_edit_update_hapus_setoran_iqro_berhasil(): void
    {
        $iqro = Iqro::create([
            'nama_iqro' => 'Iqro 1',
            'jumlah_halaman' => 32,
        ]);

        $setoran = SetoranIqro::create([
            'siswa_id' => $this->siswa->id,
            'guru_id' => $this->admin->id,
            'iqro_id' => $iqro->id,
            'halaman' => 10,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        // 1. Edit page
        $resEdit = $this->actingAs($this->admin)->get(route('setoran.iqro.edit', $setoran));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Catatan Setoran Iqro', false);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('setoran.iqro.update', $setoran), [
            'siswa_id' => $this->siswa->id,
            'iqro_id' => $iqro->id,
            'halaman' => 12,
            'tanggal' => now()->toDateString(),
            'nilai' => Penilaian::BSB->value,
        ]);
        $resUpdate->assertRedirect(route('setoran.iqro.index'));
        $this->assertEquals(12, $setoran->fresh()->halaman);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('setoran.iqro.destroy', $setoran));
        $resDelete->assertRedirect(route('setoran.iqro.index'));
        $this->assertDatabaseMissing('setoran_iqros', ['id' => $setoran->id]);
    }

    public function test_edit_update_hapus_baca_buku_berhasil(): void
    {
        $alfabet = Alfabet::create(['nama_alfabet' => 'Ba']);

        $baca = BacaBuku::create([
            'siswa_id' => $this->siswa->id,
            'guru_id' => $this->admin->id,
            'jilid' => '1',
            'halaman' => 5,
            'alfabet_id' => $alfabet->id,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSH,
        ]);

        // 1. Edit page
        $resEdit = $this->actingAs($this->admin)->get(route('baca-buku.edit', $baca));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Catatan Setoran Alfabet', false);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('baca-buku.update', $baca), [
            'siswa_id' => $this->siswa->id,
            'jilid' => '1',
            'halaman' => 8,
            'jenis_huruf' => 'hijaiyah',
            'alfabet_id' => $alfabet->id,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => Penilaian::BSB->value,
        ]);
        $resUpdate->assertRedirect(route('baca-buku.index'));
        $this->assertEquals(8, $baca->fresh()->halaman);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('baca-buku.destroy', $baca));
        $resDelete->assertRedirect(route('baca-buku.index'));
        $this->assertDatabaseMissing('baca_bukus', ['id' => $baca->id]);
    }

    public function test_edit_update_hapus_alfabet_latin_berhasil(): void
    {
        $latin = AlfabetLatin::create(['huruf' => 'Z', 'ket' => 'Huruf Z']);

        // 1. Edit
        $resEdit = $this->actingAs($this->admin)->get(route('katalog.alfabet-latin.edit', $latin));
        $resEdit->assertStatus(200);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('katalog.alfabet-latin.update', $latin), [
            'huruf' => 'Z',
            'ket' => 'Huruf Z Update',
        ]);
        $resUpdate->assertRedirect(route('katalog.alfabet-latin.index'));
        $this->assertEquals('Huruf Z Update', $latin->fresh()->ket);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('katalog.alfabet-latin.destroy', $latin));
        $resDelete->assertRedirect(route('katalog.alfabet-latin.index'));
        $this->assertDatabaseMissing('alfabet_latins', ['id' => $latin->id]);
    }

    public function test_edit_update_hapus_spp_berhasil(): void
    {
        $spp = PembayaranSpp::create([
            'siswa_id' => $this->siswa->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        // 1. Edit
        $resEdit = $this->actingAs($this->admin)->get(route('spp.edit', $spp));
        $resEdit->assertStatus(200);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('spp.update', $spp), [
            'siswa_id' => $this->siswa->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 200000,
            'tgl_bayar' => now()->toDateString(),
            'ket' => 'SPP Mei update',
        ]);
        $resUpdate->assertRedirect(route('spp.index'));
        $this->assertEquals(200000, $spp->fresh()->nominal);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('spp.destroy', $spp));
        $resDelete->assertRedirect(route('spp.index'));
        $this->assertDatabaseMissing('pembayaran_spps', ['id' => $spp->id]);
    }

    public function test_edit_update_hapus_guru_berhasil(): void
    {
        $guru = User::create([
            'nip' => 'guru999',
            'name' => 'Ustadzah Maryam',
            'password' => bcrypt('password'),
        ]);
        $guru->assignRole('guru');

        // 1. Edit
        $resEdit = $this->actingAs($this->admin)->get(route('master.guru.edit', $guru));
        $resEdit->assertStatus(200);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('master.guru.update', $guru), [
            'nip' => 'guru999',
            'name' => 'Ustadzah Maryam Update',
            'roles' => ['guru'],
        ]);
        $resUpdate->assertRedirect(route('master.guru.index'));
        $this->assertEquals('Ustadzah Maryam Update', $guru->fresh()->name);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('master.guru.destroy', $guru));
        $resDelete->assertRedirect(route('master.guru.index'));
        $this->assertDatabaseMissing('users', ['id' => $guru->id]);
    }

    public function test_edit_update_hapus_kelas_berhasil(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Melati Kosong']);

        // 1. Edit
        $resEdit = $this->actingAs($this->admin)->get(route('master.kelas.edit', $kelas));
        $resEdit->assertStatus(200);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('master.kelas.update', $kelas), [
            'nama_kelas' => 'Kelas Melati Update',
        ]);
        $resUpdate->assertRedirect(route('master.kelas.index'));
        $this->assertEquals('Kelas Melati Update', $kelas->fresh()->nama_kelas);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('master.kelas.destroy', $kelas));
        $resDelete->assertRedirect(route('master.kelas.index'));
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    public function test_edit_update_hapus_siswa_berhasil(): void
    {
        $siswaBaru = Siswa::create([
            'nis' => '9999',
            'nama_siswa' => 'Santri Siap Hapus',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2020-05-05',
            'nama_orang_tua' => 'Ibu Siti',
        ]);

        // 1. Edit
        $resEdit = $this->actingAs($this->admin)->get(route('master.siswa.edit', $siswaBaru));
        $resEdit->assertStatus(200);

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)->put(route('master.siswa.update', $siswaBaru), [
            'nis' => '9999',
            'nama_siswa' => 'Santri Update',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2020-05-05',
            'nama_orang_tua' => 'Ibu Siti',
        ]);
        $resUpdate->assertRedirect(route('master.siswa.index'));
        $this->assertEquals('Santri Update', $siswaBaru->fresh()->nama_siswa);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)->delete(route('master.siswa.destroy', $siswaBaru));
        $resDelete->assertRedirect(route('master.siswa.index'));
        $this->assertDatabaseMissing('siswas', ['id' => $siswaBaru->id]);
    }
}
