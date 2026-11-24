<?php

namespace Tests\Feature;

use App\Enums\Penilaian;
use App\Models\Alfabet;
use App\Models\AlfabetLatin;
use App\Models\BacaBuku;
use App\Models\HafalanSurat;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\SetoranQuran;
use App\Models\Siswa;
use App\Models\Surat;
use App\Models\User;
use Database\Seeders\AlfabetLatinSeeder;
use Database\Seeders\SuratSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Revisi1Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected Kelas $kelas;
    protected Siswa $siswa;
    protected Surat $suratAlFatihah;
    protected Alfabet $alfabetHijaiyah;
    protected AlfabetLatin $alfabetLatinA;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleGuru = Role::create(['name' => 'guru']);
        $permSpp = Permission::create(['name' => 'kelola-spp']);
        $roleAdmin->givePermissionTo($permSpp);

        $this->admin = User::factory()->create(['nip' => 'ADM999', 'name' => 'Admin Utama']);
        $this->admin->assignRole('admin');

        $this->guru = User::factory()->create(['nip' => 'GUR999', 'name' => 'Ustadz Ahmad']);
        $this->guru->assignRole('guru');

        $this->kelas = Kelas::create(['nama_kelas' => 'Kelas Mawar']);
        $this->guru->kelas()->attach($this->kelas->id);

        $this->siswa = Siswa::create([
            'nis' => '20269901',
            'nama_siswa' => 'Santri Cilik',
            'alamat' => 'Jl. Kenanga No. 1',
            'nama_orang_tua' => 'Bapak Santri',
            'nama_wali' => 'K.H. Mustofa',
            'no_hp_ortu' => '081299990001',
            'kelas_id' => $this->kelas->id,
        ]);

        $this->suratAlFatihah = Surat::create([
            'nomor' => 1,
            'nama_surat' => 'Al-Fatihah',
            'nama_arab' => 'الفاتحة',
            'juz' => 1,
            'jumlah_ayat' => 7,
        ]);

        $this->alfabetHijaiyah = Alfabet::create([
            'nama_alfabet' => 'Alif',
            'ket' => 'ا',
        ]);

        $this->alfabetLatinA = AlfabetLatin::create([
            'huruf' => 'A',
            'ket' => 'Huruf latin kapital A',
        ]);
    }

    public function test_accessor_wali_tampil_berfungsi_dengan_benar(): void
    {
        $this->assertEquals('K.H. Mustofa', $this->siswa->wali_tampil);

        $siswaTanpaWali = Siswa::create([
            'nis' => '20269902',
            'nama_siswa' => 'Santri Kedua',
            'alamat' => 'Jl. Melati No. 2',
            'nama_orang_tua' => 'Ibu Halimah',
            'nama_wali' => null,
            'no_hp_ortu' => '081299990002',
            'kelas_id' => $this->kelas->id,
        ]);
        $this->assertEquals('Ibu Halimah', $siswaTanpaWali->wali_tampil);

        $siswaPolos = Siswa::create([
            'nis' => '20269903',
            'nama_siswa' => 'Santri Ketiga',
            'alamat' => 'Jl. Mawar No. 3',
            'nama_orang_tua' => null,
            'nama_wali' => null,
            'no_hp_ortu' => '081299990003',
            'kelas_id' => $this->kelas->id,
        ]);
        $this->assertEquals('-', $siswaPolos->wali_tampil);
    }

    public function test_admin_bisa_tambah_dan_ubah_siswa_dengan_field_nama_wali(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master.siswa.store'), [
            'nis' => '20269904',
            'nama_siswa' => 'Santri Baru',
            'alamat' => 'Jl. Anggrek',
            'nama_orang_tua' => 'Bapak Hendra',
            'nama_wali' => 'Drs. H. Maimun',
            'no_hp_ortu' => '081299990004',
            'kelas_id' => $this->kelas->id,
        ]);

        $response->assertRedirect(route('master.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'nis' => '20269904',
            'nama_wali' => 'Drs. H. Maimun',
        ]);

        $siswaBaru = Siswa::where('nis', '20269904')->first();
        $responseUpdate = $this->actingAs($this->admin)->put(route('master.siswa.update', $siswaBaru), [
            'nis' => '20269904',
            'nama_siswa' => 'Santri Baru Diedit',
            'alamat' => 'Jl. Anggrek Baru',
            'nama_orang_tua' => 'Bapak Hendra',
            'nama_wali' => 'Ustadz Zakaria',
            'no_hp_ortu' => '081299990004',
            'kelas_id' => $this->kelas->id,
        ]);

        $responseUpdate->assertRedirect(route('master.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'id' => $siswaBaru->id,
            'nama_wali' => 'Ustadz Zakaria',
        ]);
    }

    public function test_route_hafalan_surat_sudah_dihapus(): void
    {
        $response = $this->actingAs($this->guru)->get('/hafalan/surat');
        $response->assertNotFound();
    }

    public function test_tambah_siswa_dan_guru_dengan_jenis_kelamin_radio_button(): void
    {
        // Uji simpan santri dengan jenis kelamin L
        $resSiswa = $this->actingAs($this->admin)->post(route('master.siswa.store'), [
            'nis' => '20268801',
            'nama_siswa' => 'Santri Putra',
            'jenis_kelamin' => 'L',
            'nama_ayah_kandung' => 'Ayah Santri',
            'kelas_id' => $this->kelas->id,
        ]);
        $resSiswa->assertRedirect(route('master.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'nis' => '20268801',
            'jenis_kelamin' => 'L',
        ]);

        $siswa = Siswa::where('nis', '20268801')->first();
        $this->assertEquals('Laki-laki', $siswa->jenis_kelamin_label);

        // Uji simpan guru dengan jenis kelamin P
        $resGuru = $this->actingAs($this->admin)->post(route('master.guru.store'), [
            'nip' => 'GUR8801',
            'name' => 'Ustadzah Aisyah',
            'jenis_kelamin' => 'P',
            'password' => 'password123',
            'roles' => ['guru'],
        ]);
        $resGuru->assertRedirect(route('master.guru.index'));
        $this->assertDatabaseHas('users', [
            'nip' => 'GUR8801',
            'jenis_kelamin' => 'P',
        ]);

        $guru = User::where('nip', 'GUR8801')->first();
        $this->assertEquals('Perempuan', $guru->jenis_kelamin_label);
    }

    public function test_seeder_surat_menghasilkan_114_surat_dengan_nama_arab_dan_total_ayat_6236(): void
    {
        $this->seed(SuratSeeder::class);

        $this->assertEquals(114, Surat::count());
        $this->assertEquals(6236, Surat::sum('jumlah_ayat'));

        $alBaqarah = Surat::where('nomor', 2)->first();
        $this->assertNotNull($alBaqarah);
        $this->assertEquals('البقرة', $alBaqarah->nama_arab);
        $this->assertStringContainsString('البقرة', $alBaqarah->nama_lengkap);

        $anNas = Surat::where('nomor', 114)->first();
        $this->assertNotNull($anNas);
        $this->assertEquals('الناس', $anNas->nama_arab);
    }

    public function test_admin_bisa_crud_kumpulan_alfabet_latin(): void
    {
        $this->seed(AlfabetLatinSeeder::class);
        $this->assertEquals(26, AlfabetLatin::count());

        $indexRes = $this->actingAs($this->admin)->get(route('katalog.alfabet-latin.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Kumpulan Alfabet');

        // Non-admin ditolak
        $guruRes = $this->actingAs($this->guru)->get(route('katalog.alfabet-latin.index'));
        $guruRes->assertForbidden();

        // Admin tambah huruf latin baru
        $storeRes = $this->actingAs($this->admin)->post(route('katalog.alfabet-latin.store'), [
            'huruf' => 'CH',
            'ket' => 'Diftong CH',
        ]);
        $storeRes->assertRedirect(route('katalog.alfabet-latin.index'));
        $this->assertDatabaseHas('alfabet_latins', ['huruf' => 'CH']);
    }

    public function test_baca_buku_bisa_memilih_antara_hijaiyah_atau_latin_secara_eksklusif(): void
    {
        // 1. Simpan dengan Huruf Hijaiyah
        $resHijaiyah = $this->actingAs($this->guru)->post(route('baca-buku.store'), [
            'siswa_id' => $this->siswa->id,
            'jenis_huruf' => 'hijaiyah',
            'alfabet_id' => $this->alfabetHijaiyah->id,
            'jilid' => 'Jilid 1',
            'halaman' => 12,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => 'BSH',
        ]);
        $resHijaiyah->assertRedirect(route('baca-buku.index'));
        $this->assertDatabaseHas('baca_bukus', [
            'siswa_id' => $this->siswa->id,
            'alfabet_id' => $this->alfabetHijaiyah->id,
            'alfabet_latin_id' => null,
        ]);

        // 2. Simpan dengan Huruf Latin
        $resLatin = $this->actingAs($this->guru)->post(route('baca-buku.store'), [
            'siswa_id' => $this->siswa->id,
            'jenis_huruf' => 'latin',
            'alfabet_latin_id' => $this->alfabetLatinA->id,
            'jilid' => 'Jilid 2',
            'halaman' => 5,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => 'BSB',
        ]);
        $resLatin->assertRedirect(route('baca-buku.index'));
        $this->assertDatabaseHas('baca_bukus', [
            'siswa_id' => $this->siswa->id,
            'alfabet_latin_id' => $this->alfabetLatinA->id,
            'alfabet_id' => null,
        ]);

        // 3. Menolak jika keduanya kosong
        $resEmpty = $this->actingAs($this->guru)->post(route('baca-buku.store'), [
            'siswa_id' => $this->siswa->id,
            'jilid' => 'Jilid 1',
            'halaman' => 1,
            'tgl_pengujian' => now()->toDateString(),
            'nilai' => 'BSH',
        ]);
        $resEmpty->assertSessionHasErrors();

        // 4. Huruf yang dipakai tidak dapat dihapus (isDipakai)
        $this->assertTrue($this->alfabetHijaiyah->isDipakai());
        $this->assertTrue($this->alfabetLatinA->isDipakai());

        $delRes = $this->actingAs($this->admin)->delete(route('katalog.alfabet-latin.destroy', $this->alfabetLatinA));
        $delRes->assertRedirect(route('katalog.alfabet-latin.index'));
        $delRes->assertSessionHas('error');
        $this->assertDatabaseHas('alfabet_latins', ['id' => $this->alfabetLatinA->id]);
    }

    public function test_sidebar_memiliki_penamaan_transaksi_baru_dan_tanpa_setoran_surat(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Kumpulan Materi');
        $response->assertSee('Huruf Hijaiyah');
        $response->assertSee('Kumpulan Alfabet');
        $response->assertSee('Setoran Nama Surat');
        $response->assertSee('Setoran Doa Harian');
        $response->assertSee('Setoran Hadist');
        $response->assertSee('Setoran Alfabet');
        $response->assertDontSee('Setoran Surat');
    }

    public function test_input_nominal_uang_berhasil_menerima_format_bulat_ribuan(): void
    {
        $testCases = [
            ['input' => '400.000', 'expected' => 400000, 'bulan' => 1],
            ['input' => 'Rp 150.000', 'expected' => 150000, 'bulan' => 2],
            ['input' => '50.000', 'expected' => 50000, 'bulan' => 3],
            ['input' => '1.000.000', 'expected' => 1000000, 'bulan' => 4],
        ];

        foreach ($testCases as $tc) {
            $response = $this->actingAs($this->admin)->post(route('spp.store'), [
                'siswa_id' => $this->siswa->id,
                'bulan' => $tc['bulan'],
                'tahun' => 2026,
                'nominal' => $tc['input'],
                'tgl_bayar' => now()->toDateString(),
                'ket' => 'Pembayaran uji nominal',
            ]);

            $response->assertRedirect(route('spp.index'));
            $this->assertDatabaseHas('pembayaran_spps', [
                'siswa_id' => $this->siswa->id,
                'bulan' => $tc['bulan'],
                'tahun' => 2026,
                'nominal' => $tc['expected'],
            ]);
        }

        // Uji pengaturan default juga menerima 400.000
        $resPengaturan = $this->actingAs($this->admin)->post(route('spp.pengaturan.simpan'), [
            'spp_nominal_default' => '400.000',
            'spp_jatuh_tempo_hari' => 10,
        ]);
        $resPengaturan->assertRedirect(route('spp.pengaturan'));
        $this->assertEquals(400000, (int) Pengaturan::get('spp_nominal_default'));
    }

    public function test_command_lemon_bersihkan_data_berhasil_membersihkan_transaksi(): void
    {
        // Buat data transaksi
        PembayaranSpp::create([
            'siswa_id' => $this->siswa->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $this->assertDatabaseCount('pembayaran_spps', 1);

        $this->artisan('lemon:bersihkan-data', ['--force' => true])
            ->assertExitCode(0);

        $this->assertDatabaseCount('pembayaran_spps', 0);
        $this->assertDatabaseCount('siswas', 0);
        $this->assertDatabaseCount('surats', 1);
        $this->assertDatabaseCount('alfabets', 1);
        $this->assertDatabaseCount('alfabet_latins', 1);
        $this->assertDatabaseHas('users', ['nip' => 'admin']);
    }

    public function test_role_kepsek_dapat_dibuat_dan_dipakai_untuk_surat(): void
    {
        Role::firstOrCreate(['name' => 'kepsek']);

        $res = $this->actingAs($this->admin)->post(route('master.guru.store'), [
            'nip' => 'KEPSEK_TEST',
            'name' => 'Dr. H. Ahmad Dahlan, M.Pd.',
            'jenis_kelamin' => 'L',
            'email' => 'dahlan@lemon.test',
            'no_hp' => '081234567800',
            'alamat' => 'Jakarta Selatan',
            'password' => 'secret123',
            'roles' => ['kepsek'],
        ]);
        $res->assertRedirect(route('master.guru.index'));

        $kepsek = User::where('nip', 'KEPSEK_TEST')->first();
        $this->assertNotNull($kepsek);
        $this->assertTrue($kepsek->hasRole('kepsek'));

        // Cek bahwa data kepsek ditarik di Pengaturan::getNamaKepala()
        $this->assertEquals('Dr. H. Ahmad Dahlan, M.Pd.', Pengaturan::getNamaKepala());
        $this->assertEquals('KEPSEK_TEST', Pengaturan::getNipKepala());

        // Cek pada surat tunggakan
        $resSurat = $this->actingAs($this->admin)->get(route('spp.surat-tunggakan', [
            'siswa' => $this->siswa->id,
            'bulan' => 5,
            'tahun' => 2026,
        ]));
        $resSurat->assertOk();
    }

    public function test_form_create_guru_password_clear_dan_ada_toggle_view_pass(): void
    {
        $res = $this->actingAs($this->admin)->get(route('master.guru.create'));
        $res->assertOk();
        $res->assertSee('showPass', false);
        $res->assertSee('autocomplete="new-password"', false);
    }

    public function test_alamat_real_sekolah_pada_surat_dan_laporan(): void
    {
        $realAlamat = 'Jl. H. Nirin No. 63, RT.3/RW.5, Petukangan Selatan, Kec. Pesanggrahan, Kota Jakarta Selatan, DKI Jakarta 12270';
        $this->assertEquals($realAlamat, Pengaturan::get('alamat_lembaga'));
        $this->assertEquals($realAlamat, Pengaturan::getAlamatLembaga());

        // Buat role kepsek dan user kepsek dengan alamat khusus
        Role::firstOrCreate(['name' => 'kepsek', 'guard_name' => 'web']);
        $customAlamat = 'Jl. Villa Anggrek No. 88, Kebayoran Lama, Jakarta Selatan';
        $userKepsek = User::factory()->create([
            'nip' => 'KEPSEK_ADDR_TEST',
            'name' => 'H. Mukhlis, M.Pd.',
            'alamat' => $customAlamat,
        ]);
        $userKepsek->assignRole('kepsek');

        // Pastikan alamat lembaga ditarik dari alamat user kepsek
        $this->assertEquals($customAlamat, Pengaturan::get('alamat_lembaga'));
        $this->assertEquals($customAlamat, Pengaturan::getAlamatLembaga());

        // Pastikan surat tunggakan dan kuitansi dapat diakses
        $resSurat = $this->actingAs($this->admin)->get(route('spp.surat-tunggakan', [
            'siswa' => $this->siswa->id,
            'bulan' => 5,
            'tahun' => 2026,
        ]));
        $resSurat->assertOk();

        // Buat satu pembayaran spp untuk tes kuitansi HTML
        $pembayaran = PembayaranSpp::factory()->create([
            'siswa_id' => $this->siswa->id,
            'dicatat_oleh' => $this->admin->id,
        ]);
        $resKuitansi = $this->actingAs($this->admin)->get(route('spp.kuitansi', $pembayaran->id));
        $resKuitansi->assertOk();
        $resKuitansi->assertSee($customAlamat);
    }
}
