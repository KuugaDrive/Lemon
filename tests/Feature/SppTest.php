<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SppTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $guruAdmin;
    protected User $bendahara;
    protected Kelas $kelasA;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'guru']);
        $bendaharaRole = Role::create(['name' => 'bendahara']);

        $permissionSpp = Permission::create(['name' => 'kelola-spp']);
        $adminRole->givePermissionTo($permissionSpp);
        $bendaharaRole->givePermissionTo($permissionSpp);

        $this->admin = User::factory()->create(['nip' => 'ADM001', 'name' => 'Admin Utama']);
        $this->admin->assignRole('admin');

        $this->guru = User::factory()->create(['nip' => 'GUR001', 'name' => 'Guru Biasa']);
        $this->guru->assignRole('guru');

        $this->guruAdmin = User::factory()->create(['nip' => 'GUR002', 'name' => 'Guru Merangkap Admin']);
        $this->guruAdmin->assignRole(['admin', 'guru']);

        $this->bendahara = User::factory()->create(['nip' => 'BEN001', 'name' => 'Staff Bendahara']);
        $this->bendahara->assignRole('bendahara');

        $this->kelasA = Kelas::create(['nama_kelas' => 'Kelas A (Abu Bakar)']);
        $this->guru->kelas()->attach($this->kelasA->id);

        $this->siswa1 = Siswa::create([
            'nis' => '1001',
            'nama_siswa' => 'Ahmad Santri',
            'alamat' => 'Jl. Mawar 1',
            'nama_orang_tua' => 'Bapak Ahmad',
            'no_hp_ortu' => '08123456789',
            'kelas_id' => $this->kelasA->id,
        ]);

        $this->siswa2 = Siswa::create([
            'nis' => '1002',
            'nama_siswa' => 'Budi Santri',
            'alamat' => 'Jl. Mawar 2',
            'nama_orang_tua' => 'Bapak Budi',
            'no_hp_ortu' => '08123456788',
            'kelas_id' => $this->kelasA->id,
        ]);

        Pengaturan::set('spp_nominal_default', 150000);
        Pengaturan::set('spp_jatuh_tempo_hari', 10);
    }

    public function test_guru_non_admin_ditolak_pada_semua_route_spp(): void
    {
        $this->actingAs($this->guru)->get(route('spp.index'))->assertStatus(403);
        $this->actingAs($this->guru)->get(route('spp.create'))->assertStatus(403);
        $this->actingAs($this->guru)->get(route('spp.massal'))->assertStatus(403);
        $this->actingAs($this->guru)->get(route('spp.tunggakan'))->assertStatus(403);
        $this->actingAs($this->guru)->get(route('spp.pengaturan'))->assertStatus(403);
    }

    public function test_admin_dan_bendahara_dapat_mengakses_halaman_spp(): void
    {
        $this->actingAs($this->admin)->get(route('spp.index'))->assertStatus(200);
        $this->actingAs($this->bendahara)->get(route('spp.index'))->assertStatus(200);
        $this->actingAs($this->guruAdmin)->get(route('spp.index'))->assertStatus(200);
    }

    public function test_admin_dapat_mencatat_pembayaran_spp_satuan(): void
    {
        $payload = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'ket' => 'Lunas via Transfer',
        ];

        $response = $this->actingAs($this->admin)->post(route('spp.store'), $payload);
        $response->assertRedirect(route('spp.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pembayaran_spps', [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'dicatat_oleh' => $this->admin->id,
        ]);
    }

    public function test_duplikasi_pembayaran_spp_periode_sama_ditolak(): void
    {
        PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $payload = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->admin)->post(route('spp.store'), $payload);
        $response->assertSessionHasErrors('bulan');
    }

    public function test_nominal_kurang_dari_satu_dan_tanggal_masa_depan_ditolak(): void
    {
        $payload = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 0, // Tidak valid
            'tgl_bayar' => now()->addDays(2)->toDateString(), // Masa depan
        ];

        $response = $this->actingAs($this->admin)->post(route('spp.store'), $payload);
        $response->assertSessionHasErrors(['nominal', 'tgl_bayar']);
    }

    public function test_input_spp_massal_satu_kelas_berhasil_dan_mengabaikan_yang_sudah_lunas(): void
    {
        // Siswa 1 sudah bayar duluan
        PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $payload = [
            'kelas_id' => $this->kelasA->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'siswa_ids' => [$this->siswa1->id, $this->siswa2->id],
            'ket' => 'Pembayaran massal',
        ];

        $response = $this->actingAs($this->admin)->post(route('spp.massal.store'), $payload);
        $response->assertRedirect(route('spp.index'));
        $response->assertSessionHas('success');

        // Total transaksi harus 2 (siswa 1 tidak diduplikat, siswa 2 berhasil dibuat)
        $this->assertDatabaseCount('pembayaran_spps', 2);
        $this->assertDatabaseHas('pembayaran_spps', ['siswa_id' => $this->siswa2->id, 'bulan' => 11, 'tahun' => 2026]);
    }

    public function test_rekap_tunggakan_menghitung_siswa_yang_belum_bayar_dengan_benar(): void
    {
        // Siswa 1 bayar
        PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        // Siswa 2 belum bayar
        $tunggakan = SppService::tunggakan(11, 2026, $this->kelasA->id);

        $this->assertEquals(1, $tunggakan['total_santri']);
        $this->assertEquals(150000, $tunggakan['total_nominal']);
        $this->assertEquals($this->siswa2->id, $tunggakan['siswa_list']->first()->id);
    }

    public function test_perhitungan_spp_dinamis_parsial_dan_pelunasan_lanjutan(): void
    {
        Pengaturan::set('spp_nominal_default', 150000);

        // 1. Santri membayar sebagian: 100k dari 150k
        $payload1 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 100000,
            'tgl_bayar' => '2026-05-02',
            'ket' => 'Bayar awal 100k',
        ];

        $response1 = $this->actingAs($this->admin)->post(route('spp.store'), $payload1);
        $response1->assertRedirect(route('spp.index'));

        // 2. Cek endpoint cek-tagihan JSON
        $cekRes = $this->actingAs($this->admin)->getJson(route('spp.cek-tagihan', [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 5,
            'tahun' => 2026,
        ]));
        $cekRes->assertOk();
        $cekRes->assertJson([
            'default_nominal' => 150000,
            'total_terbayar' => 100000,
            'sisa_tagihan' => 50000,
            'is_lunas' => false,
            'status_label' => 'Kurang Bayar',
        ]);

        // 3. Cek monitoring tunggakan: Siswa 1 harus TETAP muncul di daftar tunggakan dengan status kurang bayar dan sisa 50k
        $tunggakan = SppService::tunggakan(5, 2026, $this->kelasA->id);
        $this->assertEquals(2, $tunggakan['total_santri']); // Siswa 1 dan Siswa 2
        // Total tunggakan = 50k (siswa 1) + 150k (siswa 2) = 200k
        $this->assertEquals(200000, $tunggakan['total_nominal']);

        $itemSiswa1 = $tunggakan['siswa_list']->firstWhere('id', $this->siswa1->id);
        $this->assertNotNull($itemSiswa1);
        $this->assertEquals('kurang_bayar', $itemSiswa1->status_tunggakan);
        $this->assertEquals(100000, $itemSiswa1->total_dibayar);
        $this->assertEquals(50000, $itemSiswa1->sisa_tunggakan);

        // Halaman monitoring tunggakan menampilkan info Kurang Bayar dan sisa
        $resTunggakan = $this->actingAs($this->admin)->get(route('spp.tunggakan', ['bulan' => 5, 'tahun' => 2026]));
        $resTunggakan->assertOk();
        $resTunggakan->assertSee('Kurang Bayar');
        $resTunggakan->assertSee('Terbayar: Rp 100.000');
        $resTunggakan->assertSee('Sisa: Rp 50.000');

        // 4. Pembayaran lanjutan 50k diizinkan untuk melunasi
        $payload2 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 50000,
            'tgl_bayar' => '2026-05-15',
            'ket' => 'Pelunasan 50k',
        ];
        $response2 = $this->actingAs($this->admin)->post(route('spp.store'), $payload2);
        $response2->assertRedirect(route('spp.index'));

        // 5. Setelah total 150k, Siswa 1 sudah LUNAS dan hilang dari daftar tunggakan
        $tunggakanSetelahLunas = SppService::tunggakan(5, 2026, $this->kelasA->id);
        $this->assertEquals(1, $tunggakanSetelahLunas['total_santri']); // Hanya Siswa 2
        $this->assertNull($tunggakanSetelahLunas['siswa_list']->firstWhere('id', $this->siswa1->id));

        // 6. Pembayaran ketiga setelah lunas harus DITOLAK
        $payload3 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 50000,
            'tgl_bayar' => '2026-05-20',
        ];
        $response3 = $this->actingAs($this->admin)->post(route('spp.store'), $payload3);
        $response3->assertSessionHasErrors('bulan');
    }

    public function test_nominal_melebihi_tarif_spp_atau_sisa_tagihan_ditolak(): void
    {
        Pengaturan::set('spp_nominal_default', 150000);

        // 1. Siswa belum bayar (tarif 150k), coba input 250k -> Ditolak!
        $payloadLebih = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 6,
            'tahun' => 2026,
            'nominal' => 250000,
            'tgl_bayar' => now()->toDateString(),
        ];
        $resLebih = $this->actingAs($this->admin)->post(route('spp.store'), $payloadLebih);
        $resLebih->assertSessionHasErrors('nominal');

        // 2. Bayar 100k -> Berhasil (sisa 50k)
        $payload100 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 6,
            'tahun' => 2026,
            'nominal' => 100000,
            'tgl_bayar' => now()->toDateString(),
        ];
        $res100 = $this->actingAs($this->admin)->post(route('spp.store'), $payload100);
        $res100->assertRedirect(route('spp.index'));

        // 3. Sisa 50k, coba bayar 60k -> Ditolak karena melebihi sisa 50k!
        $payload60 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 6,
            'tahun' => 2026,
            'nominal' => 60000,
            'tgl_bayar' => now()->toDateString(),
        ];
        $res60 = $this->actingAs($this->admin)->post(route('spp.store'), $payload60);
        $res60->assertSessionHasErrors('nominal');

        // 4. Bayar tepat 50k -> Berhasil!
        $payload50 = [
            'siswa_id' => $this->siswa1->id,
            'bulan' => 6,
            'tahun' => 2026,
            'nominal' => 50000,
            'tgl_bayar' => now()->toDateString(),
        ];
        $res50 = $this->actingAs($this->admin)->post(route('spp.store'), $payload50);
        $res50->assertRedirect(route('spp.index'));

        // 5. Input massal melebihi tarif default 150k -> Ditolak!
        $payloadMassal = [
            'kelas_id' => $this->kelasA->id,
            'bulan' => 7,
            'tahun' => 2026,
            'nominal' => 200000,
            'tgl_bayar' => now()->toDateString(),
            'siswa_ids' => [$this->siswa1->id],
        ];
        $resMassal = $this->actingAs($this->admin)->post(route('spp.massal.store'), $payloadMassal);
        $resMassal->assertSessionHasErrors('nominal');
    }

    public function test_siswa_dengan_riwayat_spp_tidak_dapat_dihapus(): void
    {
        PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master.siswa.destroy', $this->siswa1));
        $response->assertRedirect(route('master.siswa.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('siswas', ['id' => $this->siswa1->id]);
    }

    public function test_pengguna_pencatat_spp_tidak_dapat_dihapus(): void
    {
        $petugas = User::factory()->create(['nip' => 'PET001', 'name' => 'Petugas SPP']);
        $petugas->assignRole('admin');

        PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $petugas->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master.guru.destroy', $petugas));
        $response->assertRedirect(route('master.guru.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $petugas->id]);
    }

    public function test_admin_dan_bendahara_dapat_melihat_dan_mencetak_kuitansi_spp(): void
    {
        $pembayaran = PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
            'ket' => 'Lunas Tunai',
        ]);

        $response = $this->actingAs($this->admin)->get(route('spp.kuitansi', $pembayaran));
        $response->assertStatus(200);
        $response->assertViewIs('spp.kuitansi');
        $response->assertSee($pembayaran->nomor_kuitansi);
        $response->assertSee($this->siswa1->nama_siswa);
        $response->assertSee('Seratus Lima Puluh Ribu Rupiah');
        $response->assertSee('Lunas Tunai');

        $responseBendahara = $this->actingAs($this->bendahara)->get(route('spp.kuitansi', $pembayaran));
        $responseBendahara->assertStatus(200);
    }

    public function test_guru_non_admin_ditolak_melihat_kuitansi_spp(): void
    {
        $pembayaran = PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->guru)->get(route('spp.kuitansi', $pembayaran));
        $response->assertStatus(403);
    }

    public function test_terbilang_rupiah_dan_nomor_kuitansi_bekerja_dengan_benar(): void
    {
        $this->assertEquals('Nol Rupiah', SppService::terbilangRupiah(0));
        $this->assertEquals('Seratus Lima Puluh Ribu Rupiah', SppService::terbilangRupiah(150000));
        $this->assertEquals('Dua Juta Lima Ratus Ribu Rupiah', SppService::terbilangRupiah(2500000));
        $this->assertEquals('Tujuh Puluh Lima Ribu Rupiah', SppService::terbilangRupiah(75000));

        $pembayaran = PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 5,
            'tahun' => 2026,
            'nominal' => 200000,
            'tgl_bayar' => now()->toDateString(),
            'dicatat_oleh' => $this->admin->id,
        ]);

        $this->assertStringStartsWith('KW-202605-', $pembayaran->nomor_kuitansi);
        $this->assertEquals('Dua Ratus Ribu Rupiah', $pembayaran->terbilang);
    }

    public function test_is_telat_pada_pembayaran_spp_melewati_jatuh_tempo(): void
    {
        Pengaturan::set('spp_jatuh_tempo_hari', 10);

        // Bayar tanggal 5 (tepat waktu)
        $bayarTepat = PembayaranSpp::create([
            'siswa_id' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => '2026-11-05',
            'dicatat_oleh' => $this->admin->id,
        ]);
        $this->assertFalse($bayarTepat->is_telat);

        // Bayar tanggal 24 (telat)
        $bayarTelat = PembayaranSpp::create([
            'siswa_id' => $this->siswa2->id,
            'bulan' => 11,
            'tahun' => 2026,
            'nominal' => 150000,
            'tgl_bayar' => '2026-11-24',
            'dicatat_oleh' => $this->admin->id,
        ]);
        $this->assertTrue($bayarTelat->is_telat);

        $response = $this->actingAs($this->admin)->get(route('spp.index'));
        $response->assertOk();
        $response->assertSee('Telat');
        $response->assertSee('Nama Orang Tua');
    }

    public function test_cetak_surat_tunggakan_pdf_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('spp.surat-tunggakan', [
            'siswa' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());

        // Guru non-admin ditolak
        $responseGuru = $this->actingAs($this->guru)->get(route('spp.surat-tunggakan', [
            'siswa' => $this->siswa1->id,
            'bulan' => 11,
            'tahun' => 2026,
        ]));
        $responseGuru->assertForbidden();
    }
}
